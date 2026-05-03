-- =============================================================================
-- AviatorTutor — Phase 15: ATA 36 Pneumatics — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'pneumatics' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Pneumatics — Practice', 'Twenty-five-question practice quiz on Q400 APU + bleed system. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'Can the Q400 APU operate in flight?',
 'mcq', JSON_ARRAY(
   'Yes, with PWR pushed',
   'NO — APU cannot operate in flight; shutoff valve auto-closes airborne',
   'Only above FL250',
   'Only with external power applied'
 ), JSON_OBJECT('correct_index', 1),
 'APU is ground-only. Mnemonic: APU-GROUND-ONLY.',
 'easy', 10),
(@quiz_practice, 'What replaces the standard composite tailcone for the APU installation?',
 'mcq', JSON_ARRAY('Aluminium tailcone','Titanium tailcone with firewall','Carbon-fibre tailcone','No replacement'),
 JSON_OBJECT('correct_index', 1),
 'Titanium tailcone with firewall. Required for APU fire-rating.',
 'medium', 20),
(@quiz_practice, 'How is the APU accessed for maintenance?',
 'mcq', JSON_ARRAY('Single side door','Two clamshell doors on the bottom of the tailcone','Top access hatch','No access — sealed'),
 JSON_OBJECT('correct_index', 1),
 'Two clamshell doors on bottom. Mnemonic: TITANIUM-TAIL-2-CLAMSHELL.',
 'medium', 30),
(@quiz_practice, 'Where is the APU air intake located?',
 'mcq', JSON_ARRAY('Top of fuselage','RIGHT REAR of fuselage; screened inlet duct','Left rear of fuselage','Centre fuselage'),
 JSON_OBJECT('correct_index', 1),
 'Right rear of fuselage. Mnemonic: RIGHT-REAR-INLET.',
 'medium', 40),
(@quiz_practice, 'When does the APU starter disengage during a normal start?',
 'mcq', JSON_ARRAY('Immediately','At HALF operating speed','At full operating speed','When GEN is selected'),
 JSON_OBJECT('correct_index', 1),
 'Half operating speed. Mnemonic: HALF-SPEED-STARTER.',
 'hard', 50),
(@quiz_practice, 'What three conditions must all be true for the APU PWR switchlight to arm?',
 'mcq', JSON_ARRAY(
   'Aircraft running, no fire, EXTG pulled',
   'On ground, no fire detected, EXTG NOT selected',
   'Engines running, AC power ON, on ground',
   'Aircraft in flight, parking brake set'
 ), JSON_OBJECT('correct_index', 1),
 '3-COND-PWR-ARM: ground + no fire + EXTG not selected.',
 'hard', 60),
(@quiz_practice, 'What four conditions cause the APU fuel shutoff valve to close?',
 'mcq', JSON_ARRAY(
   'PWR off, fire, EXTG, aircraft in flight',
   'PWR off only',
   'PWR off and BL AIR off',
   'External power applied'
 ), JSON_OBJECT('correct_index', 0),
 '4-COND-FUEL-CLOSE: PWR off / fire / EXTG / aircraft in flight.',
 'hard', 70),
(@quiz_practice, 'Where does the APU fuel come from?',
 'mcq', JSON_ARRAY(
   'Right wing collector bay',
   'LEFT wing collector bay through APU shutoff valve',
   'Centre fuselage tank',
   'Independent APU tank'
 ), JSON_OBJECT('correct_index', 1),
 'Left wing collector bay. Mnemonic: LEFT-COLLECTOR-APU-FUEL.',
 'medium', 80),
(@quiz_practice, 'During a battery-only APU start at 100% charge, what is the bus voltage drop?',
 'mcq', JSON_ARRAY('5 V','10 V','To about 20 V','To about 25 V'),
 JSON_OBJECT('correct_index', 2),
 'Bus drops to ~20 V at 100% charge. At 50%, drops to ~18 V. Mnemonic: 100-20-50-18.',
 'hard', 90),
(@quiz_practice, 'What happens to APU GEN output if external AC or DC power is applied?',
 'mcq', JSON_ARRAY(
   'Both feed the buses in parallel',
   'APU GEN output is automatically PREVENTED',
   'APU shuts down',
   'External power inhibits engine GENs only'
 ), JSON_OBJECT('correct_index', 1),
 'External AC/DC inhibits APU GEN output. System protects against parallel feeds.',
 'hard', 100),
