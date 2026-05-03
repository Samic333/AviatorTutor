-- =============================================================================
-- AviatorTutor — Phase 2: ATA 21 Air Conditioning & Pressurization
-- Two quizzes:
--   1) "Air Cond & Press — Practice"        : 25 questions, no time limit
--   2) "Air Cond & Press — Type Rating Mock" : 10 questions, 12 min, pass 80%
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'air-cond-press' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

-- ----------------------------------------------------------------------------
-- Quiz 1 — Practice
-- ----------------------------------------------------------------------------
INSERT INTO quizzes
    (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES
    (@system_id,
     'Air Cond & Press — Practice',
     'Twenty-five-question practice quiz covering ECS architecture, air sources, BLEED selector logic, FCSOV and ECU failure modes, recirc and avionics cooling, pressurization scheduling, limits, indications, and abnormals. No time limit.',
     'practice',
     NULL,
     70,
     1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions
    (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order)
VALUES
(@quiz_practice,
 'How many air-cycle machines (ACMs) and how many heat exchangers does the Q400 ECS use?',
 'mcq',
 JSON_ARRAY('One ACM, one primary + one secondary heat exchanger',
            'Two ACMs, two complete sets of heat exchangers',
            'Two ACMs sharing one primary + one secondary heat exchanger',
            'Two ACMs and a single combined heat exchanger'),
 JSON_OBJECT('correct_index', 2),
 'TWO ACMs share ONE primary + ONE secondary heat exchanger. The shared dual-heat-exchanger design saves weight and gives a larger heat-rejection surface than two separate small ones.',
 'easy', 10),

(@quiz_practice,
 'Where are the Q400 ACMs and shared heat exchangers physically located?',
 'mcq',
 JSON_ARRAY('Forward equipment bay (under the cockpit)',
            'Wing root, between the engine nacelles',
            'Aft equipment bay (the unpressurised aft fuselage section)',
            'Tailcone above the elevators'),
 JSON_OBJECT('correct_index', 2),
 'Both ACMs and the shared heat exchangers live in the aft equipment bay. That section is unpressurised — a pack failure or smoke event vents overboard rather than into the cabin.',
 'easy', 20),

(@quiz_practice,
 'Which BLEED selector position is permitted for take-off on the Q400?',
 'mcq',
 JSON_ARRAY('NORM only',
            'MAX for shorter runways, NORM for longer',
            'MIN only — NORM and MAX trigger amber BLEED indication',
            'Any position; the BLEED selector is independent of takeoff'),
 JSON_OBJECT('correct_index', 2),
 'MIN is the only legal take-off setting. The ED shows BLEED amber if NORM or MAX is selected with a take-off rating set.',
 'easy', 30),

(@quiz_practice,
 'Where is the ECS Ground Air connection located on the Q400?',
 'mcq',
 JSON_ARRAY('Left forward fuselage at station X 200',
            'Right aft fuselage at station X 860.00',
            'Underside of the wing root',
            'Forward of the nose-wheel well'),
 JSON_OBJECT('correct_index', 1),
 'The connection is on the right aft fuselage at fuselage station X 860.00. Latched door, 8-inch industry-standard fitting, with a flapper check valve at the distribution-system junction to prevent reverse flow when pressurised.',
 'medium', 40),

(@quiz_practice,
 'Single ECU digital channel fails in flight. What does the pack inlet FCSOV default to, and what is the consequence?',
 'mcq',
 JSON_ARRAY('Defaults CLOSED; ECS stops; ram air ventilation',
            'Defaults OPEN pneumatically; ECS continues; other digital channel takes control',
            'Stays at last position; minor flow change',
            'Cycles between open and closed every 30 seconds'),
 JSON_OBJECT('correct_index', 1),
 'Single channel failure → FCSOV defaults OPEN. ECS continues. The remaining digital channel takes control of the FCSOV.',
 'medium', 50),

(@quiz_practice,
 'BOTH ECU digital channels lose electrical power or fail. What does the FCSOV default to?',
 'mcq',
 JSON_ARRAY('Defaults OPEN pneumatically',
            'Defaults CLOSED — ECS stops, emergency ram-air ventilation needed',
            'Stays at last commanded position',
            'Defaults to 50% open'),
 JSON_OBJECT('correct_index', 1),
 'DUAL channel failure → FCSOV defaults CLOSED. The ACMs shut off, conditioned-air flow ends, and the cabin must be ventilated using emergency ram air. Descend and divert.',
 'hard', 60),

(@quiz_practice,
 'In single-pack operation, what flow rate runs and what speed is the recirc fan?',
 'mcq',
 JSON_ARRAY('100% flow, recirc fan high speed',
            '70% of selected flow, recirc fan low speed',
            '50% flow, recirc fan off',
            '85% flow, recirc fan medium'),
 JSON_OBJECT('correct_index', 1),
 'Single-pack mode: 70% flow, recirc fan low. Dual-pack mode: full performance, recirc fan high.',
 'medium', 70),

(@quiz_practice,
 'When does the Q400 recirc fan start, and at what speed initially?',
 'mcq',
 JSON_ARRAY('Always at high speed when RECIRC is selected',
            'Starts at low speed (limit current inrush) then auto-switches to high',
            'Only runs when both engines are running',
            'Manually selectable speed'),
 JSON_OBJECT('correct_index', 1),
 'Recirc fan starts at LOW speed to limit current inrush, then auto-switches to HIGH speed. Speed adapts to operating conditions.',
 'medium', 80),

(@quiz_practice,
 'How many fans does the Q400 avionics cooling system have, and what do they cool?',
 'mcq',
 JSON_ARRAY('Two fans cooling avionics rack only',
            'Three fans (Pilot, Copilot, Standby) — extraction-type cooling for avionics rack, five LCD displays, and wardrobe rack',
            'Four fans, two per side',
            'One central fan with redundant motor'),
 JSON_OBJECT('correct_index', 1),
 'Three fans (Fan 1 Pilot side, Fan 2 Copilot side, Fan 3 Standby). Extraction-type loop. Cools avionics rack, five LCDs in instrument panel, and wardrobe rack. Fully automatic.',
 'hard', 90),

(@quiz_practice,
 'Name the three pressurization valves on the Q400.',
 'mcq',
 JSON_ARRAY('Forward outflow, mid outflow, aft outflow',
            'Aft outflow valve (primary), aft safety valve (backup), forward safety valve (emergency)',
            'Two outflow valves and one safety valve',
            'Single outflow with double-redundancy actuators'),
 JSON_OBJECT('correct_index', 1),
 'The aft outflow valve is primary. The aft safety valve (also on aft pressure dome) is backup. The forward safety valve (on forward pressure bulkhead) is emergency-only. Three valves total.',
 'easy', 100),

(@quiz_practice,
 'Can the forward safety valve be modulated by its selector?',
 'mcq',
 JSON_ARRAY('Yes — selector has continuous adjustment',
            'No — selector has only NORMAL or OPEN positions; cannot modulate',
            'Yes — but only between 0 and 50% open',
            'Only the FWD OUTFLOW knob can modulate it; selector is open/closed only'),
 JSON_OBJECT('correct_index', 1),
 'The forward safety valve selector has ONLY NORMAL or OPEN. It cannot be modulated by the selector. For progressive bleed, use the FWD OUTFLOW knob on the CPC panel.',
 'medium', 110),

(@quiz_practice,
 'What is the maximum cabin-to-ambient differential pressure on the Q400?',
 'mcq',
 JSON_ARRAY('4.5 PSI', '5.5 PSI', '7.5 PSI', '8.6 PSI'),
 JSON_OBJECT('correct_index', 1),
 'Maximum differential is 5.5 PSI. The 8.6 figure is from larger jets (737 family) — do not confuse.',
 'easy', 120),

(@quiz_practice,
 'At what cabin altitude does the CABIN PRESS warning light come on?',
 'mcq',
 JSON_ARRAY('8,500 ft', '9,800 ft', '10,000 ft', '12,000 ft'),
 JSON_OBJECT('correct_index', 1),
 'CABIN PRESS warning fires at cabin altitude > 9,800 ft. Memory item event when seen.',
 'easy', 130),

(@quiz_practice,
 'What is the power-lever angle threshold that switches the Q400 from ground (outflow open) to take-off mode (outflow modulating)?',
 'mcq',
 JSON_ARRAY('30°', '45°', '60°', '75°'),
 JSON_OBJECT('correct_index', 2),
 'Above 60° power-lever angle, the CPC commands aft outflow to modulate and pre-pressurisation begins. Below 60°, aft outflow is fully open.',
 'medium', 140),

(@quiz_practice,
 'During pre-pressurisation, the cabin is pressurised to what altitude and at what rate?',
 'mcq',
 JSON_ARRAY('400 ft above take-off altitude at +300 fpm',
            '400 ft below take-off altitude at -300 fpm',
            '1,000 ft below TO altitude at -200 fpm',
            'LDG ALT setting at -100 fpm'),
 JSON_OBJECT('correct_index', 1),
 'Cabin pre-pressurises to 400 ft BELOW take-off altitude at -300 fpm. Done deliberately to avoid a cabin pressure "bump" at lift-off.',
 'medium', 150),

(@quiz_practice,
 'How long does the CPC stay in take-off mode after lift-off, and why?',
 'mcq',
 JSON_ARRAY('5 minutes — to confirm a clean climb',
            '10 minutes — to support emergency return without re-selecting LDG ALT',
            '20 minutes — until top of climb',
            'Until 5,000 ft AGL'),
 JSON_OBJECT('correct_index', 1),
 'CPC stays in take-off mode for 10 minutes after lift-off. Lets you fly an emergency return without re-setting LDG ALT (valid only for take-off altitudes over 8,000 ft).',
 'hard', 160),

(@quiz_practice,
 'In manual mode, holding the AUTO-MAN-DUMP toggle to DECR causes what?',
 'mcq',
 JSON_ARRAY('Closes outflow; cabin pressure increases; cabin altitude decreases',
            'Opens outflow; cabin pressure decreases; cabin altitude increases',
            'Opens both safety valves',
            'Resets to AUTO mode'),
 JSON_OBJECT('correct_index', 1),
 'DECR opens the aft outflow valve. Cabin pressure decreases, cabin altitude increases. INCR is the opposite (close outflow, cabin pressure increases, cabin altitude decreases).',
 'hard', 170),

(@quiz_practice,
 'What is the ground anti-suckback differential limit on the Q400?',
 'mcq',
 JSON_ARRAY('0.1 PSI', '0.5 PSI', '1.0 PSI', '2.0 PSI'),
 JSON_OBJECT('correct_index', 1),
 'External pressure cannot exceed internal cabin pressure by more than 0.5 PSI on the ground. Anti-suckback feature.',
 'hard', 180),

(@quiz_practice,
 'When does the aft safety valve open on the ground?',
 'mcq',
 JSON_ARRAY('Always when on the ground',
            'Only when both engines are at idle',
            'When at least one engine is running at idle, OR the APU is operating',
            'Only when commanded by the crew'),
 JSON_OBJECT('correct_index', 2),
 'Aft safety valve opens on the ground when at least one engine is at idle OR the APU is running. Backup release while ground-pressurisation is active.',
 'medium', 190),

(@quiz_practice,
 'TRUE or FALSE — APU bleed flow is controlled by the BLEED selector knob (MIN/NORM/MAX).',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. APU bleed follows an internal ECU flow schedule, not the BLEED selector knob. Engine bleed flow IS controlled by the selector.',
 'medium', 200),

(@quiz_practice,
 'TRUE or FALSE — The Q400 has two complete and independent ECS packs, each with its own primary and secondary heat exchanger.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. There are two ACMs but they SHARE one primary + one secondary heat exchanger. Two-machines-one-shared-exchanger is the Q400 design.',
 'medium', 210),

(@quiz_practice,
 'TRUE or FALSE — The forward safety valve can be set to a partial open position via its selector.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. The selector has only NORMAL (closed) or OPEN (fully open). For modulated control, use the FWD OUTFLOW knob on the CPC panel.',
 'medium', 220),

(@quiz_practice,
 'TRUE or FALSE — When operating in MANUAL pressurisation mode, the cabin altitude, DIFF PSI, and rate-of-change indicators must be monitored continuously.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 0),
 'TRUE. Manual mode hands the schedule responsibility to the crew. Continuous monitoring of all three indicators is required.',
 'medium', 230),

(@quiz_practice,
 'TRUE or FALSE — Avionics cooling on the Q400 requires pilot action under abnormal conditions.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Avionics cooling control is fully automatic and requires NO pilot action — even under abnormal conditions.',
 'medium', 240),

(@quiz_practice,
 'TRUE or FALSE — The Q400 recirc filter is located in the forward equipment bay.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. The recirc filter is mounted behind the AFT class-C baggage compartment.',
 'easy', 250);

-- ----------------------------------------------------------------------------
-- Quiz 2 — Type Rating Mock
-- ----------------------------------------------------------------------------
INSERT INTO quizzes
    (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES
    (@system_id,
     'Air Cond & Press — Type Rating Mock',
     'Ten-question mock at type-rating oral standard. Twelve-minute timer, pass score 80%.',
     'exam',
     12,
     80,
     1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions
    (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order)
VALUES
(@quiz_exam,
 'Describe the Q400 ECS architecture in one sentence.',
 'mcq',
 JSON_ARRAY(
   'One ACM with one primary and one secondary heat exchanger',
   'Two ACMs each with their own primary and secondary heat exchangers',
   'Two ACMs sharing one primary and one secondary heat exchanger, in the aft equipment bay',
   'Three ACMs with shared cooling'
 ),
 JSON_OBJECT('correct_index', 2),
 'Two ACMs sharing one primary + one secondary heat exchanger. Located in the aft equipment bay (unpressurised). Mnemonic: ACM-2-DUAL-HX.',
 'medium', 10),

(@quiz_exam,
 'List the four conditioning-air sources for the Q400 ECS.',
 'mcq',
 JSON_ARRAY(
   'No.1 bleed and No.2 bleed only',
   'No.1 bleed, No.2 bleed, APU, and ECS Ground Air connection',
   'No.1 bleed, No.2 bleed, and APU',
   'Engines, APU, ram air, and electric heater'
 ),
 JSON_OBJECT('correct_index', 1),
 'Three normal sources (No.1 bleed, No.2 bleed, APU) plus the ECS Ground Air connection at fuselage station X 860.00 right side.',
 'medium', 20),

(@quiz_exam,
 'BLEED selector position required for take-off, and the ED indication if a different position is set.',
 'mcq',
 JSON_ARRAY(
   'NORM required; MIN/MAX trigger amber BLEED',
   'MIN required; NORM/MAX trigger amber BLEED on ED',
   'Any position; no ED indication',
   'MAX required for short runways'
 ),
 JSON_OBJECT('correct_index', 1),
 'MIN is the only legal take-off setting. NORM/MAX with NTOP set: ED shows BLEED amber. Mnemonic: MIN-FOR-TO.',
 'easy', 30),

(@quiz_exam,
 'Single ECU digital channel fails in flight. Pack inlet FCSOV behaviour?',
 'mcq',
 JSON_ARRAY(
   'Defaults CLOSED, ECS stops',
   'Defaults OPEN pneumatically, ECS continues, other digital channel takes control',
   'Stays at last position',
   'Cycles automatically'
 ),
 JSON_OBJECT('correct_index', 1),
 'Single channel = OPEN default, continued ops. Dual channel = CLOSED default, ECS stops, emergency ram air.',
 'medium', 40),

(@quiz_exam,
 'Maximum cabin-to-ambient differential pressure and the cabin altitude warning trip.',
 'mcq',
 JSON_ARRAY(
   '8.6 PSI max; warning at 10,000 ft',
   '5.5 PSI max; warning at 9,800 ft',
   '7.5 PSI max; warning at 9,000 ft',
   '4.5 PSI max; warning at 8,500 ft'
 ),
 JSON_OBJECT('correct_index', 1),
 '5.5 PSI max differential. CABIN PRESS warning at 9,800 ft cabin altitude. Mnemonic: 5-5-9-8.',
 'easy', 50),

(@quiz_exam,
 'Pre-pressurisation parameters on take-off?',
 'mcq',
 JSON_ARRAY(
   '400 ft above TO altitude at +300 fpm',
   '400 ft below TO altitude at -300 fpm',
   '1,000 ft below TO altitude at -500 fpm',
   'LDG ALT at -200 fpm'
 ),
 JSON_OBJECT('correct_index', 1),
 'Pre-pressurise to 400 ft BELOW take-off altitude at -300 fpm. Smooth lift-off, no cabin "bump." Mnemonic: 400-AT-300.',
 'medium', 60),

(@quiz_exam,
 'How long does the CPC stay in take-off mode after lift-off, and what does it support?',
 'mcq',
 JSON_ARRAY(
   '5 minutes; clean-up checks',
   '10 minutes; emergency return without re-selecting LDG ALT',
   '20 minutes; top-of-climb mode',
   'Until 5,000 ft AGL'
 ),
 JSON_OBJECT('correct_index', 1),
 'Ten minutes after lift-off the CPC stays in TO mode. Supports emergency return to departure airport without re-selecting LDG ALT.',
 'hard', 70),

(@quiz_exam,
 'In MANUAL pressurisation mode, AUTO-MAN-DUMP toggle to DECR causes what?',
 'mcq',
 JSON_ARRAY(
   'Closes outflow; cabin pressure increases; cabin altitude decreases',
   'Opens outflow; cabin pressure decreases; cabin altitude increases',
   'No effect; manual mode is one-way',
   'Resets to AUTO'
 ),
 JSON_OBJECT('correct_index', 1),
 'DECR opens outflow → cabin pressure DECREASES → cabin altitude INCREASES. Mnemonic: DECR = UP (cabin altitude up).',
 'hard', 80),

(@quiz_exam,
 'Forward safety valve modulation capability via its selector?',
 'mcq',
 JSON_ARRAY(
   'Continuous modulation 0% to 100%',
   'Three positions: closed, half, full',
   'NORMAL or OPEN only — cannot be modulated; for fine control use FWD OUTFLOW knob',
   'Modulated only via the autopilot'
 ),
 JSON_OBJECT('correct_index', 2),
 'FSV selector = NORMAL or OPEN only. Cannot modulate. For progressive bleed, use the FWD OUTFLOW knob on the CPC panel. Mnemonic: FSV = ON-OFF.',
 'medium', 90),

(@quiz_exam,
 'CABIN PRESS warning light at FL230. List the memory items.',
 'mcq',
 JSON_ARRAY(
   'Continue at altitude and run QRH',
   'Oxygen masks 100%, EMERGENCY DESCENT, transponder 7700, advise ATC, run QRH',
   'Reduce thrust and brief cabin',
   'Switch to MAN mode and adjust outflow'
 ),
 JSON_OBJECT('correct_index', 1),
 'Memory items: masks 100%, emergency descent, transponder 7700, advise ATC, run QRH for secondary actions, divert nearest suitable. Memory chant: Mask · 100% · Descend · 7700 · ATC.',
 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN
        (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
