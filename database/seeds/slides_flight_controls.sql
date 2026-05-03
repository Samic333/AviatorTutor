-- =============================================================================
-- AviatorTutor — Phase 7: ATA 27 Flight Controls — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'flight-controls' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'flight-controls-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'The System That Lets You Fly the Aeroplane',
 'Flight controls are the system that connects your hands and feet to the airframe. On the Q400 the design choice that defines everything is which surfaces are mechanical and which are hydraulic: ailerons are mechanical/cable, elevators and rudder and spoilers are hydraulic. The brain of the powered side is the FCECU — it regulates rudder authority, elevator feel, pitch trim rates, and the spoiler airspeed lockout. Then there is a Stall Protection System layered on top with two SPMs, stick shakers, and a stick pusher. This lesson walks the architecture, the failure logic, and the disconnect handles you must know cold.',
 'image', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Q400 flight controls system overview',
 'Ailerons MECHANICAL · elevators/rudder/spoilers HYDRAULIC · FCECU brain · SPS layer.',
 'A check ride that starts "you have a roll jam, then on rollout the lift dump fails" tests three branches of this system in 90 seconds. Know the disconnect handles and the spoiler logic cold.',
 NULL),

(@lesson_id, 20, 'concept',
 'PFCS — What Is Powered, What Is Mechanical',
 'PFCS = Powered Flight Control Surfaces. On the Q400 those are the ELEVATORS, the SPOILERS, and the RUDDER. Each runs through hydraulic Power Control Units (PCUs). The AILERONS are NOT in this group — they are mechanical, cable-driven, with a geared tab on each surface for aerodynamic assist. Why this matters: when a hydraulic system fails, the ailerons keep working. When an aileron jams, you cannot bypass with hydraulic muscle — you have to physically disconnect with the ROLL DISC handle. The PFCS positions are displayed on the pilot''s MFD in the Permanent Systems Data Area (PSDA), fed from the surfaces via the Integrated Flight Cabinet (IFC).',
 'diagram', '/assets/aircraft/q400/flight-controls-flow.svg',
 'PFCS surfaces vs mechanical ailerons',
 'PFCS = elevators + spoilers + rudder. Ailerons stand alone mechanically.',
 'On a triple-hydraulic loss the ailerons are still alive — that is the only reason the Q400 stays controllable in the rare worst-case.',
 NULL),

(@lesson_id, 30, 'concept',
 'Three PCUs Per Elevator — The 1/2/3 Mapping',
 'Each elevator has THREE hydraulic PCUs, allocated cleverly across the three main hydraulic systems: <strong>OUTBOARD PCU = No.1 hyd (active);</strong> <strong>CENTRE PCU = No.2 hyd (active);</strong> <strong>INBOARD PCU = No.3 hyd (STANDBY).</strong> In normal flight only the outboard and centre PCUs are working; the inboard sits passively, pressurised by the standby No.3 system but not actively driving the surface. If No.1 or No.2 fails, the standby inboard PCU automatically activates via the No.3 isolation valve. There is a HYD #3 ISOL VLV pushbutton that lets the crew manually activate the inboard PCU — but doing so with No.1 and No.2 healthy will illuminate the ELEVATOR PRESS caution. Memorise the mapping: 1 outboard, 2 centre, 3 inboard standby.',
 'image', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Three elevator PCUs per surface, with hydraulic source allocation',
 '3-PCU-ELEV · 1-2-3-HYD-ELEV. 1 outbd, 2 ctr, 3 inbd standby. Auto-activate on 1 or 2 fail.',
 'On a No.2 fail in cruise, the elevator inboard standby PCU takes over silently. You will see the No.2 hyd cautions but the elevator surface keeps responding.',
 JSON_OBJECT(
   'prompt', 'Which hydraulic system powers the INBOARD elevator PCU on the Q400?',
   'options', JSON_ARRAY(
     'No.1 hydraulic system, active in normal flight',
     'No.2 hydraulic system, active in normal flight',
     'No.3 hydraulic system, STANDBY — auto-activates on No.1 or No.2 failure',
     'Pneumatic backup, no hydraulic'
   ),
   'correct_index', 2,
   'explanation', 'Inboard elevator PCU = No.3 hyd, STANDBY. Auto-activates on No.1 or No.2 fail. Manual activation via HYD #3 ISOL VLV illuminates ELEVATOR PRESS if No.1 and No.2 are healthy. Mnemonic: 1-2-3-HYD-ELEV.'
 )),

