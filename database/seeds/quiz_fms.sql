-- =============================================================================
-- AviatorTutor — Phase 18: ATA 22B FMS — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fms' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'FMS — Practice', 'Twenty-five-question practice quiz on Q400 FMS. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'What FMS is fitted to the Q400?',
 'mcq', JSON_ARRAY('Honeywell Pegasus','GE Aviation Boeing','Universal Avionics UNS-1 series','Rockwell Collins'),
 JSON_OBJECT('correct_index', 2),
 'Universal Avionics UNS-1 series. Standard build single FMS; some operators dual.',
 'easy', 10),
(@quiz_practice, 'What are the four main components of the FMS?',
 'mcq', JSON_ARRAY(
   'NCU + FPCDU + DTU + Configuration Module',
   'PEC + FADEC + ECIU + ARCDU',
   'AHRS + ADC + GPS + DME',
   'EFCP + ESCP + ARCDU + IFC'
 ), JSON_OBJECT('correct_index', 0),
 'NCU + FPCDU + DTU + Config. Mnemonic: UNS-1-NCU-FPCDU-DTU-CONFIG.',
 'medium', 20),
(@quiz_practice, 'What is the FMS position sensor priority?',
 'mcq', JSON_ARRAY(
   'AHRS primary, GPS backup',
   'GPS primary, scanning DME backup, VOR/DME, then AHRS+ADC',
   'VOR/DME primary, GPS backup',
   'No priority; all equal'
 ), JSON_OBJECT('correct_index', 1),
 'GPS-DME-VOR-AHRS.',
 'hard', 30),
(@quiz_practice, 'How does scanning DME work, and what minimum stations are needed?',
 'mcq', JSON_ARRAY(
   'Single station fix',
   'FMS scans multiple DME stations and triangulates a position; requires AT LEAST 2 stations in range',
   'AHRS dead reckoning',
   'GPS only'
 ), JSON_OBJECT('correct_index', 1),
 'Scanning DME triangulates from 2+ stations. Mnemonic: SCAN-DME-2-STATIONS.',
 'hard', 40),
(@quiz_practice, 'What is the navigation database cycle structure?',
 'mcq', JSON_ARRAY(
   'Single 14-day cycle',
   'Dual-cycle: current AIRAC + next AIRAC simultaneously available; 28-day AIRAC',
   'Triple-cycle for international',
   'No cycle structure'
 ), JSON_OBJECT('correct_index', 1),
 'Dual-cycle, 28-day. Mnemonic: DUAL-CYCLE-28-DAYS.',
 'hard', 50),
(@quiz_practice, 'How long is the standard ICAO AIRAC cycle?',
 'mcq', JSON_ARRAY('7 days','14 days','28 days','56 days'),
 JSON_OBJECT('correct_index', 2),
 '28 days, ICAO standard.',
 'medium', 60),
(@quiz_practice, 'What three conditions cause the Position Uncertain message?',
 'mcq', JSON_ARRAY(
   'GPS loss only',
   'GPS lost AND DME inputs insufficient AND no other valid sensor',
   'AHRS failed only',
   'Database expired'
 ), JSON_OBJECT('correct_index', 1),
 'POSITION-UNCERTAIN-3.',
 'hard', 70),
(@quiz_practice, 'TRUE or FALSE — A GPS loss in cruise immediately triggers Position Uncertain.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. GPS loss reverts to scanning DME silently. Position Uncertain requires GPS lost + DME insufficient + no other valid sensor.',
 'hard', 80),
(@quiz_practice, 'When the FMS bearing pointer is set to FMS, where does it point?',
 'mcq', JSON_ARRAY(
   'Destination airport',
   'Next waypoint in active flight plan',
   'Current position (parking)',
   'True north'
 ), JSON_OBJECT('correct_index', 1),
 'NEXT WAYPOINT, not destination. Mnemonic: FMS-NEXT-WPT.',
 'medium', 90),
(@quiz_practice, 'How does the FMS handle high-latitude operations?',
 'mcq', JSON_ARRAY(
   'Stays on magnetic',
   'Switches heading reference from MAGNETIC to TRUE near the poles',
   'Disables heading',
   'Switches to grid only'
 ), JSON_OBJECT('correct_index', 1),
 'High-latitude switches to TRUE. Standby compass still magnetic — disagreement normal. Mnemonic: HIGH-LAT-TRUE.',
 'hard', 100),
