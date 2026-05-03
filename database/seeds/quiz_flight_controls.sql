-- =============================================================================
-- AviatorTutor — Phase 7: ATA 27 Flight Controls
-- Two quizzes: Practice (25 Q, no time limit, pass 70) + Type Rating Mock (10 Q, 12 min, pass 80)
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'flight-controls' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

-- ----------------------------------------------------------------------------
-- Quiz 1 — Practice
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Flight Controls — Practice',
 'Twenty-five-question practice quiz on PFCS architecture, spoiler airspeed logic, rudder PCUs, pitch trim, flap auto-trim, gust locks, SPS, and IAS mismatch cascade. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'Which Q400 surfaces are part of the Powered Flight Control Surfaces (PFCS)?',
 'mcq',
 JSON_ARRAY('Ailerons, elevators, rudder','Elevators, spoilers, rudder','Ailerons, spoilers, flaps','Ailerons, elevators, spoilers, rudder'),
 JSON_OBJECT('correct_index', 1),
 'PFCS = elevators + spoilers + rudder. All hydraulic. Ailerons are mechanical / cable-driven and NOT part of PFCS.',
 'easy', 10),

(@quiz_practice, 'How many PCUs does each elevator have, and how are they powered hydraulically?',
 'mcq',
 JSON_ARRAY(
   '2 PCUs: outboard and inboard, both on No.1 hyd',
   '3 PCUs: outboard No.1 (active), centre No.2 (active), inboard No.3 (standby)',
   '3 PCUs all powered by No.2 hyd',
   '4 PCUs: two on each elevator, alternating No.1 and No.2'
 ),
 JSON_OBJECT('correct_index', 1),
 'Each elevator has 3 PCUs. Outboard = No.1 (active), Centre = No.2 (active), Inboard = No.3 (STANDBY). Mnemonic: 1-2-3-HYD-ELEV.',
 'medium', 20),

(@quiz_practice, 'When does the inboard standby elevator PCU activate?',
 'mcq',
 JSON_ARRAY(
   'Continuously in normal flight',
   'Only on takeoff and landing',
   'Auto-activates on No.1 OR No.2 hydraulic failure; or manually via HYD #3 ISOL VLV pushbutton',
   'Only when commanded by the autopilot'
 ),
 JSON_OBJECT('correct_index', 2),
 'Standby — auto-activates on No.1 or No.2 fail. Manual activation with No.1 and No.2 healthy illuminates ELEVATOR PRESS caution.',
 'hard', 30),

(@quiz_practice, 'How are the four roll spoilers allocated to hydraulic systems?',
 'mcq',
 JSON_ARRAY(
   'All four on No.1 hyd',
   'Inboards on No.1 hyd; outboards on No.2 hyd',
   'Inboards on No.2 hyd; outboards on No.1 hyd',
   'Inboards on No.3 hyd; outboards on No.1 hyd'
 ),
 JSON_OBJECT('correct_index', 1),
 'Inboards = No.1 hyd; outboards = No.2 hyd. Mnemonic: IN-N1-OUT-N2.',
 'medium', 40),

(@quiz_practice, 'At what airspeed does the FCECU disable the outboard spoilers, and at what airspeed does it re-enable them?',
 'mcq',
 JSON_ARRAY(
   'Disabled above 165 KIAS, enabled below 170 KIAS',
   'Disabled above 170 KIAS, enabled below 165 KIAS',
   'Disabled above 200 KIAS, enabled below 180 KIAS',
   'Outboards always operate; never disabled'
 ),
 JSON_OBJECT('correct_index', 1),
 'Above 170 KIAS only inboards. Below 165 KIAS both. Mnemonic: 170-OFF-165-ON.',
 'medium', 50),

(@quiz_practice, 'In normal flight, which control wheel commands which surfaces?',
 'mcq',
 JSON_ARRAY(
   'Pilot wheel = ailerons; copilot wheel = spoilers',
   'Pilot wheel = SPOILERS; copilot wheel = AILERONS',
   'Both wheels command both surfaces independently with no interconnect',
   'Only the pilot wheel commands roll surfaces'
 ),
 JSON_OBJECT('correct_index', 1),
 'Pilot wheel = spoilers; copilot wheel = ailerons. Both interconnected normally.',
 'medium', 60),

