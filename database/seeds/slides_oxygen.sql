-- =============================================================================
-- AviatorTutor — Phase 14: ATA 35 Oxygen — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'oxygen' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'oxygen-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Layered Defence — Fixed, Portable, PBE',
 'Q400 oxygen splits into four overlapping systems. The flight-deck FIXED crew system has 3 full-face masks (pilot, copilot, observer) on a single common cylinder in the right lower nose compartment. The PORTABLE passenger O2 system uses cylinders kept in the cabin. PBE (Protective Breathing Equipment) units serve both flight crew and cabin attendants for low-O2 environments such as cabin smoke. First-aid oxygen is kept in the passenger compartment for medical use. Three numbers to memorise: 4 minutes (descent to 14,000 ft), 116 minutes (level flight at 14,000 ft), and less than 5 seconds (mask donning target).',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'Q400 oxygen system overview',
 '4 systems · 3 masks · 1 cylinder · 4-min descent · 116-min level · <5-sec don.',
 'On a real depressurisation, the 5-second don is the difference between a brief usable consciousness window and an incapacitation event.',
 NULL),

(@lesson_id, 20, 'concept',
 '3 Masks, 1 Common Cylinder',
 'The flight-deck system supplies 3 full-face microphone-equipped masks: pilot, copilot, observer. All three draw from a SINGLE common cylinder in the right lower nose compartment. The observer mask plugs into a DUAL outlet on the copilot oxygen supply line. Crucially, masks are CROSS-COMPATIBLE — if an outlet fails, the mask can be plugged into another working outlet, and if a mask itself fails, the observer mask can be used by either crew member. A pressure gauge on the cylinder shows bottle pressure; a lighted gauge on the COPILOT side console shows pressure available to the masks. Cylinder OFF: mask pressure drops to atmospheric, but the flight-deck gauge continues to show BOTTLE pressure.',
 'diagram', '/assets/aircraft/q400/oxygen-flow.svg',
 'Crew O2 architecture: 3 masks, 1 cylinder, dual observer outlet',
 '3-MASKS-1-CYLINDER · OBSERVER-DUAL-COPILOT · cross-compatible outlets.',
 'On a single mask failure mid-descent, the observer mask is your spare. Drill the swap.',
 NULL),

(@lesson_id, 30, 'concept',
 'System Capacity — 14000 / 4 / 116',
 'The crew oxygen system is sized for the standard depressurisation profile: an emergency descent from cruise to 14,000 ft in <strong>4 minutes</strong>, followed by level flight at <strong>14,000 ft for 116 minutes</strong>. This 4 + 116 minute envelope is what determines the cylinder size and the flow rate to all 3 masks simultaneously. Why 14,000 ft? It''s the standard altitude at which supplemental oxygen is no longer required for survival but at which a typical Q400 sector can still be completed to a divert field. The 116 minutes covers most reasonable diversions from Q400 cruise altitudes. If your divert is more than ~115 minutes away at 14,000 ft, you are running on borrowed cylinder time.',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'System capacity numbers',
 '14000-IN-4-LEVEL-116. Descent 4 min · level 116 min.',
 'On a real depress at FL230, your descent time to 14,000 is well under 4 min. The 116 min margin is for the divert.',
 JSON_OBJECT(
   'prompt', 'What altitude profile does the Q400 crew oxygen system supply for, in terms of descent time and level-flight duration?',
   'options', JSON_ARRAY(
     'Descent to 10,000 ft in 2 min + level for 60 min',
     'Descent to 14,000 ft in 4 min + level at 14,000 for 116 min',
     'Descent to FL250 in 5 min + level at FL250 for 30 min',
     'No fixed capacity'
   ),
   'correct_index', 1,
   'explanation', 'Descent to 14,000 ft in 4 min + 116 minutes at 14,000 ft. Mnemonic: 14000-IN-4-LEVEL-116.'
 )),

(@lesson_id, 40, 'system',
 'Regulator — NORM, 100%, EMER',
 'Each crew mask has a diluter-demand regulator with a rotary knob and three positions. <strong>NORM:</strong> automatic air/oxygen mixture varying with cabin altitude. The regulator senses cabin altitude and adjusts the O2 fraction — pure ambient air at low altitudes; increasing O2 ratio at higher altitudes. Default position. <strong>100%:</strong> regulator supplies 100% oxygen regardless of cabin altitude. Used when air contamination is a concern but no positive pressure is needed. <strong>EMER:</strong> 100% oxygen at slight POSITIVE pressure. Used for cabin smoke / fire — positive pressure flushes contaminants out around the mask seal AND purges smoke from any smoke goggles being worn. <strong>CAUTION:</strong> keeping the regulator in EMER continuously will DEPLETE the cylinder. Return to NORM or 100% as soon as the smoke event is contained.',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'Three regulator positions',
 'NORM-100-EMER · EMER = 100% positive + smoke purge · EMER-DEPLETES.',
 'On the smoke chant: MASK ON, EMER, 100% positive, smoke goggles purged. Once smoke is gone, regulator back to NORM or 100%.',
 NULL),

