-- =============================================================================
-- AviatorTutor — Phase 11: ATA 32 Landing Gear — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'landing-gear' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'landing-gear-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Hydraulic Brain, Mechanical Heart',
 'The Q400 landing gear is electrically controlled, hydraulically operated, and ultimately mechanically locked. No.2 hydraulic system drives gear retraction/extension and nosewheel steering. No.1 hydraulic system drives the multi-disc anti-skid brakes. The PSEU is the brain that knows the aircraft state — weight on wheels, gear position, lift-dump conditions. Backups are layered: PTU for No.2 hyd, alternate extension for the gear, accumulator for the emergency brake. The defining captain skills here are: knowing the alternate extension three-door sequence cold, knowing which warning tone groups can be muted, and knowing the airspeed thresholds at which anti-skid arms, prevents self-test, and cancels brake delay.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Q400 landing gear architecture overview',
 'N2-GEAR-N1-BRAKES · PSEU brain · alternate extension layered backup.',
 'On a real gear-down failure at the worst moment of the leg, the alternate-extension three-door sequence is what gets you on the ground.',
 NULL),

(@lesson_id, 20, 'concept',
 'Retraction Direction — Nose Forward, Mains Aft',
 'Easy fact, easy trap. The Q400 nose gear retracts <strong>FORWARD</strong> into the nose section. The two main gears retract <strong>AFT</strong> into the engine nacelles. Doors fully enclose the gear when retracted; partially when down. Hydraulic doors handle the heavy lifting (nose forward door, main aft doors close hydraulically after retraction). Mechanical doors are linked to the gear motion (aft nose doors close with retracting nose; forward main doors close with retracting mains). The combination gives a clean stowage in the nacelle/nose with no door drag in cruise.',
 'diagram', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Nose forward + mains aft retraction',
 'NOSE-FWD-MAINS-AFT. Hydraulic doors enclose; mech doors track gear motion.',
 'On the rejected-takeoff scan, knowing that mains retract aft means a partial gear retraction shows aft door open + mains tucked — distinct from a nose-up failure.',
 NULL),

(@lesson_id, 30, 'concept',
 'No.2 Drives Gear; No.1 Drives Brakes',
 '<strong>No.2 hydraulic system</strong> drives gear extension and retraction AND the nosewheel steering. The PTU backs up No.2. <strong>No.1 hydraulic system</strong> drives the normal anti-skid brakes (multiple disc brake units, modulated by ASCU). The emergency/park brake runs on No.2 or, if No.2 is dead, on the parking brake accumulator (good for ~6 applications). This split means a No.2 hydraulic loss disables both gear extension AND nosewheel steering — but the normal brakes still work because they''re on No.1. A No.1 hydraulic loss disables normal anti-skid braking but the gear and steering still work, and the EMERG BRAKE (No.2) gives you stopping ability without anti-skid or differential.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Hydraulic allocation: gear vs brakes',
 'N2-GEAR-N1-BRAKES. EMERG = N2 or accumulator. ~6 applications.',
 'On a No.2 hydraulic failure, plan a divert that minimises taxi (long-runway, into-wind) — you''ve lost steering and you''re relying on emergency brake only.',
 JSON_OBJECT(
   'prompt', 'Which hydraulic system powers normal landing gear extension/retraction, and which powers the normal anti-skid brakes?',
   'options', JSON_ARRAY(
     'Both gear and brakes on No.1',
     'Gear on No.2; brakes on No.1',
     'Gear on No.1; brakes on No.2',
     'Both on No.3 standby'
   ),
   'correct_index', 1,
   'explanation', 'No.2 = gear + steering. No.1 = brakes. Mnemonic: N2-GEAR-N1-BRAKES.'
 )),

