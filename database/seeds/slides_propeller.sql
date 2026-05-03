-- =============================================================================
-- AviatorTutor — Phase 16: ATA 61 Propeller — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'propeller' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'propeller-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Six Composite Blades, Counterweighted',
 'The Q400 propeller is a six-blade composite assembly per engine, counterweighted so the blades naturally seek HIGH PITCH in flight. The control system is electronic + hydromechanical: the PEC (Propeller Electronic Control) commands the PCU (Pitch Control Unit), which meters HP engine oil into fine or coarse pitch chambers via a two-stage servo valve. The result is fail-safe behaviour: HP oil loss → blades autocoarsen to a safe windmilling pitch (low drag). Layered overspeed protection: hydraulic OSG at 1071 RPM, electronic FADEC at 1122 RPM. Flight fine stop logic prevents in-flight blade angles below 16°. Autofeather watches both engines and feathers a failed engine after a 3-second confirm window. This lesson walks the architecture, modes, overspeed protection, and autofeather logic.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Q400 propeller overview',
 '6-BLADE-COMPOSITE · counterweighted · AUTOCOARSEN-HP-LOSS · layered overspeed.',
 'Counterweight effort and the layered OSG are the design choices that make the prop fail-safe. Memorise the numbers.',
 NULL),

(@lesson_id, 20, 'concept',
 'Why Counterweights — Fail-Safe High Pitch',
 'The natural twisting moment on a propeller blade in flight is dominated by the centrifugal twisting moment, which seeks LOW pitch. The Q400 design adds counterweights, phased around the blade root, that produce a moment toward HIGH pitch — strong enough to dominate the centrifugal effect at in-flight blade angles. So the natural blade-force tendency is COARSE-SEEKING. Implication: if HP oil supply is lost during flight, the blades autocoarsen to a safe high-pitch underspeed condition with low windmilling drag. No HP oil = blades coarsen, prop slows, low drag, low yaw moment. The PCU is therefore actively pushing OIL TO FINE PITCH to balance the counterweights and achieve the demanded RPM in normal flight. Around flat pitch (0°), the natural moment is small; at NEGATIVE blade angle (reverse), counterweights act toward NEGATIVE pitch — so HP loss in reverse goes toward MAX REVERSE.',
 'diagram', '/assets/aircraft/q400/propeller-flow.svg',
 'Counterweight + HP loss behaviour',
 'AUTOCOARSEN-HP-LOSS in flight · MAX REVERSE on HP loss in reverse.',
 'A loss-of-oil event in cruise is benign on the Q400 — windmills, low drag. In reverse the same loss goes the other way.',
 NULL),

(@lesson_id, 30, 'concept',
 'Two Layers of Overspeed Protection',
 'Two independent layers protect against propeller overspeed. The <strong>hydraulic OSG</strong> drops the HP oil supply at approximately <strong>1071 RPM (105%)</strong>. With HP gone, the counterweight effort coarsens the blades, RPM drops, and the OSG reconnects HP. Stable governing at 1071 RPM until the cause is removed. The <strong>electronic OSG (FADEC NP overspeed circuit)</strong> kicks in at approximately <strong>1122 RPM</strong> — the FADEC signals the FMU to reduce fuel flow, which drops engine power and prop RPM. Important: the hydraulic OSG section is <strong>LOCKED OUT in REVERSE</strong> — the FADEC electronic section is the primary protection in reverse. The OSG can be tested on the ground via the PROP O''SPEED GOVERNOR test switch on the Pilots Side Panel.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Two-layer overspeed protection: hydraulic OSG + electronic FADEC',
 '1071-HYD-1122-ELEC · OSG-LOCKED-REVERSE.',
 'When prop RPM holds steady at 1071, the hydraulic OSG is doing its job. Investigate cause; this is not normal.',
 JSON_OBJECT(
   'prompt', 'At what propeller RPM does the hydraulic Overspeed Governor (OSG) start to drop the HP oil supply?',
   'options', JSON_ARRAY(
     '850 RPM',
     '~1071 RPM (105%)',
     '~1122 RPM',
     '1500 RPM'
   ),
   'correct_index', 1,
   'explanation', 'Hydraulic OSG at ~1071 RPM (105%). Electronic FADEC at ~1122 RPM. Hyd locked out in reverse. Mnemonic: 1071-HYD-1122-ELEC.'
 )),