(@lesson_id, 40, 'system',
 'Roll Control — Ailerons + Spoilers + Wheel Mapping',
 'Roll is controlled by ONE aileron and TWO spoilers (one inboard, one outboard) per wing. The aileron is mechanical/cable, deflects ±17° (handwheel turns 70° L/R of centre), and has a geared tab for aerodynamic assist; the right aileron has a ground-adjustable trim tab as well. The spoilers are hydraulically powered — INBOARD on No.1 hyd, OUTBOARD on No.2 hyd. Wheel mapping is the trick: the PILOT''S wheel controls the SPOILERS; the COPILOT''S wheel controls the AILERONS. They are interconnected in normal flight so either wheel commands both. When you pull the ROLL DISC handle and rotate it 90°, the clutch disengages and the pilot keeps SPOILERS only while the copilot keeps AILERONS only. The pilot with the unjammed wheel has roll control.',
 'diagram', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Aileron + spoiler architecture, wheel-to-surface mapping',
 'Pilot wheel = SPOILERS. Copilot wheel = AILERONS. ROLL DISC splits them.',
 'The "pilot wheel = spoilers" mapping is unintuitive and easy to forget under stress. Brief it on every recurrent.',
 NULL),

(@lesson_id, 50, 'system',
 'Spoilers — 170 KIAS Lockout and Three Modes',
 'The Q400 has FOUR roll spoilers — two inboard (No.1 hyd) and two outboard (No.2 hyd). The FCECU runs a clean airspeed-based lockout: ABOVE 170 KIAS, only the inboard spoilers operate (outboards are disabled). BELOW 165 KIAS, both inboard and outboard operate. The 5-knot deadband prevents rapid hunting. The SPLR OUTBD caution illuminates if outboards fail to disable above 185 KIAS or fail to enable below 150 KIAS, OR No.2 hyd is lost, OR there is an IAS mismatch. Spoilers operate in three modes: FLIGHT (proportional roll), GROUND (lift dump on touchdown), and TAXI (retracted via FLIGHT/TAXI switch — auto-returns to FLIGHT when power levers advance).',
 'image', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Spoiler airspeed lockout and three modes',
 '170-OFF-165-ON. Inboard always · outboards on/off by airspeed. 3 modes: flight, ground, taxi.',
 'On a slow approach below 165, both spoilers wake up — better roll authority right when you need it. The FCECU does this without a single crew action.',
 NULL),

(@lesson_id, 60, 'system',
 'Lift Dump on Landing — Three Conditions',
 'Ground mode lift dump requires THREE conditions, all met. (1) FLIGHT/TAXI switch in FLIGHT (lever-locked, mandatory for takeoff). (2) BOTH power levers below FLIGHT IDLE +12°. (3) WOW (Weight-On-Wheels) on BOTH main gear. When all three are true, the FCECU and PSEU energise the lift-dump valves; both inboard and outboard spoilers extend fully. Roll input commands are cancelled. ROLL INBD and ROLL OUTBD advisory lights illuminate on the glareshield. If a lift-dump valve fails to energise, that side''s spoilers will not extend; ROLL SPLR INBD GND or ROLL SPLR OUTBD GND caution illuminates after a 5-SECOND delay. The FLIGHT/TAXI switch auto-returns to FLIGHT when power levers exceed FLT IDLE +12°, retracting all spoilers for takeoff.',
 'video', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Three-condition lift dump logic with 5-second fault delay',
 '3-COND-LIFT-DUMP: FLIGHT/TAXI · PWR<FI+12 · WOW both. 5-sec fault delay.',
 'A late lift-dump on a wet runway is a real eye-opener — landing distance grows substantially. Brief the FO to call any anomaly on the rollout scan.',
 JSON_OBJECT(
   'prompt', 'Which THREE conditions must all be met for the Q400 spoilers to extend in ground (lift-dump) mode on landing?',
   'options', JSON_ARRAY(
     'FLIGHT/TAXI in TAXI · power levers above FLT IDLE +12° · WOW both gear',
     'FLIGHT/TAXI in FLIGHT · power levers BELOW FLT IDLE +12° · WOW both gear',
     'FLIGHT/TAXI in FLIGHT · airspeed below 80 kts · WOW one gear',
     'Brakes applied · reverse selected · WOW both gear'
   ),
   'correct_index', 1,
   'explanation', 'Three conditions: FLIGHT/TAXI in FLIGHT, both power levers below FLT IDLE +12°, WOW on both main gear. Mnemonic: 3-COND-LIFT-DUMP. Lift-dump valve fault triggers ROLL SPLR GND caution after a 5-second delay.'
 )),

