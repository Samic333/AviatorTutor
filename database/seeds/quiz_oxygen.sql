-- =============================================================================
-- AviatorTutor — Phase 14: ATA 35 Oxygen — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'oxygen' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Oxygen — Practice', 'Twenty-five-question practice quiz on Q400 oxygen system. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many crew oxygen masks does the Q400 have, and from how many cylinders?',
 'mcq', JSON_ARRAY('2 masks, 2 cylinders','3 masks, 1 common cylinder','3 masks, 3 cylinders','4 masks, 2 cylinders'),
 JSON_OBJECT('correct_index', 1),
 '3 masks (pilot/copilot/observer), 1 common cylinder. Mnemonic: 3-MASKS-1-CYLINDER.',
 'easy', 10),
(@quiz_practice, 'Where is the crew oxygen cylinder located?',
 'mcq', JSON_ARRAY('Left main wheel well','Right LOWER nose compartment','Aft equipment bay','Centre fuselage'),
 JSON_OBJECT('correct_index', 1),
 'Right lower nose. Mnemonic: RIGHT-NOSE-CYLINDER.',
 'medium', 20),
(@quiz_practice, 'Where is the green burst disc, and what does it do?',
 'mcq', JSON_ARRAY(
   'Left main gear; oil pressure relief',
   'Right exterior of nose; ejects on cylinder over-pressurisation',
   'Inside cabin; smoke detector',
   'On regulator; pressure indicator'
 ), JSON_OBJECT('correct_index', 1),
 'Green burst disc, right exterior of nose. Pre-flight check item. Mnemonic: GREEN-BURST-RIGHT.',
 'hard', 30),
(@quiz_practice, 'Where is the flight-deck oxygen pressure gauge?',
 'mcq', JSON_ARRAY('Pilot side console','COPILOT side console (lighted)','Centre pedestal','Overhead'),
 JSON_OBJECT('correct_index', 1),
 'COPILOT side console, lighted. Mnemonic: COPILOT-CONSOLE-GAUGE.',
 'medium', 40),
(@quiz_practice, 'When the cylinder is turned OFF, what happens to mask pressure and the gauge reading?',
 'mcq', JSON_ARRAY(
   'Both go to zero',
   'Mask pressure drops to atmospheric; gauge continues to show BOTTLE pressure',
   'Mask pressure unchanged; gauge goes to zero',
   'Both stay at full bottle pressure'
 ), JSON_OBJECT('correct_index', 1),
 'OFF: mask atmospheric, gauge still bottle. Mnemonic: OFF-ATMO-GAUGE-BOTTLE.',
 'hard', 50),
(@quiz_practice, 'What is the system capacity in terms of descent and level flight?',
 'mcq', JSON_ARRAY(
   'Descent to 10,000 in 2 min + level 60 min',
   'Descent to 14,000 ft in 4 min + level at 14,000 for 116 min',
   'Descent to FL250 in 5 min + level 30 min',
   'No fixed capacity'
 ), JSON_OBJECT('correct_index', 1),
 '14000 in 4 + 116 at 14000. Mnemonic: 14000-IN-4-LEVEL-116.',
 'hard', 60),
(@quiz_practice, 'What is the donning time target for the crew oxygen mask?',
 'mcq', JSON_ARRAY('Less than 30 seconds','Less than 5 seconds via inflatable harness','Less than 60 seconds','No specific target'),
 JSON_OBJECT('correct_index', 1),
 '<5 seconds via inflatable harness. Mnemonic: 5-SEC-DON.',
 'medium', 70),
(@quiz_practice, 'How does the inflatable harness work?',
 'mcq', JSON_ARRAY(
   'Mechanical strap adjustment',
   'Red button inflates harness with O2 pressure; release deflates and secures around head',
   'Velcro adjustment',
   'Magnetic clasp'
 ), JSON_OBJECT('correct_index', 1),
 'Red button inflate; release deflate. Inflation gas is O2.',
 'medium', 80),
(@quiz_practice, 'What three positions does the regulator have?',
 'mcq', JSON_ARRAY(
   'OFF / ON / EMER',
   'NORM (auto mix) / 100% (pure O2) / EMER (positive pressure)',
   'LOW / MED / HIGH',
   'AUTO / MANUAL / TEST'
 ), JSON_OBJECT('correct_index', 1),
 'NORM/100%/EMER. Mnemonic: NORM-100-EMER.',
 'hard', 90),
(@quiz_practice, 'In NORM mode, what does the regulator supply?',
 'mcq', JSON_ARRAY(
   '100% O2 always',
   'Air/oxygen MIXTURE varying with cabin altitude',
   'Pure O2 with positive pressure',
   'No oxygen — manual only'
 ), JSON_OBJECT('correct_index', 1),
 'Air/O2 mix varying with cabin altitude. NORM is default for normal flight.',
 'medium', 100),
