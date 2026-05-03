-- =============================================================================
-- AviatorTutor — Phase 6: ATA 26 Fire Protection
-- Two quizzes: Practice (25 Q, no time limit, pass 70) + Type Rating Mock (10 Q, 12 min, pass 80)
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fire-protection' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

-- ----------------------------------------------------------------------------
-- Quiz 1 — Practice
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Fire Protection — Practice',
 'Twenty-five-question practice quiz covering APDs, smoke detectors, engine and baggage bottle architecture, lavatory protection, panel layout, and crew procedures. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many Advanced Pneumatic Detectors (APDs) are installed on the Q400?',
 'mcq',
 JSON_ARRAY('Six total','Seven total (six engine + one APU)','Four total (two per engine)','Eight total (six engine + one APU + one lavatory)'),
 JSON_OBJECT('correct_index', 1),
 'Seven APDs: 6 in the engine nacelles (3 per engine — PEZ, LEZ, MWW) + 1 in the APU. Easy trap: forgetting the APU.',
 'medium', 10),

(@quiz_practice, 'Which three engine zones have an APD each?',
 'mcq',
 JSON_ARRAY('Inlet, Compressor, Turbine','Primary Engine Zone, Leading Edge Zone, Main Wheel Well','Nacelle, Pylon, Wing Root','Combustion, Exhaust, Tail Pipe'),
 JSON_OBJECT('correct_index', 1),
 'PEZ + LEZ + MWW. Three APDs per nacelle.',
 'medium', 20),

(@quiz_practice, 'How does an APD detect a fire condition?',
 'mcq',
 JSON_ARRAY(
   'Optical sensor detects flame radiation',
   'Thermocouple measures temperature',
   'Helium-filled sensor tube — heat raises pressure, alarm switch closes',
   'Smoke ionisation chamber'
 ),
 JSON_OBJECT('correct_index', 2),
 'Helium-filled sensor tube. Heat → pressure rise → alarm switch closes. The integrity switch handles the fault path (rupture → pressure drop).',
 'medium', 30),

(@quiz_practice, 'What does the FAULT A or FAULT B (amber) light indicate?',
 'mcq',
 JSON_ARRAY(
   'A fire has been detected in zone A or B',
   'Loop detector circuit malfunction — APD rupture or wiring fault',
   'Bottle low pressure',
   'Test detection complete'
 ),
 JSON_OBJECT('correct_index', 1),
 'FAULT = loop circuit fault (integrity switch opens on pressure drop). NOT a fire indication.',
 'medium', 40),

(@quiz_practice, 'How many smoke detectors does the Q400 have?',
 'mcq',
 JSON_ARRAY('Three (one per zone)','Four (2 aft baggage + 1 fwd baggage + 1 lavatory)','Five','Six'),
 JSON_OBJECT('correct_index', 1),
 '4 total: 2 aft baggage + 1 fwd baggage + 1 lavatory. Mnemonic: 2-2-1-1-SMOKE.',
 'easy', 50),

(@quiz_practice, 'Where are the engine fire bottles physically installed?',
 'mcq',
 JSON_ARRAY(
   'One in each engine nacelle',
   'Both in the LEFT wing root, FWD and AFT positions',
   'Both in the right wing root',
   'In the centre fuselage near the APU'
 ),
 JSON_OBJECT('correct_index', 1),
 'Two dual-port bottles in the LEFT wing root. Each plumbed to both engines. Mnemonic: 2-BOTTLES-LEFT-WING.',
 'medium', 60),

(@quiz_practice, 'How many shots of fire suppressant are available per engine?',
 'mcq',
 JSON_ARRAY('One','Two','Three','Four'),
 JSON_OBJECT('correct_index', 1),
 'Two shots per engine — FWD bottle first, AFT bottle second if fire persists.',
 'easy', 70),

(@quiz_practice, 'What does PULLING the FUEL/HYD OFF T-handle accomplish?',
 'mcq',
 JSON_ARRAY(
   'Closes fuel valve only',
   'Closes hydraulic valve only',
   'Closes BOTH fuel and hydraulic shut-off valves AND arms the bottle squibs',
   'Discharges the FWD bottle'
 ),
 JSON_OBJECT('correct_index', 2),
 'PULL closes fuel + hydraulic valves and arms the bottle squibs. The EXTG switch then fires the chosen bottle.',
 'medium', 80),