(@quiz_practice, 'After ROLL DISC handle pulled out and rotated 90°, which surfaces does each pilot retain?',
 'mcq',
 JSON_ARRAY(
   'Pilot keeps ailerons; copilot keeps spoilers',
   'Pilot keeps SPOILERS only; copilot keeps AILERONS only',
   'Both pilots retain full roll control',
   'Roll control is lost entirely'
 ),
 JSON_OBJECT('correct_index', 1),
 'Pilot keeps spoilers, copilot keeps ailerons. Pilot with the UNJAMMED wheel has roll control.',
 'hard', 70),

(@quiz_practice, 'What is the maximum aileron deflection on the Q400, and what handwheel travel produces it?',
 'mcq',
 JSON_ARRAY(
   '±10° aileron, 50° handwheel',
   '±15° aileron, 60° handwheel',
   '±17° aileron, 70° handwheel',
   '±25° aileron, 90° handwheel'
 ),
 JSON_OBJECT('correct_index', 2),
 '±17° aileron deflection from neutral. Handwheel turns 70° L/R of centre.',
 'medium', 80),

(@quiz_practice, 'Which THREE conditions must all be met for ground-mode lift dump on landing?',
 'mcq',
 JSON_ARRAY(
   'FLIGHT/TAXI in TAXI · power above FLT IDLE +12° · WOW both gear',
   'FLIGHT/TAXI in FLIGHT · power BELOW FLT IDLE +12° · WOW both main gear',
   'FLIGHT/TAXI in FLIGHT · airspeed below 80 kts · WOW one gear',
   'Brakes applied · reverse selected · airspeed below 100 kts'
 ),
 JSON_OBJECT('correct_index', 1),
 'Three conditions: FLIGHT/TAXI in FLIGHT, both power levers below FLT IDLE +12°, WOW on both main gear.',
 'medium', 90),

(@quiz_practice, 'How long is the time delay before ROLL SPLR INBD/OUTBD GND caution illuminates if a lift-dump valve fails?',
 'mcq',
 JSON_ARRAY('Immediate','2 seconds','5 seconds','15 seconds'),
 JSON_OBJECT('correct_index', 2),
 'Five-second delay before the caution illuminates after a lift-dump valve fault on landing.',
 'medium', 100),

(@quiz_practice, 'What does RUD 1 PUSH OFF control vs RUD 2 PUSH OFF?',
 'mcq',
 JSON_ARRAY(
   'RUD 1 = upper PCU; RUD 2 = lower PCU',
   'RUD 1 = LOWER PCU; RUD 2 = UPPER PCU',
   'Both control the same PCU — redundant',
   'RUD 1 = trim; RUD 2 = damper'
 ),
 JSON_OBJECT('correct_index', 1),
 'RUD 1 = LOWER. RUD 2 = UPPER. Mnemonic: RUD-1-LOWER-RUD-2-UPPER.',
 'medium', 110),

(@quiz_practice, 'AFM 4.18.12 — what is the rule about RUD PUSH OFF switchlights?',
 'mcq',
 JSON_ARRAY(
   'Both must be pushed simultaneously to depressurise the rudder',
   'Only ONE may be pushed at a time',
   'They alternate automatically — no crew action required',
   'Both are pushed to engage yaw damper'
 ),
 JSON_OBJECT('correct_index', 1),
 'Only ONE at a time. Pushing both inadvertently re-pressurises the previously-pushed PCU. Push the non-jammed side again to recover.',
 'hard', 120),

(@quiz_practice, 'How does the FCECU regulate rudder authority?',
 'mcq',
 JSON_ARRAY(
   'By increasing pedal force as airspeed increases',
   'By reducing hydraulic pressure to the rudder PCUs as airspeed increases',
   'By disabling rudder above 200 KIAS',
   'By increasing yaw damper authority above 200 KIAS'
 ),
 JSON_OBJECT('correct_index', 1),
 'FCECU reduces hyd pressure to PCUs as airspeed rises. Same pedal input → less rudder deflection. Airspeed from ADUs.',
 'hard', 130),

