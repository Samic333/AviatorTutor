-- =============================================================================
-- AviatorTutor — Phase 8: ATA 28 Fuel — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fuel' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fuel-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Two Tanks, No Crossfeed, One Big Number',
 'The Q400 fuel system is intentionally simple. Two integral wing tanks, one per side. Each engine drinks only from its own tank. There is no engine crossfeed — the only way to balance the aeroplane laterally is by transferring fuel tank-to-tank through the central plumbing. The total usable fuel is 5,318 kg and the maximum lateral imbalance is 272 kg before the BALANCE message wakes you up. Most fuel cautions during a check ride come down to those two numbers, plus the 150-kg collector-bay low threshold. This lesson walks the architecture, the indications, the transfer logic, the refueling sequence, and the abnormals you will see in the sim.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'Q400 fuel system overview',
 'Two tanks · NO crossfeed · 5318 usable · 272 imbalance · 150 low.',
 'Captain mental model: my left engine eats from my left tank. The only way to share fuel is to physically pump it across.',
 NULL),

(@lesson_id, 20, 'concept',
 'Three Bays Per Tank — What Each Does',
 'Open up either wing tank and you find three distinct bays. The <strong>surge bay</strong> is at the outboard end — its job is venting (through 2 outboard float vent valves + 1 inboard vent line + 2 NACA vents on the bottom of the wing) and fuel recovery (any spill into the surge bay is sucked back into the main tank by the partial vacuum as fuel is consumed). The <strong>main tank</strong> is the bulk volume — that is where most of your fuel sits. The <strong>collector bay</strong> is at the inboard, aft corner — its job is feeding the engine regardless of attitude. Scavenge ejector pumps continuously top up the collector bay from the main tank low points so that climb/descent/turn slosh never starves the engine. A primary ejector pump in the collector bay then provides low-pressure fuel to the engine.',
 'diagram', '/assets/aircraft/q400/fuel-flow.svg',
 'Three bays per tank: surge / main / collector',
 '3-BAY-TANK: surge (vent) · main (storage) · collector (engine feed).',
 'On a steep descent the collector bay is what keeps the engines fed. The scavenge ejectors are working hard during pitch transients.',
 NULL),

(@lesson_id, 30, 'concept',
 'No Engine Crossfeed — The Defining Quirk',
 'Most twin turboprops have an engine crossfeed valve that lets either tank feed either engine. The Q400 does NOT. The left tank exclusively feeds the left engine plus the optional APU; the right tank exclusively feeds the right engine. There is no crossfeed plumbing. The ONLY way to balance the aeroplane laterally — or to feed both engines from one tank in an emergency — is to transfer fuel tank-to-tank through the central transfer plumbing using the FUEL TRANSFER switch on the FUEL CONTROL TRANSFER panel. This design choice is unusual and a favourite check-captain question. Memorise: NO CROSSFEED. Tank-to-tank transfer only.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'No engine crossfeed — tank-to-tank transfer only',
 'NO-CROSSFEED-TANK-TO-TANK. Defining Q400 quirk.',
 'On a single-engine ferry you cannot run both pumps from one tank. Plan fuel split that respects the imbalance limit.',
 JSON_OBJECT(
   'prompt', 'On the Q400, how can the crew route fuel from the No.1 (left) tank to the No.2 (right) engine in flight?',
   'options', JSON_ARRAY(
     'By selecting the engine crossfeed valve open',
     'By transferring fuel tank-to-tank using the FUEL TRANSFER switch — the Q400 has NO engine crossfeed',
     'By opening the fuel dump valve and re-routing through the surge bay',
     'By increasing No.1 aux pump pressure above No.2'
   ),
   'correct_index', 1,
   'explanation', 'No engine crossfeed exists on the Q400. Lateral fuel sharing or balancing is achieved only by tank-to-tank transfer via the central transfer plumbing controlled by the FUEL TRANSFER switch. Mnemonic: NO-CROSSFEED-TANK-TO-TANK.'
 )),