(@quiz_practice, 'What does the APU bleed valve supply when open?',
 'mcq', JSON_ARRAY(
   'ECS only',
   'Bleed for ECS AND holds the CPCS aft safety valve open',
   'Engine starter air',
   'Cabin O2'
 ), JSON_OBJECT('correct_index', 1),
 'ECS supply + CPCS aft safety valve held open.',
 'medium', 110),
(@quiz_practice, 'What happens to the APU BL AIR switchlight if either engine BLEED switch is set to 1 or 2?',
 'mcq', JSON_ARRAY(
   'No change',
   'APU BL AIR auto-DE-ENERGIZES — prevents simultaneous APU + engine bleed',
   'APU shuts down',
   'BL AIR locks open'
 ), JSON_OBJECT('correct_index', 1),
 'BL-AIR-AUTO-OFF-WITH-ENG.',
 'hard', 120),
(@quiz_practice, 'What happens to APU bleed if APU EGT reaches an established temperature limit?',
 'mcq', JSON_ARRAY(
   'No change',
   'Bleed air supply REDUCED — priority to APU GEN load',
   'APU shuts down',
   'Bleed valve fully opens'
 ), JSON_OBJECT('correct_index', 1),
 'EGT-LIMIT-BLEED-DOWN. GEN load gets priority.',
 'hard', 130),
(@quiz_practice, 'How long after FIRE detection does the APU extinguishing agent automatically release?',
 'mcq', JSON_ARRAY('Immediate','3 sec','7 sec','15 sec'),
 JSON_OBJECT('correct_index', 2),
 '7-SEC-AUTO-EXTG.',
 'medium', 140),
(@quiz_practice, 'After the APU fire bottle has discharged, can the APU be restarted?',
 'mcq', JSON_ARRAY(
   'Yes, after 30 min cooldown',
   'NO — restart is PREVENTED until bottle is replaced',
   'Yes, with manual override',
   'Only with AC power applied'
 ), JSON_OBJECT('correct_index', 1),
 'NO-RESTART-AFTER-DISCHARGE.',
 'hard', 150),
(@quiz_practice, 'What does GEN OHT (amber advisory) indicate?',
 'mcq', JSON_ARRAY(
   'APU at high load',
   'APU starter-generator overheat — APU auto-shuts down',
   'APU starting',
   'External power applied'
 ), JSON_OBJECT('correct_index', 1),
 'GEN OHT → auto APU shutdown.',
 'medium', 160),
(@quiz_practice, 'What is the correct APU normal shutdown sequence?',
 'mcq', JSON_ARRAY(
   'Push PWR; bleed and GEN auto-close',
   'Close BL AIR → GEN off → push PWR',
   'GEN off → BL AIR → PWR',
   'Push EXTG to safely stop'
 ), JSON_OBJECT('correct_index', 1),
 'BL AIR off (EGT stabilise) → GEN off (load transition) → PWR off (stop).',
 'medium', 170),
(@quiz_practice, 'Ambient temperature limit with composite cooling duct REMOVED?',
 'mcq', JSON_ARRAY('21°C','30°C OR ISA+25°C, whichever LOWER','40°C','No limit'),
 JSON_OBJECT('correct_index', 1),
 'LOUVRE-21-DUCT-30. Composite duct removed: 30°C / ISA+25, whichever lower.',
 'hard', 180),
(@quiz_practice, 'Ambient temperature limit with Air Inlet Louvre INSTALLED?',
 'mcq', JSON_ARRAY('21°C','30°C','40°C','No limit'),
 JSON_OBJECT('correct_index', 0),
 '21°C with Louvre installed.',
 'hard', 190),
(@quiz_practice, 'TRUE or FALSE — APU bleed air can be used for airframe de-icing.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. APU check valve and wing duct check valves prevent APU bleed from entering engine bleed supply (which includes airframe de-icing).',
 'hard', 200),
(@quiz_practice, 'On any APU FAULT detected by FADEC, what happens?',
 'mcq', JSON_ARRAY(
   'No automatic action',
   'APU auto-shuts down; FAIL amber; PWR must be reselected',
   'GEN automatically goes off-line only',
   'BL AIR closes only'
 ), JSON_OBJECT('correct_index', 1),
 'Auto shutdown + FAIL amber + reselect PWR.',
 'medium', 210),
(@quiz_practice, 'TRUE or FALSE — Once main engine DC GENs are on line, APU GEN automatically goes off-line.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. APU GEN continues in PARALLEL until manually selected off (GEN switchlight push to ON out) or normal shutdown.',
 'hard', 220),