(@quiz_practice, 'What is the maximum yaw damper authority?',
 'mcq',
 JSON_ARRAY('±2.5° rudder','±3.5° rudder','±4.5° rudder','±7.5° rudder'),
 JSON_OBJECT('correct_index', 2),
 '±4.5° max. Yaw damper requires inputs from BOTH FGM #1 and FGM #2.',
 'medium', 140),

(@quiz_practice, 'What is the pitch trim priority order?',
 'mcq',
 JSON_ARRAY(
   'Autopilot > pilot > copilot',
   'PILOT > COPILOT > autopilot',
   'Copilot > pilot > autopilot',
   'All three are equal — first input wins'
 ),
 JSON_OBJECT('correct_index', 1),
 'Pilot > copilot > autopilot. FCECU enforces this priority for pitch trim.',
 'medium', 150),

(@quiz_practice, 'What happens if a manual pitch trim command persists for more than 3 seconds?',
 'mcq',
 JSON_ARRAY(
   'Trim auto-resets to neutral',
   'ELEVATOR TRIM SHUTOFF switchlight illuminates and aural clicking sounds',
   'Autopilot engages automatically',
   'Stick pusher activates'
 ),
 JSON_OBJECT('correct_index', 1),
 '3-sec rule → ELEVATOR TRIM SHUTOFF + aural. Push either left or right SHUTOFF switchlight to deactivate trim.',
 'medium', 160),

(@quiz_practice, 'When is flap auto-pitch trim active?',
 'mcq',
 JSON_ARRAY(
   'Always when flaps are moving',
   'Only flaps 15° to 35°, AP off, airspeed below 180 KIAS, no manual trim',
   'Only when AP is engaged',
   'Only flaps 0° to 15°, any airspeed'
 ),
 JSON_OBJECT('correct_index', 1),
 'Flap auto-trim active 15°–35° flap range only, AP off, airspeed below 180 KIAS, no manual trim. Mnemonic: 15-35-AUTO-TRIM.',
 'hard', 170),

(@quiz_practice, 'How many flap-selector lever gates does the Q400 have, and what positions?',
 'mcq',
 JSON_ARRAY('Three (0°, 15°, 35°)','Four (0°, 5°, 15°, 35°)','Five (0°, 5°, 10°, 15°, 35°)','Six (0°, 5°, 10°, 15°, 25°, 35°)'),
 JSON_OBJECT('correct_index', 2),
 'Five gates: 0°, 5°, 10°, 15°, 35°. Mnemonic: FLAP-5-GATES.',
 'easy', 180),

(@quiz_practice, 'Which hydraulic system powers the flaps?',
 'mcq',
 JSON_ARRAY('No.1','No.2','No.3 standby','Independent flap-only hydraulic system'),
 JSON_OBJECT('correct_index', 0),
 'Flaps run on No.1 hyd via the Flap Power Unit (FPU). FCU controls; bi-directional no-backs lock the flaps.',
 'medium', 190),

(@quiz_practice, 'TRUE or FALSE — Q400 ailerons are hydraulically powered.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Ailerons are MECHANICAL / cable-driven. Each surface has a geared tab for aerodynamic assist.',
 'easy', 200),

(@quiz_practice, 'What four cautions illuminate simultaneously on an IAS mismatch >17 kts?',
 'mcq',
 JSON_ARRAY(
   'RUD CTRL + ELEV PRESS + SPLR INBD + PITCH TRIM',
   'RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM',
   'AC GEN 1 + AC GEN 2 + DC BUS + IAS MISMATCH',
   'ROLL SPLR INBD + OUTBD + ELEV ASYMMETRY + ELEV FEEL'
 ),
 JSON_OBJECT('correct_index', 1),
 'IAS mismatch cascade: RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM. Mnemonic: 17-KTS-MISMATCH.',
 'hard', 210),

(@quiz_practice, 'During the daily stall-warning test, how long must each TEST1 and TEST2 position be held?',
 'mcq',
 JSON_ARRAY('Momentary tap','More than 5 seconds','More than 10 seconds','30 seconds minimum'),
 JSON_OBJECT('correct_index', 2),
 'Both TEST1 and TEST2 each held >10 seconds. Tests SPM1 and SPM2 separately.',
 'medium', 220),