(@lesson_id, 40, 'system',
 'Engine Feed Path — From Collector to FMU',
 'Trace the fuel path from collector bay to the engine: collector bay → primary ejector pump (motive-flow driven, low-pressure boost) → engine driven pump → FOHE → FMU. The FOHE (Fuel Oil Heat Exchanger) filters AND heats the fuel before it enters the Fuel Metering Unit. Heating prevents ice crystal formation in cold-soak fuel; filtering prevents particulates from reaching the metering valves. If the filter clogs, fuel automatically bypasses around it — and the #1 or #2 FUEL FLTR BYPASS caution illuminates to warn you that the filter is on its way out. The AC variable-frequency auxiliary pump in the collector bay is the boost backup; it activates manually via switchlight, automatically during fuel transfer, or to rescue the primary ejector if it fails.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'Engine feed path through FOHE',
 'COLL → primary ejector → engine pump → FOHE (heats + filters) → FMU.',
 'On a cold-soaked overnight at altitude, the FOHE is what keeps your fuel above the ice point. If you see FUEL FLTR BYPASS, the filter is clogging — flag for maintenance.',
 NULL),

(@lesson_id, 50, 'system',
 'Auxiliary Pumps — Boost for Takeoff and Transfer',
 'Each collector bay houses an AC variable-frequency auxiliary pump. Its job is twofold. (1) BOOST backup: required ON for takeoff and landing — it backs up the primary ejector pump during the high-thrust regimes where any boost pressure loss would be a problem. The pilot uses the TANK 1 AUX PUMP and TANK 2 AUX PUMP switchlights to enable. (2) TRANSFER service: when you select the FUEL TRANSFER switch toward a receiver tank, the donor tank''s aux pump activates AUTOMATICALLY — its ON segment turns green without anyone pushing the switchlight. Recognise this pattern on the panel: an ON-green segment with the switch NOT depressed = transfer in progress. The aux pump pressure-status circle on the MFD fuel page tells you the truth: WHITE-fill = low/no pressure; GREEN-fill = normal pressure.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'Aux pump roles: takeoff/landing boost + transfer service',
 'AUX-AC-VF-COLLECTOR · ON for T/O and LDG · AUTO-AUX-DURING-TRANSFER.',
 'Brief the FO: "AUX pumps both ON for takeoff." If you see one of the AUX pumps light up uncommanded, check the transfer switch — it''s donating.',
 NULL),

(@lesson_id, 60, 'system',
 'Imbalance — 272 KG Triggers BALANCE',
 'The Fuel Quantity Computer (FQC) continuously compares left and right tank quantity. If the difference exceeds <strong>272 kg</strong>, two things happen on your panel. First, a yellow [BALANCE] message starts FLASHING just above the FUEL legend on the Engine Display. Second, the analog quantity dials on the MFD fuel page change to a SOLID YELLOW pointer — both of them. Crew action: select the FUEL TRANSFER switch toward the lighter tank. The donor tank''s aux pump automatically starts. The transfer shutoff valve opens (VALVE OPEN reverse-video green on the MFD). The triangle indicator on the fuel page points toward the receiver tank. Transfer continues until you deselect the switch OR the receiver''s high-level sensor detects an overfill, which automatically halts transfer.',
 'video', '/assets/aircraft/q400/fuel-flow.svg',
 'BALANCE message + transfer logic',
 '272-IMBALANCE → flash BALANCE + yellow dials → TRANSFER toward lighter tank.',
 'A slow imbalance is more often a fuel-burn asymmetry than a leak. Match it with engine torque/fuel flow before assuming a leak.',
 JSON_OBJECT(
   'prompt', 'In cruise the [BALANCE] message starts flashing yellow above the FUEL legend on the ED, and both analog quantity dials turn solid yellow. What is the lateral imbalance threshold that triggered this?',
   'options', JSON_ARRAY(
     '100 kg',
     '200 kg',
     '272 kg',
     '500 kg'
   ),
   'correct_index', 2,
   'explanation', 'Maximum lateral imbalance is 272 kg. Above this the FQC flashes [BALANCE] and turns the analog dials yellow. Action: TRANSFER toward the lighter tank; donor aux pump auto-starts. Mnemonic: 272-IMBALANCE.'
 )),