(@lesson_id, 40, 'system',
 'Constant Speed Mode + CL RPM Selections',
 'Constant Speed Mode is the in-flight governing mode. Entered when propeller speed reaches <strong>850, 900, or 1020 RPM</strong> per Condition Lever (CL) selection. The PEC controls the PCU servo valve to balance the counterweight effort against the demanded RPM: more HP oil to fine pitch on underspeed, more to coarse on overspeed. HP oil for governing passes through the OSG before reaching the servo valve, so the OSG can isolate HP if RPM exceeds 105%. The PEC responds not just to RPM error but also to acceleration, giving smooth governing during transients. CL position selection: 850 (low cruise), 900 (mid cruise), 1020 (max — takeoff/landing). MIN 850 / MAX 1020 are the labels on the CL quadrant.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Constant speed mode + CL RPM selections',
 '850-900-1020-CL. PEC + PCU + OSG governing chain.',
 'On a low-power cruise, 850 is fuel-efficient; on takeoff and landing, 1020 ensures full responsiveness. CL movement during the flight is normal.',
 NULL),

(@lesson_id, 50, 'system',
 'Beta Range — PLA Below Flight Idle, Ground Only',
 'Beta range is propeller blade-angle control with the PLA below Flight Idle. PEC directs the servo valve to meter oil for the desired blade angle (closed-loop blade-angle control instead of closed-loop RPM control). NP is governed by the FADEC + engine fuel system at <strong>660 RPM</strong> in beta range. Important constraints: blade angles below 16° require PLA below Flight Idle AND weight-on-wheels. PROPELLER GROUND RANGE lights illuminate when blade angles are below 16°. A detent on the PLA quadrant prevents unintentional movement of the PLA below Flight Idle in flight, and a beta warning horn sounds if the gate is raised in flight. The Ground Beta Enable valve (GBE) locks out the OSG hydraulic section during ground beta — prevents transient overspeed at flat pitch from interfering with pitch control. GBE failure caught by the scheduled OSG test.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Beta range + 16° flight fine stop + GBE',
 'BETA-660-NP · 16-HARD-16.5-SOFT · GBE-LOCKS-OSG-GROUND-BETA.',
 'During approach the prop may briefly enter flight beta as airspeed drops; PEC re-enters constant speed automatically when speed builds.',
 JSON_OBJECT(
   'prompt', 'What is the propeller flight fine stop in CONSTANT SPEED mode (in flight, PLA at or above Flight Idle)?',
   'options', JSON_ARRAY(
     '8° hard hydraulic / 10° soft PEC',
     '16° hard hydraulic / 16.5° soft PEC',
     '20° hard hydraulic / 25° soft PEC',
     'No flight fine stop'
   ),
   'correct_index', 1,
   'explanation', '16° hard hydraulic stop + 16.5° soft PEC stop while PLA at/above Flight Idle. Below 16° requires PLA <Flight Idle + WOW. Mnemonic: 16-HARD-16.5-SOFT.'
 )),

(@lesson_id, 60, 'system',
 'Reverse — 660–950 RPM, 1500 SHP Max',
 'Reverse Speed Control is closed-loop propeller RPM control with the prop driving more negative blade angle to absorb power. Normal reverse range: <strong>660–950 RPM</strong>. Engine schedules fuel based on a power schedule vs PLA, with a maximum limit of <strong>1500 SHP</strong>. At low airspeeds the prop may reach the maximum reverse stop — at that point engine overspeed governor takes over speed control, allowing RPM up to 1020 RPM. Critical: the hydraulic OSG section is LOCKED OUT in reverse, so the FADEC electronic NP overspeed circuit is the primary overspeed protection. This makes sense — at flat pitch in reverse, transient overspeeds from a loss of HP oil would otherwise interfere with pitch control. The system allows the FADEC to handle reverse overspeed via fuel reduction.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Reverse speed control 660-950 RPM 1500 SHP max',
 'REVERSE-660-950-1500 · OSG-LOCKED-REVERSE.',
 'On a contaminated runway, full reverse + brakes is your friend. Just don''t expect the hydraulic OSG to protect you in reverse — FADEC does that.',
 NULL),