(@quiz_practice, 'In EMER mode, what does the regulator supply, and what is the additional function?',
 'mcq', JSON_ARRAY(
   'Air/O2 mix at positive pressure',
   '100% O2 at slight POSITIVE pressure; also purges smoke goggles',
   'Atmospheric pressure only',
   'No oxygen — emergency standby'
 ), JSON_OBJECT('correct_index', 1),
 '100% O2 + positive pressure + smoke goggle purge. Cylinder depletes fast — manage time.',
 'hard', 110),
(@quiz_practice, 'What is the caution about EMER position?',
 'mcq', JSON_ARRAY(
   'EMER provides only ambient air',
   'Keeping in EMER will DEPLETE the cylinder fast — return to NORM/100% once smoke contained',
   'EMER does not provide positive pressure',
   'EMER cannot be used on the ground'
 ), JSON_OBJECT('correct_index', 1),
 'EMER depletes cylinder. Mnemonic: EMER-DEPLETES.',
 'medium', 120),
(@quiz_practice, 'What is the in-line pressure indicator, and what colours mean what?',
 'mcq', JSON_ARRAY(
   'On the cylinder; red = full',
   'On the supply hose; GREEN with correct pressure / RED if low',
   'On the regulator; flashes during use',
   'No in-line indicator on Q400'
 ), JSON_OBJECT('correct_index', 1),
 'GREEN good, RED low. If RED, check hose connection.',
 'medium', 130),
(@quiz_practice, 'How is the observer mask supplied?',
 'mcq', JSON_ARRAY(
   'Separate cylinder',
   'DUAL outlet on the copilot oxygen supply line',
   'Independent regulator',
   'No oxygen for observer'
 ), JSON_OBJECT('correct_index', 1),
 'DUAL outlet on copilot supply line. Observer mask kept adjacent to copilot mask. Mnemonic: OBSERVER-DUAL-COPILOT.',
 'hard', 140),
(@quiz_practice, 'TRUE or FALSE — Crew oxygen mask outlets are dedicated and masks cannot be plugged into other outlets.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Outlets are CROSS-COMPATIBLE. If one fails, plug into another.',
 'medium', 150),
(@quiz_practice, 'What is the rule about smoking and oxygen?',
 'mcq', JSON_ARRAY(
   'Permitted in NORM mode only',
   'Smoking is NOT permitted when oxygen is in use, in any mode',
   'Permitted on the ground only',
   'Permitted with EMER mode'
 ), JSON_OBJECT('correct_index', 1),
 'No smoking when O2 in use. Pure O2 + ignition = fire risk. Mnemonic: NO-SMOKE-O2.',
 'easy', 160),
(@quiz_practice, 'What are the three additional oxygen items beyond the fixed crew system?',
 'mcq', JSON_ARRAY(
   'Portable cabin O2 + PBE + first aid O2',
   'Only PBE',
   'Only portable cabin O2',
   'No additional systems'
 ), JSON_OBJECT('correct_index', 0),
 'Portable cabin O2 cylinders + PBE for crews + first aid O2 in cabin. Mnemonic: PBE-FLT-CABIN.',
 'medium', 170),
(@quiz_practice, 'What is PBE and who uses it?',
 'mcq', JSON_ARRAY(
   'Pilot Brake Equipment',
   'Protective Breathing Equipment — for flight deck crew AND cabin attendants in low-O2 environments',
   'Portable Battery Equipment',
   'Passenger Boarding Equipment'
 ), JSON_OBJECT('correct_index', 1),
 'PBE = self-contained smoke hoods. Used in cabin smoke / fire by both crews.',
 'medium', 180),
(@quiz_practice, 'What is the FIRST action on a rapid depressurisation?',
 'mcq', JSON_ARRAY(
   'Initiate emergency descent',
   'Oxygen masks ON immediately (less than 5 sec via inflatable harness)',
   'Cabin announcement',
   'Run QRH'
 ), JSON_OBJECT('correct_index', 1),
 'Mask first. Don in <5 sec. Then descent and comm.',
 'hard', 190),
(@quiz_practice, 'What is the FIRST action on cockpit smoke / fire?',
 'mcq', JSON_ARRAY(
   'Open a window to vent smoke',
   'Oxygen masks ON, regulator EMER (100% positive + smoke goggle purge)',
   'Cycle the FCECU',
   'Discharge cabin Halon'
 ), JSON_OBJECT('correct_index', 1),
 'Mask + EMER. Positive pressure flushes contaminants and purges smoke goggles.',
 'hard', 200),
(@quiz_practice, 'You don the mask and the in-line indicator is RED. What is the most likely cause?',
 'mcq', JSON_ARRAY(
   'Cylinder overpressure',
   'Supply hose disconnected at mask or outlet — verify connection',
   'Regulator failure',
   'Cabin altitude too high'
 ), JSON_OBJECT('correct_index', 1),
 'Most common cause: hose disconnect. Verify connection first.',
 'medium', 210),
