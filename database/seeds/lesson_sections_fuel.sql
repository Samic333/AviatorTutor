-- =============================================================================
-- AviatorTutor — Phase 8: ATA 28 Fuel — lesson sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fuel' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fuel-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Two Tanks, Three Bays, No Crossfeed',
 '<p>The Q400 carries fuel in two integral wing tanks — No.1 on the left, No.2 on the right — extending laterally from the fuselage to the rib just inboard of each aileron. Each tank is divided into three bays (surge, main, collector) and feeds only its dedicated engine; the left tank also feeds the optional APU. Crucially, there is <strong>no engine crossfeed</strong> capability on the Q400 — the only way to balance the aircraft laterally is via tank-to-tank fuel transfer through the central transfer plumbing. Fuel handling at the engine level uses ejector pumps driven by motive flow (not centrifugal boost pumps); AC variable-frequency auxiliary pumps in each collector bay back them up. The system supports both single-point pressure refuel (DC required) and gravity overwing refuel.</p>',
 'overview', 10),

(@lesson_id, 'Components — Tanks, Pumps, Valves, Indications',
 '<ul>
  <li><strong>Wing tanks:</strong> 2 integral (wet) tanks, No.1 left + No.2 right.</li>
  <li><strong>Tank bays:</strong> 3 per tank — SURGE bay (outboard, vent + fuel recovery), MAIN tank (bulk storage), COLLECTOR bay (inboard/aft, engine feed at any attitude).</li>
  <li><strong>Scavenge ejector pumps:</strong> in each tank. Draw fuel from low points back to the collector bay using high-pressure motive flow.</li>
  <li><strong>Primary ejector pump:</strong> 1 per collector bay. Low-pressure boost to engine.</li>
  <li><strong>AC auxiliary pump:</strong> 1 per collector bay. AC variable-frequency. Backup boost for takeoff/landing and primary-pump failure. Also activates automatically when the donor tank''s pump runs during fuel transfer.</li>
  <li><strong>Engine feed shutoff valve:</strong> 1 per engine. Closed by PULL FUEL/HYD OFF T-handle on Fire Protection Panel.</li>
  <li><strong>Fuel transfer shutoff valves:</strong> electrically operated. Open during transfer, close when transfer stops.</li>
  <li><strong>FOHE:</strong> Fuel Oil Heat Exchanger. Filters and heats fuel before the FMU.</li>
  <li><strong>Fuel filter bypass:</strong> automatic when filter clogs. Caution illuminates on impending bypass.</li>
  <li><strong>Surge bay venting:</strong> 2 outboard float vent valves + 1 inboard vent line per side; routed to 2 NACA vents on the bottom of each wing through integral standpipes.</li>
  <li><strong>Refuel/defuel panel:</strong> under flush door on rear underside of No.2 nacelle. Includes rotary selector (OFF/PRESELECT REFUEL/REFUEL/DEFUEL), MASTER VALVE CLOSED light, PRECHECK switches, RDI quantity display.</li>
  <li><strong>Magnetic dipsticks:</strong> on underside of wings. Float magnet attracts dipstick magnet at fuel level for ground quantity check.</li>
  <li><strong>FQC:</strong> Fuel Quantity Computer — drives MFD fuel page, monitors imbalance.</li>
  <li><strong>FUEL CONTROL TRANSFER panel:</strong> TANK 1 AUX PUMP, TRANSFER switch (TO TANK 1 / CENTER / TO TANK 2), TANK 2 AUX PUMP.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — Feed, Transfer, Refuel, Vent',
 '<h4>Engine feed</h4>
<ul>
  <li>Collector bay → primary ejector pump (or AC aux pump if primary low) → engine driven pump → FOHE → FMU.</li>
  <li>Aux pumps must be ON for takeoff and landing.</li>
  <li>If engine driven pump inlet pressure drops below preset limit → #1 or #2 ENG FUEL PRESS caution.</li>
</ul>
<h4>Tank-to-tank transfer</h4>
<ul>
  <li>TRANSFER switch on FUEL CONTROL TRANSFER panel: TO TANK 1 / CENTER / TO TANK 2.</li>
  <li>Donor tank''s aux pump auto-activates; ON segment turns green automatically.</li>
  <li>Electrically operated transfer shutoff valves open; MFD VALVE OPEN annunciator illuminates.</li>
  <li>Triangle on MFD fuel page points toward the receiver tank.</li>
  <li>Transfer continues until: crew deselects OR receiver tank high-level sensor detects overfill (auto stop).</li>
  <li>Transfer is INHIBITED when FUELING ON caution is illuminated (refuel door open).</li>
</ul>
<h4>Pressure refuel</h4>
<ul>
  <li>Open access door on rear underside of No.2 nacelle. FUELING ON caution illuminates.</li>
  <li>DC power is REQUIRED. Loss of DC during refuel halts refueling.</li>
  <li>Rotary selector: OFF → PRESELECT REFUEL (auto stop at preset KG) or REFUEL (manual via PRECHECK/OPEN/CLOSE).</li>
  <li>MASTER VALVE CLOSED light extinguishes when selector turned to refuel position.</li>
  <li>Vent/dump valve in each tank opens during refuel for tank breathing; DUMP VALVE OPEN amber illuminates.</li>
  <li>If both normal AND backup shutoff features fail, fuel spills via surge bay → standpipe → NACA vent overboard.</li>
  <li>PRECHECK position simulates a full tank to verify the high-level shutoff system; REFUEL SHUTOFF amber illuminates if working.</li>
</ul>
<h4>Gravity refuel</h4>
<ul>
  <li>Through wing-mounted gravity refuel adapter on top of each wing. No DC required.</li>
</ul>
<h4>Venting + scavenging</h4>
<ul>
  <li>Surge bay vents to 2 NACA vents on bottom of each wing.</li>
  <li>Float vent valves open/close based on fuel level near the top of the tank.</li>
  <li>Scavenge ejector pumps continually return fuel from low points to the collector bay using motive flow.</li>
  <li>Flapper check valves at base of collector bay ensure gravity feed if scavenge flow insufficient.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — MFD Fuel Page and Cruise Discipline',
 '<h4>MFD Fuel Page (call up via FUEL SYS pushbutton on ESCP)</h4>
<ul>
  <li>Analog quantity dials per tank — pointer WHITE normal, YELLOW on imbalance.</li>
  <li>Digital quantity per tank in KG (or LB depending on cockpit config).</li>
  <li>Total fuel digital display 0–15,000 KG in 5-KG increments.</li>
  <li>Tank temperature digital display per tank, °C, -99 to +99 in 1° increments. White text TANK label, blue °C unit.</li>
  <li>TANK 1 / TANK 2 AUX PUMP annunciators: OFF (white text in white box) or ON (reverse video, black text on green).</li>
  <li>Aux pump pressure-status circle: WHITE-fill = low/no pressure; GREEN-fill = normal pressure.</li>
  <li>Fuel transfer shutoff valve annunciator: VALVE CLOSED (upper rectangle) or OPEN (lower, reverse video).</li>
  <li>FUEL TRANSFER SW indication: triangle pointing toward the active receiver tank.</li>
</ul>
<h4>Pre-flight</h4>
<ul>
  <li>Compare crew fuel order vs MFD total fuel and rounded LB/KG conversion.</li>
  <li>Magnetic dipstick check on cold soak departures (verify against gauges within tolerance).</li>
  <li>Confirm no FUELING ON caution (door closed and latched).</li>
  <li>Confirm aux pump pressure circles green when activated for taxi/takeoff.</li>
</ul>
<h4>Cruise scan</h4>
<ol>
  <li>No FUEL caution illuminated.</li>
  <li>Imbalance pointer WHITE on both tanks (no yellow).</li>
  <li>TANK temperature within band — particular attention if using JET B/JP-4 above 35°C.</li>
  <li>Aux pumps OFF in cruise (unless active for transfer).</li>
</ol>',
 'normal', 40),

