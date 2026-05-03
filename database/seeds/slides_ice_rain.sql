-- =============================================================================
-- AviatorTutor — Phase 9: ATA 30 Ice & Rain Protection — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'ice-rain' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'ice-rain-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Approved for Known Icing — How the Q400 Stays Clean',
 'The Q400 is approved for flight into known icing. Translating that approval into safe ops needs four layered systems: detection (two automatic Ice Detector Probes that fire at 0.5 mm of ice), de-icing (pneumatic rubber boots on wings, tails, and nacelle inlets fed by engine bleed at 18 PSI), anti-icing (electric heating on probes, windshields, AOA vanes, intake flanges, and the pilot side window), and rain removal (electrically driven wipers with PARK / OFF / LOW / HIGH plus an alternate pilot pushbutton). Layered on top: a REF SPEEDS switch at INCR that tells the Stall Protection System to adjust margins for the iced airframe. This lesson walks each layer, the limits, and the abnormals.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Q400 ice and rain protection overview',
 'Detect · de-ice · anti-ice · wipe. Layered defence for known icing.',
 'On any leg with TAT below +5 and visible moisture, the icing chant runs in your head: REF SPEEDS INCR · PROPS ON · AIRFRAME FAST · INTAKES ON · monitor DEICE PRESS.',
 NULL),

(@lesson_id, 20, 'concept',
 'Ice Detection — Two Probes, No Switch',
 'The Ice Detection System (IDS) has TWO probes — one each side of the front fuselage — and is fully automatic. There is NO flight-deck control: as soon as 115 VAC is available, the IDS is alive. When either probe accumulates more than 0.5 mm of ice, the ED shows a flashing white reverse-video ICE DETECTED message for 5 seconds. The probe then heats itself with 115 VAC to clear the ice and resume detection. The system is redundant — single probe failure is silent. Only when BOTH probes fail does the ICE DETECT FAIL caution illuminate. Setting REF SPEEDS to INCR clears the reverse video; the message stays in normal video while in icing, with [INCR REF SPEED] white below it confirming the SPS adjustment.',
 'diagram', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Two IDPs + ICE DETECTED logic',
 '2-IDP-AUTO · 0.5-MM-ICE · BOTH-IDPS-FAIL-CAUTION.',
 'There is no switch to flip — the IDS just works. Pre-flight check is "no ICE DETECT FAIL caution" and you are done.',
 NULL),

(@lesson_id, 30, 'concept',
 'Pneumatic vs Electric — Which Surface Gets Which',
 'Memorise the split. <strong>Pneumatic boots</strong> protect: wing leading edges (extension + outboard + outboard centre + inboard centre + inboard sections), horizontal stabiliser (inboard + outboard), vertical stabiliser (upper + lower), and the nacelle inlet lips. <strong>Electric anti-ice</strong> protects: pilot/copilot/standby pitot-static probes, left/right AOA vanes, left/right engine intake flanges, both windshields, and the pilot side window. The propeller blade leading edges are also electric (heating elements covering 70% of each blade). One way to remember: anything that has to remain perfectly smooth for sensing or visibility is ELECTRIC; the big lifting surfaces and inlets are BOOTS.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Pneumatic boots vs electric anti-ice mapping',
 'BOOTS-LIPS-WINGS-TAIL · ELEC-PROBES-WINDOWS-INTAKE-AOA.',
 'Walking the airframe pre-flight: visualise which surface gets which. It builds the mental model for the panel switches.',
 JSON_OBJECT(
   'prompt', 'Which surface on the Q400 is protected by ELECTRIC anti-icing rather than pneumatic boots?',
   'options', JSON_ARRAY(
     'Wing leading edges',
     'Horizontal stabiliser leading edge',
     'Pitot/static probes',
     'Nacelle inlet lips'
   ),
   'correct_index', 2,
   'explanation', 'Pitot/static probes are ELECTRIC. Wing/tail leading edges and nacelle inlet lips use pneumatic BOOTS. Mnemonic split: BOOTS-LIPS-WINGS-TAIL vs ELEC-PROBES-WINDOWS-INTAKE-AOA.'
 )),

