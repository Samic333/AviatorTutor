-- =============================================================================
-- AviatorTutor — Phase 16: ATA 61 Propeller — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'propeller' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'propeller-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — PEC + PCU + OSG + Counterweights',
 '<p>Q400 propeller control is a layered electronic + hydromechanical system. The PEC (Propeller Electronic Control) is a dual-channel microprocessor that commands the PCU (Pitch Control Unit) — a hydromechanical valve that meters HP engine oil to the fine or coarse pitch chambers of the propeller pitch change cylinder. Counterweighted blades naturally seek HIGH pitch in flight (counterweight effort dominates centrifugal twisting moment) — so a hydraulic loss autocoarsens the blades to a safe windmilling pitch with low drag. Over-speed protection has two layers: hydraulic OSG at 1071 RPM (105%) and electronic FADEC NP at 1122 RPM. The hydraulic section is locked out in reverse so the FADEC takes priority. Flight fine stop logic (16° hard + 16.5° soft) keeps in-flight blade angles above the counterweight effort threshold. Autofeather watches both engines on takeoff and feathers a failed engine after a 3-second confirm window.</p>',
 'overview', 10),
(@lesson_id, 'Components — PEC, PCU, Pumps, Sensors',
 '<ul>
  <li><strong>PEC:</strong> Propeller Electronic Control. Dual-channel microprocessor in each engine nacelle. Manages constant-speed governing, beta, reverse, autofeather, AUPC, UPTRIM.</li>
  <li><strong>PCU:</strong> Pitch Control Unit. Hydromechanical, electrically commanded by PEC. Two-stage servo valve meters HP engine oil to fine or coarse pitch chambers.</li>
  <li><strong>HP PCU pump:</strong> driven from reduction gearbox. Supplies HP oil to PCU.</li>
  <li><strong>Propeller Overspeed Governor (OSG):</strong> independent flyweight design, driven from HP pump driver gear. Hydraulic overspeed protection at ~1071 RPM (105%).</li>
  <li><strong>Auxiliary Propeller Feathering Pump:</strong> 28 VDC electrical motor + external gear pump. Independent feather oil source. Used for autofeather, alternate feather, manual feather, and maintenance feather/unfeather.</li>
  <li><strong>Magnetic Pickup Unit (MPU):</strong> propeller speed sensor. Signal to PEC for governing + synchrophasing + ANVS balance monitoring.</li>
  <li><strong>Counterweighted blades:</strong> 6 composite blades per propeller. Counterweight phased to bias toward HIGH pitch in flight.</li>
  <li><strong>Pitch change cylinder:</strong> with fine and coarse pitch chambers. Receives HP oil from PCU.</li>
  <li><strong>Ground Beta Enable valve (GBE):</strong> locks out OSG hydraulic section on ground beta. Tested by scheduled OSG test.</li>
  <li><strong>FADEC NP overspeed circuit:</strong> electronic OSG layer at ~1122 RPM. Signals FMU to reduce fuel.</li>
  <li><strong>AUTOFEATHER switchlight:</strong> on engine instrument panel. Selected ON for takeoff only.</li>
  <li><strong>SELECT + ARM lights:</strong> on AUTOFEATHER switchlight. SELECT illuminates when armed; ARM lights when both engines torque >50% + PLA >60°.</li>
  <li><strong>PROPELLER GROUND RANGE lights:</strong> illuminate when blade angle below 16° (PLA below Flight Idle).</li>
  <li><strong>Beta warning horn:</strong> sounds if PLA brought below Flight Idle gate in flight.</li>
  <li><strong>Detent on PLA quadrant:</strong> prevents unintentional movement of PLA below Flight Idle in flight.</li>
 </ul>',
 'components', 20),