(@quiz_practice, 'TRUE or FALSE — A pre-flight walk-around includes the burst disc on the right exterior of the nose.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 0),
 'TRUE. Burst disc check is a pre-flight item. Missing/damaged = over-pressurisation event.',
 'medium', 220),
(@quiz_practice, 'On a single mask failure, what is the recommended action?',
 'mcq', JSON_ARRAY(
   'Use the observer mask — designed for cross-crew use',
   'Continue without oxygen',
   'Land immediately',
   'Use the F/A PBE unit'
 ), JSON_OBJECT('correct_index', 0),
 'Observer mask cross-compatible. Either crew member can use it.',
 'medium', 230),
(@quiz_practice, 'TRUE or FALSE — The flight-deck gauge will read zero when the cylinder is turned OFF.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Gauge continues to show BOTTLE pressure even when cylinder is off. The valve closure affects delivery, not the gauge.',
 'hard', 240),
(@quiz_practice, 'Mic + audio connector on the mask: what is its role?',
 'mcq', JSON_ARRAY(
   'Tests the regulator',
   'Crew communication is preserved on oxygen — plug audio into related receptacle',
   'Increases O2 flow',
   'Dust filter'
 ), JSON_OBJECT('correct_index', 1),
 'Mask mic preserves crew comm during O2 use.',
 'medium', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Oxygen — Type Rating Mock', 'Ten-question mock at type-rating oral standard.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'Crew O2 architecture: masks + cylinder.',
 'mcq', JSON_ARRAY('3 masks 1 cylinder','3 masks 3 cylinders','2 masks 1 cylinder','4 masks 2 cylinders'),
 JSON_OBJECT('correct_index', 0),
 '3 masks, 1 common cylinder.',
 'easy', 10),
(@quiz_exam, 'Capacity numbers.',
 'mcq', JSON_ARRAY('14000 in 4 + 116 level','10000 in 2 + 60 level','FL250 in 5 + 30 level','No capacity'),
 JSON_OBJECT('correct_index', 0),
 '14000 in 4 + 116 at 14000. Mnemonic: 14000-IN-4-LEVEL-116.',
 'medium', 20),
(@quiz_exam, 'Donning target time + mechanism.',
 'mcq', JSON_ARRAY('30 sec via straps','<5 sec via inflatable harness, red button','60 sec via velcro','No target'),
 JSON_OBJECT('correct_index', 1),
 '<5 sec via inflatable harness.',
 'medium', 30),
(@quiz_exam, 'Three regulator positions and EMER function.',
 'mcq', JSON_ARRAY(
   'OFF/ON/MAX',
   'NORM (mix), 100% (pure), EMER (positive + smoke goggle purge)',
   'LOW/MED/HIGH',
   'AUTO/MANUAL/TEST'
 ), JSON_OBJECT('correct_index', 1),
 'NORM/100%/EMER.',
 'hard', 40),
(@quiz_exam, 'Cockpit smoke first action.',
 'mcq', JSON_ARRAY(
   'Open window',
   'Mask ON + regulator EMER',
   'Discharge Halon',
   'Cycle FCECU'
 ), JSON_OBJECT('correct_index', 1),
 'Mask + EMER. 100% positive + smoke goggle purge.',
 'medium', 50),
(@quiz_exam, 'EMER caution.',
 'mcq', JSON_ARRAY(
   'Provides only ambient air',
   'Depletes cylinder fast — return to NORM/100% once smoke contained',
   'Cannot be used on the ground',
   'Inhibits mic'
 ), JSON_OBJECT('correct_index', 1),
 'EMER-DEPLETES.',
 'hard', 60),
(@quiz_exam, 'Observer mask supply.',
 'mcq', JSON_ARRAY(
   'Separate cylinder',
   'DUAL outlet on copilot supply line',
   'Adjacent to pilot, separate outlet',
   'No observer mask'
 ), JSON_OBJECT('correct_index', 1),
 'OBSERVER-DUAL-COPILOT.',
 'medium', 70),
(@quiz_exam, 'In-line indicator colours and meaning.',
 'mcq', JSON_ARRAY(
   'GREEN low / RED good',
   'GREEN with correct pressure / RED if low — check hose if RED',
   'No colour, only number',
   'AMBER warning only'
 ), JSON_OBJECT('correct_index', 1),
 'GREEN good RED low.',
 'easy', 80),
(@quiz_exam, 'Burst disc location and significance.',
 'mcq', JSON_ARRAY(
   'On regulator; pressure indicator',
   'Right exterior of nose, GREEN; ejected = over-pressurisation event has occurred',
   'On cylinder; vents at end of life',
   'No burst disc on Q400'
 ), JSON_OBJECT('correct_index', 1),
 'GREEN-BURST-RIGHT. Pre-flight check item.',
 'hard', 90),
(@quiz_exam, 'TRUE or FALSE — When the cylinder is OFF, the flight-deck gauge reads atmospheric.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Gauge continues to read BOTTLE pressure regardless of cylinder valve state. Mask delivery is what drops to atmospheric.',
 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
