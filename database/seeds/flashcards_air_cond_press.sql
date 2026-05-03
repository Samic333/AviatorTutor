-- =============================================================================
-- AviatorTutor — Phase 2: ATA 21 Air Conditioning & Pressurization
-- 36 flashcards covering ECS architecture, pressurization scheduling,
-- limits, indications, abnormals, and Q400 quirks.
--
-- Idempotent: re-running wipes prior cards for this system and re-inserts.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'air-cond-press' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM flashcards WHERE system_id = @system_id;

INSERT INTO flashcards
    (system_id, front, back, hint, difficulty, tags)
VALUES
-- ----- ECS architecture (8) -----
(@system_id,
 'How many ACMs does the Q400 ECS have, and what heat exchangers do they use?',
 'Two ACMs sharing ONE primary heat exchanger and ONE secondary heat exchanger.',
 'Mnemonic: ACM-2-DUAL-HX.',
 'easy',
 JSON_ARRAY('air-cond-press','ecs','architecture')),

(@system_id,
 'Where are the Q400 ACMs and heat exchangers physically located?',
 'In the aft equipment bay (the unpressurised aft fuselage section).',
 NULL,
 'easy',
 JSON_ARRAY('air-cond-press','ecs','location')),

(@system_id,
 'List the four sources of conditioning air for the Q400 ECS.',
 'No.1 engine bleed, No.2 engine bleed, APU bleed, and the ECS Ground Air connection.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','ecs','sources')),

(@system_id,
 'Where is the ECS Ground Air connection located on the Q400?',
 'Right aft fuselage at fuselage station X 860.00. Latched door, 8-inch industry-standard fitting.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','ground-ops','location')),

(@system_id,
 'What prevents reverse flow through the ECS Ground Air connection when the cabin is pressurised?',
 'A flapper-style check valve at the junction with the air distribution system.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','ground-ops','safety')),

(@system_id,
 'Is APU bleed flow controlled by the BLEED selector knob (MIN/NORM/MAX)?',
 'NO. APU bleed flow is controlled by an internal ECU flow schedule, not by the BLEED selector.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','apu','ecs')),

(@system_id,
 'What is the approximate flow split between cabin and flight deck on the Q400?',
 'About 75% to cabin, 25% to flight deck. The right ECU digital channel handles cabin temp using half of left pack flow plus all of right pack flow; left channel handles flight-deck temp using half of left pack flow.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','ecs','distribution')),

(@system_id,
 'Where is the recirc filter located on the Q400?',
 'Behind the AFT class-C baggage compartment.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','recirc','location')),

-- ----- BLEED selector + pack control (5) -----
(@system_id,
 'What BLEED selector position is required for take-off on the Q400?',
 'MIN. It is the only legal selection. NORM/MAX trigger an amber BLEED indication on the ED.',
 'Mnemonic: MIN-FOR-TO.',
 'easy',
 JSON_ARRAY('air-cond-press','bleed','take-off')),

(@system_id,
 'On the ED, BLEED appears in white. What does that mean?',
 'BLEED switches are ON, MIN is selected, and a take-off rating is set. Normal takeoff configuration.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','indications')),

(@system_id,
 'On the ED, BLEED appears in amber. What does that mean?',
 'BLEED switches ON with NORM or MAX selected and NTOP set — incorrect take-off bleed setting (caution).',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','indications','take-off')),

(@system_id,
 'In single-pack operation, what flow rate does the Q400 deliver, and what speed does the recirc fan run at?',
 '70% of selected flow. Recirc fan runs at LOW speed.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','single-pack','operation')),

(@system_id,
 'In dual-pack operation, what flow rate runs and what recirc fan speed?',
 'Full performance based on flow selection and environmental conditions. Recirc fan runs at HIGH speed.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','dual-pack','operation')),

-- ----- FCSOV + ECU (3) -----
(@system_id,
 'If a single ECU digital channel fails, what does the pack inlet FCSOV default to?',
 'OPEN (pneumatic default). ECS continues to operate. The other digital channel takes control.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','ecu','failure-modes')),

(@system_id,
 'If BOTH ECU digital channels lose electrical power or fail, what does the FCSOV default to?',
 'CLOSED. ECS operation stops, ACMs shut off. Cabin must be ventilated using emergency ram-air.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','ecu','failure-modes','emergency')),

(@system_id,
 'What does the analog backup channel control?',
 'Only fully OPEN or fully CLOSED on the FCSOV. It does NOT modulate. Acts as a fallback if the digital channels lose function.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','ecu','backup')),

-- ----- Avionics + recirc (3) -----
(@system_id,
 'How many fans does the Q400 avionics cooling system have?',
 'Three: Pilot side (Fan 1), Copilot side (Fan 2), and Standby (Fan 3).',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','avionics-cooling')),

(@system_id,
 'When does the Q400 recirc fan start, and at what speed?',
 'When RECIRC is selected. Starts at LOW speed (to limit current inrush), then auto-switches to HIGH speed.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','recirc')),

(@system_id,
 'What is the avionics cooling system''s normal operation in terms of pilot action?',
 'Fully automatic — no pilot action required for normal or abnormal operation.',
 NULL,
 'easy',
 JSON_ARRAY('air-cond-press','avionics-cooling','automation')),