(@lesson_id, 'Operation — Constant Speed, Beta, Reverse, Feather',
 '<h4>Constant Speed Mode (in flight)</h4>
<ul>
  <li>Entered when prop speed reaches 850 / 900 / 1020 RPM per CL selection.</li>
  <li>PEC controls servo valve to balance net coarse-seeking moment from counterweights.</li>
  <li>HP oil for governing passes through OSG before reaching servo valve.</li>
  <li>Underspeed: more oil to fine pitch.</li>
  <li>Overspeed: more oil to coarse pitch.</li>
</ul>
<h4>Beta Range (PLA below Flight Idle, ground)</h4>
<ul>
  <li>PEC directs servo valve to meter oil for desired blade angle (closed-loop blade angle control).</li>
  <li>NP underspeed governed at 660 RPM by FADEC + engine fuel system.</li>
  <li>Detent prevents inadvertent below-Flight-Idle in flight; horn if gate raised in flight.</li>
  <li>Below 16° blade angle requires PLA below Flight Idle AND WOW.</li>
  <li>PROPELLER GROUND RANGE lights illuminate.</li>
  <li>GBE valve locks out OSG hydraulic section.</li>
</ul>
<h4>Reverse Speed Control</h4>
<ul>
  <li>Closed-loop propeller RPM control 660–950 RPM.</li>
  <li>Engine schedules fuel per PLA, max 1500 SHP.</li>
  <li>At low airspeeds may reach max reverse stop; engine overspeed governor limits speed up to 1020 RPM.</li>
  <li>Hydraulic OSG section LOCKED OUT in reverse — FADEC NP overspeed is primary protection.</li>
</ul>
<h4>Overspeed Protection</h4>
<ul>
  <li>Hydraulic OSG: drops HP oil at ~1071 RPM (105%). Pitch coarsens via counterweights. Reconnects below threshold. Stable governing at 1071 RPM until cause removed.</li>
  <li>Electronic FADEC NP: signals FMU to reduce fuel at ~1122 RPM. Reduces engine power and prop RPM. Restores fuel below threshold.</li>
</ul>
<h4>Synchrophasing</h4>
<ul>
  <li>Active when both prop speeds within predetermined difference, in flight.</li>
  <li>PEC enters synchrophase mode. MPU signals time the phase difference between master and slave propellers.</li>
  <li>CLA position determines phase demand.</li>
  <li>NOT active at takeoff (high-power transients).</li>
</ul>
<h4>Autofeather</h4>
<ul>
  <li>AUTOFEATHER switchlight ON for takeoff only. SELECT light + A/F SELECT on ED.</li>
  <li>ARM: both engine torques >50% AND both PLAs beyond 60°.</li>
  <li>TRIGGER: ONE engine torque <25% OR Np <816 (80%) for at least 3 seconds.</li>
  <li>Actions: A/F ARM light goes out, AUX FEATHER PUMP energized, prop feathers, UPTRIM signal to operating engine FADEC.</li>
</ul>',
 'operation', 30),
(@lesson_id, 'Normal — Pre-Flight, T-O, Cruise, Approach',
 '<h4>Pre-flight</h4>
<ul>
  <li>Run PROP O''SPEED GOVERNOR test on ground via Pilots Side Panel test switch — confirms hydraulic OSG functional.</li>
  <li>Run propeller heater test (Phase 9 Ice & Rain).</li>
  <li>Brief autofeather strategy — ON for takeoff per company SOP.</li>
</ul>
<h4>Takeoff</h4>
<ul>
  <li>Autofeather ON. AUTOFEATHER switchlight: SELECT illuminated; ARM illuminates as torque + PLA conditions met during takeoff roll.</li>
  <li>Both PLAs to TAKEOFF (rating detent). Confirm A/F ARM illuminated.</li>
  <li>Synchrophasing inactive at takeoff (designed).</li>
  <li>Climb-out: maintain torque + PLA. A/F ARM remains until conditions removed.</li>
</ul>
<h4>Cruise</h4>
<ul>
  <li>CL set per cruise rating (typically 850 RPM low / 900 RPM mid / 1020 high).</li>
  <li>Synchrophasing active — cabin quieter than non-synced.</li>
  <li>Autofeather typically OFF in cruise (per company SOP).</li>
</ul>
<h4>Approach</h4>
<ul>
  <li>CL to 1020 RPM (high) for landing.</li>
  <li>Autofeather typically OFF (some operators ON).</li>
  <li>Below Flight Idle on touchdown: PROPELLER GROUND RANGE lights illuminate; GBE locks out OSG.</li>
  <li>Reverse: PLA into reverse arc; 660–950 RPM range; 1500 SHP max.</li>
</ul>',
 'normal', 40),
