-- =============================================================================
-- AviatorTutor — Phase 10: ATA 31 Indicating & Recording — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'indicating-recording' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Indicating & Recording — Practice',
 'Twenty-five-question practice quiz on EIS architecture, color rules, ESCP reversion, recorders, and warning system. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many Display Units are in the Q400 EIS, and are they identical?',
 'mcq',
 JSON_ARRAY(
   '4 DUs, 2 PFDs and 2 MFDs, all different',
   '5 DUs (PFD1, MFD1, ED, MFD2, PFD2), all identical and interchangeable',
   '6 DUs including a standby instrument',
   '5 DUs but only PFDs and MFDs are interchangeable; ED is unique'
 ),
 JSON_OBJECT('correct_index', 1),
 'Five DUs total, all identical and interchangeable. Mnemonic: 5-DU-IDENTICAL.',
 'easy', 10),
(@quiz_practice, 'How are EFIS and ESID DUs allocated?',
 'mcq',
 JSON_ARRAY(
   'EFIS = 5 DUs; ESID = 0 DUs',
   'EFIS = 4 DUs (PFDs+MFDs); ESID = 3 DUs (MFDs+ED). MFDs are shared.',
   'EFIS = 2 DUs; ESID = 3 DUs. No overlap.',
   'Each system has 5 dedicated DUs (10 total)'
 ),
 JSON_OBJECT('correct_index', 1),
 'EFIS = 4, ESID = 3. MFDs shared. Mnemonic: EFIS-4-ESID-3.',
 'medium', 20),
(@quiz_practice, 'Which control panel owns the ED, and which control panels own the PFDs?',
 'mcq',
 JSON_ARRAY(
   'ESCP owns ED; EFCP1 owns PFD1+MFD1; EFCP2 owns PFD2+MFD2',
   'EFCP1 owns ED; ESCP owns the PFDs',
   'There is one master EFCP that owns all DUs',
   'ED has no control panel; it''s autonomous'
 ),
 JSON_OBJECT('correct_index', 0),
 'ESCP owns MFD1+MFD2+ED. EFCP1 owns PFD1+MFD1. EFCP2 owns PFD2+MFD2.',
 'medium', 30),
(@quiz_practice, 'After ESCP power loss, what happens to the MFD1 and MFD2 selectors?',
 'mcq',
 JSON_ARRAY(
   'Both stop working',
   'MFD1 selector remains operative; MFD2 selector does NOT operate',
   'MFD2 keeps working; MFD1 stops',
   'Both keep working'
 ),
 JSON_OBJECT('correct_index', 1),
 'MFD1 selector remains operative. MFD2 selector dies. Mnemonic: MFD2-DIES-WITH-ESCP.',
 'hard', 40),
(@quiz_practice, 'In what order does the ALL pushbutton cycle the system pages?',
 'mcq',
 JSON_ARRAY(
   'ELEC → ENG → FUEL → DOORS → ELEC',
   'ENG → FUEL → DOORS → ELEC → ENG',
   'FUEL → DOORS → ELEC → ENG → FUEL',
   'Random order based on caution state'
 ),
 JSON_OBJECT('correct_index', 1),
 'ALL cycles ENG → FUEL → DOORS → ELEC → ENG. Mnemonic: ALL-CYCLE-EFDD-E.',
 'hard', 50),
(@quiz_practice, 'When both MFDs are failed or none is set to SYS, how can you still see a system page?',
 'mcq',
 JSON_ARRAY(
   'You cannot — system pages require an MFD',
   'Press-and-HOLD a system pushbutton; the page shows on the ED in composite format. Release returns the ED to engine display',
   'The pages reroute automatically to the PFDs',
   'Use the standby instrument bus'
 ),
 JSON_OBJECT('correct_index', 1),
 'Press-and-HOLD a system pushbutton (ELEC/ENG/FUEL/DOORS). ED shows that page in composite format. Release returns to engine display.',
 'hard', 60),
(@quiz_practice, 'Which colour is used for pilot-SELECTABLE parameters such as selected heading or altitude bugs?',
 'mcq',
 JSON_ARRAY('GREEN','WHITE','CYAN','MAGENTA'),
 JSON_OBJECT('correct_index', 2),
 'CYAN = pilot-selectable. MAGENTA = TCAS / FMS / VOR / ILS / DME data. White = aircraft actual.',
 'medium', 70),
(@quiz_practice, 'Which colour is used for TCAS proximate traffic and FMS-related data?',
 'mcq',
 JSON_ARRAY('GREEN','CYAN','YELLOW','MAGENTA'),
 JSON_OBJECT('correct_index', 3),
 'MAGENTA = TCAS, VOR, ILS, DME, FMS data, FD commands. CYAN = pilot-selectable.',
 'medium', 80),
