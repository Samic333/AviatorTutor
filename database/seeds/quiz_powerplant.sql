-- =============================================================================
-- AviatorTutor — Phase 17: ATA 71 Powerplant — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'powerplant' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Powerplant — Practice', 'Twenty-five-question practice quiz on Q400 PW150A powerplant. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many engines does the Q400 have, and what model?',
 'mcq', JSON_ARRAY('1 PT6A','2 PW150A turboprops','2 CFM56 turbofans','4 PW100'),
 JSON_OBJECT('correct_index', 1), '2 PW150A. Mnemonic: PW150A-2-ENG.', 'easy', 10),
(@quiz_practice, 'What is normal takeoff SHP per engine?',
 'mcq', JSON_ARRAY('2,500','4,580','5,071','5,500'), JSON_OBJECT('correct_index', 1),
 '4,580 SHP NTOP per engine. Mnemonic: 4580-NTOP-5071-MTOP.', 'medium', 20),
(@quiz_practice, 'What is MTOP UPTRIM SHP available on engine failure?',
 'mcq', JSON_ARRAY('4,580','5,071','5,500','6,000'), JSON_OBJECT('correct_index', 1),
 'MTOP UPTRIM 5,071 SHP for brief period.', 'medium', 30),
(@quiz_practice, 'What type of compressor is NL, and what type is NH?',
 'mcq', JSON_ARRAY(
   'NL centrifugal / NH axial',
   'NL AXIAL / NH CENTRIFUGAL',
   'Both axial',
   'Both centrifugal'
 ), JSON_OBJECT('correct_index', 1),
 'NL axial 1st stage / NH centrifugal 2nd stage. Mnemonic: NL-AXIAL-NH-CENTRIFUGAL.', 'medium', 40),
(@quiz_practice, 'What does the Power Lever (PLA) control in the forward range?',
 'mcq', JSON_ARRAY(
   'Prop blade angle only',
   'FADEC for engine power',
   'Both PEC and FADEC',
   'Nothing'
 ), JSON_OBJECT('correct_index', 1),
 'Forward: PLA → FADEC. Beta/reverse: PLA → blade angle via PEC.', 'medium', 50),
(@quiz_practice, 'What does the Condition Lever (CL) control?',
 'mcq', JSON_ARRAY(
   'Engine power only',
   'PEC for RPM, ratings, manual feather, fuel on/off',
   'Brake pressure',
   'Hydraulic system'
 ), JSON_OBJECT('correct_index', 1),
 'CL drives PEC for RPM, ratings, feather, fuel.', 'medium', 60),
(@quiz_practice, 'What are the five core engine ratings?',
 'mcq', JSON_ARRAY(
   'NTOP / MTOP / MCP / MCL / MCR',
   'NTOP / MTOP / MGOM / MTRQ / MFLT',
   'TO / GA / CL / CR / FL',
   'NTOP only'
 ), JSON_OBJECT('correct_index', 0),
 'NTOP/MTOP/MCP/MCL/MCR + RDC TOP. Mnemonic: 5-RATINGS-CORE.', 'medium', 70),
(@quiz_practice, 'NTOP rating conditions on the ED?',
 'mcq', JSON_ARRAY(
   'CL 1020 + BLEED MIN/OFF or ON/MIN',
   'CL 850 + MTOP pushbutton',
   'CL 900 + BLEED OFF',
   'Any CL with BLEED ON/NORM'
 ), JSON_OBJECT('correct_index', 0),
 'NTOP: CL 1020 + bleed MIN/OFF.', 'hard', 80),
(@quiz_practice, 'MCP rating conditions?',
 'mcq', JSON_ARRAY(
   'CL 850 + MCP pushbutton',
   'CL MAX/1020 + BLEED ON/NORM or MAX',
   'CL 900 + MTOP pushbutton',
   'No conditions'
 ), JSON_OBJECT('correct_index', 1),
 'MCP: CL 1020 + bleed ON/NORM or MAX (bleed forces NTOP → MCP).', 'hard', 90),
(@quiz_practice, 'MCL rating conditions?',
 'mcq', JSON_ARRAY(
   'CL MIN/850 + MCL pushbutton (displays 900 RPM)',
   'CL 1020 + MCL',
   'CL 900 + MTOP',
   'No conditions'
 ), JSON_OBJECT('correct_index', 0),
 'MCL: CL MIN/850 + MCL pushbutton; ED shows 900 RPM.', 'hard', 100),
(@quiz_practice, 'MCR rating conditions?',
 'mcq', JSON_ARRAY(
   'CL MIN/850 + MCR',
   'CL 900 + MCR pushbutton (displays 850 RPM)',
   'CL 1020 + MCR',
   'No conditions'
 ), JSON_OBJECT('correct_index', 1),
 'MCR: CL 900 + MCR pushbutton; ED shows 850 RPM.', 'hard', 110),
