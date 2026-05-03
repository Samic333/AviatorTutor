-- =============================================================================
-- AviatorTutor — Phase 9: ATA 30 Ice & Rain Protection — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'ice-rain' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'ice-rain-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'ICE DETECTED — Entering Icing Conditions',
 'ICE DETECTED message appears on the ED in flashing white reverse video for 5 seconds, then transitions to normal video while in icing. One or both IDPs have detected more than 0.5 mm of ice. Crew action (icing-entry chant): (1) REF SPEEDS to INCR — SPS adjusts stall margin for icing; [INCR REF SPEED] message appears below ICE DETECTED. (2) PROP selector to ON — TMCU starts cycling per TAT; PROPS advisory lights illuminate per propeller. (3) AIRFRAME MODE SELECT to FAST (or SLOW for light icing) — TMU starts boot sequence; BOOT INFLATION lights cycle green. (4) ENGINE INTAKE OPN HTR ON for both engines. (5) WINDSHIELD WARM if needed; PITOT STATIC heat ON; PLT SIDE WDO/HT ON if not already. (6) Monitor DEICE PRESS within 18 ± 3 PSI; advance NL on descent / hold / approach if pressure slumps below 15.',
 0,
 'The icing chant must run in order. REF SPEEDS first sets the SPS before you''re actually in ice. Failing to set REF SPEEDS leaves you with the dry-air stall margin in icing.',
 'ICE DETECTED message on ED (white reverse video flashing for 5 sec, then normal video).',
 'REF SPEEDS · PROPS · AIRFRAME · INTAKES · MONITOR.',
 10),

(@lesson_id, NULL,
 'ICE DETECT FAIL — Both Probes Failed',
 'Both IDPs have failed. The system has lost automatic detection. Action: assume in-icing for all protection if conditions support — visible moisture + TAT ≤ +5°C. Manually monitor for visible ice accretion on probes / windscreen wipers / wing leading edges. If conditions are clearly icing, run the icing-entry chant and treat as in-icing. If conditions are clearly NOT icing (dry air, warm OAT, no moisture), continue with vigilant visual scan and brief the FO on extra workload.',
 0,
 'A single probe failure is silent — the system is redundant. ICE DETECT FAIL only with BOTH failed. Treat conservatively: when in doubt, run the chant.',
 'ICE DETECT FAIL caution illuminated; ICE DETECTED message no longer appearing despite icing conditions.',
 'BOTH FAILED · TREAT AS ICING IF CONDITIONS SUPPORT.',
 20),

(@lesson_id, NULL,
 'DE-ICE PRESS — Pressure Below 15 PSI or DDV Issue',
 'DE-ICE PRESS caution: main de-ice pressure on either side < 15 PSI, OR boot pressure fails to reach 15 PSI after DDV opens, OR boot pressure stays at 15 PSI after DDV closes. Diagnostic: (1) Check NL — at idle in hold/descent NL may be too low to provide bleed; advance POWER levers. (2) Switch BOOT AIR to ISO and read individual side pressure on DEICE PRESS gauge — identifies the leaker. (3) If one side is healthy, leave at ISO with the leaker isolated; the healthy side continues to protect that wing/stabilizer/inlet. Stabilizer boots are pneumatically cross-connected — tail protection survives a one-side loss. (4) If failure is a stuck-open DDV (boot stuck at 15 PSI after closure command), select MANUAL on AIRFRAME MODE SELECT.',
 0,
 'A DE-ICE PRESS in moderate or worse icing without clean isolation is a divert candidate. The layered defence depends on the boots cycling cleanly.',
 'DE-ICE PRESS caution illuminated; DEICE PRESS gauge below 15 PSI on one or both sides.',
 'NL UP · BOOT AIR ISO · IDENTIFY LEAKER · ISOLATE.',
 30),

(@lesson_id, NULL,
 'DE-ICE TIMER — TMU Failure / Manual Cycling',
 'DE-ICE TIMER caution: TMU failure (auto sequencer, logic, or input disagreement). Auto cycling is gone. Recovery: select AIRFRAME MODE SELECT to MANUAL — DDVs and check valve heaters power on permanently. Use AIRFRAME MANUAL SELECT 6-detent rotary to cycle each pair sequentially. Hold each detent until corresponding pair of green BOOT INFLATION lights illuminate (full inflation confirmed). Move to next detent. Minimum 24-second dwell before re-firing the same pair (boot bond stress + cleaner ice shed). Brief the FO to read the lights and call out failures while you cycle through the rotary. Increased workload — consider exit from icing if TMU recovery is unsuccessful.',
 0,
 'Manual cycling is workload-heavy. Brief the FO to read lights; you focus on the rotary. Plan an exit from icing if practicable.',
 'DE-ICE TIMER caution illuminated; auto BOOT INFLATION lights have stopped cycling.',
 'MANUAL · 6-DETENT SEQUENCE · 24-SEC DWELL · LIGHT CONFIRMS.',
 40),

(@lesson_id, NULL,
 'PROP HEATER FAULT — Single-Prop Loss',
 'A single propeller''s heater system has failed (TMCU caution or PROPS advisory failing to illuminate during the expected heat cycle). The affected propeller has lost ice protection — the other propeller remains protected. Action: run QRH PROP HEATER FAULT non-normal; consider exit from icing as soon as practicable; brief approach for asymmetric ice-handling characteristics. Cross-check by switching the PROP selector through TEST after 30-second cooldown — confirm the failure is the heater, not the indication.',
 0,
 'Single-prop ice protection loss is a divert candidate in moderate or worse icing. Asymmetric ice on the propellers can cause vibration and asymmetric thrust at higher power settings.',
 'PROPS advisory light not illuminating during expected heat cycle on one side; possible TMCU caution.',
 'EXIT ICING · PROP TEST AFTER COOLDOWN · DIVERT IF MODERATE+.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