(@lesson_id, 70, 'system',
 'FUEL LOW — Three Conditions, Not One',
 'The #1 or #2 TANK FUEL LOW caution illuminates only when ALL THREE conditions are met: (1) the related collector bay drops below approximately <strong>150 kg</strong>, (2) the related engine is running, and (3) the park brake is OFF. The three-condition logic prevents nuisance illuminations during ground refuelling and parking — you don''t want the caution lighting up every time someone leaves the brake on with a low collector bay. In flight, of course, the brake is off and engines are running, so the caution is purely a quantity warning. When you see it, compare the collector bay quantity against your remaining fuel-on-board plan, check for any unexpected burn rate, and consider divert if the trend is unfavourable.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'FUEL LOW three-condition logic',
 '150-COLLECTOR-LOW: park brake OFF + collector <150 + engine running. ALL THREE.',
 'On a long sector with descent into a high-headwind diversion, the FUEL LOW is your gate to commit. Compare planned remaining vs collector bay reading.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'MFD Fuel Page — Single Source of Truth',
 'Press the FUEL SYS pushbutton on the ESCP to bring the fuel page to whichever MFD is set to SYS. Press-and-hold (with both MFDs not on SYS) shows it on the ED. The fuel page gives you everything in one view: analog quantity dials per tank (white pointer normal, yellow on imbalance), digital quantity per tank in KG, total fuel digital display 0–15,000 KG in 5-KG increments, tank temperature -99 to +99°C in 1° increments, AUX PUMP annunciators (OFF white-on-box; ON reverse-video green), aux pump pressure-status circles (white-fill low, green-fill normal), VALVE annunciator (OPEN green / CLOSED white), and fuel transfer switch direction triangle. Drill the page layout — you should be able to scan it in 3 seconds and answer "is anything wrong?"',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'MFD fuel page anatomy',
 'FUEL SYS push · scan: dials · digital · temp · AUX · valve · transfer.',
 'Brief the FO at top of climb: "scan the fuel page every 10 minutes." A slow leak shows up there before any caution.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Refueling — Pressure or Gravity, DC Required',
 'Two ways to put fuel on board. <strong>Pressure refuel:</strong> open the flush access door on the rear underside of NO.2 nacelle (right side). FUELING ON caution illuminates in the cockpit. DC POWER is REQUIRED — without it, refueling halts. Rotary selector OFF → PRESELECT REFUEL (auto stop at preset KG via INCR/DECR) or REFUEL (manual via PRECHECK/OPEN/CLOSE switches). MASTER VALVE CLOSED light extinguishes when the selector is in a refuel position. The vent/dump valve in each tank opens during refuel for tank breathing; DUMP VALVE OPEN amber illuminates. The PRECHECK position simulates a full tank — REFUEL SHUTOFF amber illuminates if the high-level shutoff is working. <strong>Gravity refuel:</strong> through the wing-mounted gravity refuel adapter on top of each wing. No DC required, slower flow rate. CRITICAL: while FUELING ON is illuminated in flight (or on the ground), tank-to-tank transfer is INHIBITED.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'Refueling — pressure (DC required) or gravity overwing',
 'DC-FOR-REFUEL · No.2 nacelle access · FUELING-ON-INHIBITS-TRANSFER · gravity overwing.',
 'On a quick turnaround in remote ops, the gravity refuel adapter is your friend if the GPU is U/S — but it takes time.',
 JSON_OBJECT(
   'prompt', 'You are doing a single-point pressure refuel and the GPU goes off-line. What happens to the refuel?',
   'options', JSON_ARRAY(
     'Refuel continues normally — pressure refuel is mechanical',
     'Refuel HALTS — pressure refuel requires DC POWER to operate the level control valves',
     'Refuel switches automatically to gravity mode',
     'Tank vents open and fuel spills overboard'
   ),
   'correct_index', 1,
   'explanation', 'Pressure refuel requires DC power. Loss of DC during refuel halts the operation. Restore DC or switch to gravity refuel via the wing-mounted overwing adapter. Mnemonic: DC-FOR-REFUEL.'
 )),