(@quiz_practice, 'What does the BTL ARM (amber) advisory indicate?',
 'mcq', JSON_ARRAY(
   'Bottle is low',
   'APU fire bottle ready to be activated; OUT after auto-extg or no power',
   'Engine fire bottle armed',
   'External power active'
 ), JSON_OBJECT('correct_index', 1),
 'BTL ARM = bottle ready. Goes out after auto-extg.',
 'medium', 230),
(@quiz_practice, 'Where does the APU exhaust go?',
 'mcq', JSON_ARRAY(
   'Forward, through nose',
   'Out the side fuselage',
   'Through ejector + upwards-pointing outlet at AFT END of titanium tailcone',
   'No exhaust — internal cycle'
 ), JSON_OBJECT('correct_index', 2),
 'Aft tailcone, upwards. Directs hot exhaust away from ground crew.',
 'medium', 240),
(@quiz_practice, 'What is the role of the APU louvered air-inlet cover?',
 'mcq', JSON_ARRAY(
   'Tests the APU',
   'Helps prevent snow and sleet from entering APU inlet during long turnarounds / overnight',
   'Reduces noise',
   'Inhibits APU start'
 ), JSON_OBJECT('correct_index', 1),
 'Snow/sleet protection. Optional accessory.',
 'medium', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Pneumatics — Type Rating Mock', 'Ten-question mock at type-rating oral standard.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'APU in-flight operation rule.',
 'mcq', JSON_ARRAY('Allowed','Not permitted; auto shutoff valve closes airborne','Only with PWR pushed','Only above FL250'),
 JSON_OBJECT('correct_index', 1), 'APU-GROUND-ONLY.', 'easy', 10),
(@quiz_exam, 'PWR arming preconditions.',
 'mcq', JSON_ARRAY('Engine running, no fire','GROUND + NO FIRE + EXTG NOT SELECTED','EXTG selected','External AC applied'),
 JSON_OBJECT('correct_index', 1), '3-COND-PWR-ARM.', 'medium', 20),
(@quiz_exam, 'Fuel valve close conditions (4).',
 'mcq', JSON_ARRAY(
   'PWR off only',
   'PWR off / FIRE / EXTG / aircraft in flight',
   'GEN off',
   'External power'
 ), JSON_OBJECT('correct_index', 1), '4-COND-FUEL-CLOSE.', 'hard', 30),
(@quiz_exam, 'Auto-extg time delay after FIRE.',
 'mcq', JSON_ARRAY('Immediate','3 sec','7 sec','15 sec'), JSON_OBJECT('correct_index', 2),
 '7-SEC-AUTO-EXTG.', 'medium', 40),
(@quiz_exam, 'Restart after fire bottle discharge.',
 'mcq', JSON_ARRAY('Yes after cooldown','NO until bottle replaced','Yes with override','Yes with AC power'),
 JSON_OBJECT('correct_index', 1), 'NO-RESTART-AFTER-DISCHARGE.', 'hard', 50),
(@quiz_exam, 'Starter cutoff speed.',
 'mcq', JSON_ARRAY('Idle','HALF operating speed','Full operating speed','When GEN selected'),
 JSON_OBJECT('correct_index', 1), 'HALF-SPEED-STARTER.', 'medium', 60),
(@quiz_exam, 'Battery start bus voltage at 50% charge.',
 'mcq', JSON_ARRAY('20 V','18 V','24 V','15 V'), JSON_OBJECT('correct_index', 1),
 '~18 V at 50% charge. Mnemonic: 100-20-50-18.', 'hard', 70),
(@quiz_exam, 'BL AIR auto-de-energization condition.',
 'mcq', JSON_ARRAY('When external power applied','When either engine BLEED switch is at 1 or 2','When GEN OHT','Never'),
 JSON_OBJECT('correct_index', 1), 'BL-AIR-AUTO-OFF-WITH-ENG.', 'hard', 80),
(@quiz_exam, 'Composite duct removed temperature limit.',
 'mcq', JSON_ARRAY('21°C','30°C OR ISA+25°C lower','40°C','No limit'), JSON_OBJECT('correct_index', 1),
 'LOUVRE-21-DUCT-30.', 'hard', 90),
(@quiz_exam, 'TRUE or FALSE — APU bleed air can be used for airframe de-icing.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Check valves prevent APU bleed entering engine bleed supply (which feeds de-ice).', 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
