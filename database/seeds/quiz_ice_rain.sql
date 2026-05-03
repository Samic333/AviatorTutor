-- =============================================================================
-- AviatorTutor — Phase 9: ATA 30 Ice & Rain Protection
-- Two quizzes: Practice (25 Q, no time limit, pass 70) + Type Rating Mock (10 Q, 12 min, pass 80)
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'ice-rain' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

-- ----------------------------------------------------------------------------
-- Quiz 1 — Practice
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Ice & Rain — Practice',
 'Twenty-five-question practice quiz on IDS, pneumatic boots, propeller heaters, electric anti-ice, REF SPEEDS, and abnormals. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many Ice Detector Probes does the Q400 have, and how is the IDS controlled from the cockpit?',
 'mcq',
 JSON_ARRAY(
   '1 IDP, controlled by the IDS switch on the ice protection panel',
   '2 IDPs, fully automatic with NO flight-deck control',
   '4 IDPs, automatic with manual override',
   '2 IDPs, controlled by a guarded switch'
 ),
 JSON_OBJECT('correct_index', 1),
 'Two IDPs (left and right front fuselage). Fully automatic, operates whenever 115 VAC is available. NO flight-deck switch. Mnemonic: 2-IDP-AUTO.',
 'medium', 10),

(@quiz_practice, 'What ice thickness on the IDP triggers detection?',
 'mcq',
 JSON_ARRAY('0.1 mm','0.5 mm','1.0 mm','2.0 mm'),
 JSON_OBJECT('correct_index', 1),
 'More than 0.5 mm of ice. Mnemonic: 0.5-MM-ICE.',
 'medium', 20),

(@quiz_practice, 'When does the ICE DETECT FAIL caution illuminate?',
 'mcq',
 JSON_ARRAY(
   'When a single probe fails',
   'Only when BOTH probes fail (system is redundant)',
   'When the IDS is switched off',
   'When TAT exceeds +5°C'
 ),
 JSON_OBJECT('correct_index', 1),
 'BOTH must fail. Single-probe failure is silent. Mnemonic: BOTH-IDPS-FAIL-CAUTION.',
 'hard', 30),

(@quiz_practice, 'Which Q400 surface uses ELECTRIC anti-icing rather than pneumatic boots?',
 'mcq',
 JSON_ARRAY(
   'Wing leading edges',
   'Horizontal stabiliser leading edge',
   'Pitot/static probes',
   'Nacelle inlet lips'
 ),
 JSON_OBJECT('correct_index', 2),
 'Pitot/static probes are electric. Wings, tails, and inlet lips use pneumatic boots. Mnemonic split: BOOTS-LIPS-WINGS-TAIL vs ELEC-PROBES-WINDOWS-INTAKE-AOA.',
 'medium', 40),

(@quiz_practice, 'What is the regulated boot pressure on the Q400?',
 'mcq',
 JSON_ARRAY('12 PSI','15 PSI','18 PSI','22 PSI'),
 JSON_OBJECT('correct_index', 2),
 '18 PSI ± 3 PSI regulated. The 15 PSI threshold is when the BOOT INFLATION advisory light illuminates.',
 'medium', 50),

(@quiz_practice, 'At what boot pressure does the BOOT INFLATION advisory light (green) illuminate?',
 'mcq',
 JSON_ARRAY('12 PSI','15 PSI','18 PSI','22 PSI'),
 JSON_OBJECT('correct_index', 1),
 '15 PSI threshold. Different from 18 PSI regulated. Mnemonic: 18-PSI-BOOTS-15-LIGHT.',
 'medium', 60),

(@quiz_practice, 'TRUE or FALSE — Boot air will not inflate the boots if the BLEED control switch is OFF.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Boot air comes from each engine''s bleed port and is INDEPENDENT of the BLEED switch. Mnemonic: BLEED-INDEPENDENT.',
 'hard', 70),

