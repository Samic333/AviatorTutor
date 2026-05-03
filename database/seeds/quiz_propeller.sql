-- =============================================================================
-- AviatorTutor — Phase 16: ATA 61 Propeller — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'propeller' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Propeller — Practice', 'Twenty-five-question practice quiz on Q400 propeller system. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many composite blades does each Q400 propeller have?',
 'mcq', JSON_ARRAY('4','5','6','8'), JSON_OBJECT('correct_index', 2),
 '6 composite blades. Counterweighted. Mnemonic: 6-BLADE-COMPOSITE.',
 'easy', 10),
(@quiz_practice, 'Why are the propeller blades counterweighted?',
 'mcq', JSON_ARRAY(
   'To reduce blade flutter',
   'To bias the natural twisting moment toward HIGH PITCH in flight — fail-safe windmilling on HP loss',
   'To improve cruise speed',
   'For cabin noise reduction'
 ), JSON_OBJECT('correct_index', 1),
 'Counterweights ensure HP loss → autocoarsen to safe windmill. Mnemonic: AUTOCOARSEN-HP-LOSS.',
 'medium', 20),
(@quiz_practice, 'What does the PEC do?',
 'mcq', JSON_ARRAY(
   'Direct hydraulic servo to PCU only',
   'Dual-channel microprocessor in each nacelle. Manages governing, beta, reverse, autofeather, AUPC, UPTRIM. Commands PCU',
   'Single-channel pump driver',
   'Mechanical pitch control'
 ), JSON_OBJECT('correct_index', 1),
 'PEC = electronic brain. Commands PCU.',
 'medium', 30),
(@quiz_practice, 'What does the PCU do?',
 'mcq', JSON_ARRAY(
   'Pure electronic control',
   'Hydromechanical, electrically commanded by PEC. Two-stage servo valve meters HP engine oil to fine/coarse pitch chambers',
   'Mechanical only, no electronics',
   'Replaces the OSG'
 ), JSON_OBJECT('correct_index', 1),
 'PCU is the hydromechanical actuator commanded by PEC.',
 'medium', 40),
(@quiz_practice, 'What happens to the propeller blades on HP oil loss in flight?',
 'mcq', JSON_ARRAY(
   'Blades go to flat pitch',
   'Blades AUTOCOARSEN to safe high-pitch windmilling',
   'Blades go to MAX REVERSE',
   'Blades stay at last commanded angle'
 ), JSON_OBJECT('correct_index', 1),
 'Counterweight effort autocoarsens to safe windmill. Mnemonic: AUTOCOARSEN-HP-LOSS.',
 'hard', 50),
(@quiz_practice, 'What happens to the propeller blades on HP oil loss in REVERSE?',
 'mcq', JSON_ARRAY(
   'Blades autocoarsen to high pitch',
   'Blades go to MAX REVERSE blade angle',
   'Blades go to flat pitch (0°)',
   'Blades disconnect from engine'
 ), JSON_OBJECT('correct_index', 1),
 'In reverse, counterweights act toward NEGATIVE pitch → blades go MAX REVERSE on HP loss.',
 'hard', 60),
(@quiz_practice, 'At what propeller RPM does the hydraulic OSG drop the HP oil supply?',
 'mcq', JSON_ARRAY('850 RPM','~1071 RPM (105%)','~1122 RPM','1500 RPM'),
 JSON_OBJECT('correct_index', 1),
 'Hydraulic OSG at 1071 RPM. Mnemonic: 1071-HYD-1122-ELEC.',
 'hard', 70),
(@quiz_practice, 'At what propeller RPM does the FADEC NP electronic overspeed circuit signal fuel reduction?',
 'mcq', JSON_ARRAY('~1071 RPM','~1122 RPM','1020 RPM','1500 RPM'),
 JSON_OBJECT('correct_index', 1),
 'Electronic FADEC at 1122 RPM. Mnemonic: 1071-HYD-1122-ELEC.',
 'hard', 80),
(@quiz_practice, 'In which propeller mode is the hydraulic OSG section LOCKED OUT?',
 'mcq', JSON_ARRAY('Constant Speed','Beta','Reverse','Manual feather'),
 JSON_OBJECT('correct_index', 2),
 'OSG locked out in REVERSE. FADEC NP electronic is primary protection there. Mnemonic: OSG-LOCKED-REVERSE.',
 'hard', 90),
(@quiz_practice, 'What are the three CL positions for Constant Speed RPMs?',
 'mcq', JSON_ARRAY(
   '500 / 750 / 1000 RPM',
   '850 / 900 / 1020 RPM',
   '660 / 950 / 1500 RPM',
   '1020 / 1071 / 1122 RPM'
 ), JSON_OBJECT('correct_index', 1),
 '850/900/1020 per CL. Mnemonic: 850-900-1020-CL.',
 'medium', 100),