(@quiz_practice, 'After discharging the FWD engine bottle on a fire, how long must you wait before the AFT bottle?',
 'mcq',
 JSON_ARRAY('5 seconds','10 seconds','30 seconds','60 seconds'),
 JSON_OBJECT('correct_index', 2),
 '30 seconds — suppressant needs time to soak the zone. Firing the second bottle too soon wastes the last shot.',
 'medium', 90),

(@quiz_practice, 'How many High-Rate Discharge (HRD) and Low-Rate Discharge (LRD) bottles serve the baggage compartments?',
 'mcq',
 JSON_ARRAY('1 HRD + 1 LRD','2 HRD + 1 shared LRD','2 HRD + 2 LRD','1 HRD + 2 LRD'),
 JSON_OBJECT('correct_index', 1),
 'Two HRD bottles (one per compartment) and ONE shared LRD bottle in the aft equipment bay.',
 'medium', 100),

(@quiz_practice, 'What happens when the AFT BAGGAGE SMOKE/EXTG switchlight is pushed?',
 'mcq',
 JSON_ARRAY(
   'AFT HRD and LRD discharge simultaneously',
   'AFT HRD discharges immediately; LRD discharges automatically 7 minutes later',
   'Only the LRD discharges; HRD is reserved for fwd baggage',
   'Vent valves open to clear smoke'
 ),
 JSON_OBJECT('correct_index', 1),
 'Aft baggage: HRD on push, LRD auto 7 min later. Mnemonic: HRD-AFT-7-LRD.',
 'medium', 110),

(@quiz_practice, 'What happens when the FWD BAGGAGE SMOKE/EXTG switchlight is pushed?',
 'mcq',
 JSON_ARRAY(
   'HRD only — LRD discharges 7 minutes later',
   'HRD AND LRD discharge SIMULTANEOUSLY',
   'Only the LRD discharges',
   'Vent valves close but no bottle discharges'
 ),
 JSON_OBJECT('correct_index', 1),
 'Forward baggage discharges HRD AND LRD simultaneously — no 7-minute delay. Mnemonic: FWD-BOTH-NOW.',
 'medium', 120),

(@quiz_practice, 'On an aft-baggage smoke alarm, what happens to the inlet and outlet vent valves?',
 'mcq',
 JSON_ARRAY(
   'They open to vent smoke overboard',
   'They close automatically — Control Amplifier drops their power',
   'They remain at their last commanded position',
   'They cycle open/closed every 30 seconds'
 ),
 JSON_OBJECT('correct_index', 1),
 'Vent valves close automatically on smoke alarm — INLT and OTLT CLOSED (white) lights illuminate. Starves the fire of airflow.',
 'hard', 130),

(@quiz_practice, 'Where is the lavatory fire bottle located, and how is it activated?',
 'mcq',
 JSON_ARRAY(
   'In the cabin overhead — cabin crew discharges manually',
   'In the waste-bin cabinet — thermally activated by fusible end-cap seals',
   'In the aft equipment bay — discharges automatically on smoke alarm',
   'Same plumbing as fwd baggage HRD bottle'
 ),
 JSON_OBJECT('correct_index', 1),
 'Inside the waste-bin cabinet. Thermally fused — seals melt at temperature, end caps blow off, dual outlets discharge. NO electrical interface. Mnemonic: POTTY-FUSE.',
 'hard', 140),

(@quiz_practice, 'TRUE or FALSE — Lavatory smoke is indicated on the flight-deck Fire Protection Panel.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Lavatory smoke = cabin only: cabin repeater lights, smoke detector LED, audible chime through P/A. NO flight-deck indication. Mnemonic: LAV-CABIN-ONLY.',
 'medium', 150),

(@quiz_practice, 'How many portable Halon 1211 extinguishers are on board?',
 'mcq',
 JSON_ARRAY('Two — one cockpit + one cabin','Three — one cockpit + two cabin','Four — one cockpit + three cabin','Five — two cockpit + three cabin'),
 JSON_OBJECT('correct_index', 2),
 '4 total — 1 cockpit + 3 cabin. Mnemonic: HALON-1-3-411.',
 'easy', 160),

(@quiz_practice, 'What types of fires is Halon 1211 effective on?',
 'mcq',
 JSON_ARRAY(
   'Class A only (combustible solids)',
   'Electrical, oil, and fuel fires',
   'Magnesium and titanium fires',
   'Class D metal fires only'
 ),
 JSON_OBJECT('correct_index', 1),
 'Halon 1211 is effective on electrical, oil, and fuel. Non-corrosive, non-toxic, will not freeze.',
 'medium', 170),