(@quiz_practice, 'What does the BOOT AIR switch do?',
 'mcq',
 JSON_ARRAY(
   'Turns boot heaters on/off',
   'NORM = isolator open, both sides connected. ISO = isolated, for individual pressure check or to isolate a leak',
   'Selects the engine bleed source',
   'Activates manual boot inflation'
 ),
 JSON_OBJECT('correct_index', 1),
 'BOOT AIR controls the isolator valve between left and right boot systems.',
 'medium', 80),

(@quiz_practice, 'What are the cycle timings for AIRFRAME MODE SELECT SLOW vs FAST?',
 'mcq',
 JSON_ARRAY(
   'SLOW = 1 min, FAST = 30 sec',
   'SLOW = 3 min (144-sec dwell), FAST = 1 min (24-sec dwell)',
   'SLOW = 5 min, FAST = 2 min',
   'SLOW = 10 min, FAST = 5 min'
 ),
 JSON_OBJECT('correct_index', 1),
 'SLOW = 3-minute cycle, 144-sec dwell. FAST = 1-minute cycle, 24-sec dwell. Mnemonic: SLOW-3-MIN-FAST-1-MIN.',
 'medium', 90),

(@quiz_practice, 'How many boot combinations are in one full cycle, and how long does each combination inflate?',
 'mcq',
 JSON_ARRAY(
   '4 combinations, 10 sec each',
   '6 combinations, 6 sec each',
   '8 combinations, 5 sec each',
   '6 combinations, 12 sec each'
 ),
 JSON_OBJECT('correct_index', 1),
 'Six combinations. Each inflates for 6 seconds. Mnemonic: 6-COMB-6-SEC.',
 'hard', 100),

(@quiz_practice, 'How many propeller blades are on each Q400 prop, and what percentage of each blade is heated?',
 'mcq',
 JSON_ARRAY(
   '4 blades, 50% coverage',
   '5 blades, 60% coverage',
   '6 blades, 70% coverage',
   '6 blades, 100% coverage'
 ),
 JSON_OBJECT('correct_index', 2),
 'Six blades per prop, electric heater on leading 70% of each blade.',
 'medium', 110),

(@quiz_practice, 'What three conditions must be met for the propeller heaters to cycle?',
 'mcq',
 JSON_ARRAY(
   'PROP ON + TAT ≤ +5°C + NP > 400 RPM',
   'PROP ON + SAT ≤ 0°C + NP > 600 RPM',
   'PROP ON + ICE DETECTED + any RPM',
   'PROP ON + IAS > 200 KIAS + TAT < 0°C'
 ),
 JSON_OBJECT('correct_index', 0),
 'PROP ON + TAT ≤ +5°C + NP > 400 RPM. Mnemonic: TAT-5-NP-400.',
 'hard', 120),

(@quiz_practice, 'Why does the system use TAT instead of SAT for propeller heater cycling?',
 'mcq',
 JSON_ARRAY(
   'TAT is more conservative — always lower than SAT',
   'TAT reflects the actual surface temperature of the airframe, which can be much warmer than SAT at high airspeed (kinetic heating)',
   'SAT is unreliable at altitude',
   'TAT is required by regulation; no engineering reason'
 ),
 JSON_OBJECT('correct_index', 1),
 'TAT = SAT + kinetic heating. At +5°C SAT and high airspeed, TAT can be much warmer than +5°C. Heaters wouldn''t cycle if SAT-based — that''s why TAT is used.',
 'hard', 130),

(@quiz_practice, 'How long is the PROP TEST cycle per propeller, and what cooldown is required before retest?',
 'mcq',
 JSON_ARRAY(
   '10 sec / 60 sec cooldown',
   '5 sec / 30 sec cooldown',
   '15 sec / 15 sec cooldown',
   '30 sec / 5 sec cooldown'
 ),
 JSON_OBJECT('correct_index', 1),
 '5 sec per prop. 30-sec cooldown before retest. Mnemonic: 30-SEC-PROP-TEST-COOL.',
 'medium', 140),