(@lesson_id, 40, 'system',
 'Boot Pressure — 18 PSI From Bleed, Independent of BLEED Switch',
 'The boots inflate with engine bleed air, regulated to <strong>18 PSI ± 3</strong>. Critical detail: this bleed source is taken from the bleed port of each engine and is INDEPENDENT of the BLEED control switch position. Even with bleed selected OFF in the cockpit, boots will still inflate. The DEICE PRESS gauge on the copilot side panel reads pressure: NORM mode shows the average of left and right; ISO mode shows the individual side under check. The BOOT AIR switch controls the isolator valve (NORM = both sides connected; ISO = sides separated for individual pressure check or to isolate a leak). On any inflated boot, the green BOOT INFLATION advisory light illuminates when that boot reaches ≥ 15 PSI.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Boot air regulated 18 PSI vs 15 PSI BOOT INFLATION light',
 '18-PSI-BOOTS-15-LIGHT · BLEED-INDEPENDENT · BOOT AIR NORM/ISO.',
 'On descent into an icy field, watch the DEICE PRESS gauge — if it slumps below 15 PSI you may need to advance the power levers to bring NL up.',
 NULL),

(@lesson_id, 50, 'system',
 'AIRFRAME MODE SELECT — SLOW vs FAST Cycles',
 'The AIRFRAME MODE SELECT rotary has four positions: OFF, MANUAL, SLOW, FAST. The two automatic modes differ only in cycle timing. <strong>SLOW</strong>: 3-minute cycle, 144-second dwell between end-of-cycle and restart. <strong>FAST</strong>: 1-minute cycle, 24-second dwell. Each cycle inflates 6 paired combinations of boots, with each combination held for 6 seconds. The TMU (Timer and Monitor Unit) controls the sequence and monitors valve health. Use SLOW in light icing where ice accretes slowly between cycles; use FAST in moderate-to-heavy icing where you need the boots cycling quickly. MANUAL mode lets you fire individual pairs via the AIRFRAME MANUAL SELECT 6-detent rotary — used when the TMU fails (DE-ICE TIMER caution).',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'SLOW vs FAST mode timing comparison',
 'SLOW-3-MIN-FAST-1-MIN · 6-COMB-6-SEC.',
 'In real moderate icing the FAST cycle keeps the leading edges clean. In light intermittent icing SLOW is gentler on the boots and bleed.',
 JSON_OBJECT(
   'prompt', 'On the AIRFRAME MODE SELECT rotary, what are the cycle timings for SLOW and FAST modes?',
   'options', JSON_ARRAY(
     'SLOW = 1 minute, FAST = 30 seconds',
     'SLOW = 3 minutes (144-sec dwell), FAST = 1 minute (24-sec dwell)',
     'SLOW = 5 minutes, FAST = 2 minutes',
     'Both cycles are identical — only the dwell differs'
   ),
   'correct_index', 1,
   'explanation', 'SLOW = 3-minute cycle, 144-sec dwell. FAST = 1-minute cycle, 24-sec dwell. Each combination of boots inflates for 6 seconds, 6 combinations per cycle. Mnemonic: SLOW-3-MIN-FAST-1-MIN.'
 )),

(@lesson_id, 60, 'system',
 'Propeller Heaters — TAT, Not SAT',
 'Each propeller has six blades. Each blade has an electric heating element covering 70% of the blade leading edge, fed from the related 115 VAC variable-frequency bus. The TMCU (Timer Monitor Control Unit, one per propeller) cycles the heater based on TAT, not SAT. All six blades on one propeller are heated simultaneously, then the other propeller — load balancing across the AC bus. The PROP selector has three positions: TEST (each prop heated 5 sec separately, NP &gt; 400 RPM and AC required, 30-sec cooldown before retest), OFF, ON (TMCU cycles automatically). Cycling conditions: PROP ON + TAT ≤ +5°C + NP &gt; 400 RPM. Important quirk: at high airspeed, TAT can be many degrees warmer than SAT — at SAT 5°C and high speed, the heaters may not cycle. With visible ice accretion, the system functions regardless of indicated SAT.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Prop heater TAT and NP cycling logic',
 'TAT-5-NP-400. 6 blades simultaneous; one prop then the other.',
 'On a checkride the trap is "the SAT is +3°C, why aren''t the prop heaters cycling?" Answer: TAT is +9 because of airspeed.',
 NULL),