(@quiz_practice, 'What does the RDC TOP TRQ DEC pushbutton do, and what are its limits?',
 'mcq', JSON_ARRAY(
   'Reduces NTOP power 5% per push, max 25%',
   'Reduces NTOP power 2% per push, max 10%; cannot activate in MTOP/MCP',
   'Increases NTOP power 10%',
   'No effect'
 ), JSON_OBJECT('correct_index', 1),
 '2% steps to 10% limit. Cannot activate in MTOP/MCP. Mnemonic: RDC-TOP-2-10.', 'hard', 120),
(@quiz_practice, 'What is the RDC NP LDG window from CL movement to MAX/1020?',
 'mcq', JSON_ARRAY('5 sec','10 sec','15 sec','30 sec'),
 JSON_OBJECT('correct_index', 2),
 '15 seconds. Mnemonic: RDC-NP-LDG-15-65.', 'hard', 130),
(@quiz_practice, 'What cancels RDC NP LDG mode?',
 'mcq', JSON_ARRAY(
   'Touchdown',
   'PLA ≥ 65° OR push RDC NP LDG button again',
   'CL to FUEL OFF',
   'Autofeather trigger'
 ), JSON_OBJECT('correct_index', 1),
 'PLA ≥65° or button push. Mnemonic: RDC-NP-LDG-15-65.', 'hard', 140),
(@quiz_practice, 'What does the EVENT MARKER pushbutton do?',
 'mcq', JSON_ARRAY(
   'Resets the FADEC',
   'Bookmarks the EMS — captures 2 min before + 1 min after',
   'Triggers autofeather',
   'Reduces takeoff power'
 ), JSON_OBJECT('correct_index', 1),
 'EVENT-2-1.', 'medium', 150),
(@quiz_practice, 'What is the PMA, and at what NH threshold is it active?',
 'mcq', JSON_ARRAY(
   'Permanent Magnet Alternator; active above NH 20%',
   'Pneumatic Master Amp; active above NL 30%',
   'Pilot Master Auth; always active',
   'Power Management Anchor; below NH 20%'
 ), JSON_OBJECT('correct_index', 0),
 'PMA active above NH 20%. ESS bus alternate. Mnemonic: PMA-NH-20.', 'hard', 160),
(@quiz_practice, 'How many HBOVs are on each engine, and what type?',
 'mcq', JSON_ARRAY(
   '1 LP only',
   '2: one LP + one HP — for surge margin during start, steady state, transient',
   '3 valves',
   'No HBOVs'
 ), JSON_OBJECT('correct_index', 1),
 '2 HBOV per engine LP+HP. Mnemonic: 2-HBOV-LP-HP.', 'medium', 170),
(@quiz_practice, 'When are bypass doors opened?',
 'mcq', JSON_ARRAY(
   'Always in flight',
   'Icing condition / heavy precipitation / bird activity / contaminated runways',
   'Only on engine failure',
   'Only at takeoff'
 ), JSON_OBJECT('correct_index', 1),
 'Manual selection in 4 conditions. Mnemonic: BYPASS-ICING-PRECIP-BIRD-CONTAM.', 'medium', 180),
(@quiz_practice, 'What is the normal engine shutdown procedure, and what does it test?',
 'mcq', JSON_ARRAY(
   'PLA to idle; tests nothing',
   'CL to FUEL OFF; tests NH overspeed protection circuitry',
   'Pull T-handle; tests fire bottles',
   'Auto only'
 ), JSON_OBJECT('correct_index', 1),
 'CL FUEL OFF tests NH O/S. Mnemonic: FUEL-OFF-TESTS-OS.', 'hard', 190),
(@quiz_practice, 'How does fire handle shutdown differ from normal CL FUEL OFF?',
 'mcq', JSON_ARRAY(
   'No difference — both same path',
   'Fire handle activates dedicated FMU fuel shutoff switch (different path); also closes hyd valves and arms fire bottles',
   'Only fire handle works',
   'Only CL works'
 ), JSON_OBJECT('correct_index', 1),
 'Fire handle = FMU dedicated path. CL = engine system test path.', 'hard', 200),
(@quiz_practice, 'At what blade angle do the PROPELLER GROUND RANGE lights illuminate?',
 'mcq', JSON_ARRAY('5°','10°','16°','20°'),
 JSON_OBJECT('correct_index', 1),
 '10° and below. Mnemonic: 10-DEG-GROUND-RANGE. (Different from 16° flight fine stop.)', 'hard', 210),