(@quiz_practice, 'What is the propeller flight fine stop in CONSTANT SPEED mode?',
 'mcq', JSON_ARRAY(
   '8° / 8.5°',
   '16° hard hyd / 16.5° soft PEC (PLA at/above Flight Idle)',
   '20° / 25°',
   'No fine stop'
 ), JSON_OBJECT('correct_index', 1),
 '16° hard / 16.5° soft. Mnemonic: 16-HARD-16.5-SOFT.',
 'hard', 110),
(@quiz_practice, 'What conditions are required to enable blade angles below 16°?',
 'mcq', JSON_ARRAY(
   'Flight Idle PLA only',
   'PLA below Flight Idle AND weight-on-wheels',
   'PLA at MAX REV',
   'CL at FEATHER'
 ), JSON_OBJECT('correct_index', 1),
 'Below 16° requires PLA <Flight Idle + WOW. PROPELLER GROUND RANGE lights illuminate.',
 'hard', 120),
(@quiz_practice, 'What does the GBE valve do?',
 'mcq', JSON_ARRAY(
   'Locks gear in down position',
   'Locks out hydraulic OSG section during ground beta',
   'Locks PLA below Flight Idle',
   'Locks autofeather ON'
 ), JSON_OBJECT('correct_index', 1),
 'GBE locks OSG on ground beta to avoid transient overspeed at flat pitch. Mnemonic: GBE-LOCKS-OSG-GROUND-BETA.',
 'hard', 130),
(@quiz_practice, 'What is the NP underspeed governing speed in beta range?',
 'mcq', JSON_ARRAY('500 RPM','660 RPM','850 RPM','1020 RPM'), JSON_OBJECT('correct_index', 1),
 '660 RPM. Engine controls speed in beta. Mnemonic: BETA-660-NP.',
 'medium', 140),
(@quiz_practice, 'What is the normal reverse RPM range and max SHP?',
 'mcq', JSON_ARRAY(
   '500-800 RPM, 1000 SHP',
   '660-950 RPM, 1500 SHP max',
   '850-1020 RPM, 5071 SHP',
   '1071-1122 RPM, 1500 SHP'
 ), JSON_OBJECT('correct_index', 1),
 'Reverse 660-950, 1500 SHP. Mnemonic: REVERSE-660-950-1500.',
 'medium', 150),
(@quiz_practice, 'When does synchrophasing operate?',
 'mcq', JSON_ARRAY(
   'Always',
   'In flight when both prop speeds within predetermined difference; NOT at takeoff',
   'Only at takeoff',
   'Only on the ground'
 ), JSON_OBJECT('correct_index', 1),
 'In flight, not at takeoff. Mnemonic: SYNC-NO-TAKEOFF.',
 'medium', 160),
(@quiz_practice, 'What two conditions must be met for autofeather to ARM?',
 'mcq', JSON_ARRAY(
   'One torque >50%',
   'Both engine torques >50% AND both PLAs beyond 60°',
   'Autofeather switchlight ON only',
   'Both engines failed'
 ), JSON_OBJECT('correct_index', 1),
 'Both torque >50% AND both PLA >60°.',
 'hard', 170),
(@quiz_practice, 'What conditions trigger autofeather (engine failure detection)?',
 'mcq', JSON_ARRAY(
   'Both torque <50%',
   'ONE torque <25% OR Np <816 (80%) for at least 3 SECONDS',
   'Manual switchlight push',
   'Pre-flight test'
 ), JSON_OBJECT('correct_index', 1),
 'One torque <25% OR Np <816 for ≥3 sec. Mnemonic: AF-50-25-816-3SEC.',
 'hard', 180),
(@quiz_practice, 'What actions happen on autofeather trigger?',
 'mcq', JSON_ARRAY(
   'Manual feather required',
   'A/F ARM out, AUX FEATHER PUMP energized, prop feathers, UPTRIM to operating FADEC',
   'Both engines feather',
   'No auto action'
 ), JSON_OBJECT('correct_index', 1),
 'Auto-feather + UPTRIM on operating engine. Mnemonic: AF-UPTRIMS-OPERATING.',
 'hard', 190),
(@quiz_practice, 'What happens if PLA is moved below Flight Idle in flight?',
 'mcq', JSON_ARRAY(
   'Nothing',
   'BETA WARNING HORN sounds; PLA must be moved back above gate IMMEDIATELY',
   'Engine shuts down',
   'Autofeather fires'
 ), JSON_OBJECT('correct_index', 1),
 'Beta horn → PLA back above gate now. Mnemonic: BETA-HORN-IN-FLIGHT.',
 'hard', 200),