(@quiz_practice, 'What is SBAS, and what does it support?',
 'mcq', JSON_ARRAY(
   'Surge Bleed Air System',
   'Spaced-Based Augmentation System (e.g. WAAS); supports LPV approaches',
   'Slow Battery Auxiliary Source',
   'Standby Backup Air System'
 ), JSON_OBJECT('correct_index', 1),
 'SBAS = augments GPS for LPV. Mnemonic: SBAS-LPV.',
 'medium', 110),
(@quiz_practice, 'What is the magnetic variation discrepancy threshold for FMS vs raw VOR?',
 'mcq', JSON_ARRAY(
   '<0.1° (must be exact)',
   '<1° is normal (FMS uses database variation, VOR uses station declination)',
   '<5° is normal',
   'No discrepancy is normal'
 ), JSON_OBJECT('correct_index', 1),
 '<1° normal. Mnemonic: VAR-VS-DECLINATION-1.',
 'hard', 120),
(@quiz_practice, 'How does the Sensor Watchdog work?',
 'mcq', JSON_ARRAY(
   'Manual selection only',
   'Continuously monitors all position sensors; selects best source automatically; excludes faulted sensors',
   'Annual maintenance test',
   'Pilot pushbutton'
 ), JSON_OBJECT('correct_index', 1),
 'SENSOR-WATCHDOG.',
 'medium', 130),
(@quiz_practice, 'What does LNAV provide, and where is the output sent?',
 'mcq', JSON_ARRAY(
   'Lateral Navigation; output to AFCS as steering commands',
   'Lateral and vertical navigation; output to FADEC',
   'Audio navigation only',
   'Display only, no AFCS output'
 ), JSON_OBJECT('correct_index', 0),
 'LNAV → AFCS steering.',
 'medium', 140),
(@quiz_practice, 'What does VNAV provide?',
 'mcq', JSON_ARRAY(
   'Lateral guidance only',
   'Vertical profile per altitude constraints; computes top-of-descent (TOD); output to AFCS for vertical mode',
   'Fuel management only',
   'Frequency management only'
 ), JSON_OBJECT('correct_index', 1),
 'VNAV vertical profile + TOD + AFCS.',
 'medium', 150),
(@quiz_practice, 'How does FMS frequency management work?',
 'mcq', JSON_ARRAY(
   'Manual tuning only',
   'FMS auto-tunes nav radios via ARCDU; manual override via ARCDU',
   'No automatic tuning',
   'Crew sets frequency on FPCDU only'
 ), JSON_OBJECT('correct_index', 1),
 'FREQ-AUTO-FMS-MANUAL-ARCDU.',
 'medium', 160),
(@quiz_practice, 'TRUE or FALSE — Pilot data storage is wiped when the navigation database is updated.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Pilot data preserved across DB updates.',
 'medium', 170),
(@quiz_practice, 'What does the DTU do?',
 'mcq', JSON_ARRAY(
   'Displays flight plan',
   'Loads navigation database into NCU via memory cartridge',
   'Tunes radios',
   'Stores aircraft configuration'
 ), JSON_OBJECT('correct_index', 1),
 'DTU = Data Transfer Unit. Database loader.',
 'medium', 180),
(@quiz_practice, 'What is the recommended cross-check frequency for FMS vs raw nav in cruise?',
 'mcq', JSON_ARRAY('Once per leg','Every 5 min','At least every 30 min','Only at top of descent'),
 JSON_OBJECT('correct_index', 2),
 'At least every 30 min cross-check.',
 'medium', 190),
(@quiz_practice, 'What is AFIS?',
 'mcq', JSON_ARRAY(
   'Aircraft Flight Information Service — datalink for weather/NOTAMs/dispatch',
   'Auto-Feather Inhibit Switch',
   'Air-Fuel Injection System',
   'Aft Fuselage Inspection Software'
 ), JSON_OBJECT('correct_index', 0),
 'AFIS-UNILINK.',
 'medium', 200),
(@quiz_practice, 'TRUE or FALSE — On Position Uncertain, the captain should restart the FMS.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Revert to RAW NAV (VOR/ADF/DME via Bearing pointers); cross-check; run QRH; consider divert. Restart is not the procedure.',
 'hard', 210),