(@lesson_id, 50, 'system',
 '5-Second Don — The Inflatable Harness',
 'The crew mask is designed for emergency donning in less than 5 seconds. The mechanism is a quick-don inflatable harness controlled by a red button on the regulator. The procedure: pull the mask out of the stowage cup; press the red harness inflation button — the harness inflates with O2 pressure, expanding to slip over your head; release the button — the harness deflates and compresses, securing the mask snugly. Practice the motion until it''s muscle memory. If after donning you have breathing difficulty or the in-line pressure indicator is RED, verify the supply hose is connected to its outlet — that''s the most common cause of post-don issues.',
 'video', '/assets/aircraft/q400/oxygen-flow.svg',
 'Quick-don inflatable harness in less than 5 sec',
 '5-SEC-DON. Red button inflate · release deflate · check hose if RED indicator.',
 'A real depressurisation gives you maybe 15 seconds of useful consciousness at FL250. The 5-second don is a survival item.',
 JSON_OBJECT(
   'prompt', 'What is the donning time target for a Q400 crew oxygen mask, and how is the rapid don achieved?',
   'options', JSON_ARRAY(
     'Less than 30 seconds via standard straps',
     'Less than 5 seconds via inflatable harness — red button inflates, release deflates and secures',
     'Less than 60 seconds via velcro adjustment',
     'No specific target'
   ),
   'correct_index', 1,
   'explanation', 'Less than 5 seconds via inflatable harness. Red button inflates with O2; release deflates and secures. Mnemonic: 5-SEC-DON.'
 )),

(@lesson_id, 60, 'system',
 'Pressure Indicators — Three Different Things',
 'There are THREE different pressure indications associated with the crew oxygen system. <strong>Cylinder gauge</strong> on the cylinder itself: shows bottle pressure continuously. <strong>Flight-deck gauge</strong> on the COPILOT side console: lighted, shows pressure available to the masks. <strong>In-line pressure indicator</strong> on the mask supply hose: GREEN with correct pressure, RED if pressure is low. Note: when the cylinder is turned OFF (cylinder shutoff knob), the available mask pressure drops to atmospheric, but the flight-deck gauge continues to show bottle pressure. So an OFF cylinder gives gauge showing pressure but no actual delivered O2. The in-line indicator is your truth-teller — if it''s RED, oxygen is not flowing regardless of what the gauges say.',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'Three pressure indicators',
 'CYLINDER · FLT DECK COPILOT · IN-LINE GREEN/RED · OFF-ATMO-GAUGE-BOTTLE.',
 'When the in-line is RED but the gauges look healthy, suspect the cylinder shutoff or a hose disconnect.',
 NULL),