(@quiz_practice, 'What does setting REF SPEEDS to INCR do?',
 'mcq',
 JSON_ARRAY(
   'Increases the SPS stall margin for icing conditions',
   'Increases approach speed by 20 kts',
   'Activates the propeller heaters',
   'Switches to high-speed boot cycling'
 ),
 JSON_OBJECT('correct_index', 0),
 'REF SPEEDS to INCR signals the SPS to adjust stall margin for the iced airframe. [INCR REF SPEED] message confirms. Mnemonic: INCR-FOR-ICING.',
 'medium', 150),

(@quiz_practice, 'When does the DE-ICE PRESS caution illuminate?',
 'mcq',
 JSON_ARRAY(
   'Only when boot pressure exceeds 25 PSI',
   'Pressure < 15 PSI on either side, OR DDV opens but boot fails to reach 15 PSI, OR boot stays at 15 PSI after DDV closes',
   'Whenever the BOOT AIR switch is at ISO',
   'Only during MANUAL mode'
 ),
 JSON_OBJECT('correct_index', 1),
 'Three triggers: low pressure, failure to inflate, or stuck-open DDV. All trigger the same caution.',
 'hard', 160),

(@quiz_practice, 'When does the DE-ICE TIMER caution illuminate, and how do you recover?',
 'mcq',
 JSON_ARRAY(
   'On bleed pressure failure; recover with engine restart',
   'On TMU failure; recover by switching AIRFRAME MODE SELECT to MANUAL and using AIRFRAME MANUAL SELECT 6-detent rotary',
   'On AC power failure; recover with APU start',
   'On windshield heater failure; recover with WARM mode'
 ),
 JSON_OBJECT('correct_index', 1),
 'TMU failure (sequencer/logic/input disagreement). Recover with MANUAL mode + 6-detent rotary, 24-sec dwell minimum.',
 'hard', 170),

(@quiz_practice, 'In a hold at idle in moderate icing, BOOT INFLATION lights stop cycling and DEICE PRESS slumps below 15 PSI. Best action?',
 'mcq',
 JSON_ARRAY(
   'Switch BOOT AIR to ISO',
   'Advance both POWER levers to raise NL and restore bleed pressure',
   'Switch AIRFRAME MODE SELECT to MANUAL',
   'Disable propeller heaters to reduce electrical load'
 ),
 JSON_OBJECT('correct_index', 1),
 'Low NL at idle starves the bleed system. Advance power levers to raise NL → bleed pressure recovers. Mnemonic: NL-FOR-PRESSURE.',
 'medium', 180),

(@quiz_practice, 'Which surfaces are protected by pneumatic boots?',
 'mcq',
 JSON_ARRAY(
   'Wing leading edges only',
   'Wings + horizontal stabiliser + vertical stabiliser + nacelle inlet lips',
   'Wings + windshield + propellers',
   'Only the inlet lips'
 ),
 JSON_OBJECT('correct_index', 1),
 'Boots on wings, both stabilisers (horiz + vert), and nacelle inlet lips. Mnemonic: BOOTS-LIPS-WINGS-TAIL.',
 'medium', 190),

(@quiz_practice, 'What other systems share the regulated boot air?',
 'mcq',
 JSON_ARRAY(
   'Forward passenger door seal + aft baggage door seal + AFT safety-valve ejector for pressurization',
   'Engine starter air',
   'Cabin pressurisation supply',
   'APU air supply only'
 ),
 JSON_OBJECT('correct_index', 0),
 'Pressurised boot air also feeds: fwd pax door seal, aft baggage door seal, and the AFT safety-valve ejector for the pressurisation system.',
 'hard', 200),

