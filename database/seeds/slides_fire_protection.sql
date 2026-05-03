-- =============================================================================
-- AviatorTutor — Phase 6: ATA 26 Fire Protection — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fire-protection' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fire-protection-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Why Fire Protection Is the Captain''s System',
 'Of every system on the Q400, fire protection is the one you must run from memory at 200 knots and 5000 feet. There is no time to read. The detection logic is pneumatic and clever, the panel is busy and unforgiving, and the actions — flash, press, pull, EXTG — must come out in order without thinking. This lesson walks you through each protected zone, the panel layout, the bottle architecture, and the procedures. By the end of it the chant should write itself: PULL arms the bottles, EXTG fires them, wait 30 seconds, the other bottle if it persists.',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Q400 fire protection system overview',
 'Fire protection = detect · indicate · extinguish. Captain''s memory drill, every flight.',
 'A check captain''s favourite ride is an engine fire on rotation. Drill the chant before every sector and you will run it correctly when it happens.',
 NULL),

(@lesson_id, 20, 'concept',
 'Three Things the System Does',
 'The Q400 fire protection system has three jobs and only three. <strong>Detection</strong> — APDs in the engines and APU, smoke detectors in the baggage compartments and lavatory. <strong>Indication</strong> — Fire Protection Panel on the centre overhead, plus CHECK FIRE DET on the C&W panel and an optional fire tone. <strong>Extinguishing</strong> — two bottles in the left wing root for engines, two HRD bottles plus one shared LRD for baggage, and a thermally-activated Potty Bottle for the lavatory. Detection and indication are continuously active; extinguishing is on demand for engines and baggage, automatic for the lavatory.',
 'diagram', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Detection · indication · extinguishing block diagram',
 'D · I · E. Detect, indicate, extinguish — three jobs only.',
 'When you brief the FO, name the three roles in that order. It builds the mental model of where to look first when a caution appears.',
 NULL),

(@lesson_id, 30, 'concept',
 'APDs — The Pneumatic Detection Magic',
 'Advanced Pneumatic Detectors (APDs) are sensor tubes filled with helium gas. Heat the tube and the helium expands; pressure rises and an alarm switch closes — that is your fire signal. Cut the tube and the helium escapes; pressure drops and an integrity switch opens — that is your fault signal. Two switches, two distinct meanings, one elegant detector. Six APDs live in the engine nacelles (three per engine: PEZ, LEZ, MWW), one in the APU. Seven detectors total. The Control Amplifier reads all of them and drives the panel. Fault and alarm are intentionally on different switches so the system can tell you "loop is broken" vs "something is hot."',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'APD sensor tube + integrity and alarm switches',
 '7-APDs total. Pressure UP = fire. Pressure DOWN = fault. Two switches, two meanings.',
 'When you see FAULT A or B without a fire light, the loop is damaged but the airplane is not on fire. Do not pull anything — run the QRH.',
 JSON_OBJECT(
   'prompt', 'How many Advanced Pneumatic Detectors (APDs) are installed on a Q400, and where?',
   'options', JSON_ARRAY(
     'Six total — three in each engine nacelle',
     'Seven total — six in the engine nacelles (three per engine) and one in the APU',
     'Eight total — six engine + one APU + one lavatory',
     'Four total — two per engine'
   ),
   'correct_index', 1,
   'explanation', 'Seven APDs: three per engine in the Primary Engine Zone (PEZ), Leading Edge Zone (LEZ), and Main Wheel Well (MWW), plus one in the APU. Mnemonic: 7-APDs.'
 )),