(@quiz_practice, 'How is the OSG tested?',
 'mcq', JSON_ARRAY(
   'In flight only',
   'On ground via PROP O''SPEED GOVERNOR test switch on Pilots Side Panel',
   'Auto only',
   'Cannot be tested'
 ), JSON_OBJECT('correct_index', 1),
 'Ground test via Pilots Side Panel switch. Catches GBE valve fault.',
 'medium', 210),
(@quiz_practice, 'TRUE or FALSE — Synchrophasing operates during takeoff to reduce engine noise.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Not at takeoff. High-power transients confuse timing.',
 'medium', 220),
(@quiz_practice, 'What is the role of the auxiliary propeller feathering pump?',
 'mcq', JSON_ARRAY(
   '28 VDC electrical motor + external gear pump for independent feather oil source. Used for autofeather, alternate, manual, maintenance feather/unfeather',
   'Backup engine fuel pump',
   'Hydraulic pump for landing gear',
   'Ice protection'
 ), JSON_OBJECT('correct_index', 0),
 '28 VDC feather pump.',
 'medium', 230),
(@quiz_practice, 'TRUE or FALSE — The hydraulic OSG protects against overspeed in REVERSE mode.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Hyd OSG locked out in reverse. FADEC NP electronic is primary.',
 'hard', 240),
(@quiz_practice, 'What does PROPELLER GROUND RANGE light indicate?',
 'mcq', JSON_ARRAY(
   'Engine running',
   'Blade angles below 16° (PLA below Flight Idle)',
   'Reverse selected',
   'Autofeather armed'
 ), JSON_OBJECT('correct_index', 1),
 'Below 16° blade angle (PLA below Flight Idle).',
 'medium', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Propeller — Type Rating Mock', 'Ten-question mock at type-rating oral standard.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'OSG hydraulic + electronic limits.',
 'mcq', JSON_ARRAY('1071 hyd / 1122 elec','1020 hyd / 1500 elec','850 hyd / 1071 elec','No OSG'),
 JSON_OBJECT('correct_index', 0), '1071-HYD-1122-ELEC.', 'medium', 10),
(@quiz_exam, 'Flight fine stop in constant speed mode.',
 'mcq', JSON_ARRAY('8°/8.5°','16°/16.5°','20°/22°','No stop'),
 JSON_OBJECT('correct_index', 1), '16-HARD-16.5-SOFT.', 'hard', 20),
(@quiz_exam, 'Autofeather ARM conditions.',
 'mcq', JSON_ARRAY('Both torque >50% + both PLA >60°','One engine failed','Manual switch','Pre-flight'),
 JSON_OBJECT('correct_index', 0), 'Both torque + both PLA.', 'medium', 30),
(@quiz_exam, 'Autofeather TRIGGER conditions.',
 'mcq', JSON_ARRAY('Both torque <50%','One torque <25% OR Np <816 for ≥3 sec','Both engines failed','Manual'),
 JSON_OBJECT('correct_index', 1), 'AF-50-25-816-3SEC.', 'hard', 40),
(@quiz_exam, 'Hyd OSG locked out in which mode?',
 'mcq', JSON_ARRAY('Constant Speed','Beta','Reverse','Manual feather'),
 JSON_OBJECT('correct_index', 2), 'OSG-LOCKED-REVERSE.', 'hard', 50),
(@quiz_exam, 'CL constant-speed RPMs.',
 'mcq', JSON_ARRAY('500/750/1000','850/900/1020','660/950/1500','1020/1071/1122'),
 JSON_OBJECT('correct_index', 1), '850-900-1020-CL.', 'medium', 60),
(@quiz_exam, 'Beta NP underspeed governing speed.',
 'mcq', JSON_ARRAY('500','660','850','1020'),
 JSON_OBJECT('correct_index', 1), 'BETA-660-NP.', 'medium', 70),
(@quiz_exam, 'PLA below Flight Idle in flight.',
 'mcq', JSON_ARRAY(
   'Allowed',
   'BETA HORN sounds; PLA back above gate immediately; never operate below in flight',
   'Engine shuts down',
   'No effect'
 ), JSON_OBJECT('correct_index', 1), 'BETA-HORN-IN-FLIGHT.', 'hard', 80),
(@quiz_exam, 'HP oil loss in flight propeller behaviour.',
 'mcq', JSON_ARRAY(
   'Blades stay at last command',
   'Blades AUTOCOARSEN to safe high-pitch windmilling',
   'Blades go MAX REVERSE',
   'Engine shuts down'
 ), JSON_OBJECT('correct_index', 1), 'AUTOCOARSEN-HP-LOSS.', 'medium', 90),
(@quiz_exam, 'TRUE or FALSE — Synchrophasing is active during takeoff.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Not at takeoff. SYNC-NO-TAKEOFF.', 'medium', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