(@quiz_practice, 'What is UniLink?',
 'mcq', JSON_ARRAY(
   'Universal Avionics datalink module — ACARS-equivalent',
   'A landing gear component',
   'A cabin lighting system',
   'A fire detection circuit'
 ), JSON_OBJECT('correct_index', 0),
 'UniLink = Universal Avionics ACARS equivalent.',
 'medium', 220),
(@quiz_practice, 'What does the Configuration Module do?',
 'mcq', JSON_ARRAY(
   'Stores aircraft-specific config: sensor types, output formats, software options. Read by NCU on power-up',
   'Stores flight plans',
   'Stores fuel data',
   'Stores ATC clearances'
 ), JSON_OBJECT('correct_index', 0),
 'Aircraft-specific config storage.',
 'hard', 230),
(@quiz_practice, 'TRUE or FALSE — Q400 FMS is GE Aviation manufactured.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Q400 FMS is Universal Avionics UNS-1 series.',
 'easy', 240),
(@quiz_practice, 'How is a database cycle change handled mid-flight?',
 'mcq', JSON_ARRAY(
   'Land immediately',
   'Dual-cycle architecture: swap from current to next AIRAC via FPCDU on the new cycle effective date — no ground service required',
   'Database change auto-occurs',
   'Database change is forbidden mid-flight'
 ), JSON_OBJECT('correct_index', 1),
 'Dual-cycle FPCDU swap mid-flight.',
 'hard', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'FMS — Type Rating Mock', 'Ten-question mock at type-rating oral standard.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'FMS manufacturer + model.',
 'mcq', JSON_ARRAY('Honeywell Pegasus','Universal Avionics UNS-1','GE','Rockwell Collins'),
 JSON_OBJECT('correct_index', 1), 'UNS-1.', 'easy', 10),
(@quiz_exam, 'Position sensor priority.',
 'mcq', JSON_ARRAY('VOR primary','GPS primary, scanning DME backup, VOR/DME, AHRS','AHRS primary','None — equal weighting'),
 JSON_OBJECT('correct_index', 1), 'GPS-DME-VOR-AHRS.', 'medium', 20),
(@quiz_exam, 'Position Uncertain conditions.',
 'mcq', JSON_ARRAY('GPS loss only','GPS lost + DME insufficient + no other valid sensor','Database expired','AHRS failure'),
 JSON_OBJECT('correct_index', 1), 'POSITION-UNCERTAIN-3.', 'hard', 30),
(@quiz_exam, 'Database cycle structure + AIRAC length.',
 'mcq', JSON_ARRAY('Single 14-day','Dual-cycle current+next, 28-day AIRAC','Triple 56-day','No cycle'),
 JSON_OBJECT('correct_index', 1), 'DUAL-CYCLE-28-DAYS.', 'medium', 40),
(@quiz_exam, 'High-latitude FMS heading reference.',
 'mcq', JSON_ARRAY('Magnetic always','Auto-switches to TRUE near poles','Disables heading','Grid only'),
 JSON_OBJECT('correct_index', 1), 'HIGH-LAT-TRUE.', 'hard', 50),
(@quiz_exam, 'Scanning DME minimum stations.',
 'mcq', JSON_ARRAY('1','2','3','5'),
 JSON_OBJECT('correct_index', 1), '2 stations for triangulation. Mnemonic: SCAN-DME-2-STATIONS.', 'medium', 60),
(@quiz_exam, 'FMS bearing pointer target.',
 'mcq', JSON_ARRAY('Destination','Next waypoint','True north','Current position'),
 JSON_OBJECT('correct_index', 1), 'NEXT WAYPOINT. Mnemonic: FMS-NEXT-WPT.', 'medium', 70),
(@quiz_exam, 'Frequency management logic.',
 'mcq', JSON_ARRAY('Manual only','FMS auto-tunes via ARCDU; manual override via ARCDU','FPCDU manual','No frequency mgmt'),
 JSON_OBJECT('correct_index', 1), 'FREQ-AUTO-FMS-MANUAL-ARCDU.', 'medium', 80),
(@quiz_exam, 'Cross-check FMS vs raw nav frequency.',
 'mcq', JSON_ARRAY('Once per leg','At least every 30 min','Only at top of descent','Annual'),
 JSON_OBJECT('correct_index', 1), 'Every 30 min minimum.', 'medium', 90),
(@quiz_exam, 'TRUE or FALSE — Pilot data is wiped when the database is updated.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Pilot data preserved across DB updates.', 'medium', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