(@lesson_id, 70, 'system',
 'Synchrophasing — Cabin Noise Reduction',
 'Synchrophasing is a comfort feature, not a safety system. When both propeller speeds are within a predetermined difference of each other in flight, the PEC enters a synchrophasing mode. The MPU (Magnetic Pickup Unit) signals time the phase difference between the master propeller and the slave propeller over a complete revolution. The PEC then controls the slave to maintain a demanded phase angle relative to the master, calculated to minimise cabin noise via tonal cancellation. The phase demand is determined by the CLA (Condition Lever Angle) position. Important: synchrophasing does <strong>NOT operate at takeoff</strong> — high-power transients would confuse the timing logic, and noise certification doesn''t require it. Synchrophase failure: cabin noise increases; no flight-safety effect. Defer per MEL.',
 'video', '/assets/aircraft/q400/propeller-flow.svg',
 'Synchrophasing for cabin noise reduction',
 'SYNC-NO-TAKEOFF. CLA-driven phase demand.',
 'On a smooth cruise, ask the F/A about cabin noise. If it''s noticeably louder than usual, suspect synchrophase fault.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight + OSG Test',
 'Pre-flight propeller checks. (1) Run the PROP O''SPEED GOVERNOR test on the ground via the test switch on the Pilots Side Panel — confirms hydraulic OSG functional. (2) Run propeller heater test (Phase 9) — TEST 5 sec each prop, 30-sec cooldown. (3) Brief autofeather strategy: ON for takeoff per company SOP. Verify AUTOFEATHER switchlight ready. (4) Confirm CL position START & FEATHER for engine start. (5) On power-up: confirm prop turns to feather at engine start, then unfeathers. (6) Synchrophasing not active until takeoff is past — noise check during cruise.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Pre-flight propeller checks',
 'OSG TEST · prop heat test · autofeather brief · CL feather position.',
 'Skipping the OSG test is common but bad practice — the test catches GBE failure that would otherwise show up only as overspeed in reverse.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Takeoff with Autofeather Armed',
 'Autofeather sequence on takeoff. Pre-takeoff: AUTOFEATHER switchlight ON. SELECT light + A/F SELECT on ED illuminate. Takeoff roll: as torque builds and PLA advances, ARM light illuminates when both engine torques >50% AND both PLAs beyond 60°. Confirm ARM. Continue takeoff and climb-out. ARM stays illuminated as long as both engines healthy. On engine failure: torque drops on the failing engine. If torque <25% OR Np <816 (80%) for ≥3 SECONDS on one engine, autofeather TRIGGERS. Sequence: A/F ARM light goes out, AUX FEATHER PUMP energized, prop feathers, FADEC of operating engine receives UPTRIM command (extra power on surviving engine). Crew runs ENGINE FAILURE on TAKEOFF QRH non-normal.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Autofeather arm + trigger sequence',
 'AF-50-25-816-3SEC · AF-UPTRIMS-OPERATING.',
 'On a real engine failure at V1, autofeather is the difference between a controllable single-engine climb-out and a wrestling match.',
 JSON_OBJECT(
   'prompt', 'Autofeather is armed on takeoff. At what conditions does it TRIGGER an automatic feather of the failing engine?',
   'options', JSON_ARRAY(
     'Any torque drop',
     'ONE engine torque <25% OR Np <816 (80%) for at least 3 SECONDS',
     'Both engines torque <50%',
     'Manual switchlight push'
   ),
   'correct_index', 1,
   'explanation', 'TRIGGER: ONE engine torque <25% OR Np <816 (80%) for ≥3 sec. ARM is both >50% torque + PLA >60°. Mnemonic: AF-50-25-816-3SEC.'
 )),