(@lesson_id, 40, 'system',
 'Nosewheel Steering — Three Modes by Speed',
 'Nosewheel steering has three operating modes. <strong>Low-speed taxi:</strong> Steering Hand Control (tiller) on the pilot side console drives the nosewheel up to <strong>±70°</strong> either side of centre. STEERING switch must be at STEERING; nose gear must be down + locked + WOW + within 70° of centre. <strong>High-speed taxi / take-off / landing roll:</strong> rudder pedals drive the nosewheel up to <strong>±8°</strong> either side of centre. <strong>Passive caster mode:</strong> ±120° either side, no powered steering. Triggered by nosewheel angle >70°, SCU fault, or STEERING switch OFF. In caster, use differential braking + power for directional control. CAUTION: never set STEERING to STEERING with a tow bar connected — torque from the tow bar can damage the SCU.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Steering modes by airspeed and switch position',
 '70-HAND-8-PEDAL-120-CASTER. Reverse: STEERING ON, no input.',
 'On reverse taxi the rule is "STEERING ON, hands off the tiller, feet flat." Differential brake + power for direction.',
 NULL),

(@lesson_id, 50, 'system',
 'Gear Warning Tone — 3 Logic Groups',
 'A gear-not-down warning tone sounds via the flight-deck speakers when gear is up + locked AND any of three logic groups is met. <strong>Group 1:</strong> flaps &gt; 8.5° + either engine torque &lt; 50% + both PLA below RATING detent (approach configuration with reduced thrust). <strong>Group 2:</strong> both PLA &lt; FLIGHT IDLE +12° + KIAS &lt; 156 + RA &lt; 1053 ft (if RA valid) — both engines pulled back at low altitude. <strong>Group 3:</strong> one PLA &lt; FLIGHT IDLE +12° + both PLA &lt; RATING detent + HORN switch NOT at MUTE + KIAS &lt; 156 + RA &lt; 1053 ft — single-engine failure at low speed/altitude. <strong>Critical:</strong> ONLY case 3 may be muted via the HORN switch, and only for engine-failure handling at low speed. Cases 1 and 2 cannot be muted.',
 'video', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Gear warning tone trigger groups + mute logic',
 '3-TONE-GROUPS-MUTE-3 · 156-KIAS-1053-RA.',
 'On a single-engine failure at low altitude, you push the HORN MUTE so you can hear the FO calls without a constant tone. But for normal approaches with thrust pulled back (case 2), the tone is your friend — never mute it.',
 JSON_OBJECT(
   'prompt', 'Of the three gear-warning-tone trigger groups on the Q400, which one(s) may be MUTED via the HORN switch?',
   'options', JSON_ARRAY(
     'All three groups can be muted',
     'Only case 1 (flaps > 8.5°)',
     'Only case 3 (single-engine failure at low speed)',
     'None — the tone cannot be muted'
   ),
   'correct_index', 2,
   'explanation', 'Only case 3 (one engine failed, low speed, low alt) may be muted. Cases 1 and 2 cannot be muted. Mnemonic: 3-TONE-GROUPS-MUTE-3.'
 )),

(@lesson_id, 60, 'system',
 'Anti-Skid — Three Speed Thresholds',
 'The anti-skid system has three distinct speed thresholds you must remember. (1) The system <strong>arms above 10 kts</strong> wheel speed — below 10 kts no anti-skid modulation. (2) <strong>Self-test is prevented above 17 kts</strong> — TEST only valid below this speed. (3) The 5-second brake-delay protection (delay applied if wheels haven''t spun up after WOW) <strong>cancels at 35 kts wheel speed</strong> — at that speed the system trusts the wheels are spinning and applies brakes immediately. The TEST cycle on the ground holds INBD/OUTBD ANTISKID caution lights for 6 seconds, then they extinguish. In air with gear extended, TEST cycles for 3 seconds. ASCU receives WOW + gear up/locked signals from PSEU.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Anti-skid 10/17/35 kts speed thresholds',
 '10-17-35-BRAKE · 5-SEC-BRAKE-DELAY.',
 'On a contaminated runway the 5-sec delay is your saviour. If you brake too soon, wheels lock. The system delays for you.',
 NULL),

