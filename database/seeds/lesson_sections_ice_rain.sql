-- =============================================================================
-- AviatorTutor — Phase 9: ATA 30 Ice & Rain Protection — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'ice-rain' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'ice-rain-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Detect, De-Ice, Anti-Ice, Wipe',
 '<p>The Q400 is approved for flight into KNOWN ICING conditions. Ice and rain protection on the aeroplane breaks into four distinct functions. <strong>Detect:</strong> two automatic Ice Detector Probes (IDPs) on the front fuselage. <strong>De-ice (airframe):</strong> pneumatic rubber boots on wings, horizontal + vertical stabilisers, and nacelle inlet lips, fed by engine bleed regulated to 18 PSI. <strong>Anti-ice (electric):</strong> pitot/static probes, AOA vanes, engine intake flanges, both windshields, and the pilot side window. <strong>Rain removal:</strong> electrically operated wipers with PARK / OFF / LOW / HIGH plus an alternate pilot-side pushbutton. Layered on top: the SPS receives a REF SPEEDS INCR signal that adjusts stall margins for the iced-airframe condition.</p>',
 'overview', 10),

(@lesson_id, 'Components — Probes, Boots, Heaters, Wipers',
 '<ul>
  <li><strong>Ice Detector Probes (IDP):</strong> 2 probes — left and right side of the front fuselage. Self-deicing via 115 VAC heater after ice trigger. Trigger threshold: 0.5 mm of ice.</li>
  <li><strong>Pneumatic boots:</strong> rubber, bonded to leading edges of wings (extension + outboard + outboard centre + inboard centre + inboard sections), horizontal stabilizer (inboard + outboard), vertical stabilizer (upper + lower), and nacelle inlet lips. Suction holds them flat when not inflated.</li>
  <li><strong>DDVs:</strong> Dual Distributing Valves. Energised open inflates a paired set of boots. Heated valves prevent freezing.</li>
  <li><strong>TMU:</strong> Timer and Monitor Unit. Controls the automatic boot inflation sequence and monitors valve health. DE-ICE TIMER caution on TMU failure.</li>
  <li><strong>Bleed source:</strong> bleed port of each engine, INDEPENDENT of BLEED control switch. Regulated to 18 PSI ± 3.</li>
  <li><strong>BOOT AIR isolator valve:</strong> connects/isolates left and right systems. NORM = open; ISO = closed (individual pressure check or isolate a leak).</li>
  <li><strong>Propeller blade heaters:</strong> electrically heated elements on leading 70% of each blade. 6 blades per prop. 115 VAC variable-frequency bus.</li>
  <li><strong>TMCU:</strong> Timer Monitor Control Unit. One per propeller. Controls heater cycle based on TAT.</li>
  <li><strong>Electric anti-icing elements:</strong> pilot/copilot/standby pitot-static probes, left/right AOA vanes, left/right engine intake flanges, both windshields, pilot side window.</li>
  <li><strong>Windshield wipers:</strong> electrically operated. PARK / OFF / LOW / HIGH speeds. Alternate pilot wiper pushbutton drives pilot wiper at HIGH if normal control fails.</li>
  <li><strong>REF SPEEDS switch:</strong> OFF / INCR. INCR signals the SPS to adjust stall margins for icing.</li>
  <li><strong>Ice Protection panel:</strong> AIRFRAME MODE SELECT, AIRFRAME MANUAL SELECT, BOOT AIR, PROP selector, ENGINE INTAKE OPN HTR (left/right), PITOT STATIC switches, WINDSHIELD modes, WIPER controls, PLT SIDE WDO/HT, REF SPEEDS.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — How Each Layer Works',
 '<h4>Ice detection</h4>
<ul>
  <li>Fully automatic. No flight-deck switch. Operates as soon as 115 VAC is available.</li>
  <li>Trigger: an IDP detects more than 0.5 mm of ice → ED shows ICE DETECTED in flashing white reverse video for 5 seconds.</li>
  <li>Setting REF SPEEDS to INCR clears the reverse video; message stays in normal video while in icing.</li>
  <li>The IDP heats itself with 115 VAC to clear the ice, then resumes detection.</li>
  <li>ICE DETECT FAIL caution: BOTH probes failed (single failure is silent — system is redundant).</li>
</ul>
<h4>Airframe de-icing</h4>
<ul>
  <li>AIRFRAME MODE SELECT: OFF / MANUAL / SLOW / FAST.</li>
  <li>SLOW: 3-minute cycle, 144-second dwell between end of cycle and restart.</li>
  <li>FAST: 1-minute cycle, 24-second dwell between end of cycle and restart.</li>
  <li>Each combination of boots inflates for 6 seconds. 6 combinations = full cycle.</li>
  <li>BOOT INFLATION advisory light (green): pressure ≥ 15 PSI on that segment.</li>
  <li>MANUAL: AIRFRAME MANUAL SELECT 6-detent rotary fires individual pairs. Hold each position until lights confirm; minimum 24-sec dwell before re-inflating same boots.</li>
  <li>DEICE PRESS gauge on copilot panel. NORM = average L+R pressure; ISO = individual pressure per side.</li>
</ul>
<h4>Propeller heating</h4>
<ul>
  <li>PROP selector ON: TMCU controls cycle based on TAT.</li>
  <li>Conditions for cycle: PROP ON + TAT ≤ +5°C + NP > 400 RPM.</li>
  <li>Both props alternate (one then the other) to balance electrical load.</li>
  <li>PROP TEST: each propeller heated 5 sec separately; NP > 400 RPM and AC power required; 30-sec cooldown before retest.</li>
  <li>PROPS advisory light (green): related propeller blade heaters energised.</li>
</ul>
<h4>Windshield + wipers</h4>
<ul>
  <li>WINDSHIELD: WARM / NORM modes. NORM is normal cruise; WARM is pre-warming.</li>
  <li>WIPER speeds: PARK / OFF / LOW / HIGH.</li>
  <li>Alternate pilot wiper pushbutton: pushed in = pilot wiper HIGH (used if main wiper control fails).</li>
  <li>PLT SIDE WDO/HT: OFF / ON for pilot side window heat.</li>
</ul>
<h4>Pitot-static + AOA + intake</h4>
<ul>
  <li>PITOT STATIC switches: 1 / 2 / STBY for each probe.</li>
  <li>AOA vane heaters auto-on with PITOT STATIC heaters.</li>
  <li>ENGINE INTAKE OPN HTR (open heater) for each engine intake flange.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Cruise, Pre-Icing',
 '<h4>Pre-flight</h4>
<ul>
  <li>No ICE DETECT FAIL caution.</li>
  <li>Run PROP selector to TEST — both PROPS advisory lights illuminate for 5 seconds; verify no fault. Wait 30 sec before retest.</li>
  <li>Verify AIRFRAME boot pressure (DEICE PRESS gauge) within 18 ± 3 PSI on engine start.</li>
  <li>Confirm pitot/static heat ON (or per dispatch / OAT).</li>
  <li>Run windshield wipers briefly to confirm operation; return to PARK.</li>
</ul>
<h4>Entering icing</h4>
<ul>
  <li>ICE DETECTED message appears on ED.</li>
  <li>Select REF SPEEDS to INCR — [INCR REF SPEED] message appears below ICE DETECTED. SPS adjusts.</li>
  <li>Select PROP heaters ON.</li>
  <li>Select AIRFRAME MODE SELECT to FAST or SLOW per icing intensity.</li>
  <li>Confirm ENGINE INTAKE heaters ON (open heater) for both intakes.</li>
  <li>WINDSHIELD WARM if needed; WIPER as required.</li>
  <li>During descent / holding / approach, monitor DEICE PRESS — may need higher NL (advance power levers) to maintain 15 PSI.</li>
</ul>
<h4>Cruise scan in icing</h4>
<ol>
  <li>BOOT INFLATION lights cycling green per selected mode.</li>
  <li>PROPS advisory lights cycling per TMCU schedule.</li>
  <li>DEICE PRESS within band.</li>
  <li>No DE-ICE PRESS caution / no DE-ICE TIMER caution / no ICE DETECT FAIL.</li>
  <li>Reference [INCR REF SPEED] message present.</li>
</ol>',
 'normal', 40),