-- ----- Pressurization architecture (5) -----
(@system_id,
 'What are the three pressurization valves on the Q400?',
 'Aft outflow valve (primary), aft safety valve (backup, on aft pressure dome), forward safety valve (emergency, on forward pressure bulkhead).',
 NULL,
 'easy',
 JSON_ARRAY('air-cond-press','pressurization','valves')),

(@system_id,
 'When does the aft safety valve open on the ground?',
 'When at least one engine is running at idle OR the APU is operating.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','aft-safety-valve','ground-ops')),

(@system_id,
 'Can the forward safety valve be modulated?',
 'NO. The selector has only NORMAL or OPEN positions — it can ONLY be fully closed or fully opened.',
 'Mnemonic: FSV = ON-OFF.',
 'medium',
 JSON_ARRAY('air-cond-press','forward-safety-valve')),

(@system_id,
 'How is fine progressive pressure release achieved if the aft outflow is unserviceable?',
 'Use the FWD OUTFLOW knob on the CPC panel to bleed pressure progressively through the forward safety valve.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','manual-control','emergency')),

(@system_id,
 'Where is the FORWARD SAFETY VALVE selector located?',
 'On the copilot''s side console. Lift the safety guard to access. Two positions: NORMAL / OPEN.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','forward-safety-valve','location')),

-- ----- Limits + warnings (5) -----
(@system_id,
 'What is the maximum cabin-to-ambient differential pressure on the Q400?',
 '5.5 PSI.',
 'Mnemonic: 5-5-9-8.',
 'easy',
 JSON_ARRAY('air-cond-press','limits','differential')),

(@system_id,
 'At what cabin altitude does the CABIN PRESS warning light come on?',
 'Cabin altitude above 9,800 ft.',
 'Mnemonic: 5-5-9-8.',
 'easy',
 JSON_ARRAY('air-cond-press','limits','warnings')),

(@system_id,
 'What is the ground anti-suckback differential limit?',
 '0.5 PSI. External pressure cannot exceed internal cabin pressure by more than 0.5 PSI on the ground.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','limits','ground-ops')),

(@system_id,
 'What power-lever angle triggers the CPC to switch from ground mode to take-off mode?',
 '60 degrees. Below 60° = aft outflow fully OPEN (cabin = ambient). Above 60° = aft outflow modulates and pre-pressurisation begins.',
 NULL,
 'medium',
 JSON_ARRAY('air-cond-press','schedule','take-off')),

(@system_id,
 'How long does the CPC stay in take-off mode after lift-off, and why?',
 '10 minutes. Supports emergency return to the departure airport without having to re-select LDG ALT (valid only for take-off altitudes over 8,000 ft).',
 'Mnemonic: 10-MIN-RETURN.',
 'medium',
 JSON_ARRAY('air-cond-press','schedule','take-off')),

-- ----- Pre-pressurisation + manual mode (4) -----
(@system_id,
 'During the pre-pressurisation sequence, where is the cabin pressurised TO and AT WHAT RATE?',
 '400 ft below take-off altitude, at a rate of -300 fpm.',
 'Mnemonic: 400-AT-300.',
 'medium',
 JSON_ARRAY('air-cond-press','schedule','pre-pressurisation')),

(@system_id,
 'In manual mode, what does holding the AUTO-MAN-DUMP toggle to DECR do?',
 'Opens the aft outflow valve. Cabin pressure DECREASES. Cabin altitude INCREASES.',
 'Mnemonic: DECR = UP (cabin altitude up).',
 'hard',
 JSON_ARRAY('air-cond-press','manual','toggle')),

(@system_id,
 'In manual mode, what does holding the AUTO-MAN-DUMP toggle to INCR do?',
 'Closes the aft outflow valve. Cabin pressure INCREASES. Cabin altitude DECREASES.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','manual','toggle')),

(@system_id,
 'What three indicators must be monitored continuously when operating in MANUAL pressurisation mode?',
 'Cabin altitude, cabin DIFF PSI (differential pressure), and cabin rate of change.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','manual','monitoring')),

-- ----- Crew actions (3) -----
(@system_id,
 'You see CABIN PRESS warning at FL230. What are the memory items?',
 'Oxygen masks 100%, EMERGENCY DESCENT, transponder 7700, advise ATC, run QRH for secondary actions, divert to nearest suitable.',
 'Memory chant: Mask · 100% · Descend · 7700 · ATC.',
 'hard',
 JSON_ARRAY('air-cond-press','memory-items','warnings')),

(@system_id,
 'You see DIFF PSI dropping from 4.4 to 3.8 over ten minutes at FL220 with NO warning lights. Captain action?',
 'Descend NOW to FL150 or below, advise ATC, prepare cabin and crew, divert to nearest suitable. Treat the trend as the leading indicator — do not wait for the warning.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','captain-decisions','trends')),

(@system_id,
 'How is the cabin ventilated if both ECU digital channels fail and the pack FCSOVs close?',
 'Emergency ram-air ventilation — outside ram air admitted to ventilate the cabin and flight deck during unpressurised flight.',
 NULL,
 'hard',
 JSON_ARRAY('air-cond-press','emergency','ventilation'));

SELECT COUNT(*) AS flashcards_inserted FROM flashcards WHERE system_id = @system_id;