(@lesson_id, 70, 'system',
 'Park Brake — 6 Applications, 500 PSI Minimum',
 'The emergency/parking brake system runs from No.2 hyd or, if No.2 is unavailable, from a parking brake accumulator. The accumulator gives approximately <strong>6 applications</strong> when fully charged. Pre-engine-start minimum: <strong>500 PSI</strong> on the PK BRK indicator (PSA on the MFD). Below 500 PSI, hand-pump in the right main wheel well to recharge, OR run the SPU + PTU on AC power. The EMERG BRAKE lever has proportional pull (more pull = more pressure) with a PARK detent at full back. PARK detent illuminates the PARKING BRAKE caution light. With park brake set, application of engine power triggers the T/O warning horn. There is NO differential braking and NO anti-skid in EMERG BRAKE mode.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Park brake accumulator + 500 PSI minimum',
 '6-APP-500-PSI-PARK · no differential · no anti-skid.',
 'When you set park brake on a hot day on a sloped ramp, watch the PK BRK gauge — accumulator pressure drops as the system holds. Pump it up if needed.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Normal Sequence — Pre-Flight, Up, Down',
 'Pre-flight: park brake set, accumulator ≥ 500 PSI. Confirm gear handle DN, three green LEFT/NOSE/RIGHT, no amber DOOR or HANDLE. ANTI SKID ON + TEST: caution lights cycle for 6 sec on the ground. STEERING switch to STEERING once towbar is disconnected. Take-off roll: rudder pedals only (±8°). Pitch up + positive rate: gear UP. Hold LOCK RELEASE button, move handle to UP. During retraction: red unsafe + amber HANDLE + amber DOOR advisories illuminate momentarily; green extinguish. When fully retracted: all advisories extinguish. Approach: gear DN before glide intercept. Three green; no red; no amber. ANTI SKID confirmed on. Touchdown: brake pedals as required; anti-skid modulates. Park brake set after taxi-in.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Normal gear sequence — pre-flight, T-O, approach',
 'Pre-flight 3 green · T-O pedals only · approach 3 green · post-land park brake.',
 'Three green is your contract with the aeroplane: no red, no amber. If you see anything else, you''re running a non-normal.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Pre-Flight Checks — ANTI SKID, STEERING, EMER BRAKE',
 'Three pre-flight checks worth drilling. <strong>ANTI SKID:</strong> switch to ON; the system runs a self-test at start-up; lights illuminate then extinguish. Or hold momentary TEST: INBD + OUTBD ANTISKID caution lights illuminate 6 seconds on the ground, then extinguish. (Self-test is prevented above 17 kts wheel speed.) <strong>STEERING:</strong> verify STEERING switch off when towbar connected; on once disconnected. Check tiller travel. Confirm hydraulic pressure available (No.2 hyd green). <strong>EMER BRAKE:</strong> verify accumulator pressure ≥ 500 PSI on PK BRK indicator. If below 500, hand-pump in right main wheel well or run SPU + PTU. Set park brake at PARK detent before crew briefing; verify PARKING BRAKE caution light illuminates.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Three pre-flight checks',
 'ANTI SKID · STEERING · EMER BRAKE pressure ≥ 500.',
 'Skipping the EMER BRAKE pressure check is the most common pre-flight skip. A flat accumulator on a sloped ramp is a recipe for an unscheduled push.',
 JSON_OBJECT(
   'prompt', 'What is the minimum park brake accumulator pressure required before engine start, and how do you increase it if low?',
   'options', JSON_ARRAY(
     '300 PSI; hand pump only',
     '500 PSI; hand pump in right main wheel well, OR run SPU + PTU on AC power',
     '1000 PSI; only via No.2 hyd pump',
     'Any pressure; no minimum required'
   ),
   'correct_index', 1,
   'explanation', '500 PSI minimum. Increase via hand pump in right main wheel well, or run SPU + PTU on AC power. Mnemonic: 6-APP-500-PSI-PARK.'
 )),