(@quiz_practice, 'TRUE or FALSE — At SAT +3°C and high airspeed, propeller heaters may not cycle.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 0),
 'TRUE. TAT is the trigger, not SAT. At high airspeed, TAT can exceed +5°C even when SAT is +3°C — heaters wait. With visible ice, however, system functions regardless of SAT.',
 'hard', 210),

(@quiz_practice, 'How are the two propellers heated relative to each other?',
 'mcq',
 JSON_ARRAY(
   'Both heated simultaneously',
   'One propeller heated then the other (load balancing)',
   'Only the prop on the warm engine',
   'Random alternating per cycle'
 ),
 JSON_OBJECT('correct_index', 1),
 'Alternating — one prop, then the other — to balance the AC bus load. PROPS advisory shows which is currently heating.',
 'medium', 220),

(@quiz_practice, 'What is the icing-entry crew chant on the Q400?',
 'mcq',
 JSON_ARRAY(
   'BLEED ON · BOOT AIR ISO · WIPER HIGH',
   'REF SPEEDS INCR · PROPS ON · AIRFRAME FAST · ENGINE INTAKE heaters · monitor DEICE PRESS',
   'AIRFRAME OFF · PROPS OFF · WINDSHIELD WARM',
   'PITOT ON · STBY ON · IDS ON'
 ),
 JSON_OBJECT('correct_index', 1),
 'REF SPEEDS first (informs SPS), then PROPS, AIRFRAME, INTAKES, monitor pressure. Order matters.',
 'medium', 230),

(@quiz_practice, 'TRUE or FALSE — A single failed IDP illuminates the ICE DETECT FAIL caution.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Both must fail. System is redundant.',
 'medium', 240),

(@quiz_practice, 'What is the alternate pilot wiper pushbutton, and when is it used?',
 'mcq',
 JSON_ARRAY(
   'A momentary switch to test the wiper motor',
   'A guarded alternate-action pushbutton — pushed in, the pilot windshield wiper operates at HIGH speed independently of the normal wiper control',
   'Activates the rain repellent system',
   'Switches the windshield from NORM to WARM'
 ),
 JSON_OBJECT('correct_index', 1),
 'Used when normal wiper control fails. Drives pilot wiper at HIGH speed.',
 'hard', 250);

-- ----------------------------------------------------------------------------
-- Quiz 2 — Type Rating Mock
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Ice & Rain — Type Rating Mock',
 'Ten-question mock at type-rating oral standard. Twelve-minute timer, pass score 80%.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'Quote the IDP count, ice trigger threshold, and detection logic.',
 'mcq',
 JSON_ARRAY(
   '1 IDP, 1.0 mm trigger, manual detection',
   '2 IDPs, >0.5 mm trigger, fully automatic',
   '4 IDPs, >0.5 mm trigger, manual override',
   '2 IDPs, >2.0 mm trigger, fully automatic'
 ),
 JSON_OBJECT('correct_index', 1),
 '2 IDPs (left + right front fuselage), >0.5 mm trigger, fully automatic with no flight-deck control. Mnemonic: 2-IDP-AUTO · 0.5-MM-ICE.',
 'medium', 10),

(@quiz_exam, 'Quote boot regulated pressure, BOOT INFLATION light threshold, and the bleed-source detail.',
 'mcq',
 JSON_ARRAY(
   '15 PSI / 12 PSI / from BLEED switch',
   '18 PSI ± 3 / 15 PSI / from engine bleed port INDEPENDENT of BLEED switch',
   '22 PSI / 18 PSI / from APU only',
   '18 PSI / 18 PSI / from BLEED switch'
 ),
 JSON_OBJECT('correct_index', 1),
 '18 ± 3 regulated; 15 light; bleed-port direct, independent of BLEED switch. Mnemonics: 18-PSI-BOOTS-15-LIGHT · BLEED-INDEPENDENT.',
 'hard', 20),