(@lesson_id, 70, 'system',
 'PROP TEST — 5 Seconds, 30-Second Cooldown',
 'PROP TEST is part of every pre-flight when icing is forecast. Set the PROP selector to TEST. Each propeller is heated for 5 seconds — first one prop, then the other — with the corresponding PROPS advisory light illuminating to confirm. Mandatory NP &gt; 400 RPM (so condition lever at MIN or higher with the engines running). AC power must be available. After the test, the system enforces a 30-SECOND COOLDOWN before another test can be initiated — this prevents element overheating. Forgetting the cooldown is a common pre-flight skip.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'PROP TEST sequence with 30-sec cooldown',
 '30-SEC-PROP-TEST-COOL. NP > 400 RPM. AC required.',
 'When the FO calls "TEST again" within 30 seconds and nothing happens, do not call maintenance — wait the cooldown.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Entering Icing — The Crew Chant',
 'The full sequence when entering icing conditions: <strong>(1) REF SPEEDS to INCR</strong> — SPS adjusts stall margin; [INCR REF SPEED] message confirms. <strong>(2) PROPS ON</strong> — TMCU starts cycling per TAT. PROPS advisory lights illuminate. <strong>(3) AIRFRAME MODE SELECT to FAST</strong> (or SLOW for light icing) — TMU starts boot sequence. BOOT INFLATION lights cycle green. <strong>(4) ENGINE INTAKE OPN HTR ON</strong> for both engines. <strong>(5) WINDSHIELD WARM</strong> if needed; PITOT STATIC heat ON if not already; PLT SIDE WDO/HT ON. <strong>(6) Monitor DEICE PRESS</strong> — gauge should stay within 18 ± 3 PSI; advance NL on descent / holding / approach if pressure slumps below 15.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Icing entry chant — six steps',
 'REF SPEEDS INCR · PROPS ON · AIRFRAME FAST · INTAKES · monitor DEICE PRESS.',
 'Brief the icing chant at top-of-climb whenever icing is forecast. The order matters: REF SPEEDS first so the SPS is set before you''re actually in ice.',
 JSON_OBJECT(
   'prompt', 'You enter visible icing. What is the FIRST switch action to inform the Stall Protection System?',
   'options', JSON_ARRAY(
     'AIRFRAME MODE SELECT to FAST',
     'PROP selector to ON',
     'REF SPEEDS to INCR',
     'BOOT AIR to ISO'
   ),
   'correct_index', 2,
   'explanation', 'REF SPEEDS to INCR signals the SPS to adjust stall margin for icing. [INCR REF SPEED] message appears below ICE DETECTED. Mnemonic: INCR-FOR-ICING.'
 )),

(@lesson_id, 90, 'normal_op',
 'Cruise Discipline in Icing',
 'Once the icing chant is complete, the cruise scan adds a few items every 10 minutes. Watch the DEICE PRESS gauge — within 18 ± 3 PSI on NORM. Confirm BOOT INFLATION lights are cycling per the selected mode. Confirm PROPS advisory lights are cycling per TMCU schedule. Watch for any DE-ICE PRESS / DE-ICE TIMER / ICE DETECT FAIL caution. On descent, holding, or approach the engine NL drops naturally and bleed pressure can fall below 15 PSI — be ready to advance the POWER levers to maintain pressure. Brief the FO: "Call any boot light failure or pressure dip." When you exit icing, REF SPEEDS to OFF; PROPS OFF; AIRFRAME OFF; intake heaters as required.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Cruise scan + descent NL discipline',
 'DEICE PRESS · boot lights · PROP lights · NL-FOR-PRESSURE.',
 'On a long approach into a cold field with throttles at idle, the boots may struggle to find pressure. Push up NL early.',
 NULL),