(@lesson_id, 70, 'system',
 'Burst Disc — The External Tell-Tale',
 'On the right exterior of the nose, there is a <strong>green burst disc</strong>. Its function is to relieve cylinder over-pressurisation: if the cylinder pressure rises beyond a safety threshold (e.g. due to thermal expansion in extreme heat or a fault in the regulator), the disc ejects, venting overboard and preventing cylinder rupture. Crucially, this is an EXTERNAL indicator: a missing or visibly compromised burst disc on the pre-flight walk-around tells you a cylinder over-pressurisation event has occurred. Maintenance write-up; do not dispatch with the crew oxygen system inoperative. The disc location on the right exterior of the nose is unobtrusive but important — train your eye to find it on every walk-around.',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'Green burst disc — right exterior nose',
 'GREEN-BURST-RIGHT · external pre-flight tell-tale.',
 'A walk-around captain who knows where to look will spot a missing burst disc — and save a sortie.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight Discipline',
 'Pre-flight oxygen checks per company SOP. (1) Verify cylinder pressure on flight-deck gauge — within company minimum (typically 1500-1800 PSI for full bottle). (2) Check 3 masks in stowage cups; in-line pressure indicators GREEN. (3) Quick test don: pull mask, press red button, fit, release. Verify rapid donning. (4) Verify regulator at NORM (default for normal flight). (5) Check the burst disc on the right exterior of the nose during the walk-around — green disc visible and intact. (6) Brief F/A on cabin O2 layout, PBE locations, first-aid O2. (7) Confirm no smoking signs are operational (PA chime when lit).',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'Pre-flight oxygen checks',
 'Cylinder · 3 masks · don test · regulator NORM · burst disc · F/A brief.',
 'Skipping the burst disc check on the walk-around is common — it''s on the right side, off the standard scan.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Cabin O2 + PBE + First Aid',
 'Beyond the fixed crew system, three other oxygen items are on board. <strong>Portable passenger O2 cylinders</strong> in the cabin: used by cabin attendants for passenger O2 supply during a depressurisation event in addition to whatever PSU drop-down system is fitted. <strong>PBE (Protective Breathing Equipment)</strong>: smoke-hood-style units for both flight deck and cabin attendants. Used in low-oxygen environments such as cabin fire or smoke evacuation. Each unit is a self-contained breathing system with its own O2 supply. <strong>First aid oxygen</strong>: in the passenger compartment for medical use during normal flight (passenger illness, dizziness). Cabin attendants administer per their training. Brief F/A on locations and quantities every leg.',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'Cabin oxygen + PBE + first aid',
 'PBE-FLT-CABIN · portable cabin O2 · first aid O2.',
 'A passenger medical event mid-flight: F/A grabs first-aid O2. A cabin smoke event: F/A grabs PBE. Different equipment, different uses.',
 NULL),