(@quiz_practice, 'What is the FLASHING attribute on the EIS — rate, duty cycle, and time limit?',
 'mcq',
 JSON_ARRAY(
   '2 Hz, 25% duty, 10 sec',
   '1 Hz, 50% duty cycle. Time-limited to ~5 seconds OR maintained until crew action',
   '5 Hz, 50% duty, 30 sec',
   '0.5 Hz, 50% duty, no limit'
 ),
 JSON_OBJECT('correct_index', 1),
 '1 Hz, 50% duty, time-limited 5 sec. Mnemonic: FLASH-1HZ-5SEC.',
 'medium', 90),
(@quiz_practice, 'What does REVERSE VIDEO indicate?',
 'mcq',
 JSON_ARRAY(
   'A pilot-initiated mode change',
   'A change in operating state that was NOT pilot-initiated. Time-limited (~5 sec)',
   'Critical fault requiring immediate action',
   'Cross-side source selection'
 ),
 JSON_OBJECT('correct_index', 1),
 'Reverse video = NOT pilot-initiated state change. Black on coloured rectangle. Time-limited.',
 'medium', 100),
(@quiz_practice, 'What do messages enclosed in BRACKETS mean?',
 'mcq',
 JSON_ARRAY(
   'Pilot-selectable parameter',
   'Required crew action / instruction',
   'TCAS data',
   'Ground-derived data'
 ),
 JSON_OBJECT('correct_index', 1),
 '[BRACKETS] = required crew action.',
 'easy', 110),
(@quiz_practice, 'How many digital clocks does the Q400 have, and how do they connect to the recorders?',
 'mcq',
 JSON_ARRAY(
   '1 clock, drives both FDR and CVR',
   '2 clocks: No.1 to CVR direct + FDR via FDPS; No.2 to FDR via FDPS only',
   '3 clocks: 1 per recorder + 1 spare',
   '2 clocks but only one connects to recorders'
 ),
 JSON_OBJECT('correct_index', 1),
 'Two digital clocks. No.1 → CVR direct + FDR. No.2 → FDR only. FDR auto-switches.',
 'hard', 120),
(@quiz_practice, 'What does the FDR do if clock No.1 fails?',
 'mcq',
 JSON_ARRAY(
   'Stops recording',
   'Auto-switches to clock No.2',
   'Records without timestamps',
   'Triggers a Master Caution'
 ),
 JSON_OBJECT('correct_index', 1),
 'FDR auto-switches from No.1 to No.2 on No.1 failure. CVR has no such backup — fed only by No.1.',
 'hard', 130),
(@quiz_practice, 'What colour is the Master Warning switchlight, and what colour is Master Caution?',
 'mcq',
 JSON_ARRAY(
   'Both red',
   'Master Warning RED; Master Caution AMBER',
   'Both amber',
   'Master Warning AMBER; Master Caution RED'
 ),
 JSON_OBJECT('correct_index', 1),
 'Master Warning RED (flashing). Master Caution AMBER (flashing). Mnemonic: MASTER-RED-AMBER.',
 'easy', 140),
(@quiz_practice, 'What does pushing the Master Warning switchlight do?',
 'mcq',
 JSON_ARRAY(
   'Clears the underlying fault',
   'Resets the FLASH only — underlying caution/warning panel light remains steady if the fault persists',
   'Triggers an automatic divert',
   'Disables further warnings for 60 seconds'
 ),
 JSON_OBJECT('correct_index', 1),
 'Reset = flash off. Underlying light remains lit if fault persists.',
 'medium', 150),
(@quiz_practice, 'What is the STALL WARN test sequence?',
 'mcq',
 JSON_ARRAY(
   'Press once and hold for 5 sec',
   'TEST 1 (held >10 sec) tests SPS channel 1 / L shaker; TEST 2 (held >10 sec) tests channel 2 / R shaker',
   'Cycle through TEST 1 / TEST 2 / TEST 3',
   'Hold the test switch for 30 sec total'
 ),
 JSON_OBJECT('correct_index', 1),
 'TEST 1 left shaker / channel 1, then TEST 2 right shaker / channel 2. Each held >10 sec. Mnemonic: TEST1-LSHAKE-TEST2-RSHAKE.',
 'medium', 160),