(@lesson_id, 70, 'system',
 'Rudder — Two PCUs, FCECU Authority, AFM 4.18.12',
 'The rudder has TWO hydraulic PCUs — a LOWER and an UPPER. The crew interface is two switchlights: RUD 1 PUSH OFF (lower PCU) and RUD 2 PUSH OFF (upper PCU). On a PCU jam, the corresponding switchlight illuminates amber; pushing it depressurises that PCU and the FCECU re-schedules pressure to the surviving one to maintain rudder authority. AFM 4.18.12 is non-negotiable: <strong>only ONE RUD PUSH OFF switchlight may be pushed at a time.</strong> If both are inadvertently pressed, the OFF legends extinguish, both PUSH legends illuminate, and the previously-pushed PCU re-pressurises. This is the system protecting you from accidentally killing rudder authority. To recover, push the NON-jammed switch again. Above the PCU layer, the FCECU reduces hydraulic pressure to the rudder as airspeed increases — limiting deflection so high-speed full-pedal does not over-stress the structure.',
 'diagram', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Two-PCU rudder with FCECU authority limit and AFM 4.18.12 rule',
 'RUD-1-LOWER-RUD-2-UPPER. Only ONE PUSH OFF at a time. FCECU limits authority by airspeed.',
 'A double-press by mistake is recoverable — push the non-jammed side again. Drill it in the sim.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Trim Logic and Flap Auto-Trim',
 'Pitch trim has a clear priority order: pilot &gt; copilot &gt; autopilot. The FCECU enforces it. Trim rate is HIGH speed mode below 150 KIAS, LOW speed mode above 250 KIAS, scaled in between — the slow rate at high speed prevents over-trim. The 3-second rule: any manual pitch trim command held longer than 3 sec triggers an aural warning and illuminates the ELEVATOR TRIM SHUTOFF switchlight on the glareshield. Push either left or right ELEVATOR TRIM SHUTOFF to deactivate trim. Flap auto-pitch trim is a separate feature: it runs ONLY between 15° and 35° flap, ONLY with autopilot off, ONLY at airspeed below 180 KIAS, and ONLY if no manual trim is being commanded. Nose-down trim on flap extension; nose-up on retraction. Aileron and rudder trim each have their own switches and indicators on the centre console; both run from the Left Essential bus.',
 'image', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Pitch trim priority + flap auto-trim conditions',
 'Trim priority: pilot &gt; copilot &gt; AP. 3-SEC-TRIM. Flap auto-trim 15–35°/AP off/&lt;180 KIAS.',
 'On a flap retraction in level flight you should expect a tiny nose-up auto-trim. If you do not feel it, the auto-trim has dropped — check the conditions.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Pre-Flight Discipline — Locks, Trim, Stall Test',
 'Pre-flight: the CONTROL LOCK lever (aileron gust lock) must be FORWARD (OFF). When AFT (ON) the ailerons are locked at neutral and — by design — the lever physically restricts power-lever travel, so you cannot accidentally take off with the lock engaged. Confirm full and free travel of all flight controls. Elevator trim must be in the white TAKE-OFF range on the indicator — an aural warns if you advance power levers above FLT IDLE +12° outside the range. The daily Stall Warning test: STALL WARN switch TEST1 (held &gt;10 SECONDS) then TEST2 (held &gt;10 SECONDS) — exercises both Stall Protection Modules independently. Quick taps fail. Yaw damper armed via YD pushbutton — pointers illuminate left and right of the YD switchlight when engaged. Rudder pedals adjusted to comfortable position before pushing back.',
 'image', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Pre-flight checks: gust lock, trim, stall warn TEST1+TEST2',
 'LOCK-FWD-OFF · 10-SEC-STALL-TEST · TO band on elevator trim · YD on.',
 'Skipping the 10-second hold on the stall warn test is the most common pre-flight skip. Hold it. The system needs the time to run both channels.',
 JSON_OBJECT(
   'prompt', 'During the daily stall warning test, how long must each TEST1 and TEST2 position be held?',
   'options', JSON_ARRAY(
     'A momentary tap is sufficient',
     'More than 5 seconds each',
     'More than 10 seconds each',
     'A full 30 seconds each'
   ),
   'correct_index', 2,
   'explanation', 'TEST1 and TEST2 each held for MORE THAN 10 SECONDS. The Stall Protection Modules need that time to complete their built-in test. Mnemonic: 10-SEC-STALL-TEST.'
 )),