(@lesson_id, 100, 'abnormal',
 'ENG FUEL PRESS Caution — Pump Logic',
 '#1 or #2 ENG FUEL PRESS caution illuminates when the engine driven pump inlet pressure drops below the preset limit. This usually means the primary ejector pump in that collector bay is not providing adequate boost. First action: select the AUX PUMP ON for the affected side (push the TANK x AUX PUMP switchlight). The AC variable-frequency auxiliary pump kicks in and restores boost pressure; the caution typically clears within a few seconds. If the caution does NOT clear, run the QRH non-normal — there may be a deeper problem (filter clogging, fuel quality issue, or pump failure). Confirm via the MFD aux pump pressure-status circle: it should turn from white-fill to green-fill once the AUX is doing its job.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'ENG FUEL PRESS recovery via AUX pump',
 'ENG FUEL PRESS → AUX PUMP ON → confirm green pressure circle → continue or QRH.',
 'A clean AUX-pump rescue is a non-event. A persistent ENG FUEL PRESS with AUX on is a divert candidate — flag fuel quality or pump fault.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'Fuel Leak — Recognising the Pattern',
 'Fuel leaks present as a rapidly increasing imbalance combined with a higher-than-planned burn rate. Signs: the imbalance grows faster than normal asymmetric burn would justify; the [BALANCE] message appears earlier than expected; total quantity drops faster than total fuel flow indicates; visible streaking of fuel from the leak source. <strong>CRITICAL: do NOT initiate fuel transfer.</strong> Transferring fuel into a leaking tank just wastes fuel into the leak. Run the QRH FUEL LEAK procedure: identify the leaking side, secure the affected engine if QRH directs, calculate landing fuel based on actual remaining (not planned), declare appropriate emergency, and divert to nearest suitable. Always cross-check tank quantity vs total fuel-on-board math — if they don''t add up, you have a leak.',
 'video', '/assets/aircraft/q400/fuel-flow.svg',
 'Fuel leak recognition + QRH',
 'Fast imbalance + high burn = leak. DO NOT transfer · QRH FUEL LEAK · divert.',
 'A captain who reflexively reaches for the transfer switch on a leak makes the situation worse. Identify burn rate vs quantity math first.',
 JSON_OBJECT(
   'prompt', 'In cruise the imbalance grows from 100 to 350 kg in 10 minutes; total fuel is dropping faster than fuel-flow accounts for. What is the WORST action?',
   'options', JSON_ARRAY(
     'Run the QRH FUEL LEAK procedure',
     'Initiate tank-to-tank transfer toward the lighter tank to balance',
     'Calculate landing fuel based on actual quantity vs distance',
     'Notify ATC and request descent'
   ),
   'correct_index', 1,
   'explanation', 'NEVER transfer into a leaking tank — you just feed the leak. Identify burn vs quantity math first. The four-light pattern of: rapid imbalance + high burn vs FOB + visible streaks = fuel leak. Run QRH FUEL LEAK; secure affected engine if directed; divert.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Fuel Non-Normals',
 'Q400 QRH non-normals for fuel cluster into six groups. (1) BALANCE message — manage by transfer toward the lighter tank. (2) ENG FUEL PRESS — AUX PUMP ON; if persistent, run QRH. (3) TANK FUEL LOW — verify against planned remaining; consider divert. (4) FUEL FLTR BYPASS — continue but flag for maintenance. (5) FUELING ON in flight — verify door closed; if sensor fault, run QRH; transfer is INHIBITED while caution illuminated. (6) FUEL LEAK — DO NOT transfer; identify side; secure engine per QRH; divert. Most fuel non-normals are not memory items but the discipline matters: read the MFD fuel page first, do the burn-rate math, then decide.',
 'image', '/assets/aircraft/q400/fuel-flow.svg',
 'QRH fuel non-normal cluster',
 'BALANCE · ENG FUEL PRESS · TANK FUEL LOW · FLTR BYPASS · FUELING ON · FUEL LEAK.',
 'On any fuel caution, your first action is to LOOK at the MFD fuel page — not at the panel. The page tells you the why; the panel tells you the what.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: BALANCE in Cruise',
 'Setup: cruise at FL230, 3 hours into a 5-hour sector over ocean. PNF calls "BALANCE message." You look at the MFD fuel page: tank 1 reads 1,400 kg, tank 2 reads 1,100 kg — imbalance 300 kg. Both analog dials are yellow. Engine torque and fuel flow are roughly symmetric.\n\nAssessment: imbalance is 28 kg above the threshold. Burn rate is normal — this is not a leak signature. Action: select FUEL TRANSFER switch TO TANK 2 (toward the lighter tank). Tank 1 aux pump auto-illuminates ON green. Transfer shutoff valve opens (MFD VALVE OPEN reverse video). Triangle on fuel page points right. Brief the FO: "Transfer running, monitor for normal stop." After roughly 5 minutes, the imbalance reaches zero; deselect the transfer switch to CENTER (or let the high-level sensor halt automatically if you''re going for a slight over-correction). Confirm BALANCE message extinguishes; analog dials return to white pointers. Document the event.',
 'animation', '/assets/aircraft/q400/fuel-flow.svg',
 'BALANCE handling scenario',
 'Read MFD · confirm not a leak · transfer toward lighter · monitor · stop at zero.',
 'A clean balance correction is a 3-minute exercise that earns you nothing on the panel — but doing it wrong by overshooting or by transferring into a leak is how careers stall.',
 JSON_OBJECT(
   'prompt', 'BALANCE flashes in cruise: tank 1 = 1,400 kg, tank 2 = 1,100 kg. Burn rate is symmetric and matches planned. Best action?',
   'options', JSON_ARRAY(
     'Suspect a fuel leak; declare PAN-PAN; divert',
     'FUEL TRANSFER switch TO TANK 2; monitor donor aux pump auto-on; stop when balanced',
     'Push both AUX PUMP switchlights ON',
     'Open the engine crossfeed valve'
   ),
   'correct_index', 1,
   'explanation', 'Symmetric burn rate + clean imbalance = balance correction, not a leak. Transfer toward the lighter tank. Donor aux pump auto-starts. Monitor for normal stop at zero imbalance. (There is no engine crossfeed on the Q400.)'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Fuel in 60 Seconds',
 'Recap:\n  • Two integral wing tanks. No.1 left feeds left engine + APU; No.2 right feeds right engine. NO engine crossfeed.\n  • Three bays per tank: surge (vent + recovery), main (storage), collector (engine feed).\n  • Total usable: 5,318 kg. Imbalance limit: 272 kg → BALANCE message + yellow dials.\n  • FUEL LOW threshold: ~150 kg in collector bay. Three trigger conditions: park brake OFF + low quantity + engine running.\n  • Aux pump: AC variable-frequency, in collector bay. ON for T/O and LDG. Auto-on during transfer (donor side).\n  • FOHE filters AND heats fuel before the FMU. FUEL FLTR BYPASS = filter clogging.\n  • Refuel: pressure (DC required, No.2 nacelle access) or gravity overwing.\n  • FUELING ON caution INHIBITS tank-to-tank transfer.\n  • JET B / JP-4 with TANK temp >35°C: max altitude FL200.\n  • Fuel leak: rapid imbalance + high burn vs FOB. NEVER transfer into a leaking tank.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 '5318-USABLE · 272-IMBALANCE · 150-COLLECTOR-LOW · 3-BAY-TANK · NO-CROSSFEED-TANK-TO-TANK · AUX-AC-VF-COLLECTOR · AUTO-AUX-DURING-TRANSFER · FOHE-HEATS-FILTERS · JP4-35-FL200 · DC-FOR-REFUEL · FUELING-ON-INHIBITS-TRANSFER',
 'Eleven mnemonics carry every fuel question on a recurrent. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