(@lesson_id, 100, 'abnormal',
 'PLA Below Flight Idle in Flight',
 'PLA inadvertently moved below the FLIGHT IDLE gate in flight. Indications: <strong>BETA WARNING HORN sounds</strong>, PROPELLER GROUND RANGE lights may illuminate. Critical action: <strong>immediately move the PLA back above the gate</strong>. Do NOT operate below Flight Idle in flight. The Q400 has a layered defence: a detent on the quadrant prevents unintentional below-Flight-Idle movement, and the GBE valve is normally in its in-flight position to keep OSG protection active in flight. But if the gate is raised and PLA pushed down, the propeller can enter beta blade angles in flight — reducing thrust unpredictably and potentially producing an unrecoverable yaw and pitch event. WARNING from the AOM: NEVER MOVE THE POWER LEVERS BELOW FLIGHT IDLE IN FLIGHT.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'Below-Flight-Idle in flight is forbidden',
 'BETA-HORN-IN-FLIGHT · PLA back above gate immediately.',
 'On a recurrent question "what if PLA goes below Flight Idle in flight?" the answer is "horn sounds; immediately back above the gate. Never intentional in flight."',
 NULL),

(@lesson_id, 110, 'abnormal',
 'OSG Action and HP Oil Loss',
 'Two scenarios that demand recognition. <strong>OSG hydraulic action (1071 RPM):</strong> propeller RPM holds steady at ~1071 RPM despite a higher demand, fluctuating in and out of overspeed as the OSG cycles. Investigate cause — usually a stuck servo valve at fine pitch or PEC fault. PEC, FADEC, electrical supply may all be involved. <strong>HP oil loss in flight:</strong> blades autocoarsen via counterweight effort to safe high-pitch windmill. Prop RPM drops, drag is low, yaw effect minimal. Run QRH; consider engine shutdown if oil pressure low warns. <strong>HP oil loss in reverse (during landing rollout):</strong> blades go toward MAX REVERSE blade angle. Different effect from in-flight loss. Be aware on rollout.',
 'video', '/assets/aircraft/q400/propeller-flow.svg',
 'OSG action vs HP oil loss',
 '1071 stable governing · AUTOCOARSEN-HP-LOSS · MAX REVERSE on HP loss in reverse.',
 'OSG cycling at 1071 is the system protecting you. Investigate but don''t panic — it''s by design.',
 JSON_OBJECT(
   'prompt', 'Propeller RPM is fluctuating around 1071 RPM, cycling in and out of overspeed governing. What is happening, and what should you do?',
   'options', JSON_ARRAY(
     'Engine failure imminent — autofeather',
     'Hydraulic OSG is doing its job — investigate the cause (stuck servo, PEC fault); not an immediate emergency',
     'Synchrophase fault — turn off',
     'PLA below Flight Idle — push above gate'
   ),
   'correct_index', 1,
   'explanation', 'OSG cycling at 1071 RPM is the system protecting against overspeed. Investigate cause; not an emergency. PEC, servo valve, electrical may all be involved.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Propeller Non-Normals',
 'Q400 QRH non-normals for the propeller cluster into six groups. (1) ENGINE FAILURE on takeoff — autofeather should auto-feather; verify A/F ARM out, prop feathered, UPTRIM active. Run ENGINE FAILURE on TAKEOFF non-normal. (2) Manual feather — CL to FEATHER or alternate feather switch. (3) PROP OVERSPEED — investigate cause of 1071 RPM cycling or 1122 RPM FADEC action. (4) GBE valve fault (caught by OSG test on ground): defer per MEL — significant restriction. (5) Synchrophase fault: cabin noise, no flight-safety. Defer per MEL. (6) PLA below Flight Idle in flight: immediately back above gate; never intentional. The 16/16.5 stops + counterweight design are the safety net for most failures.',
 'image', '/assets/aircraft/q400/propeller-flow.svg',
 'QRH propeller cluster',
 'ENGINE FAIL · MANUAL FEATHER · OVERSPEED · GBE · SYNC · BETA HORN.',
 'Most propeller faults are passive — counterweights and OSG handle them. The crew action items are autofeather verification + emergency feather + the never-below-Flight-Idle rule.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Engine Failure on Takeoff at V1',
 'Setup: takeoff roll. Both PLAs to RATING DETENT. Autofeather ON. SELECT illuminated. ARM illuminates as torque builds and PLA exceeds 60°.\n\nAt V1+5: BANG. Loud noise. Number 2 engine torque drops rapidly. Yaw onset to the right.\n\nWithin 3 seconds: Np on engine 2 drops below 816 (80%) AND torque drops below 25%. Autofeather TRIGGERS. A/F ARM light goes out. AUX FEATHER PUMP energized. Prop 2 starts feathering. Engine 1 FADEC receives UPTRIM command — Engine 1 torque rises automatically.\n\nNext 30 seconds: PF maintains directional control via rudder + aileron. Confirms autofeather worked: prop 2 feathered, drag low, yaw manageable. PNF identifies "engine 2 failed, autofeather, UPTRIM active." Cleanup begins per ENGINE FAILURE on TAKEOFF QRH.\n\nNext minutes: V2 +10 climb on Engine 1 with UPTRIM. Continue climb to safe altitude; clean up; declare; return for landing or divert. The autofeather + UPTRIM combination takes the workload off the captain''s feet at the worst moment.',
 'animation', '/assets/aircraft/q400/propeller-flow.svg',
 'Engine failure on takeoff with autofeather active',
 'AUTOFEATHER ARM · 3-sec confirm · prop feathers · UPTRIM · climb-out.',
 'Drill the autofeather sequence in every recurrent. The 3-second confirm is intentional — autofeather doesn''t fire on a momentary torque dip.',
 JSON_OBJECT(
   'prompt', 'Engine 2 fails at V1+5 with autofeather armed. The PEC senses Np <816 and torque <25% for 3 seconds. What happens next?',
   'options', JSON_ARRAY(
     'Crew must manually feather the prop',
     'A/F ARM light goes out, AUX FEATHER PUMP energized, prop 2 feathers automatically, FADEC of engine 1 receives UPTRIM command',
     'Engine 1 also feathers as a precaution',
     'Autofeather fires both engines'
   ),
   'correct_index', 1,
   'explanation', 'Autofeather: A/F ARM out, AUX FEATHER PUMP on, prop 2 feathers, UPTRIM to operating engine FADEC. Mnemonic: AF-UPTRIMS-OPERATING.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Propeller in 60 Seconds',
 'Recap:\n  • 6-blade composite per engine. Counterweighted — natural moment toward HIGH pitch in flight.\n  • PEC dual-channel commands PCU servo valve. PCU meters HP engine oil to fine/coarse pitch chambers.\n  • HP loss in flight: blades autocoarsen to safe windmill.\n  • HP loss in reverse: blades to MAX REVERSE.\n  • Modes: Constant Speed (in flight, 850/900/1020 RPM via CL), Beta (PLA <Flight Idle, NP gov 660 RPM), Reverse (660-950 RPM, 1500 SHP max).\n  • Flight fine stops: 16° hyd hard / 16.5° PEC soft (PLA at/above Flight Idle).\n  • Below 16° requires PLA <Flight Idle + WOW. PROPELLER GROUND RANGE lights illuminate.\n  • OSG hyd at 1071 RPM (105%); FADEC electronic at 1122 RPM. Hyd LOCKED OUT in reverse.\n  • GBE valve locks out OSG on ground beta.\n  • Synchrophasing reduces cabin noise; not at takeoff. CLA-driven phase demand.\n  • Autofeather: ON for takeoff. ARM both torque >50% + PLA >60°. TRIGGER one torque <25% OR Np <816 for ≥3 sec → feather + UPTRIM.\n  • Beta horn sounds if PLA below Flight Idle in flight — immediately back above gate.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 '6-BLADE-COMPOSITE · AUTOCOARSEN-HP-LOSS · 16-HARD-16.5-SOFT · 1071-HYD-1122-ELEC · 850-900-1020-CL · BETA-660-NP · REVERSE-660-950-1500 · OSG-LOCKED-REVERSE · GBE-LOCKS-OSG-GROUND-BETA · SYNC-NO-TAKEOFF · AF-50-25-816-3SEC · AF-UPTRIMS-OPERATING · BETA-HORN-IN-FLIGHT',
 'Thirteen mnemonics carry every propeller question. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