(@lesson_id, 100, 'abnormal',
 'DE-ICE PRESS Caution — Diagnostic Chain',
 'DE-ICE PRESS caution illuminates when: main de-ice pressure on either side &lt; 15 PSI, OR boot pressure fails to reach 15 PSI after the DDV opens, OR boot pressure stays at 15 PSI after the DDV closes (a stuck-open DDV). Diagnostic chain: (1) Check bleed availability — both engines providing bleed? Increase NL if possible. (2) Switch BOOT AIR to ISO and check left and right pressure individually on the DEICE PRESS gauge — identifies which side is the leaker. (3) If one side is healthy, leave BOOT AIR at ISO with the leaking side isolated; the healthy side continues to protect that wing/stab/inlet. Stabilizer boots are pneumatically cross-connected so tail protection survives one-side loss. (4) If the failure is a stuck-open DDV (boot stuck at 15 PSI after closure), select MANUAL on AIRFRAME MODE SELECT to depressurise; expect reduced boot performance.',
 'video', '/assets/aircraft/q400/ice-rain-flow.svg',
 'DE-ICE PRESS diagnostic chain',
 'Check bleed · BOOT AIR ISO · identify leaker · isolate · MANUAL if stuck DDV.',
 'A leak that doesn''t isolate cleanly is a divert candidate when the icing is moderate or worse — you''ve lost the layered defence.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'DE-ICE TIMER Fail — Manual Cycling',
 'DE-ICE TIMER caution illuminates on TMU failure (auto sequencer / logic / input disagreement). The boots no longer cycle automatically. Recovery: select AIRFRAME MODE SELECT to MANUAL — DDVs and check valve heaters power on permanently. Use the AIRFRAME MANUAL SELECT 6-detent rotary to cycle individual boot pairs. Hold each detent until the corresponding pair of green BOOT INFLATION lights illuminate (confirms full inflation), then move to the next detent. Minimum 24-second dwell before re-inflating the SAME pair (prevents bond stress and ensures cleaner ice shed). The 6-detent sequence duplicates the auto sequence, just paced manually. Brief the FO to read the lights and call out failures while you cycle.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'TMU fail recovery via AIRFRAME MANUAL SELECT',
 'TMU fail · MANUAL mode · 6 detents · 24-sec dwell · light confirms.',
 'Manual cycling is a workload event. Brief the FO to call lights; you focus on the rotary.',
 JSON_OBJECT(
   'prompt', 'DE-ICE TIMER caution illuminates in icing. What is the recovery action?',
   'options', JSON_ARRAY(
     'Set AIRFRAME MODE SELECT to FAST and continue',
     'Set AIRFRAME MODE SELECT to MANUAL and use AIRFRAME MANUAL SELECT to fire boots in 6-detent sequence with 24-sec dwell',
     'Push BOOT AIR to ISO',
     'Disable propeller heaters'
   ),
   'correct_index', 1,
   'explanation', 'TMU failure → MANUAL mode. Use the 6-detent AIRFRAME MANUAL SELECT to cycle each pair. Hold until both lights confirm; observe 24-sec dwell minimum before re-firing the same pair.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Icing Non-Normals',
 'Q400 QRH non-normals for ice/rain cluster into five groups. (1) ICE DETECT FAIL: both probes failed — manually monitor for visible ice; treat as in-icing for prop/airframe protection if conditions support. (2) DE-ICE PRESS: identify leaker via BOOT AIR ISO; isolate side; advance NL if low. (3) DE-ICE TIMER: MANUAL mode + 6-detent cycling. (4) Pitot/static heat fail: airspeed unreliable on affected side; switch source per QRH. (5) PROPS heater fault: single-prop ice protection lost — consider exit from icing. None of these are memory items but recognising the cascade between failures and your boot lights is the key skill.',
 'image', '/assets/aircraft/q400/ice-rain-flow.svg',
 'QRH ice cluster diagram',
 'ICE DETECT FAIL · DE-ICE PRESS · DE-ICE TIMER · PITOT-STATIC · PROP HTR.',
 'Most icing non-normals are recoverable in flight. The exit-from-icing decision is your captain call — based on weather, airframe ice, and remaining redundancy.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Boot Pressure Slumping in the Hold',
 'Setup: holding at 8000 ft over a snowy regional airport, in moderate icing. AIRFRAME MODE SELECT at FAST. Five minutes ago BOOT INFLATION lights were cycling cleanly green. Now you notice: lights are taking longer to come on, then dimmer, then sometimes not at all. DEICE PRESS gauge: dropping below 18, now flickering near 15. Engines are at idle in the hold.\n\nDiagnosis: bleed pressure is low because NL is low at idle. Action: advance both POWER levers to bring NL up to a useful bleed value (target a higher torque while still respecting the holding speed). Watch DEICE PRESS climb back into 18 ± 3. BOOT INFLATION lights resume normal cycling. Brief the FO: "Holding fuel-burn higher because we need the NL." Plan the descent to keep NL up — slower idle descent if needed, with appropriate speed/altitude management. If the leg ahead is short, consider exiting the hold and committing to approach.',
 'animation', '/assets/aircraft/q400/ice-rain-flow.svg',
 'Boot pressure recovery in hold via NL increase',
 'NL-FOR-PRESSURE · idle hold + icing = trap · advance levers · monitor.',
 'Holding at idle in icing is the most common boot-pressure trap. Push NL up, even at the cost of fuel burn.',
 JSON_OBJECT(
   'prompt', 'In a hold at idle thrust in moderate icing, BOOT INFLATION lights stop cycling and DEICE PRESS slumps below 15 PSI. Best action?',
   'options', JSON_ARRAY(
     'Switch BOOT AIR to ISO',
     'Advance both POWER levers to raise NL and restore bleed pressure',
     'Switch AIRFRAME MODE SELECT to MANUAL',
     'Disable propeller heaters to reduce electrical load'
   ),
   'correct_index', 1,
   'explanation', 'Low NL at idle starves the bleed system. Advance POWER levers to raise NL; bleed pressure recovers; BOOT INFLATION lights resume. Mnemonic: NL-FOR-PRESSURE.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Ice & Rain in 60 Seconds',
 'Recap:\n  • 2 IDPs, automatic, no cockpit switch. Trigger at >0.5 mm of ice. Both must fail for ICE DETECT FAIL.\n  • Pneumatic boots on wings, horizontal+vertical stab, nacelle inlet lips. Bleed-fed, regulated 18 PSI ±3. BOOT INFLATION light at ≥15 PSI.\n  • Boot air is INDEPENDENT of BLEED switch.\n  • AIRFRAME MODE SELECT: OFF / MANUAL / SLOW (3-min cycle, 144-sec dwell) / FAST (1-min cycle, 24-sec dwell). 6 boot combos × 6 sec each.\n  • BOOT AIR: NORM (sides connected) / ISO (separated; for individual check or to isolate a leak).\n  • Prop heaters: 6 blades per prop, 70% coverage, 115 VAC VF bus. TMCU per prop. TAT ≤ +5°C AND NP > 400 RPM AND PROP ON.\n  • PROP TEST: 5 sec each, 30-sec cooldown before retest.\n  • Electric anti-ice: pitot-static probes, AOA vanes, intake flanges, both windshields, pilot side window.\n  • REF SPEEDS to INCR signals SPS to adjust stall margin in icing. [INCR REF SPEED] message confirms.\n  • Icing chant: REF SPEEDS INCR · PROPS ON · AIRFRAME FAST · INTAKES ON · DEICE PRESS monitor.\n  • In hold/descent, advance NL to keep bleed pressure ≥ 15 PSI.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 '2-IDP-AUTO · 0.5-MM-ICE · 18-PSI-BOOTS-15-LIGHT · SLOW-3-MIN-FAST-1-MIN · 6-COMB-6-SEC · BOOTS-LIPS-WINGS-TAIL · ELEC-PROBES-WINDOWS-INTAKE-AOA · TAT-5-NP-400 · 30-SEC-PROP-TEST-COOL · BLEED-INDEPENDENT · INCR-FOR-ICING · NL-FOR-PRESSURE',
 'Twelve mnemonics carry every icing question on a recurrent. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