(@lesson_id, 'Abnormal — Failures, Pressure Loss, TMU Fail',
 '<ul>
  <li><strong>ICE DETECT FAIL caution:</strong> BOTH IDPs failed. System lost redundant detection. Continue per QRH; manually monitor for visible ice; treat as in-icing for prop and airframe protection if conditions support.</li>
  <li><strong>DE-ICE PRESS caution:</strong> main de-ice pressure on either side &lt; 15 PSI, OR boot pressure fails to reach 15 PSI after DDV opens, OR boot pressure stays at 15 PSI after DDV closes (stuck). Action: try BOOT AIR ISO to isolate possible leak side; cycle MANUAL to verify individual sides; advance NL to raise bleed if low.</li>
  <li><strong>DE-ICE TIMER caution:</strong> TMU failure (auto sequencer / logic / input disagreement). Use AIRFRAME MANUAL SELECT to cycle boots manually through 6 detents. Each pair held until both lights illuminate; 24-sec dwell minimum before re-inflating same pair.</li>
  <li><strong>Pneumatic line rupture:</strong> select BOOT AIR ISO to isolate the failed side. Stabilizer boots are pneumatically cross-connected to ensure pressure on both sides.</li>
  <li><strong>PROP heater fault (single propeller):</strong> PROPS advisory light fails to illuminate during expected cycle, or TMCU caution. Run QRH; one-prop ice protection lost; consider exit from icing.</li>
  <li><strong>WINDSHIELD failure:</strong> NORM heat lost. Switch to WARM; if still inoperative, use the alternate pilot wiper pushbutton for visibility; brief approach for limited forward vis.</li>
  <li><strong>Pitot/Static heater failure:</strong> airspeed indication unreliable on affected side. Switch source; treat as IAS unreliable per QRH.</li>
  <li><strong>SAT below +5°C with PROP heaters not cycling:</strong> verify TAT (not SAT) is below +5 — at high speed TAT can exceed SAT by many degrees. Visible ice on airframe or in conditions implies system should function regardless of indicated SAT.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Lights, Messages, Gauges',
 '<ul>
  <li><strong>ICE DETECTED (ED, white):</strong> flashing reverse video for 5 sec, then normal video. One or both IDPs detected &gt; 0.5 mm ice.</li>
  <li><strong>[INCR REF SPEED] (ED, white):</strong> SPS adjusted for icing — REF SPEEDS at INCR position.</li>
  <li><strong>ICE DETECT FAIL (caution):</strong> both IDPs failed.</li>
  <li><strong>DE-ICE PRESS (caution):</strong> system pressure low or DDV pressure mismatch.</li>
  <li><strong>DE-ICE TIMER (caution):</strong> TMU failure — auto sequencer or logic.</li>
  <li><strong>BOOT INFLATION advisory (green, per segment):</strong> related boot pressure ≥ 15 PSI.</li>
  <li><strong>PROPS advisory (green, per propeller):</strong> all 6 blade heaters energised on that propeller.</li>
  <li><strong>DEICE PRESS gauge (copilot panel):</strong> 18 ± 3 PSI normal. NORM mode shows L+R average; ISO mode shows individual.</li>
  <li><strong>AIRFRAME MODE SELECT switch:</strong> OFF / MANUAL / SLOW / FAST.</li>
  <li><strong>AIRFRAME MANUAL SELECT switch:</strong> 8 positions (2 OFFs + 6 boot detents).</li>
  <li><strong>BOOT AIR switch:</strong> NORM / ISO.</li>
  <li><strong>PROP selector:</strong> TEST / OFF / ON.</li>
  <li><strong>ENGINE INTAKE OPN HTR switches:</strong> OFF / ON each side.</li>
  <li><strong>PITOT STATIC switches:</strong> 1 / 2 / STBY.</li>
  <li><strong>WINDSHIELD switch:</strong> WARM / NORM.</li>
  <li><strong>WIPER switch:</strong> PARK / OFF / LOW / HIGH.</li>
  <li><strong>PLT SIDE WDO/HT switch:</strong> OFF / ON.</li>
  <li><strong>REF SPEEDS switch:</strong> OFF / INCR.</li>
  <li><strong>Alternate Pilot Wiper pushbutton (guarded):</strong> in = pilot wiper HIGH; out = stop.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>IDP trigger threshold:</strong> &gt; 0.5 mm of ice on probe.</li>
  <li><strong>Number of IDPs:</strong> 2 (left + right front fuselage).</li>
  <li><strong>Boot regulated pressure:</strong> 18 ± 3 PSI.</li>
  <li><strong>BOOT INFLATION advisory threshold:</strong> ≥ 15 PSI.</li>
  <li><strong>DEICE PRESS caution threshold:</strong> &lt; 15 PSI on main side OR DDV pressure mismatch.</li>
  <li><strong>SLOW mode:</strong> 3-minute cycle, 144-second dwell between end and restart.</li>
  <li><strong>FAST mode:</strong> 1-minute cycle, 24-second dwell.</li>
  <li><strong>Boot inflation per combination:</strong> 6 seconds.</li>
  <li><strong>Boot combinations per cycle:</strong> 6.</li>
  <li><strong>Manual mode minimum dwell:</strong> 24 seconds before re-inflating same pair.</li>
  <li><strong>Prop heater minimum NP:</strong> &gt; 400 RPM.</li>
  <li><strong>Prop heater TAT threshold:</strong> ≤ +5°C.</li>
  <li><strong>PROP TEST duration:</strong> 5 seconds per propeller.</li>
  <li><strong>PROP TEST cooldown:</strong> 30 seconds before retest.</li>
  <li><strong>Prop heater cycle (TAT -7 to +5°C):</strong> 12 sec ON / 78 sec OFF (default mode).</li>
  <li><strong>Prop heater cycle (TAT ≤ -22°C):</strong> 92 sec ON / 108 sec OFF (cold mode).</li>
  <li><strong>DDV/check-valve heater auto-on threshold:</strong> SAT &lt; +5°C with MODE SELECT at OFF / SLOW / FAST.</li>
  <li><strong>Heater element coverage on prop blade:</strong> 70% of blade.</li>
  <li><strong>Blades per propeller:</strong> 6, all heated simultaneously.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>2-IDP-AUTO</strong> — 2 ice detector probes, fully automatic, no cockpit switch.</li>
  <li><strong>0.5-MM-ICE</strong> — IDP trigger threshold: more than 0.5 mm of ice.</li>
  <li><strong>18-PSI-BOOTS-15-LIGHT</strong> — boots regulated at 18 ± 3 PSI; BOOT INFLATION light at ≥ 15 PSI.</li>
  <li><strong>SLOW-3-MIN-FAST-1-MIN</strong> — SLOW = 3-minute cycle (144-sec dwell); FAST = 1-minute cycle (24-sec dwell).</li>
  <li><strong>6-COMB-6-SEC</strong> — 6 boot combinations per cycle, 6 seconds inflation each.</li>
  <li><strong>BOOTS-LIPS-WINGS-TAIL</strong> — pneumatic boots on wings + horizontal/vertical stabilisers + nacelle inlet lips.</li>
  <li><strong>ELEC-PROBES-WINDOWS-INTAKE-AOA</strong> — electric anti-ice on probes + windshield + intake flanges + AOA vanes + side window.</li>
  <li><strong>TAT-5-NP-400</strong> — prop heaters need TAT ≤ +5°C AND NP &gt; 400 RPM.</li>
  <li><strong>30-SEC-PROP-TEST-COOL</strong> — PROP TEST 5 sec per prop; mandatory 30-sec cooldown before retest.</li>
  <li><strong>BLEED-INDEPENDENT</strong> — boot air comes from engine bleed REGARDLESS of BLEED switch position.</li>
  <li><strong>BOTH-IDPS-FAIL-CAUTION</strong> — ICE DETECT FAIL only when BOTH probes fail; single is silent.</li>
  <li><strong>INCR-FOR-ICING</strong> — REF SPEEDS to INCR signals SPS to adjust stall margin.</li>
  <li><strong>NL-FOR-PRESSURE</strong> — in descent / hold / approach, advance POWER levers to maintain 15 PSI minimum.</li>
 </ol>
<p>Icing-entry chant: <em>"REF SPEEDS INCR · PROPS ON · AIRFRAME FAST · INTAKES ON · monitor DEICE PRESS."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