(@quiz_practice, 'What 5 conditions trigger the Q400 T/O Warning Horn (engine running + TEST)?',
 'mcq',
 JSON_ARRAY(
   '3 conditions: trim, brake, flaps',
   '5 conditions: spoilers, trim, brake, condition lever not at MAX/1020, flaps >20° or <3.5°',
   '4 conditions: spoilers, trim, brake, flaps',
   '6 conditions: + airspeed and weight'
 ),
 JSON_OBJECT('correct_index', 1),
 'Five conditions. Mnemonic: 5-COND-TO-HORN.',
 'hard', 170),
(@quiz_practice, 'What does the STICK PUSHER SHUT-OFF switchlight do?',
 'mcq',
 JSON_ARRAY(
   'Tests the stick pusher',
   'Disables the stick pusher; PUSHER SYST FAIL caution illuminates; OFF inscriptions illuminate; shaker (warning) continues',
   'Resets PUSHER SYST FAIL',
   'Activates the stick shaker only'
 ),
 JSON_OBJECT('correct_index', 1),
 'Disables pusher. Shaker still works as warning. PUSHER SYST FAIL illuminates.',
 'hard', 180),
(@quiz_practice, 'What does TERRAIN INHIBIT (white switchlight) do?',
 'mcq',
 JSON_ARRAY(
   'Inhibits ALL GPWS modes',
   'Inhibits TAD and TCF only — other GPWS modes remain active',
   'Disables TCAS',
   'Inhibits the radar altimeter'
 ),
 JSON_OBJECT('correct_index', 1),
 'TERRAIN INHIBIT inhibits TAD + TCF only. Other GPWS modes (1-5) remain active.',
 'hard', 190),
(@quiz_practice, 'What does GPWS FLAP OVERRIDE do?',
 'mcq',
 JSON_ARRAY(
   'Inhibits all GPWS modes',
   'Inhibits GPWS mode 4B only — permits 0° flap landings without aural warning',
   'Inhibits the TOO LOW FLAPS aural only',
   'Bypasses the LANDING FLAP SELECT switch'
 ),
 JSON_OBJECT('correct_index', 1),
 'FLAP OVERRIDE inhibits mode 4B only. Used for intentional 0° flap landings. Other modes remain.',
 'hard', 200),
(@quiz_practice, 'What positions are on the GPWS LANDING FLAP SELECT switch?',
 'mcq',
 JSON_ARRAY('5° / 15° / 35°','10° / 15° / 35°','0° / 20° / 35°','5° / 10° / 15° / 35°'),
 JSON_OBJECT('correct_index', 1),
 '10° / 15° / 35°. "TOO LOW FLAPS" aural at <200 ft AGL with flaps less than selected.',
 'medium', 210),
(@quiz_practice, 'What does the BELOW G/S switch do?',
 'mcq',
 JSON_ARRAY(
   'Cancels "BELOW GLIDESLOPE" aural warning',
   'Inhibits all GPWS aurals',
   'Tests the GPWS',
   'Switches G/S source'
 ),
 JSON_OBJECT('correct_index', 0),
 'BELOW G/S momentary push cancels the BELOW GLIDESLOPE aural.',
 'medium', 220),
(@quiz_practice, 'TRUE or FALSE — All 5 Display Units on the Q400 are identical and any DU can take over for any other.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 0),
 'TRUE. All 5 DUs are identical interchangeable LCDs. The reversion architecture allows any DU to display any other''s content.',
 'easy', 230),
(@quiz_practice, 'What does AVAIL (white) in the centre of a failed PFD mean?',
 'mcq',
 JSON_ARRAY(
   'The PFD is healthy and available',
   'PFD content is AVAILABLE for reversion to the adjacent MFD via the ESCP rotary',
   'A reset is in progress',
   'Standby instruments are required'
 ),
 JSON_OBJECT('correct_index', 1),
 'AVAIL = reversion is available. Set the adjacent MFD to PFD via the ESCP rotary; PFD content displays.',
 'hard', 240),
(@quiz_practice, 'TRUE or FALSE — When the captain pushes Master Warning, the underlying caution/warning panel light extinguishes.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Pushing the switchlight resets the FLASH (audio, flashing). The underlying C/W panel light remains steady if the fault persists.',
 'hard', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Indicating & Recording — Type Rating Mock',
 'Ten-question mock at type-rating oral standard. Twelve-minute timer, pass score 80%.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'Quote the DU count and the EFIS/ESID allocation.',
 'mcq',
 JSON_ARRAY(
   '4 DUs; EFIS = 2, ESID = 2',
   '5 DUs (all identical); EFIS = 4 (PFDs+MFDs), ESID = 3 (MFDs+ED), MFDs shared',
   '6 DUs',
   '5 DUs but ED is not interchangeable'
 ),
 JSON_OBJECT('correct_index', 1),
 '5 identical DUs. EFIS = 4, ESID = 3, MFDs shared. Mnemonics: 5-DU-IDENTICAL · EFIS-4-ESID-3.',
 'medium', 10),