(@lesson_id, 100, 'abnormal',
 'LDG GEAR INOP — Alternate Extension Sequence',
 'LDG GEAR INOP caution illuminates on hydraulic sequencing valve fault or PSEU control loss. Run the alternate extension. The sequence is a strict three-door + hand pump pattern: <strong>(1) INHIBIT switch</strong> (overhead, guarded) to INHIBIT — isolates all hyd from the gear system. <strong>(2) MAIN LANDING GEAR ALTERNATE RELEASE door</strong> (overhead) — opens bypass valve mechanically. <strong>(3) Pull MAIN L/G RELEASE handle</strong> fully — releases main gear doors and uplocks; mains free-fall (may not fully extend). <strong>(4) LANDING GEAR ALTERNATE EXTENSION door</strong> (floor) — opens MLG alt selector valve. <strong>(5) Pull NOSE L/G RELEASE handle</strong> fully — nose gear free-falls; airflow assists. <strong>(6) Hand pump</strong> (handle behind copilot seat, into floor socket) until handle stiff — completes MLG downlock. <strong>(7) ALTERNATE DOWNLOCK VERIFICATION switch to AFT</strong> — three green floor downlock verification lights confirm. <strong>(8) BOTH DOORS LEFT FULLY OPEN</strong> after extension.',
 'video', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Alternate gear extension 8-step sequence',
 '3-DOOR-ALT-EXTEND · FREE-FALL-PUMP-LOCK · 3-GREEN-FLOOR-LIGHTS · BOTH-DOORS-OPEN-AFTER.',
 'Drill this in the sim every recurrent. The temptation under stress is to skip the INHIBIT step — don''t.',
 JSON_OBJECT(
   'prompt', 'You have an LDG GEAR INOP caution. List the FIRST step of alternate extension.',
   'options', JSON_ARRAY(
     'Pull the MAIN L/G RELEASE handle',
     'Set LANDING GEAR INHIBIT switch to INHIBIT — isolates all hydraulic pressure from the gear system',
     'Open the floor extension door',
     'Operate the hand pump'
   ),
   'correct_index', 1,
   'explanation', 'INHIBIT first — isolates hyd. Then RELEASE door + handle (mains), then EXTENSION door + handle (nose), then pump for downlock. Mnemonic: INHIBIT-RELEASE-EXTEND-PUMP.'
 )),