(@quiz_practice, 'What does the CONTROL LOCK lever lock, and what is its effect on power-lever travel?',
 'mcq',
 JSON_ARRAY(
   'Locks rudder; no effect on power levers',
   'Locks ailerons; restricts power-lever travel so takeoff thrust cannot be advanced',
   'Locks elevators; auto-disengages on engine start',
   'Locks all surfaces; spring-loaded'
 ),
 JSON_OBJECT('correct_index', 1),
 'CONTROL LOCK = aileron gust lock. AFT = ON locks ailerons + restricts power-lever travel. FWD = OFF.',
 'medium', 230),

(@quiz_practice, 'TRUE or FALSE — On the ground, both RUD 1 and RUD 2 PUSH OFF switchlights illuminating in a strong tailwind is a system fault that requires maintenance.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. With engines off and strong tailwinds, the rudder PCU bungees can compress and illuminate the switchlights. They self-clear once hydraulic pressure is established at engine start.',
 'hard', 240),

(@quiz_practice, 'What does the FLIGHT/TAXI switch auto-return logic do?',
 'mcq',
 JSON_ARRAY(
   'Auto-returns to TAXI when on the ground',
   'Auto-returns to FLIGHT when power levers exceed FLT IDLE +12°',
   'Auto-returns to FLIGHT after 30 seconds in TAXI',
   'No auto-return — must be manually selected'
 ),
 JSON_OBJECT('correct_index', 1),
 'Solenoid de-energises when power levers exceed FLT IDLE +12°; switch auto-returns to FLIGHT and retracts spoilers. Required for takeoff.',
 'hard', 250);

-- ----------------------------------------------------------------------------
-- Quiz 2 — Type Rating Mock
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Flight Controls — Type Rating Mock',
 'Ten-question mock at type-rating oral standard. Twelve-minute timer, pass score 80%. Designed to expose holes BEFORE the check ride.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'Quote the elevator PCU architecture and hydraulic mapping.',
 'mcq',
 JSON_ARRAY(
   '2 PCUs per elevator; both on No.1 hyd',
   '3 PCUs per elevator: outbd No.1 active, ctr No.2 active, inbd No.3 STANDBY',
   '3 PCUs per elevator, all on No.2 hyd',
   '4 PCUs per elevator on No.1 + No.2 alternating'
 ),
 JSON_OBJECT('correct_index', 1),
 '3 PCUs per elevator. Outbd = No.1 active. Ctr = No.2 active. Inbd = No.3 STANDBY. Mnemonic: 1-2-3-HYD-ELEV.',
 'medium', 10),

(@quiz_exam, 'Spoiler airspeed lockout — at what KIAS are the outboards disabled and re-enabled?',
 'mcq',
 JSON_ARRAY(
   'Disabled >170; re-enabled <165',
   'Disabled >165; re-enabled <170',
   'Disabled >200; re-enabled <180',
   'Always active; never disabled'
 ),
 JSON_OBJECT('correct_index', 0),
 'Above 170 disabled; below 165 re-enabled. 5-knot deadband. Mnemonic: 170-OFF-165-ON.',
 'hard', 20),

(@quiz_exam, 'In flight you observe RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM cautions ALL illuminate at once. What is the cause and the FIRST action?',
 'mcq',
 JSON_ARRAY(
   'Triple hydraulic failure — declare MAYDAY',
   'IAS mismatch >17 kts between ADUs — REDUCE BELOW 200 KIAS, identify bad ADU',
   'FCECU total loss — disconnect AP and hand-fly',
   'Stick pusher armed — push the OFF switchlight'
 ),
 JSON_OBJECT('correct_index', 1),
 'Four-light cascade = IAS mismatch >17 kts. Reduce below 200 KIAS, identify the bad ADU using standby + GPS GS, run QRH.',
 'hard', 30),