(@lesson_id, 100, 'abnormal',
 'Roll Jam — ROLL DISC and the Wheel Split',
 'Roll jam in flight: ROLL DISC handle on the centre pedestal — pull straight out to the limit, then rotate 90° clockwise or counterclockwise. The clutch on the base of the copilot''s control column disengages. Now: <strong>PILOT keeps SPOILERS only; COPILOT keeps AILERONS only.</strong> The pilot with the UNJAMMED wheel has roll control. Two important quirks. (1) Left wheel free → only spoilers operate → roll forces are LOW; resist the urge to over-control. (2) Right wheel free → only ailerons operate → if the right wheel is rotated more than 50° to maintain wings level, SPLR 1 and/or SPLR 2 switchlights may illuminate (one or both spoilers stuck extended). If they remain on continuously, push them OFF to depressurise the PCUs and retract the affected spoilers; this triggers ROLL SPLR INBD/OUTBD HYD cautions. ROLL SPLR OUTBD HYD will not illuminate until airspeed below 165 KIAS.',
 'video', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Roll disconnect handle + wheel-to-surface mapping after split',
 'ROLL-DISC-90. Pilot keeps spoilers, copilot keeps ailerons. Pilot with FREE wheel has control.',
 'On a real roll jam the temptation is to keep wrestling. Don''t — disconnect early, let the unjammed wheel take it.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'IAS Mismatch — Four Lights Together',
 'IAS mismatch is a system-level failure that lights up four cautions at once. When Airspeed #1 differs from Airspeed #2 by more than ±17 KNOTS, the FCECU asserts: RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM cautions ALL illuminate simultaneously, plus IAS MISMATCH on the PFD. The system cannot distinguish which ADU is correct — so it conservatively flags every airspeed-dependent function. Crew action: REDUCE AIRSPEED BELOW 200 KIAS. Identify which ADU is reading correctly using the standby altimeter / airspeed and crosschecking against GPS groundspeed plus expected pitch attitude. Then run the QRH non-normal. Continue per QRH; this is not necessarily an immediate divert, but it is a degraded-control event.',
 'image', '/assets/aircraft/q400/flight-controls-flow.svg',
 'IAS mismatch cascade — four caution lights together',
 '17-KTS-MISMATCH. Four lights together: RUD CTRL · SPLR OUTBD · ELEV FEEL · PITCH TRIM. Below 200.',
 'Recognising the four-light pattern in 2 seconds is what separates a clean diversion from a wrestling-match descent. Drill the pattern.',
 JSON_OBJECT(
   'prompt', 'In cruise the FO calls four cautions illuminating simultaneously: RUD CTRL, SPLR OUTBD, ELEV FEEL, PITCH TRIM. What is the most likely cause and what is the FIRST action?',
   'options', JSON_ARRAY(
     'Triple hydraulic failure — declare MAYDAY immediately',
     'IAS mismatch >17 kts between ADUs — REDUCE AIRSPEED BELOW 200 KIAS, identify the bad ADU, run QRH',
     'FCECU total failure — disconnect autopilot and hand-fly',
     'Stick pusher armed — push the OFF switchlight'
   ),
   'correct_index', 1,
   'explanation', 'Four-light cascade is the IAS mismatch signature. Threshold is ±17 kts between ADU 1 and ADU 2. Crew action: reduce below 200 KIAS, crosscheck standby airspeed and GPS groundspeed to identify the bad ADU, run the QRH. Mnemonic: 17-KTS-MISMATCH.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: PFCS Non-Normals',
 'Q400 QRH non-normals for flight controls cluster into six groups. (1) ROLL CONTROL JAM — ROLL DISC handle out + 90°; pilot with unjammed wheel takes control. (2) PITCH CONTROL JAM — pitch disconnect handle out + 90°. (3) RUD 1 or RUD 2 PUSH OFF illuminated — push the affected switchlight to depressurise; ONLY ONE at a time per AFM 4.18.12. (4) PITCH TRIM RUNAWAY / 3-sec rule — push either ELEVATOR TRIM SHUTOFF switchlight; trim deactivates. (5) ELEVATOR PRESS / ASYMMETRY / FEEL — reduce airspeed below 200 KIAS, run QRH. (6) IAS MISMATCH — reduce below 200 KIAS, identify the bad ADU, run QRH. Most of these are not memory items — but the disconnect actions need to be reflexive because they are physical, not procedural.',
 'image', '/assets/aircraft/q400/flight-controls-flow.svg',
 'QRH PFCS cluster diagram',
 'ROLL/PITCH disc · RUD PUSH OFF · TRIM SHUTOFF · ELEV PRESS/ASYM/FEEL · IAS mismatch.',
 'Drill the disconnect handles in the sim until the action is muscle memory. Trying to read the QRH while the wheel is fighting you wastes the most expensive seconds.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Pitch Trim Runaway in Climb',
 'Setup: passing FL150 in a clean climb, autopilot off, hand-flying. Suddenly the FO calls "TRIM!" The control column is loading nose-down; you can hear an aural clicking; the ELEVATOR TRIM SHUTOFF switchlight on the glareshield is illuminated. Elevator trim indicator is moving rapidly toward ND.\n\nFirst 10 seconds: PF holds the column with both hands against the trim, calls "AVIATE." PNF identifies "Elevator trim runaway, switchlight illuminated, command shutoff." Captain pushes the ELEVATOR TRIM SHUTOFF switchlight on the affected side. Trim stops. Aural stops. Now the captain has to manually retrim the elevator with whatever residual trim path is still available — typically by hand-trimming to a position that minimises column force. Brief approach for higher-than-normal pitch forces; declare PAN-PAN; brief the FO; consider a divert to the nearest suitable airport with maintenance. Elevator trim runaway is a fatigue and workload event — it ends the sector.',
 'animation', '/assets/aircraft/q400/flight-controls-flow.svg',
 'Pitch trim runaway scenario — push SHUTOFF, manually retrim',
 'Trim runaway · 3-SEC-TRIM aural · push ELEVATOR TRIM SHUTOFF · manually retrim · divert.',
 'Holding the column against a trim runaway tires the PF fast. Push the SHUTOFF in the first 5 seconds — do not try to "ride it out."',
 JSON_OBJECT(
   'prompt', 'On climb-out the ELEVATOR TRIM SHUTOFF switchlight illuminates with an aural clicking and the column loads nose-down. Your immediate action?',
   'options', JSON_ARRAY(
     'Disengage the autopilot',
     'Push either left or right ELEVATOR TRIM SHUTOFF switchlight to deactivate trim',
     'Pull the ROLL DISC handle',
     'Push both RUD 1 and RUD 2 PUSH OFF switchlights'
   ),
   'correct_index', 1,
   'explanation', 'Pitch trim runaway → push ELEVATOR TRIM SHUTOFF switchlight (either side). Trim deactivates and the aural stops. Then manually retrim and divert.',
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Flight Controls in 60 Seconds',
 'Recap:\n  • PFCS = elevators + spoilers + rudder (hydraulic). Ailerons = mechanical/cable.\n  • Each elevator has 3 PCUs: outbd N1 active, ctr N2 active, inbd N3 standby.\n  • Spoilers: 4 total. Inboards = N1 hyd; outboards = N2 hyd. Above 170 KIAS only inboards; below 165 KIAS both.\n  • Lift-dump (3 conditions): FLIGHT/TAXI in FLIGHT + power &lt;FLT IDLE +12° + WOW both gear.\n  • Rudder: 2 PCUs (lower = RUD 1 PUSH OFF, upper = RUD 2). Only ONE pushed at a time per AFM 4.18.12.\n  • Pitch trim priority: pilot &gt; copilot &gt; AP. 3-sec rule → ELEVATOR TRIM SHUTOFF.\n  • Flap auto-trim: 15°–35° only, AP off, &lt;180 KIAS.\n  • Yaw damper: ±4.5°. Both FGMs needed.\n  • CONTROL LOCK: FWD = OFF, AFT = ON. Locks ailerons + restricts power lever travel.\n  • Stall warn test daily: TEST1 + TEST2 each held &gt;10 sec.\n  • IAS mismatch >17 kts → 4-light cascade (RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM). Reduce below 200 KIAS.\n  • ROLL DISC: pilot keeps spoilers, copilot keeps ailerons. Pitch disconnect: free column has pitch.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 '3-PCU-ELEV · 1-2-3-HYD-ELEV · 170-OFF-165-ON · 3-COND-LIFT-DUMP · RUD-1-LOWER-RUD-2-UPPER · 4.5-DEG-YD · 17-KTS-MISMATCH · 3-SEC-TRIM · 15-35-AUTO-TRIM · ROLL-DISC-90 · LOCK-FWD-OFF · 10-SEC-STALL-TEST',
 'Twelve mnemonics carry every flight-controls question on a recurrent. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