(@lesson_id, 40, 'system',
 'The Fire Protection Panel — Centre Overhead',
 'The Fire Protection Panel sits on the centre overhead and is laid out left-to-right around the two big red T-handles labelled ENGINE 1 and ENGINE 2 PULL FUEL/HYD OFF. Above each T-handle: FAULT A and FAULT B amber lights, BOTTLE ARMING amber lights, the EXTG switch (FWD BTL / OFF / AFT BTL), and HYD and FUEL SHUT-OFF VALVE position lights (OPEN green / CLOSED white). Below the T-handles: BTL LOW amber, FIRE TEST switch, and TEST DETECTION switch (ENGINE 1 / ENGINE 2, spring-loaded centre off). Far right: BAGGAGE compartment section with FWD and AFT SMOKE/EXTG switchlights, ARM and LOW segments, INLT and OTLT valve CLOSED indications. Memorise the panel by zone: engines on the left, baggage on the right.',
 'diagram', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Fire Protection Panel — centre overhead layout',
 'Engines LEFT, baggage RIGHT. Two T-handles, two EXTG switches, two SMOKE/EXTG switchlights.',
 'On the walkaround mental rehearsal, run your eye over the panel from left to right and name each control aloud. The panel becomes muscle memory.',
 NULL),

(@lesson_id, 50, 'system',
 'Engine Fire Bottles — Two Shots Each',
 'Two dual-port FIRE bottles are installed in the LEFT wing root, in the FWD and AFT positions. Each bottle has TWO discharge ports — one plumbed to engine 1 nacelle, one to engine 2. Result: each engine has access to two shots of suppressant. The FWD bottle is your first shot; if the fire persists, the AFT bottle is your second shot, into the same engine. The Control Amplifier monitors bottle pressure constantly via a pressure switch — pressure low and the BTL LOW amber illuminates. Discharge plumbing routes suppressant into all three engine zones simultaneously: PEZ, LEZ, MWW.',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Two dual-port engine fire bottles in left wing root',
 '2-BOTTLES-LEFT-WING. Two shots per engine. PEZ + LEZ + MWW dose simultaneously.',
 'On a stubborn engine fire, do not panic between the first and second shot — wait the full 30 seconds. Suppressant needs time to soak into the zone before you can decide if it has worked.',
 NULL),

(@lesson_id, 60, 'system',
 'Baggage Compartment — HRD + LRD Logic',
 'Baggage protection is more interesting than people remember. Each compartment has its own High-Rate Discharge (HRD) bottle. There is also a single shared Low-Rate Discharge (LRD) bottle in the aft equipment bay. AFT baggage smoke triggers the AFT HRD on switchlight push — the LRD follows AUTOMATICALLY 7 MINUTES LATER. FWD baggage smoke triggers the FWD HRD AND the LRD SIMULTANEOUSLY on switchlight push. The LRD low-pressure light is SHARED — it indicates the side currently active. While the aft compartment is being suppressed the LRD low light shows the aft. The forward, the forward. Read the panel.',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'HRD and LRD bottle logic — aft 7-min delay vs fwd simultaneous',
 'HRD-AFT-7-LRD. FWD-BOTH-NOW. Aft delays 7 min; fwd discharges both at once.',
 'A 7-minute delay sounds long. It exists because aft baggage is the bigger volume — the HRD knocks down the fire, and the slow LRD soak prevents reignition. Do not try to dump the LRD manually; let the system run.',
 JSON_OBJECT(
   'prompt', 'You push the AFT BAGGAGE SMOKE/EXTG switchlight after a smoke alarm. What happens, and when?',
   'options', JSON_ARRAY(
     'AFT HRD and LRD discharge simultaneously',
     'AFT HRD discharges immediately; LRD discharges automatically 7 minutes later',
     'AFT HRD discharges; LRD must be manually triggered separately',
     'Only the LRD discharges; HRD is reserved for fwd baggage'
   ),
   'correct_index', 1,
   'explanation', 'Aft baggage: HRD on switchlight push immediately, LRD automatically 7 minutes later. Forward baggage discharges HRD and LRD simultaneously. Mnemonic: HRD-AFT-7-LRD vs FWD-BOTH-NOW.'
 )),