(@lesson_id, 100, 'abnormal',
 'Rapid Depressurisation — The Drill',
 'Rapid depressurisation in cruise. The drill: <strong>(1) Oxygen masks ON immediately.</strong> Don in less than 5 seconds via inflatable harness. <strong>(2) Regulator to NORM</strong> (or 100% if cabin altitude high). <strong>(3) Crew comm via mask mic.</strong> <strong>(4) Initiate emergency descent</strong> to 14,000 ft. System sized for descent in 4 minutes from cruise. <strong>(5) Cabin announcement</strong> — passengers don their masks per PSU drop-down. <strong>(6) Brief F/A</strong> via PA. <strong>(7) Plan divert</strong> to nearest suitable airport. <strong>(8) Run QRH</strong> RAPID DEPRESSURISATION non-normal.',
 'video', '/assets/aircraft/q400/oxygen-flow.svg',
 'Rapid depressurisation drill',
 'MASK ON · NORM/100% · EMERGENCY DESCENT · 14000 in 4 min · DIVERT.',
 'Mask first, talk after. The 5-second don is the survival window. Drill it.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'Cockpit Smoke / Fire — EMER Mode',
 'Cockpit smoke or fire in cruise. The drill: <strong>(1) Oxygen masks ON immediately.</strong> <strong>(2) Regulator to EMER position</strong> — 100% O2 at positive pressure. The positive pressure prevents smoke ingress around the mask seal AND purges smoke from any smoke goggles. <strong>(3) Smoke goggles ON</strong> if not already. <strong>(4) Crew comm via mask mic.</strong> <strong>(5) Identify and isolate</strong> the smoke source per QRH. <strong>(6) Use cockpit Halon 1211 portable</strong> (Phase 6 Fire Protection) if a discrete electrical source is identified. <strong>(7) Run QRH SMOKE/FUMES</strong> non-normal. <strong>(8) Manage EMER mode time</strong> — return to NORM or 100% as soon as the smoke is contained, since EMER depletes the cylinder fast.',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'Cockpit smoke drill — EMER mode',
 'MASK ON · EMER · 100% positive · GOGGLES PURGE · don''t leave EMER on.',
 'EMER mode is your fire-event tool. It works. But it eats the cylinder. Manage time.',
 JSON_OBJECT(
   'prompt', 'In a cockpit smoke / fire event, what regulator position should be selected?',
   'options', JSON_ARRAY(
     'NORM — preserves cylinder',
     '100% — full O2',
     'EMER — 100% O2 at positive pressure, also purges smoke goggles',
     'No regulator change needed'
   ),
   'correct_index', 2,
   'explanation', 'EMER position: 100% O2 at positive pressure flushes contaminants from mask seal and purges smoke goggles. Mnemonic: EMER-DEPLETES — return to NORM/100% once smoke is contained.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Oxygen Non-Normals',
 'Q400 QRH non-normals for oxygen cluster into four groups. (1) RAPID DEPRESSURISATION: masks on, NORM/100%, emergency descent to 14,000 ft, divert. (2) COCKPIT SMOKE / FIRE: masks on, regulator EMER, goggles, cockpit Halon if needed, run SMOKE/FUMES. (3) CREW O2 SYSTEM FAULT: cylinder loss, in-line indicator RED, mask failure — verify supply via in-line + cylinder gauge; use observer mask if a primary fails; descend to 14,000 ft if cylinder is lost. (4) CABIN SMOKE / FIRE: PBE for cabin attendants; portable O2 for passengers; first-aid O2 if medical. Run QRH cabin-fire non-normal.',
 'image', '/assets/aircraft/q400/oxygen-flow.svg',
 'QRH oxygen cluster',
 'DEPRESS · COCKPIT SMOKE · CYLINDER LOSS · CABIN SMOKE.',
 'All four are time-critical. Mask donning before reading is the discipline.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Smoke in the Cockpit at FL230',
 'Setup: cruise at FL230. Pilot smells electrical smoke; faint visible haze on the glareshield. FO confirms.\n\nFirst 10 seconds: PF calls "MASKS ON, EMER!" Both pilots pull masks from stowage cups. Pull the inflatable harness; press red button — harness inflates over head; release — harness deflates and secures. Less than 5 seconds. Regulator to EMER position. Smoke goggles on if not already. Now both pilots are on 100% O2 at positive pressure with smoke goggles purged.\n\nNext 30 seconds: Crew comm via mask mics — verify both pilots on O2. PNF runs the SMOKE/FUMES QRH: identify and isolate the source. PF maintains aircraft control; declares MAYDAY; requests immediate descent. PNF cabin announcement: "Cabin attendants — smoke event in flight deck — secure cabin."\n\nNext 5 minutes: descent and divert to nearest suitable airport. Once smoke is no longer increasing, regulator can be moved from EMER to 100% to preserve cylinder. Brief approach for smoke contingency; cockpit Halon 1211 portable on standby. ARFF on the field.',
 'animation', '/assets/aircraft/q400/oxygen-flow.svg',
 'Cockpit smoke scenario at FL230',
 'MASK ON · EMER · GOGGLES · COMM · QRH · DIVERT · MANAGE EMER time.',
 'Drill this in every recurrent. The mask-and-EMER motion must be reflexive — under 5 seconds.',
 JSON_OBJECT(
   'prompt', 'Cockpit smoke at FL230. After mask on + regulator EMER, what is your management consideration for the EMER position?',
   'options', JSON_ARRAY(
     'Keep in EMER until landing',
     'Return to NORM or 100% as soon as the smoke event is contained — EMER depletes the cylinder',
     'Cycle EMER on/off every 30 seconds',
     'No special management needed'
   ),
   'correct_index', 1,
   'explanation', 'EMER mode at 100% positive pressure depletes the cylinder fast. Use only while actively flushing smoke; return to NORM/100% once contained. Mnemonic: EMER-DEPLETES.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Oxygen in 60 Seconds',
 'Recap:\n  • 3 crew masks (pilot/copilot/observer) on 1 common cylinder. Cylinder in RIGHT LOWER nose compartment.\n  • Green burst disc on right exterior of nose — pre-flight check item.\n  • Lighted gauge on COPILOT side console + cylinder gauge + in-line pressure indicator.\n  • Cylinder OFF: mask pressure → atmospheric. Gauge still reads bottle.\n  • System capacity: descent to 14,000 ft in 4 min + 116 min level at 14,000.\n  • Donning target: less than 5 seconds via inflatable harness (red button inflates, release deflates).\n  • Regulator: NORM (mix), 100% (pure), EMER (positive + smoke goggle purge).\n  • EMER depletes cylinder — manage time.\n  • Observer mask plugs into DUAL outlet on copilot supply line.\n  • Outlets cross-compatible — failover by replug.\n  • In-line indicator: GREEN good / RED low.\n  • No smoking when O2 in use.\n  • Plus: portable passenger O2, PBE for both crews, first-aid O2 in cabin.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 '3-MASKS-1-CYLINDER · 14000-IN-4-LEVEL-116 · 5-SEC-DON · NORM-100-EMER · EMER-DEPLETES · RIGHT-NOSE-CYLINDER · GREEN-BURST-RIGHT · COPILOT-CONSOLE-GAUGE · OFF-ATMO-GAUGE-BOTTLE · OBSERVER-DUAL-COPILOT · PBE-FLT-CABIN · NO-SMOKE-O2',
 'Twelve mnemonics carry every oxygen question. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