(@lesson_id, 110, 'abnormal',
 'NOSE STEERING Caution — Two Triggers',
 'NOSE STEERING caution has TWO distinct trigger conditions and they tell you different things. <strong>(1) SCU fault with STEERING switch ON</strong> — the SCU has detected a fault while powered. The system reverts to passive caster (±120°). Use differential braking + power for directional control on the ground. <strong>(2) Hydraulic pressure detected with STEERING switch OFF</strong> — unexpected pressure in the steering system. Could indicate a stuck valve or leak. Investigate; consider towing rather than taxiing if on the ground; check no movement of the nosewheel without command. NOTE: the caution does NOT come on if electrical power is removed from the SCU — so loss of SCU power gives no caution but no steering either.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'NOSE STEERING caution — two trigger conditions',
 'ON + fault → caster mode. OFF + pressure → leak / stuck valve.',
 'On the ground at the destination, NOSE STEERING with switch OFF means something is wrong in the system — get a tow.',
 NULL),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Gear + Brake Non-Normals',
 'Q400 QRH non-normals for gear + brakes cluster into five groups. (1) LDG GEAR INOP: alternate extension 8-step sequence. (2) Loss of No.2 hydraulic: alternate extension; no nosewheel steering — taxi via differential brake. (3) Gear indication failure: ALTERNATE DOWNLOCK VERIFICATION switch to AFT confirms via 3 floor lights. (4) NOSE STEERING caution: identify trigger condition; in caster mode if SCU fault. (5) Anti-skid fault (INBD/OUTBD ANTISKID caution): brake pressure no longer modulated on affected side; brief approach for asymmetric/longer roll. (6) Park brake low: hand pump or SPU + PTU recharge. The alternate extension steps must be reflexive — drill in the sim.',
 'image', '/assets/aircraft/q400/landing-gear-flow.svg',
 'QRH gear/brake non-normal cluster',
 'LDG GEAR INOP · No.2 loss · indication fail · NOSE STEER · anti-skid fault · park brake.',
 'Reflexive alternate-extension is the difference between a controlled gear-down and an unplanned gear-up. Drill it.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Gear Down Selected, No Three Green',
 'Setup: 8 nm from the destination, on the ILS at 2500 ft AGL, configured for landing. You select gear DN. The gear cycles audibly but the LEFT and RIGHT green lights illuminate, while NOSE green stays out. RED NOSE unsafe is illuminated. Amber N. DOOR is illuminated (door open). Selector handle still amber.\n\nDiagnosis: nose gear extension failure. Possible causes: hydraulic sequencing valve stuck, mechanical jam, PSEU disagreement.\n\nFirst 30 seconds: PF maintains aircraft control, climbs back to a safe altitude (5000+ AGL) for non-normal handling. PNF calls "NOSE GEAR UNSAFE." Captain commands "Going around, alternate extension." Power up; flaps to climb setting; positive rate; gear handle remains DN per QRH.\n\nNext 5 minutes: ALTERNATE DOWNLOCK VERIFICATION switch to AFT — confirm whether the nose gear is down + locked despite the indication failure. If three green floor lights illuminate, treat as indication-only failure; continue approach with caution. If nose gear NOT confirmed down, run alternate extension: INHIBIT + MAIN RELEASE handle (mains free-fall) + EXTENSION door + NOSE RELEASE handle + hand pump if MLG not locked. Three green floor lights confirm. Brief approach for nose-gear-issue contingency; declare PAN-PAN; full ARFF on the field; longest available runway.',
 'animation', '/assets/aircraft/q400/landing-gear-flow.svg',
 'Nose gear unsafe scenario',
 'Indication first · floor lights · alt extension if needed · longest runway.',
 'Don''t descend on a non-three-green. Climb to a workable altitude and run the QRH properly.',
 JSON_OBJECT(
   'prompt', 'Gear down selected. LEFT and RIGHT green illuminate, but NOSE green stays out. NOSE red unsafe is illuminated. First action?',
   'options', JSON_ARRAY(
     'Continue the approach — green and red simultaneous is normal',
     'Climb to a workable altitude (5000+ AGL); set ALTERNATE DOWNLOCK VERIFICATION to AFT and check 3 green floor lights; if not confirmed, run alternate extension',
     'Land immediately gear-up',
     'Pull the MAIN L/G RELEASE handle'
   ),
   'correct_index', 1,
   'explanation', 'Climb first to a workable altitude. Use ALTERNATE DOWNLOCK VERIFICATION to confirm if it is just an indication failure (3 floor lights). If NOT confirmed, run full alternate extension. Brief approach for nose-gear-issue contingency.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Landing Gear in 60 Seconds',
 'Recap:\n  • Tricycle dual-wheel retractable. Mains AFT. Nose FORWARD.\n  • No.2 hyd: gear + steering. No.1 hyd: brakes. EMERG BRAKE: No.2 or accumulator.\n  • Steering: tiller ±70° (low-speed taxi); pedals ±8° (high-speed taxi/T-O/landing); caster ±120°.\n  • Reverse taxi: STEERING ON, NO tiller or pedal input.\n  • Anti-skid: arms >10 kts; self-test prevented >17 kts; 5-sec brake delay cancels at 35 kts.\n  • Park brake: ~6 applications from accumulator; 500 PSI minimum before start. Hand pump in right main wheel well, or SPU + PTU.\n  • Gear warning tone: 3 trigger groups; ONLY case 3 (single-engine fail <156 KIAS, RA <1053 ft) may be MUTED.\n  • LDG GEAR INOP: alternate extension via INHIBIT + RELEASE door + EXTENSION door + hand pump + DOWNLOCK VERIFICATION (3 green floor lights).\n  • Both alt-extension doors LEFT FULLY OPEN after.\n  • Indications: amber DOOR (door open), green LEFT/NOSE/RIGHT (down + locked), red unsafe, amber HANDLE (handle vs gear disagree).\n  • EMERG BRAKE: NO differential, NO anti-skid.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'N2-GEAR-N1-BRAKES · NOSE-FWD-MAINS-AFT · 70-HAND-8-PEDAL-120-CASTER · 3-DOOR-ALT-EXTEND · 156-KIAS-1053-RA · 3-TONE-GROUPS-MUTE-3 · 10-17-35-BRAKE · 5-SEC-BRAKE-DELAY · 6-APP-500-PSI-PARK · FREE-FALL-PUMP-LOCK · BOTH-DOORS-OPEN-AFTER · 3-GREEN-FLOOR-LIGHTS',
 'Twelve mnemonics carry every landing-gear question on a recurrent. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