(@lesson_id, 70, 'system',
 'Lavatory Protection — Thermally-Fused, No Wires',
 'The lavatory waste bin is the only volume on the aeroplane with NO electrical fire-protection wiring. It uses a Potty Bottle: a small pressurised cylinder mounted inside the waste-bin cabinet. The discharge ports have fusible end-caps — solid metal seals with a low-melting-point alloy. When the bin temperature reaches the set point, the seals melt, the end-caps blow off, and extinguishant discharges through dual outlets directly onto the source. No squib, no wiring, no panel switch. Lavatory smoke detection IS electronic — a single smoke detector in the lavatory drives cabin repeater lights, the detector LED, and an audible chime through the P/A. Crucially, NONE of this appears on the flight deck panel.',
 'video', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Lavatory Potty Bottle thermal fuse animation',
 'POTTY-FUSE · LAV-CABIN-ONLY. Thermal fuse, no wires, cabin-only smoke indication.',
 'When the senior cabin crew calls "smoke in the lav" you act on her word — there is no flight-deck light to confirm her. Brief her every flight on what to expect.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight and Cruise Discipline',
 'Pre-flight: walk the Fire Protection Panel left to right. FAULT A and B — out. BTL LOW — out. Run TEST DETECTION ENGINE 1 — observe master warning flash, CHECK FIRE DET red, PULL FUEL/HYD OFF lights, FAULT A/B amber, both ENGINE FIRE lights flashing red, fire tone if optioned. Press an ENGINE FIRE light to silence and acknowledge. Repeat for ENGINE 2. Have the cabin crew run the lavatory smoke detector self-test. Confirm 4 portable Halon 1211 extinguishers on board with gauges in GREEN. In cruise: every 10-minute scan includes Fire Protection Panel — no FAULT, no BTL LOW, no SMOKE switchlight illuminated.',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Pre-flight TEST DETECTION sequence',
 'TEST DETECTION = full chain. Both engines pre-flight. Cabin runs lavatory test.',
 'Skipping the TEST DETECTION cycle is one of the most common pre-flight skips. It takes 60 seconds and confirms a multi-million-dollar safety net. Do it every leg.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Portable Halon 1211 — Cockpit Discipline',
 'Four portable extinguishers contain Halon 1211: one in the cockpit and three in the cabin. Halon 1211 is effective on electrical, oil, and fuel fires. It is non-corrosive, non-toxic, will not freeze, will not cause cold burns. The gauge has three ranges: GREEN serviceable, YELLOW overcharge, RED recharge. A red safety catch prevents accidental discharge. CRITICAL: if the cockpit extinguisher is to be discharged, all crew members MUST wear oxygen masks with the EMERGENCY position selected — 100% oxygen at positive pressure. Halon displaces oxygen; you can extinguish a small fire and pass out at the same time without the mask.',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Portable Halon 1211 extinguisher with gauge ranges',
 'HALON-1-3-411. Cockpit discharge: 100%-MASK-COCKPIT (oxygen EMERGENCY).',
 'Brief mask-on for cockpit fire-extinguisher use on every recurrent. The instinct is "grab the extinguisher first" — no, mask first.',
 JSON_OBJECT(
   'prompt', 'A small electrical fire develops in the cockpit and the captain decides to use the portable Halon 1211 extinguisher. What MUST the crew do first?',
   'options', JSON_ARRAY(
     'Open a window to vent the smoke',
     'All crew don oxygen masks and select EMERGENCY position (100% O2 positive pressure)',
     'Discharge the extinguisher immediately — speed is critical',
     'Switch off all electrical power to the cockpit panels'
   ),
   'correct_index', 1,
   'explanation', 'Halon 1211 displaces oxygen. Cockpit discharge requires all crew on oxygen masks with EMERGENCY (100% O2 positive pressure) selected. Mnemonic: 100%-MASK-COCKPIT.'
 )),