(@lesson_id, 'Abnormal — Imbalance, Pump Failure, Filter, FUEL LOW',
 '<ul>
  <li><strong>BALANCE message ([BALANCE] yellow above FUEL legend on ED):</strong> imbalance &gt;272 kg. Analog dials turn solid yellow. Action: select TRANSFER toward the lighter tank. Donor aux pump auto-activates; valve opens; transfer until balance restored.</li>
  <li><strong>#1 or #2 TANK FUEL LOW (caution):</strong> park brake OFF + collector bay below ~150 kg + engine running. Investigate quantity vs fuel-on-board planning; verify no leak; consider divert.</li>
  <li><strong>#1 or #2 ENG FUEL PRESS (caution):</strong> engine driven pump inlet pressure low. First action: select the AUX PUMP ON for that tank to restore boost. Continue per QRH.</li>
  <li><strong>#1 or #2 FUEL FLTR BYPASS (caution):</strong> filter is clogging; bypass impending. Continue but flag for maintenance — sustained bypass operation means unfiltered fuel.</li>
  <li><strong>FUELING ON (caution) in flight:</strong> indicates the refuel/defuel access door is open or has a sensor fault. Fuel transfer is INHIBITED. Run QRH non-normal; consider divert if a sensor fault cannot be confirmed.</li>
  <li><strong>Aux pump fail (low pressure circle):</strong> pressure-status circle on MFD changes to white-fill. Loss of backup boost; primary ejector still feeding. Brief no-aux-pump landing.</li>
  <li><strong>Fuel imbalance with pump failure:</strong> if the donor tank''s aux pump is U/S, transfer is unavailable from that side. Plan asymmetric handling and fuel reserve.</li>
  <li><strong>Suspected fuel leak:</strong> rapid quantity drop in one tank, increasing imbalance. Compare planned vs actual burn. Run QRH FUEL LEAK; do NOT initiate transfer (transferring fuel into a leaking tank wastes it).</li>
  <li><strong>Loss of DC during refuel:</strong> refuel halts. Restore DC or switch to gravity refuel via overwing adapter.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Cautions, Annunciators, Switchlights',
 '<ul>
  <li><strong>BALANCE (ED, yellow flashing):</strong> imbalance &gt;272 kg. Above the FUEL legend on Engine Display.</li>
  <li><strong>#1 / #2 TANK FUEL LOW (caution):</strong> park brake OFF + collector bay &lt;150 kg + engine running.</li>
  <li><strong>#1 / #2 ENG FUEL PRESS (caution):</strong> engine driven pump inlet pressure low.</li>
  <li><strong>#1 / #2 FUEL FLTR BYPASS (caution):</strong> filter bypass impending.</li>
  <li><strong>FUELING ON (caution):</strong> refuel/defuel access door open. Transfer INHIBITED.</li>
  <li><strong>TANK 1 / TANK 2 AUX PUMP switchlight:</strong> ON segment GREEN when pump active. Auto-illuminates ON during fuel transfer (donor side).</li>
  <li><strong>FUEL TRANSFER switch:</strong> TO TANK 1 / CENTER / TO TANK 2 (lever-latched).</li>
  <li><strong>MFD VALVE annunciator:</strong> CLOSED (white in upper rectangle) or OPEN (reverse video, black on green, lower).</li>
  <li><strong>Aux pump pressure-status circle:</strong> WHITE fill = low/no pressure. GREEN fill = normal.</li>
  <li><strong>Imbalance pointer:</strong> WHITE normal, YELLOW on imbalance condition.</li>
  <li><strong>DUMP VALVE OPEN (amber, refuel panel):</strong> vent/dump valve open during refuel.</li>
  <li><strong>REFUEL SHUTOFF (amber, refuel panel):</strong> illuminates during PRECHECK to confirm high-level shutoff working.</li>
  <li><strong>MASTER VALVE CLOSED (refuel panel):</strong> extinguishes when refuel/defuel valve open.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Total usable fuel:</strong> 5,318 kg.</li>
  <li><strong>Maximum lateral imbalance:</strong> 272 kg.</li>
  <li><strong>FUEL LOW threshold:</strong> approximately 150 kg in the COLLECTOR BAY.</li>
  <li><strong>FUEL LOW trigger conditions:</strong> park brake OFF AND collector bay &lt;150 kg AND engine running. All three required.</li>
  <li><strong>JET B / JP-4 altitude limit:</strong> if TANK temperature &gt;35°C, maximum altitude FL200.</li>
  <li><strong>Total fuel digital display range:</strong> 0 to 15,000 KG in 5-KG increments.</li>
  <li><strong>Tank temperature display range:</strong> -99 to +99°C in 1° increments.</li>
  <li><strong>Tanks per aircraft:</strong> 2 (No.1 left, No.2 right).</li>
  <li><strong>Bays per tank:</strong> 3 (surge, main, collector).</li>
  <li><strong>Engine crossfeed:</strong> NONE. Tank-to-tank transfer only.</li>
  <li><strong>NACA vents per surge bay:</strong> 2 on bottom of wing.</li>
  <li><strong>Float vent valves per surge bay:</strong> 2 outboard + 1 inboard vent line.</li>
  <li><strong>Aux pump type:</strong> AC variable-frequency.</li>
  <li><strong>Refuel power requirement:</strong> DC power.</li>
  <li><strong>Refuel access location:</strong> rear underside of No.2 nacelle.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>5318-USABLE</strong> — total usable fuel 5,318 kg.</li>
  <li><strong>272-IMBALANCE</strong> — max lateral imbalance 272 kg → BALANCE message + yellow dials.</li>
  <li><strong>150-COLLECTOR-LOW</strong> — FUEL LOW caution at ~150 kg in collector bay (with park brake off + engine running).</li>
  <li><strong>3-BAY-TANK</strong> — surge, main, collector (per tank).</li>
  <li><strong>NO-CROSSFEED-TANK-TO-TANK</strong> — Q400 has no engine crossfeed; only tank-to-tank transfer.</li>
  <li><strong>AUX-AC-VF-COLLECTOR</strong> — AC variable-frequency aux pump in each collector bay.</li>
  <li><strong>AUTO-AUX-DURING-TRANSFER</strong> — donor tank''s aux pump auto-activates during transfer; ON segment goes green without push.</li>
  <li><strong>FOHE-HEATS-FILTERS</strong> — Fuel Oil Heat Exchanger filters AND heats fuel before the FMU.</li>
  <li><strong>JP4-35-FL200</strong> — JET B / JP-4 with TANK temp &gt;35°C → max altitude FL200.</li>
  <li><strong>DC-FOR-REFUEL</strong> — DC power required for pressure refueling.</li>
  <li><strong>FUELING-ON-INHIBITS-TRANSFER</strong> — FUELING ON caution blocks tank-to-tank transfer entirely.</li>
  <li><strong>YELLOW-IMBALANCE</strong> — imbalance pointer turns YELLOW (not red).</li>
 </ol>
<p>Imbalance chant: <em>"272 · BALANCE · TRANSFER toward light tank · pump auto · valve OPEN."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
