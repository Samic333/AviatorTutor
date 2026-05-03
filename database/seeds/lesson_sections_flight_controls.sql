-- =============================================================================
-- AviatorTutor — Phase 7: ATA 27 Flight Controls — lesson sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'flight-controls' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'flight-controls-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Primary, Secondary, Spoilers, SPS',
 '<p>Q400 flight controls divide cleanly into three groups. <strong>Primary controls:</strong> ailerons (roll), elevators (pitch), rudder (yaw). <strong>Secondary controls:</strong> flaps. <strong>Spoilers:</strong> roll assistant in flight, lift dump on the ground. The mechanical-vs-hydraulic split is the design fundamental — ailerons are MECHANICAL/cable-driven, while elevators, rudder, and spoilers are HYDRAULICALLY powered (the Powered Flight Control Surfaces, or PFCS). The brain of the system is the FCECU (Flight Control Electronic Control Unit), which regulates rudder authority and elevator feel as airspeed changes, manages elevator and rudder PCU redundancy, runs spoiler airspeed logic, prioritises pitch trim signals, and watches for asymmetric airspeed. Layered on top of all of this is the Stall Protection System (SPS): two SPMs driving stick shakers and a stick pusher.</p>',
 'overview', 10),

(@lesson_id, 'Components — Surfaces, PCUs, Brains',
 '<ul>
  <li><strong>Ailerons:</strong> 1 per wing, ±17° deflection (handwheel turns 70° L/R of centre). Cable / mechanical. Geared tab on each surface; ground-adjustable trim tab on right aileron.</li>
  <li><strong>Spoilers:</strong> 4 total — 2 INBOARD (No.1 hyd) + 2 OUTBOARD (No.2 hyd). Hydraulically powered PCUs. Four LVDTs feedback to FCECU + IFC.</li>
  <li><strong>Elevators:</strong> 2 surfaces, each driven by 3 PCUs — outboard (No.1 hyd, active), centre (No.2 hyd, active), inboard (No.3 hyd, STANDBY). PFTUs (Pitch Feel and Trim Units) in vertical stab provide artificial feel.</li>
  <li><strong>Rudder:</strong> 2 PCUs — RUD 1 PUSH OFF = LOWER PCU; RUD 2 PUSH OFF = UPPER PCU. Rudder authority limited by FCECU as airspeed increases.</li>
  <li><strong>Flaps:</strong> 2 single-slotted Fowler flaps per wing (inboard + outboard). Driven by FPU (Flap Power Unit) on No.1 hyd; controlled by FCU (Flap Control Unit). 4 actuators per wing, 2 per flap, bi-directional no-backs.</li>
  <li><strong>FCECU:</strong> Flight Control Electronic Control Unit. Regulates rudder authority, elevator feel, trim rates; manages PCU redundancy; watches IAS mismatch.</li>
  <li><strong>ATCU:</strong> Aileron Trim and Centering Unit — connects aileron forward quadrant to aileron trim actuator. Centres handwheels at zero trim.</li>
  <li><strong>PFTU:</strong> 2 units, in vertical stab. Artificial pitch feel + 2 pitch trim actuators.</li>
  <li><strong>Yaw damper:</strong> ±4.5° max authority. Needs BOTH FGM #1 and FGM #2.</li>
  <li><strong>SPMs:</strong> 2 Stall Protection Modules. Stick shaker per pilot column + shared stick pusher. Inputs: AoA, flap position, Mach, torque, icing.</li>
  <li><strong>Roll Disconnect handle:</strong> in centre pedestal. Pulled out + 90° splits aileron / spoiler systems.</li>
  <li><strong>Pitch Disconnect handle:</strong> on left side of centre console. Pulled out + 90° splits pilot / copilot control columns.</li>
  <li><strong>CONTROL LOCK lever:</strong> aileron gust lock. FWD = OFF; AFT = ON. Restricts power-lever travel when ON.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — How Each Axis Works',
 '<h4>Roll</h4>
<ul>
  <li>Pilot wheel turns spoilers; copilot wheel turns ailerons. Both interconnected normally so either wheel commands both.</li>
  <li>Aileron deflection ±17° from neutral; geared tab provides aerodynamic assist.</li>
  <li>Above 170 KIAS the FCECU DISABLES outboard spoilers (only inboards active for roll). Below 165 KIAS BOTH inboard + outboard active.</li>
  <li>Aileron trim via ATCU + electric trim actuator (Left ESS bus, breakers G8 / H8).</li>
</ul>
<h4>Pitch</h4>
<ul>
  <li>Pilot column → left elevator; copilot column → right elevator. Connected by pitch disconnect clutch — both columns operate together normally.</li>
  <li>Each elevator has 3 PCUs: outboard (No.1 hyd), centre (No.2 hyd), inboard (No.3 hyd, STANDBY).</li>
  <li>HYD #3 ISOL VLV pushbutton manually activates inboard PCUs (illuminates ELEVATOR PRESS if No.1 and No.2 are healthy). Auto-activates on No.1 or No.2 failure.</li>
  <li>Pitch trim priority: pilot &gt; copilot &gt; autopilot. Trim rate HIGH below 150 KIAS, LOW above 250 KIAS, scaled in between.</li>
  <li>3-second rule: pitch trim held &gt;3 sec triggers aural warning + ELEVATOR TRIM SHUTOFF illuminates.</li>
  <li>Flap auto-trim active 15°–35° flap range, AP off, &lt;180 KIAS, no manual trim. Nose-down on extension; nose-up on retraction.</li>
</ul>
<h4>Yaw</h4>
<ul>
  <li>Rudder pedals → 2 PCUs (lower + upper). Rudder feel trim and summing unit gives artificial pedal force; sums pilot + yaw damper inputs.</li>
  <li>FCECU reduces PCU hydraulic pressure as airspeed increases — limits rudder authority. ADUs supply airspeed.</li>
  <li>Yaw damper ±4.5° max. Needs both FGM #1 and FGM #2.</li>
  <li>Rudder trim via centre-console knob; first detent slow, second detent fast. Powered from Left ESS bus (RUD TRIM ACT F7 / RUD TRIM IND G7).</li>
</ul>
<h4>Spoiler ground mode (lift dump)</h4>
<ul>
  <li>Three conditions all required: FLIGHT/TAXI in FLIGHT, both power levers below FLT IDLE +12°, WOW on both main gear.</li>
  <li>Two lift-dump valves per system (inboard + outboard). Both must open in series.</li>
  <li>If a lift-dump valve fails, ROLL SPLR INBD/OUTBD GND caution illuminates after a 5-second delay.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Indications and Cruise Discipline',
 '<h4>MFD PFCS displays (Permanent Systems Data Area)</h4>
<ul>
  <li>ELEVATOR position indicator (white): left + right elevator deflection.</li>
  <li>RUDDER position indicator (white).</li>
  <li>SPOILER position indicator (white): inboard + outboard panels.</li>
  <li>Trim indicators on centre console (aileron, rudder, elevator).</li>
</ul>
<h4>Pre-flight</h4>
<ul>
  <li>CONTROL LOCK lever — FWD (OFF) before pushback. Confirm full and free flight controls.</li>
  <li>Stall warn test daily: STALL WARN switch TEST1 (held &gt;10 sec) then TEST2 (held &gt;10 sec).</li>
  <li>Rudder pedals adjustment to comfortable position; brake heels-on-floor before pushing top-of-pedal.</li>
  <li>Elevator trim in TAKE-OFF range (white band on indicator) — aural alert if power levers advanced &gt;FLT IDLE +12° outside the range.</li>
  <li>Yaw damper armed via YD pushbutton; pointer on left and right of YD switchlight illuminates when engaged.</li>
</ul>
<h4>Cruise scan</h4>
<ol>
  <li>No PFCS caution illuminated.</li>
  <li>Trim indicators within expected range.</li>
  <li>YD pointer indicators on (yaw damper engaged).</li>
  <li>MFD PFCS positions match flight path — coordinated turns, ball centred.</li>
</ol>',
 'normal', 40),