(@lesson_id, 'Abnormal — Engine Failure, Overspeed, GBE Fault',
 '<ul>
  <li><strong>Engine failure on takeoff with autofeather ON:</strong> autofeather triggers when ONE engine torque drops <25% or Np <816 for 3 seconds. Prop auto-feathers. UPTRIM increases operating engine power. A/F ARM light goes out. Continue per QRH ENGINE FAILURE non-normal.</li>
  <li><strong>Engine failure with autofeather OFF:</strong> manual feather via condition lever to FEATHER, OR alternate feather switch. Verify prop feathered (low drag, low Np).</li>
  <li><strong>Hydraulic OSG action (1071 RPM):</strong> overspeed event. Cycle of HP oil drop + reconnect. Stable governing at ~1071 RPM until cause removed.</li>
  <li><strong>Electronic FADEC overspeed (1122 RPM):</strong> FMU reduces fuel. Engine power drops, prop RPM follows. Investigate cause; do not assume PEC is healthy.</li>
  <li><strong>HP oil loss in flight:</strong> blades autocoarsen via counterweight effort. Safe windmilling pitch, low drag.</li>
  <li><strong>HP oil loss in reverse:</strong> blades go toward MAX REVERSE blade angle. Different from flight loss.</li>
  <li><strong>GBE valve fails to move to in-flight position:</strong> OSG locked out → loss of overspeed protection. Caught by scheduled OSG test on the ground.</li>
  <li><strong>Beta warning horn in flight:</strong> PLA gate has been raised. Move PLA back above Flight Idle immediately. Do NOT operate below Flight Idle in flight.</li>
  <li><strong>PEC fault:</strong> AUPC (Automatic Underspeed Propeller Control) makes propeller operate on overspeed governor in event of drive coarse failure. UPTRIM command to operating engine FADEC.</li>
  <li><strong>Synchrophasing fail:</strong> cabin noise increases. No flight-safety effect. Defer per MEL.</li>
  <li><strong>Autofeather fail to arm:</strong> verify torque both >50%, PLA both >60°. If conditions met but A/F ARM still off, investigate; may need to defer with autofeather inoperative (significant operational restriction).</li>
 </ul>',
 'abnormal', 50),
(@lesson_id, 'Indications — Lights, Switchlights, Test',
 '<ul>
  <li><strong>AUTOFEATHER switchlight (engine instrument panel):</strong> SELECT (illuminated when ON), ARM (illuminated when conditions met).</li>
  <li><strong>A/F SELECT on ED:</strong> displays autofeather selection state.</li>
  <li><strong>PROPELLER GROUND RANGE lights:</strong> illuminate when blade angles below 16° (PLA below Flight Idle).</li>
  <li><strong>Beta warning horn:</strong> sounds if PLA gate raised in flight.</li>
  <li><strong>PROP O''SPEED GOVERNOR test switch:</strong> on Pilots Side Panel. Tests hydraulic OSG.</li>
  <li><strong>Condition Lever positions:</strong> START & FEATHER / MIN 850 / 900 / MAX 1020 / FUEL OFF.</li>
  <li><strong>PLA positions:</strong> MAX REV / DISC / FLIGHT IDLE / 0 / 5 / 10 / 15 / 35 / RATING DETENT.</li>
  <li><strong>ALT FTHR switchlight:</strong> alternate feather for each engine.</li>
  <li><strong>FTHR (Feather) on switchlight:</strong> autofeather feathered the prop.</li>
  <li><strong>UPTRIM TRQ on ED:</strong> operating engine power uptrim during autofeather event.</li>
  <li><strong>MTOP indication:</strong> Maximum Takeoff Power state.</li>
 </ul>',
 'indications', 60),