(@quiz_practice, 'On the portable extinguisher gauge, what does the YELLOW range indicate?',
 'mcq',
 JSON_ARRAY('Serviceable','Overcharge','Recharge required','Discharged completely'),
 JSON_OBJECT('correct_index', 1),
 'GREEN = serviceable. YELLOW = overcharge. RED = recharge required.',
 'medium', 180),

(@quiz_practice, 'What MUST the crew do before discharging the cockpit Halon 1211 portable?',
 'mcq',
 JSON_ARRAY(
   'Open a window to vent smoke',
   'All crew don oxygen masks and select EMERGENCY (100% O2 positive pressure)',
   'Turn off cabin-pressurisation packs',
   'Switch off all avionics'
 ),
 JSON_OBJECT('correct_index', 1),
 'Cockpit discharge requires oxygen masks at EMERGENCY (100% O2 positive pressure). Halon displaces oxygen.',
 'hard', 190),

(@quiz_practice, 'On a real engine fire, what is the FIRST action after the lights start flashing?',
 'mcq',
 JSON_ARRAY(
   'Pull the affected T-handle immediately',
   'Press an ENGINE FIRE light to silence the tone — both lights stop flashing, stay on steady',
   'Discharge the FWD bottle without delay',
   'Reduce thrust on the affected engine'
 ),
 JSON_OBJECT('correct_index', 1),
 'AVIATE first. Press an ENGINE FIRE light to silence the tone before any other action. Then memory items.',
 'hard', 200),

(@quiz_practice, 'TRUE or FALSE — Loss of the Control Amplifier causes complete loss of engine fire detection.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Loss of the Control Amplifier will NOT cause complete loss of engine detection or extinguishing capability — the system has redundancy in the alarm path.',
 'hard', 210),

(@quiz_practice, 'TRUE or FALSE — The lavatory smoke detector self-test is performed from the flight deck.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. The self-test switch is on the smoke detector in the lavatory; cabin crew operate it. Confirms cabin repeater lights, single chime through P/A, red LED on detector.',
 'medium', 220),

(@quiz_practice, 'TRUE or FALSE — When you pull the engine T-handle, the bottle automatically discharges.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Pulling the T-handle ARMS the bottle squibs and closes fuel/hyd valves. The EXTG switch (FWD or AFT) is what actually fires the bottle.',
 'medium', 230),

(@quiz_practice, 'In flight detection logic: APD in PEZ FAILS and APD in LEZ DETECTS FIRE. What does the panel show?',
 'mcq',
 JSON_ARRAY(
   'Fault only — no fire indication',
   'Fire only — no fault indication',
   'BOTH a fault and a fire indication',
   'Nothing — both detectors must agree'
 ),
 JSON_OBJECT('correct_index', 2),
 'Series logic: PEZ fault + LEZ fire → BOTH fault AND fire indications. The healthy LEZ still passes the fire signal.',
 'hard', 240),

(@quiz_practice, 'TRUE or FALSE — On a baggage compartment smoke alarm, the affected vent valves remain open until the crew closes them manually.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. The Control Amplifier drops power to the inlet and outlet vent valves automatically — they close without crew action. INLT and OTLT CLOSED illuminate.',
 'medium', 250);

-- ----------------------------------------------------------------------------
-- Quiz 2 — Type Rating Mock
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Fire Protection — Type Rating Mock',
 'Ten-question mock at type-rating oral standard. Twelve-minute timer, pass score 80%. Designed to expose holes BEFORE the check ride.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'Quote the total APD count and its breakdown.',
 'mcq',
 JSON_ARRAY('6 — three per engine','7 — six engine + one APU','7 — three per engine + one lavatory','8 — three per engine + one APU + one lavatory'),
 JSON_OBJECT('correct_index', 1),
 'Seven APDs: 6 engine (3 per engine: PEZ, LEZ, MWW) + 1 APU.',
 'medium', 10),

(@quiz_exam, 'Quote the smoke-detector count and locations.',
 'mcq',
 JSON_ARRAY('3 — one per zone','4 — 2 aft baggage + 1 fwd + 1 lavatory','5 — 2 aft + 2 fwd + 1 lavatory','2 — one per baggage compartment'),
 JSON_OBJECT('correct_index', 1),
 'Four: 2 aft baggage (front and rear of compartment) + 1 fwd baggage + 1 lavatory.',
 'medium', 20),