(@lesson_id, 100, 'abnormal',
 'Engine Fire — The Memory Chant',
 'Engine fire in flight. Both ENGINE FIRE lights flash red, CHECK FIRE DET illuminates red, the affected T-handle illuminates red, fire tone sounds. The chant: <strong>FLASH · PRESS · PULL · EXTG · 30-sec · OTHER.</strong> (1) Note the FLASH on the affected engine. (2) PRESS an ENGINE FIRE light to silence the tone — both lights stop flashing and stay on steady. (3) PULL the affected T-handle — fuel and hydraulic valves close, bottle squibs ARM, ARM lights illuminate yellow. (4) Select EXTG switch FWD or AFT BTL — squib fires, suppressant discharges into PEZ + LEZ + MWW. (5) Wait 30 SECONDS while the suppressant works. (6) If the fire light remains illuminated, select the OTHER position on the EXTG switch for the second shot. Aviate, navigate, communicate throughout.',
 'video', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Engine fire procedure chant timeline',
 'FLASH · PRESS · PULL · EXTG · 30-sec · OTHER. Memorise. Drill. Don''t skip 30 sec.',
 'The temptation after the first bottle is to fire the second immediately. Do not. Suppressant needs 30 seconds to soak the zone. If you fire too fast you waste your last bottle.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'Baggage Smoke — Push, Don''t Pull',
 'Baggage smoke is different from engine fire — there is no T-handle and no PULL action. The path is simpler: SMOKE/EXTG switchlight illuminates → PUSH it → bottles discharge. Aft baggage: HRD immediately, vents drive closed (INLET + OTLT CLOSED illuminate), LRD auto 7 minutes later. Forward baggage: HRD AND LRD simultaneously. Two key Q400 quirks here: (1) the LRD low-pressure light is SHARED between the two compartments and shows the side currently active; do not assume FWD LOW means a fwd-baggage problem. (2) The vent valves close AUTOMATICALLY in aft baggage on smoke alarm — the system starves the fire of airflow without crew action. Land at nearest suitable.',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Baggage SMOKE/EXTG push action vs engine PULL action',
 'Baggage = PUSH the switchlight. Engine = PULL the T-handle. Different actions.',
 'Crews under pressure sometimes try to PULL a baggage switchlight. There is nothing to pull. PUSH and divert.',
 JSON_OBJECT(
   'prompt', 'A FWD BAGGAGE smoke alarm illuminates at FL230. Captain pushes the FWD BAGGAGE SMOKE/EXTG switchlight. Which bottles discharge, and when?',
   'options', JSON_ARRAY(
     'HRD only — LRD reserved for aft baggage',
     'HRD immediately, then LRD 7 minutes later',
     'HRD and LRD simultaneously, immediately on switchlight push',
     'No bottles discharge — the switchlight only resets the alarm'
   ),
   'correct_index', 2,
   'explanation', 'Forward baggage discharges HRD and LRD simultaneously on switchlight push. Aft baggage gets the 7-minute delay. Mnemonic: FWD-BOTH-NOW vs HRD-AFT-7-LRD.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: ENGINE FIRE / ENG FIRE OUT / SMOKE',
 'Q400 QRH non-normals for fire protection cluster into four groups. (1) ENGINE FIRE in flight: memory items — set thrust on the affected engine to MIN, condition lever to FUEL OFF, propeller lever to FEATHER, then PULL the T-handle and discharge a bottle. Verify FIRE light extinguished after 30 sec; if not, second bottle. (2) ENGINE FIRE on ground (during start or before V1 reject): memory items differ — emphasise stop, evacuate if necessary. (3) BAGGAGE SMOKE: PUSH the switchlight, divert to nearest suitable. (4) CHECK FIRE DET caution without fire (FAULT only): non-emergency, run the QRH non-normal, defer per MEL. Practice these in the sim until the actions are reflexes.',
 'image', '/assets/aircraft/q400/fire-protection-flow.svg',
 'QRH fire protection cluster diagram',
 'ENGINE FIRE flight · ENGINE FIRE ground · BAGGAGE SMOKE · FAULT DET (no fire).',
 'A real engine fire is one of the few abnormals where the QRH memory items must come out without reading. Drill them on every recurrent.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Engine Fire on Climb-Out',
 'Setup: heavy departure at 6000 ft AGL, climbing through FL120, two minutes after takeoff. PNF calls "ENGINE 1 FIRE!" — the left T-handle is illuminated red, both ENGINE FIRE lights flash, fire tone sounds. Pitch attitude is good, autopilot off, you are hand-flying.\n\nFirst 60 seconds: PF maintains aircraft control, calls "AVIATE." PNF presses an ENGINE FIRE light — tone stops, both lights go steady. PF identifies "Engine 1 fire confirmed." Memory items: PWR LVR 1 DISC, PROP LVR 1 FEATHER, CONDITION LVR 1 FUEL OFF, PULL the No.1 T-handle, EXTG switch FWD BTL. Wait 30 seconds. Fire light extinguishes. Single-engine performance, declare MAYDAY, vector for nearest suitable, brief the FO and the cabin, set up for an immediate return to the departure airport or a nearby diversion. Do not try to climb. The single-engine driftdown profile is your friend here.',
 'animation', '/assets/aircraft/q400/fire-protection-flow.svg',
 'Engine fire on climb-out scenario',
 'Aviate first · memory items · MAYDAY · single-engine return. Don''t climb on one engine.',
 'A real engine fire on climb is sudden, loud, and easy to mishandle. Drill the chant in the sim until you can run it with the autopilot disconnected.',
 JSON_OBJECT(
   'prompt', 'Engine 1 fire light illuminates at FL120 on climb-out. You discharge the FWD bottle and wait 30 seconds. The ENGINE FIRE light remains illuminated. What is the correct next action?',
   'options', JSON_ARRAY(
     'Wait another 30 seconds — the bottle may still be working',
     'Select the AFT BTL position on the EXTG switch to discharge the second bottle',
     'Pull the No.2 engine T-handle as a precaution',
     'Discharge the cockpit Halon 1211 portable into the cabin air supply'
   ),
   'correct_index', 1,
   'explanation', 'Two shots per engine. After the first bottle and 30-second wait, if the fire persists, select the OTHER position (AFT BTL in this case) to discharge the second bottle. The 30-second wait is mandatory between shots — suppressant needs time to soak the zone. Mnemonic: FLASH · PRESS · PULL · EXTG · 30-sec · OTHER.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Fire Protection in 60 Seconds',
 'Recap:\n  • 7 APDs total (6 engine + 1 APU). 3 per engine: PEZ + LEZ + MWW.\n  • 4 smoke detectors: 2 aft baggage + 1 fwd baggage + 1 lavatory.\n  • 2 dual-port FIRE bottles in LEFT wing root (FWD + AFT positions). Two shots per engine.\n  • 2 baggage HRD bottles + 1 shared LRD in aft equipment bay.\n  • Aft baggage: HRD on push, LRD auto 7 min later. Fwd baggage: HRD + LRD simultaneously.\n  • Lavatory: thermally-fused Potty Bottle, no wiring. Smoke = CABIN ONLY, no flight-deck light.\n  • 4 Halon 1211 portables: 1 cockpit + 3 cabin. Gauge: GREEN / YELLOW / RED. Cockpit use = 100% O2 mask EMERGENCY.\n  • Engine fire chant: FLASH · PRESS · PULL · EXTG · 30-sec · OTHER.\n  • Baggage = PUSH the switchlight. Engine = PULL the T-handle.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 '7-APDs · 2-2-1-1-SMOKE · 2-BOTTLES-LEFT-WING · HRD-AFT-7-LRD · FWD-BOTH-NOW · POTTY-FUSE · LAV-CABIN-ONLY · HALON-1-3-411 · 100%-MASK-COCKPIT',
 'Nine mnemonics carry every fire-protection question on a recurrent. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
