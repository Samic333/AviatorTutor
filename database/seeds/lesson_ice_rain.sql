-- =============================================================================
-- AviatorTutor — Phase 9 (ATA 30 Ice & Rain Protection) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'ice-rain' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'ice-rain-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Ice & Rain Protection — Boots, Heaters, Probes, Wipers',
     'ice-rain-overview',
     'overview',
     'Q400 is approved for flight into KNOWN ICING. Two ice-detection probes (IDPs) trigger ICE DETECTED at 0.5 mm of ice. Pneumatic rubber de-icing boots on wings, horizontal + vertical stabilizers, and nacelle inlet lips, regulated to 18 PSI from engine bleed (BOOT INFLATION light at ≥15 PSI). AIRFRAME MODE SELECT: OFF / MANUAL / SLOW (3-min cycle, 144-sec dwell) / FAST (1-min cycle, 24-sec dwell). BOOT AIR switch isolates left/right systems via ISO valve. Electric anti-icing on pitot/static probes, AOA vanes, engine intake flanges, both windshields, pilot side window. Propeller blade heaters via TMCU on 115 VAC variable-frequency bus — one prop at a time, NP >400 RPM and TAT ≤+5°C required. REF SPEEDS at INCR adjusts SPS stall margin for icing.',
     '<p>Ice protection on the Q400 is a layered defence: an automatic detection system, a pneumatic de-icing system for the airframe leading edges, an electric anti-icing system for probes and windshields, electric heating for the propeller blades, and electrically operated wipers for rain removal. The defining design choices are: (1) detection is fully automatic — there is no flight-deck control for the IDS, (2) the boots are pneumatically inflated by engine bleed regardless of the BLEED control switch position, (3) the propeller heaters cycle on TAT (not SAT) and are controlled by per-prop TMCUs, and (4) the SPS is informed of icing via the REF SPEEDS switch at INCR, which adjusts stall margins for the iced-airframe condition. Memorise the 18 PSI / 15 PSI / 0.5 mm / 400 RPM / +5°C numbers — every icing question on a recurrent has at least one of them in it.</p>',
     JSON_ARRAY(
       'Q400 is APPROVED for flight into KNOWN ICING conditions',
       'Ice Detection System (IDS): 2 Ice Detector Probes (IDPs) on left and right side of front fuselage. Operates automatically with 115 VAC available — NO flight-deck control',
       'IDP trigger: more than 0.5 mm of ice → ICE DETECTED message on ED in white reverse video for 5 seconds, then normal video if REF SPEEDS at INCR',
       'IDP self-deices: when an IDP detects ice it heats itself with 115 VAC to clear, then resumes detection',
       'ICE DETECT FAIL caution: BOTH probes failed. Single-probe failure does NOT illuminate the caution (system is redundant)',
       'Airframe de-icing: pneumatic rubber boots on wing leading edges, horizontal stabiliser, vertical stabiliser, and nacelle inlet lips',
       'Boot air source: bleed port of each engine — INDEPENDENT of BLEED control switch position',
       'Boot regulated pressure: 18 PSI (gauge shows 18 ± 3 PSI). DEICE PRESS caution if main pressure <15 PSI',
       'BOOT INFLATION advisory light (green): illuminates when related boot pressure ≥ 15 PSI',
       'AIRFRAME MODE SELECT: OFF (TMU controls valve heaters only) / MANUAL (DDVs and heaters on permanently) / SLOW (3-min cycle, 144-sec dwell) / FAST (1-min cycle, 24-sec dwell)',
       'BOOT AIR switch: NORM (ISO valve open, both sides connected) or ISO (isolated — for individual pressure check or to isolate a leak)',
       'Boot operation: 6 sec inflation per combination × 6 combinations = automatic sequence handled by TMU (Timer and Monitor Unit)',
       'AIRFRAME MANUAL SELECT: 6 detent positions duplicating the auto sequence (with OFF positions in between). Used if TMU fails',
       'DDV (Dual Distributing Valve) heaters and check-valve heaters auto-on when SAT < +5°C with switch at OFF/SLOW/FAST. Permanently on when MANUAL',
       'Propeller heaters: electric, on the leading 70% of each blade. 6 blades per prop, all heated simultaneously',
       'Prop heater power: 115 VAC variable-frequency bus. TMCU per propeller controls cycle. One prop heats then the other (load balancing)',
       'PROP selector: TEST (each prop heated 5 sec separately) / OFF / ON. TEST has 30-sec cooldown to prevent element overheat',
       'Prop heater cycle prerequisites: TAT ≤ +5°C AND NP > 400 RPM AND PROP switch ON',
       'TAT-based cycle times: e.g. -7 < TAT ≤ +5 → 12 sec ON / 78 sec OFF; TAT ≤ -22 → 92 sec ON / 108 sec OFF',
       'Electric anti-icing: pitot/static probes, AOA vanes, engine intake flanges, both windshields, pilot side window',
       'Windshield modes: WARM and NORM. WIPER speeds: PARK / OFF / LOW / HIGH. Alternate pilot wiper pushbutton drives pilot wiper at HIGH if main fails',
       'REF SPEEDS switch INCR: tells SPS we are in icing — Stall Protection System adjusts stall margin. [INCR REF SPEED] message in white below ICE DETECTED',
       'Boot air also pressurises forward passenger door seal, aft baggage door seal, and operates the AFT safety-valve ejector for the pressurisation system'
     ),
     JSON_ARRAY(
       'IDS is fully automatic — there is no flight-deck switch. Pre-flight check: power up, confirm no ICE DETECT FAIL caution.',
       'Boot air comes from engine bleed but is INDEPENDENT of the BLEED control switch. Even with BLEED selected OFF, boots will still inflate.',
       'During descent or holding, NL may need to be increased (advance POWER levers) to maintain 15 PSI minimum boot pressure.',
       'Single IDP failure does NOT illuminate ICE DETECT FAIL — the system is redundant. Both must fail.',
       'Prop heaters use TAT (not SAT). At 5°C SAT and high airspeed, TAT can be much higher than +5°C → heaters won''t cycle.',
       'With visible ice accumulation, expect prop heaters to function regardless of observed SAT — the dynamic icing logic kicks in.',
       'REF SPEEDS to INCR is required when icing is detected. The SPS adjusts stall margin; failing to set it leaves you with the dry-air margin in icing conditions.',
       'PROP TEST has a mandatory 30-second cooldown before another test can begin — prevents element overheating.',
       'Boot pressure threshold for the BOOT INFLATION light is 15 PSI (NOT 18 PSI — that is regulated normal pressure).'
     ),
     JSON_ARRAY(
       'Boot pressure: regulated at 18 PSI ± 3, but BOOT INFLATION light illuminates at 15 PSI. Two different numbers.',
       'IDP trigger threshold: 0.5 mm of ice (not 1.0 or 2.0).',
       'Prop heater minimum NP: 400 RPM (not 500 or 800).',
       'Prop heater TAT threshold: +5°C (not -5°C — the system uses positive TAT max).',
       'AIRFRAME MODE SELECT cycle times: SLOW = 3 minutes (144-sec dwell), FAST = 1 minute (24-sec dwell). Easy to confuse direction.',
       'Boots are pneumatic only on AIRFRAME (wings + tail + nacelle inlets). Probes/windows/AOA/intake flanges = ELECTRIC.',
       'IDS has NO flight-deck control. Many students assume there''s a switch.',
       'DDV heaters auto-activate at SAT < +5°C with the AIRFRAME MODE SELECT at OFF/SLOW/FAST. NOT MANUAL — under MANUAL they are permanently on.',
       'PROP TEST cooldown: 30 seconds. Trying to retest within 30 sec will not start.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'ice-rain-overview';