(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Blades per propeller:</strong> 6 composite.</li>
  <li><strong>Flight Fine Stop — hard hydraulic:</strong> 16°.</li>
  <li><strong>Flight Fine Stop — soft (PEC) when PLA at/above Flight Idle:</strong> 16.5°.</li>
  <li><strong>Hydraulic OSG threshold:</strong> ~1071 RPM (105%).</li>
  <li><strong>Electronic FADEC NP overspeed threshold:</strong> ~1122 RPM.</li>
  <li><strong>Constant Speed entry RPMs:</strong> 850 / 900 / 1020 per CL position.</li>
  <li><strong>Beta NP underspeed governing:</strong> 660 RPM (FADEC + fuel system).</li>
  <li><strong>Reverse normal range:</strong> 660–950 RPM.</li>
  <li><strong>Reverse max SHP:</strong> 1500 SHP.</li>
  <li><strong>Reverse engine OSG limit at max reverse stop:</strong> 1020 RPM.</li>
  <li><strong>Autofeather ARM thresholds:</strong> both engine torque >50% AND both PLA beyond 60°.</li>
  <li><strong>Autofeather TRIGGER thresholds:</strong> ONE torque <25% OR Np <816 (80%) for at least 3 SECONDS.</li>
  <li><strong>OSG hydraulic locked out in:</strong> REVERSE only.</li>
  <li><strong>Synchrophasing active:</strong> in flight, both speeds within predetermined difference. NOT at takeoff.</li>
  <li><strong>Feather pump:</strong> 28 VDC electrical motor + external gear pump.</li>
  <li><strong>HP loss in flight effect:</strong> blades autocoarsen to safe windmilling pitch (low drag).</li>
  <li><strong>HP loss in reverse effect:</strong> blades to MAX REVERSE blade angle.</li>
 </ul>',
 'limitations', 70),
(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>6-BLADE-COMPOSITE</strong> — 6 composite blades per propeller, counterweighted.</li>
  <li><strong>AUTOCOARSEN-HP-LOSS</strong> — HP loss in flight → blades autocoarsen to safe windmill pitch.</li>
  <li><strong>16-HARD-16.5-SOFT</strong> — flight fine stop: 16° hyd hard / 16.5° PEC soft (PLA at/above Flight Idle).</li>
  <li><strong>1071-HYD-1122-ELEC</strong> — OSG: 1071 RPM hyd / 1122 RPM electronic FADEC.</li>
  <li><strong>850-900-1020-CL</strong> — Constant Speed entry RPMs per CL: 850 / 900 / 1020.</li>
  <li><strong>BETA-660-NP</strong> — beta range NP underspeed governed at 660 RPM by FADEC.</li>
  <li><strong>REVERSE-660-950-1500</strong> — reverse 660-950 RPM normal, max 1500 SHP, up to 1020 RPM at max stop.</li>
  <li><strong>OSG-LOCKED-REVERSE</strong> — hydraulic OSG locked out in reverse; FADEC is primary.</li>
  <li><strong>GBE-LOCKS-OSG-GROUND-BETA</strong> — Ground Beta Enable valve locks out OSG on ground beta.</li>
  <li><strong>SYNC-NO-TAKEOFF</strong> — synchrophasing not active at takeoff.</li>
  <li><strong>AF-50-25-816-3SEC</strong> — autofeather: ARM at both torque >50% + PLA >60°; TRIGGER at one torque <25% OR Np <816 (80%) for ≥3 sec.</li>
  <li><strong>AF-UPTRIMS-OPERATING</strong> — autofeather sends UPTRIM command to operating engine FADEC.</li>
  <li><strong>BETA-HORN-IN-FLIGHT</strong> — beta warning horn sounds if PLA below Flight Idle gate in flight.</li>
 </ol>
<p>Engine failure chant: <em>"AUTOFEATHER ARM · torque < 25% · 3 seconds · prop feathers · UPTRIM operating."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