(@quiz_exam, 'After ESCP power loss, which selector dies?',
 'mcq',
 JSON_ARRAY('Both die','MFD1 dies','MFD2 dies','Both keep working'),
 JSON_OBJECT('correct_index', 2),
 'MFD2 selector dies; MFD1 keeps working. Mnemonic: MFD2-DIES-WITH-ESCP.',
 'hard', 20),
(@quiz_exam, 'ALL pushbutton cycle order.',
 'mcq',
 JSON_ARRAY('ELEC → ENG → FUEL → DOORS','ENG → FUEL → DOORS → ELEC','FUEL → ENG → ELEC → DOORS','Reverse alphabetic'),
 JSON_OBJECT('correct_index', 1),
 'ENG → FUEL → DOORS → ELEC → ENG. Mnemonic: ALL-CYCLE-EFDD-E.',
 'medium', 30),
(@quiz_exam, 'CYAN vs MAGENTA on the EIS.',
 'mcq',
 JSON_ARRAY(
   'CYAN = TCAS; MAGENTA = pilot-selectable',
   'CYAN = pilot-selectable (Hdg/Crs/Alt/bugs/baro); MAGENTA = TCAS / VOR / ILS / DME / FMS / FD',
   'Both interchangeable',
   'CYAN = caution; MAGENTA = warning'
 ),
 JSON_OBJECT('correct_index', 1),
 'CYAN = mine. MAGENTA = FMS/TCAS/nav radios.',
 'medium', 40),
(@quiz_exam, 'Two-clock recorder architecture.',
 'mcq',
 JSON_ARRAY(
   '1 clock to both recorders',
   'No.1 to CVR direct + FDR via FDPS; No.2 to FDR via FDPS only; FDR auto-switches on No.1 fail',
   '1 clock per recorder, no cross-feed',
   'Each recorder has its own internal clock'
 ),
 JSON_OBJECT('correct_index', 1),
 'No.1 → CVR + FDR; No.2 → FDR only. FDR auto-switches. Mnemonic: TWO-CLOCKS-FDR-CVR.',
 'hard', 50),
(@quiz_exam, '5 T/O warning horn trigger conditions.',
 'mcq',
 JSON_ARRAY(
   'Spoilers + trim + brake + flaps',
   'Spoilers + trim + parking brake + condition lever not at MAX/1020 + flaps >20° or <3.5°',
   'Engine fail + brake + flaps',
   'Trim + airspeed + flaps'
 ),
 JSON_OBJECT('correct_index', 1),
 'Five: spoilers / trim / brake / condition lever / flaps. Mnemonic: 5-COND-TO-HORN.',
 'hard', 60),
(@quiz_exam, 'Stall warn test sequence.',
 'mcq',
 JSON_ARRAY(
   'Press TEST and hold 30 sec',
   'TEST 1 (>10 sec, channel 1, L shaker) then TEST 2 (>10 sec, channel 2, R shaker)',
   'Cycle TEST 1/2/3',
   'Toggle the SPS circuit breaker'
 ),
 JSON_OBJECT('correct_index', 1),
 'TEST 1 then TEST 2, each >10 sec. Mnemonic: TEST1-LSHAKE-TEST2-RSHAKE.',
 'medium', 70),
(@quiz_exam, 'What does TERRAIN INHIBIT (white) inhibit?',
 'mcq',
 JSON_ARRAY('All GPWS modes','TAD and TCF only — other GPWS modes remain active','Only the PULL UP aural','TCAS only'),
 JSON_OBJECT('correct_index', 1),
 'TAD + TCF only. Modes 1-5 remain active.',
 'hard', 80),
(@quiz_exam, 'PFD1 fails in cruise IMC. How do you reroute primary flight content?',
 'mcq',
 JSON_ARRAY(
   'Standby instruments only',
   'ESCP MFD1 selector to PFD; AVAIL (white) in centre of PFD1 confirms reversion available',
   'Press the Master Warning to clear',
   'Cycle the EFCP1 brightness'
 ),
 JSON_OBJECT('correct_index', 1),
 'ESCP MFD1 selector to PFD; primary flight content displays on MFD1. Failed PFD1 shows AVAIL.',
 'hard', 90),
(@quiz_exam, 'TRUE or FALSE — Pushing the Master Warning switchlight clears the underlying caution/warning panel light.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Reset clears the FLASH only. Underlying light remains lit if fault persists.',
 'medium', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