(@lesson_id, 'Abnormal — Jams, PCU Failures, IAS Mismatch',
 '<ul>
  <li><strong>Roll jam:</strong> ROLL DISC handle — pull out and turn 90°. Pilot keeps SPOILERS only; copilot keeps AILERONS only. The pilot with the UNJAMMED wheel has roll control.</li>
  <li><strong>Pitch jam:</strong> Pitch disconnect handle — pull out and turn 90°. Splits pilot / copilot columns. Pilot with the FREE column has pitch control.</li>
  <li><strong>Rudder PCU jam:</strong> push the corresponding RUD 1 or RUD 2 PUSH OFF switchlight. ONLY ONE at a time per AFM 4.18.12. If both pressed inadvertently, OFF legends extinguish, both PUSH legends illuminate, previously-pushed PCU re-pressurises — push the NON-jammed switch again to recover.</li>
  <li><strong>Outboard spoiler logic failure:</strong> SPLR OUTBD caution if outboards fail to disable above 185 KIAS or fail to enable below 150 KIAS, OR No.2 hyd lost, OR IAS mismatch &gt;17 kts.</li>
  <li><strong>Roll spoiler hydraulic loss:</strong> ROLL SPLR INBD HYD if No.1 pressure &lt;900 PSI (or SPLR1 PUSH OFF pressed). ROLL SPLR OUTBD HYD if No.2 pressure &lt;900 PSI AND airspeed &lt;165 KIAS, or SPLR2 PUSH OFF pressed.</li>
  <li><strong>Lift-dump valve failure on landing:</strong> ROLL SPLR INBD GND or ROLL SPLR OUTBD GND caution after 5-second delay.</li>
  <li><strong>Elevator pressure loss:</strong> ELEVATOR PRESS caution — No.1, No.2, AND No.3 hydraulic systems all supplying / unable to maintain pressure to elevator PCUs. Reduce airspeed below 200 KIAS.</li>
  <li><strong>Elevator asymmetry:</strong> ELEVATOR ASYMMETRY caution — left and right elevators mismatch. Reduce airspeed below 200 KIAS.</li>
  <li><strong>Pitch feel actuator failure:</strong> ELEVATOR FEEL caution. FCECU holds failed actuator at last valid position; surviving actuator continues. Reduce to 200 KIAS.</li>
  <li><strong>Pitch trim runaway / 3-sec rule:</strong> ELEVATOR TRIM SHUTOFF switchlight + aural clicking. Push either left or right ELEVATOR TRIM SHUTOFF switchlight to deactivate trim.</li>
  <li><strong>IAS MISMATCH (&gt;17 kts):</strong> RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM cautions ALL illuminate together. Reduce airspeed below 200 KIAS. Identify which ADU is correct using standby/cross-check.</li>
  <li><strong>Aileron trim runaway:</strong> limit switch shuts off trim power at maximum input; mechanical stop as backup.</li>
  <li><strong>Yaw damper failure:</strong> YD will not engage if either FGM input invalid. Hand-fly the rudder coordination; brief approach for higher pedal workload in turbulence.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Caution Lights and Switches',
 '<ul>
  <li><strong>RUD 1 PUSH OFF (amber):</strong> jam in lower rudder PCU.</li>
  <li><strong>RUD 2 PUSH OFF (amber):</strong> jam in upper rudder PCU.</li>
  <li><strong>SPLR 1 PUSH OFF (amber):</strong> jam in inboard spoiler PCU/linkage.</li>
  <li><strong>SPLR 2 PUSH OFF (amber):</strong> jam in outboard spoiler PCU/linkage.</li>
  <li><strong>YD pushbutton (amber):</strong> yaw damper engage/disengage; pointers indicate engaged.</li>
  <li><strong>#1 RUD HYD / #2 RUD HYD (caution):</strong> hydraulic pressure unavailable, FCECU shut down a PCU, OR the corresponding RUD PUSH OFF switch was pressed.</li>
  <li><strong>RUD CTRL (caution):</strong> FCECU unable to control rudder pressure, OR No.1 + No.2 hyd both failed, OR IAS mismatch &gt;17 kts.</li>
  <li><strong>ROLL SPLR INBD HYD / ROLL SPLR OUTBD HYD (caution):</strong> No.1/No.2 hyd pressure low, or SPLR1/SPLR2 PUSH OFF pressed.</li>
  <li><strong>ROLL SPLR INBD GND / ROLL SPLR OUTBD GND (caution):</strong> lift-dump valve failure or spoilers fail to extend on touchdown.</li>
  <li><strong>SPLR OUTBD (caution):</strong> outboard spoiler airspeed-disable logic failure or IAS mismatch.</li>
  <li><strong>ROLL OUTBD / ROLL INBD (advisory, glareshield):</strong> spoilers extended on touchdown — appears with FLIGHT/TAXI in FLIGHT, on the ground.</li>
  <li><strong>FLIGHT/TAXI switch:</strong> FLIGHT (locked, required for takeoff); TAXI (auto-returns to FLIGHT when power levers exceed FLT IDLE +12°).</li>
  <li><strong>ELEVATOR PRESS (caution):</strong> elevator hydraulic loss. Reduce airspeed &lt;200 KIAS.</li>
  <li><strong>ELEVATOR ASYMMETRY (caution):</strong> left/right elevator mismatch. Reduce airspeed &lt;200 KIAS.</li>
  <li><strong>ELEVATOR FEEL (caution):</strong> pitch feel actuator failed or IAS mismatch. Reduce &lt;200 KIAS.</li>
  <li><strong>PITCH TRIM (caution):</strong> FCECU loss of pitch trim control or IAS mismatch.</li>
  <li><strong>ELEVATOR TRIM SHUTOFF switchlight:</strong> 3-sec rule trigger or trim runaway.</li>
  <li><strong>MISTRIM [TRIM L/R WING DN] PFD message:</strong> autopilot mistrim — disengage AP, retrim manually.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Aileron deflection:</strong> ±17° from neutral (handwheel ±70°).</li>
  <li><strong>Yaw damper authority:</strong> ±4.5° max.</li>
  <li><strong>Outboard spoiler airspeed lockout:</strong> disabled above 170 KIAS; re-enabled below 165 KIAS. Cautions outside 150–185 KIAS band.</li>
  <li><strong>Roll spoiler hydraulic threshold:</strong> caution if No.1/No.2 below 900 PSI.</li>
  <li><strong>IAS mismatch threshold:</strong> ±17 kts.</li>
  <li><strong>Reduced airspeed limit on PFCS abnormals:</strong> 200 KIAS (RUD CTRL, ELEV FEEL, ELEV PRESS, ELEV ASYMMETRY).</li>
  <li><strong>Pitch trim rate transitions:</strong> HIGH speed mode below 150 KIAS, LOW speed mode above 250 KIAS, scaled between.</li>
  <li><strong>Pitch trim 3-second rule:</strong> manual command &gt;3 sec → ELEVATOR TRIM SHUTOFF + aural.</li>
  <li><strong>Flap auto-trim active range:</strong> 15° to 35° flap, AP off, airspeed &lt;180 KIAS, no manual trim.</li>
  <li><strong>Flap gates:</strong> 0°, 5°, 10°, 15°, 35°.</li>
  <li><strong>Flap actuators:</strong> 4 per wing, 2 per flap. No.1 hyd. Bi-directional no-backs.</li>
  <li><strong>FLIGHT/TAXI auto-return:</strong> when power levers advanced beyond FLT IDLE +12°.</li>
  <li><strong>Lift-dump valve fault delay:</strong> 5-second time delay before ROLL SPLR INBD/OUTBD GND illuminates.</li>
  <li><strong>Stall warn test:</strong> TEST1 then TEST2, each held &gt;10 seconds.</li>
  <li><strong>Stick pusher AOA input:</strong> average of AOA1 and AOA2.</li>
  <li><strong>Elevator PCUs per surface:</strong> 3 (outbd active No.1 hyd, ctr active No.2 hyd, inbd standby No.3 hyd).</li>
  <li><strong>Rudder PCUs:</strong> 2 (lower = RUD 1, upper = RUD 2). AFM 4.18.12: only ONE PUSH OFF at a time.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>3-PCU-ELEV</strong> — three PCUs per elevator: outbd, ctr, inbd standby.</li>
  <li><strong>1-2-3-HYD-ELEV</strong> — No.1 outbd, No.2 ctr, No.3 inbd standby.</li>
  <li><strong>IN-N1-OUT-N2</strong> — inboard spoilers on No.1 hyd, outboard spoilers on No.2 hyd.</li>
  <li><strong>170-OFF-165-ON</strong> — outboard spoilers disabled above 170 KIAS, re-enabled below 165 KIAS.</li>
  <li><strong>RUD-1-LOWER-RUD-2-UPPER</strong> — RUD 1 PUSH OFF = lower PCU; RUD 2 PUSH OFF = upper PCU. Only ONE at a time.</li>
  <li><strong>4.5-DEG-YD</strong> — yaw damper authority ±4.5° max.</li>
  <li><strong>17-KTS-MISMATCH</strong> — IAS mismatch threshold ±17 kts; cascade lights RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM.</li>
  <li><strong>3-SEC-TRIM</strong> — pitch trim held &gt;3 sec → ELEVATOR TRIM SHUTOFF + aural.</li>
  <li><strong>FLAP-5-GATES</strong> — flap gates 0/5/10/15/35.</li>
  <li><strong>15-35-AUTO-TRIM</strong> — flap auto-trim active 15° to 35° only, AP off, &lt;180 KIAS.</li>
  <li><strong>3-COND-LIFT-DUMP</strong> — three conditions for ground spoiler extend: FLIGHT/TAXI in FLIGHT + power levers &lt;FLT IDLE +12° + WOW both gear.</li>
  <li><strong>ROLL-DISC-90 / PITCH-DISC-90</strong> — both disconnect handles: pull out and rotate 90°.</li>
  <li><strong>LOCK-FWD-OFF</strong> — CONTROL LOCK lever forward = OFF; aft = ON.</li>
  <li><strong>10-SEC-STALL-TEST</strong> — TEST1 and TEST2 each held &gt;10 sec on the daily stall warn check.</li>
 </ol>
<p>IAS-mismatch chant: <em>"RUD · SPLR · FEEL · TRIM — four lights together — REDUCE BELOW 200."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