(@quiz_exam, 'Roll control jam — describe the disconnect action and the post-disconnect wheel mapping.',
 'mcq',
 JSON_ARRAY(
   'Pull ROLL DISC straight out only; both pilots have full roll',
   'Pull ROLL DISC out and rotate 90°; pilot keeps SPOILERS only, copilot keeps AILERONS only',
   'Pull pitch disconnect; spoilers locked retracted',
   'Push both SPLR PUSH OFF switchlights; ailerons take over'
 ),
 JSON_OBJECT('correct_index', 1),
 'ROLL DISC handle out + 90° clockwise or counter-clockwise. Pilot keeps spoilers; copilot keeps ailerons. Pilot with the UNJAMMED wheel has roll control.',
 'hard', 40),

(@quiz_exam, 'Three conditions for ground-mode lift dump on landing.',
 'mcq',
 JSON_ARRAY(
   'FLIGHT/TAXI in FLIGHT · power below FLT IDLE +12° · WOW both gear',
   'FLIGHT/TAXI in TAXI · power above FLT IDLE +12° · airspeed <80',
   'Reverse selected · brakes applied · WOW one gear',
   'Power off · airspeed <60 · WOW any gear'
 ),
 JSON_OBJECT('correct_index', 0),
 'FLIGHT/TAXI in FLIGHT + both power levers below FLT IDLE +12° + WOW on both main gear. Mnemonic: 3-COND-LIFT-DUMP.',
 'medium', 50),

(@quiz_exam, 'A real pitch trim runaway begins on climb-out. ELEVATOR TRIM SHUTOFF illuminates with aural clicking. Immediate action?',
 'mcq',
 JSON_ARRAY(
   'Disconnect autopilot first',
   'Push either left or right ELEVATOR TRIM SHUTOFF switchlight to deactivate trim',
   'Pull the ROLL DISC handle',
   'Push both RUD PUSH OFF switchlights'
 ),
 JSON_OBJECT('correct_index', 1),
 'Push the ELEVATOR TRIM SHUTOFF (either side). Trim deactivates. Then manually retrim and divert.',
 'hard', 60),

(@quiz_exam, 'AFM 4.18.12 — RUD PUSH OFF switchlight rule.',
 'mcq',
 JSON_ARRAY(
   'Both must be pushed for full rudder authority',
   'Only ONE may be pushed at a time',
   'They alternate automatically',
   'Both are inhibited on the ground'
 ),
 JSON_OBJECT('correct_index', 1),
 'Only ONE at a time. Pushing both inadvertently re-pressurises the previously-pushed PCU; push non-jammed side again to recover.',
 'medium', 70),

(@quiz_exam, 'Yaw damper authority and source requirements.',
 'mcq',
 JSON_ARRAY(
   '±3° max, single FGM input',
   '±4.5° max, requires BOTH FGM #1 and FGM #2',
   '±7° max, autopilot only',
   '±2.5° max, single FGM input'
 ),
 JSON_OBJECT('correct_index', 1),
 '±4.5° max. BOTH FGMs required. Mnemonic: 4.5-DEG-YD.',
 'medium', 80),

(@quiz_exam, 'Daily stall-warning test sequence.',
 'mcq',
 JSON_ARRAY(
   'Tap STALL WARN to TEST1 then TEST2',
   'Hold STALL WARN at TEST1 for >10 sec, then at TEST2 for >10 sec',
   'Hold the test button for 30 sec total',
   'Cycle the SPS circuit breaker'
 ),
 JSON_OBJECT('correct_index', 1),
 'TEST1 (>10 sec) then TEST2 (>10 sec). Tests SPM1 and SPM2 separately. Mnemonic: 10-SEC-STALL-TEST.',
 'medium', 90),

(@quiz_exam, 'Flap auto-pitch trim activation conditions.',
 'mcq',
 JSON_ARRAY(
   'Always when flaps are moving',
   'Only flaps 15°–35° + AP OFF + <180 KIAS + no manual trim',
   'Only with AP engaged',
   'Only with WOW on both gear'
 ),
 JSON_OBJECT('correct_index', 1),
 '15°–35° flap range, AP off, <180 KIAS, no manual trim. Nose-down on extension; nose-up on retraction. Mnemonic: 15-35-AUTO-TRIM.',
 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