(@quiz_exam, 'Quote SLOW vs FAST cycle timings.',
 'mcq',
 JSON_ARRAY(
   'SLOW 1 min / FAST 30 sec',
   'SLOW 3 min (144-sec dwell) / FAST 1 min (24-sec dwell)',
   'SLOW 5 min / FAST 2 min',
   'Identical cycle, only dwell differs'
 ),
 JSON_OBJECT('correct_index', 1),
 'SLOW = 3-minute cycle, 144-sec dwell. FAST = 1-minute cycle, 24-sec dwell. Mnemonic: SLOW-3-MIN-FAST-1-MIN.',
 'medium', 30),

(@quiz_exam, 'Quote the three conditions for propeller heater cycling.',
 'mcq',
 JSON_ARRAY(
   'PROP ON + SAT < 0°C + NP > 200 RPM',
   'PROP ON + TAT ≤ +5°C + NP > 400 RPM',
   'PROP ON + ICE DETECTED + any RPM',
   'PROP ON + IAS > 200 KIAS'
 ),
 JSON_OBJECT('correct_index', 1),
 'PROP ON + TAT ≤ +5°C + NP > 400 RPM. Mnemonic: TAT-5-NP-400.',
 'medium', 40),

(@quiz_exam, 'Boot pressure slumps below 15 PSI in a holding pattern at idle thrust. First action?',
 'mcq',
 JSON_ARRAY(
   'Switch BOOT AIR to ISO',
   'Advance POWER levers to raise NL',
   'Switch AIRFRAME MODE SELECT to MANUAL',
   'Disable engine intake heaters'
 ),
 JSON_OBJECT('correct_index', 1),
 'Low NL at idle starves bleed. Advance power levers — bleed pressure recovers. Mnemonic: NL-FOR-PRESSURE.',
 'medium', 50),

(@quiz_exam, 'DE-ICE TIMER caution illuminates in icing. Recovery action?',
 'mcq',
 JSON_ARRAY(
   'AIRFRAME MODE SELECT to FAST',
   'AIRFRAME MODE SELECT to MANUAL + AIRFRAME MANUAL SELECT 6-detent rotary, 24-sec dwell minimum',
   'BOOT AIR to ISO',
   'Disable propeller heaters'
 ),
 JSON_OBJECT('correct_index', 1),
 'TMU failed. MANUAL mode + 6-detent cycling. Hold each detent until both lights confirm. 24-sec dwell minimum before re-firing same pair.',
 'hard', 60),

(@quiz_exam, 'You enter visible icing. What is the FIRST switch action to inform the SPS?',
 'mcq',
 JSON_ARRAY(
   'AIRFRAME MODE SELECT to FAST',
   'PROP selector to ON',
   'REF SPEEDS to INCR',
   'BOOT AIR to ISO'
 ),
 JSON_OBJECT('correct_index', 2),
 'REF SPEEDS to INCR — SPS adjusts stall margin. [INCR REF SPEED] confirms. Mnemonic: INCR-FOR-ICING.',
 'medium', 70),

(@quiz_exam, 'TRUE or FALSE — Single IDP failure illuminates ICE DETECT FAIL.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. System is redundant. Both must fail.',
 'easy', 80),

(@quiz_exam, 'What is the PROP TEST sequence and cooldown?',
 'mcq',
 JSON_ARRAY(
   '3 sec each prop, 60 sec cooldown',
   '5 sec each prop separately, 30 sec cooldown before retest',
   '10 sec each prop, no cooldown',
   '15 sec each prop, 15 sec cooldown'
 ),
 JSON_OBJECT('correct_index', 1),
 '5 sec per prop separately; 30-sec cooldown to prevent element overheat. NP > 400 RPM and AC required.',
 'medium', 90),

(@quiz_exam, 'TRUE or FALSE — Boot air will not inflate the boots if the BLEED switch is OFF.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Boot air comes from each engine bleed port directly, independent of BLEED switch. Mnemonic: BLEED-INDEPENDENT.',
 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