(@quiz_practice, 'What PLA value corresponds to NTOP and MTOP ratings?',
 'mcq', JSON_ARRAY(
   'NTOP 50° / MTOP 75°',
   'NTOP 80° / MTOP 95°',
   'NTOP 35° / MTOP 50°',
   'NTOP 100° / MTOP 100°'
 ), JSON_OBJECT('correct_index', 1),
 'NTOP 80° / MTOP 95°. Plus MCR 77.5° MCL 82.5° O/T 100°.', 'hard', 220),
(@quiz_practice, 'TRUE or FALSE — When BLEED is at NORM with CL at 1020, the ED shows NTOP rating.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Bleed NORM/MAX with CL 1020 forces MCP rating (Max Continuous Power), not NTOP. Bleed yellow on ED warns of this.', 'hard', 230),
(@quiz_practice, 'What does the dual analog/digital oil display on the ED show?',
 'mcq', JSON_ARRAY(
   'Oil pressure only',
   'Oil temperature (°C) AND oil pressure (PSI)',
   'Oil quantity only',
   'Engine torque'
 ), JSON_OBJECT('correct_index', 1),
 'Dual oil temp + pressure on ED.', 'medium', 240),
(@quiz_practice, 'TRUE or FALSE — The PMA powers the FADEC during engine start.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. PMA is INACTIVE below NH 20%. ESS bus powers FADEC during start; PMA takes over above NH 20%.', 'hard', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Powerplant — Type Rating Mock', 'Ten-question mock at type-rating oral standard.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'NTOP and MTOP UPTRIM SHP values.',
 'mcq', JSON_ARRAY('2500/3500','4580/5071','5071/5500','5500/6000'),
 JSON_OBJECT('correct_index', 1), '4580-NTOP-5071-MTOP.', 'medium', 10),
(@quiz_exam, 'NL and NH compressor types.',
 'mcq', JSON_ARRAY('Both axial','NL axial / NH centrifugal','Both centrifugal','NL centrifugal / NH axial'),
 JSON_OBJECT('correct_index', 1), 'NL-AXIAL-NH-CENTRIFUGAL.', 'medium', 20),
(@quiz_exam, 'Five engine ratings + variant.',
 'mcq', JSON_ARRAY(
   'NTOP/MTOP/MCP/MCL/MCR + RDC TOP',
   'NTOP/MTOP/CRZ/CLB/IDLE',
   'TO/CL/CR/MCR/IDLE',
   'NTOP only'
 ), JSON_OBJECT('correct_index', 0), '5-RATINGS-CORE.', 'medium', 30),
(@quiz_exam, 'PMA active threshold + alternate.',
 'mcq', JSON_ARRAY(
   'Always active; no alternate',
   'Above NH 20%; ESS bus alternate below 20%',
   'Above NL 50%; battery alternate',
   'Below NH 20%; PMA alternate'
 ), JSON_OBJECT('correct_index', 1), 'PMA-NH-20.', 'hard', 40),
(@quiz_exam, 'RDC TOP TRQ DEC step + limit.',
 'mcq', JSON_ARRAY(
   '5% per push, max 25%',
   '2% per push, max 10%; cannot activate in MTOP/MCP',
   '10% per push, max 50%',
   'No limits'
 ), JSON_OBJECT('correct_index', 1), 'RDC-TOP-2-10.', 'hard', 50),
(@quiz_exam, 'RDC NP LDG window and cancellation.',
 'mcq', JSON_ARRAY(
   '5 sec window, cancel at touchdown',
   '15-sec window from CL move; cancel at PLA ≥65° or push button',
   '30-sec window, no cancellation',
   'No window'
 ), JSON_OBJECT('correct_index', 1), 'RDC-NP-LDG-15-65.', 'hard', 60),
(@quiz_exam, 'EVENT MARKER capture window.',
 'mcq', JSON_ARRAY('5 sec before/after','2 min before + 1 min after','30 sec each','No data captured'),
 JSON_OBJECT('correct_index', 1), 'EVENT-2-1.', 'medium', 70),
(@quiz_exam, 'HBOV count per engine.',
 'mcq', JSON_ARRAY('1','2 (one LP + one HP)','3','None'),
 JSON_OBJECT('correct_index', 1), '2-HBOV-LP-HP.', 'medium', 80),
(@quiz_exam, 'Engine shutdown via CL FUEL OFF tests what?',
 'mcq', JSON_ARRAY('Nothing','NH overspeed protection','Fire bottles','Autofeather'),
 JSON_OBJECT('correct_index', 1), 'FUEL-OFF-TESTS-OS.', 'hard', 90),
(@quiz_exam, 'TRUE or FALSE — Bypass doors auto-open in icing conditions.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Manually selected via ICE PROTECTION panel switchlights.', 'medium', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