(@quiz_exam, 'Engine fire on climb-out at FL120. PWR reduced, prop feathered, condition lever fuel off, T-handle pulled, FWD bottle discharged. After 30 sec the FIRE light remains on. Correct next action?',
 'mcq',
 JSON_ARRAY(
   'Wait another 30 seconds; bottle may still be working',
   'Select EXTG switch AFT BTL — discharge the second bottle',
   'Pull the No.2 engine T-handle as a precaution',
   'Land immediately gear-up'
 ),
 JSON_OBJECT('correct_index', 1),
 'Two shots per engine. After first bottle and 30-sec wait with fire still illuminated → OTHER bottle position on EXTG switch.',
 'hard', 30),

(@quiz_exam, 'Differentiate the AFT vs FWD baggage discharge logic.',
 'mcq',
 JSON_ARRAY(
   'Both compartments discharge HRD + LRD simultaneously',
   'AFT: HRD now, LRD 7 min later. FWD: HRD + LRD simultaneously',
   'AFT: HRD only. FWD: LRD only',
   'AFT: LRD only. FWD: HRD only'
 ),
 JSON_OBJECT('correct_index', 1),
 'AFT delays the LRD by 7 minutes. FWD discharges both bottles at once. Mnemonics: HRD-AFT-7-LRD vs FWD-BOTH-NOW.',
 'hard', 40),

(@quiz_exam, 'Lavatory fire activation method.',
 'mcq',
 JSON_ARRAY(
   'Manual cockpit switch on Fire Protection Panel',
   'Cabin crew presses a discharge button',
   'Thermally fused — fusible end-cap seals melt at temperature, dual outlets discharge automatically',
   'Pneumatic activation tied to cabin pressurisation'
 ),
 JSON_OBJECT('correct_index', 2),
 'Lavatory Potty Bottle is thermally activated. End caps fuse, blow off, extinguishant discharges. NO electrical interface.',
 'hard', 50),

(@quiz_exam, 'TRUE or FALSE — Lavatory smoke produces a flight-deck panel indication.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Cabin only: repeater lights, smoke detector LED, P/A chime. No flight-deck indication.',
 'medium', 60),

(@quiz_exam, 'Cockpit Halon 1211 portable use — what is non-negotiable BEFORE discharge?',
 'mcq',
 JSON_ARRAY(
   'Vent the cockpit by opening a side window',
   'Don oxygen masks and select EMERGENCY (100% O2 positive pressure)',
   'Notify ATC and request descent',
   'Disengage the autopilot'
 ),
 JSON_OBJECT('correct_index', 1),
 'Halon displaces oxygen. Mask EMERGENCY 100% positive pressure first; discharge second.',
 'hard', 70),

(@quiz_exam, 'How many shots of fire suppressant are available PER ENGINE?',
 'mcq',
 JSON_ARRAY('One','Two','Three','Four'),
 JSON_OBJECT('correct_index', 1),
 'Two — one from the FWD bottle and one from the AFT bottle, both in the LEFT wing root.',
 'easy', 80),

(@quiz_exam, 'A FAULT A or B amber light WITHOUT a corresponding ENGINE FIRE indication means:',
 'mcq',
 JSON_ARRAY(
   'A fire is impending — pull the T-handle as a precaution',
   'Loop detector circuit malfunction (APD or wiring fault) — no actual fire',
   'A successful TEST DETECTION cycle just completed',
   'Bottle pressure low'
 ),
 JSON_OBJECT('correct_index', 1),
 'FAULT = loop fault only. Run the QRH non-normal, defer per MEL. Do NOT pull the T-handle.',
 'medium', 90),

(@quiz_exam, 'Engine fire detection logic: APD in LEZ FAILS (fault) and APD in PEZ DETECTS FIRE. What does the panel show?',
 'mcq',
 JSON_ARRAY(
   'Both fault and fire',
   'Fire only',
   'Fault only — NO fire indication',
   'Nothing — both must agree'
 ),
 JSON_OBJECT('correct_index', 2),
 'Series wiring: LEZ failure breaks the path so PEZ fire cannot signal. Panel shows fault only — a known limitation of the loop logic.',
 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
