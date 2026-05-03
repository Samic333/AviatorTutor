-- =============================================================================
-- AviatorTutor — Phase 9: ATA 30 Ice & Rain Protection — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'ice-rain' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Ice & Rain — Detect, De-ice, Anti-ice, Wipers',
 'Schematic of the Q400 ice and rain protection system. Two automatic Ice Detector Probes drive the ICE DETECTED message at >0.5 mm of ice. Pneumatic boots on wings, horizontal + vertical stabilisers, and nacelle inlet lips, fed from each engine''s bleed port (independent of BLEED switch) regulated to 18 PSI. AIRFRAME MODE SELECT chooses SLOW (3-min cycle, 144-sec dwell) or FAST (1-min cycle, 24-sec dwell). Electric anti-icing on pitot/static probes, AOA vanes, engine intake flanges, both windshields, and pilot side window. Propeller blade heaters via TMCU (one per prop) on 115 VAC variable-frequency bus, cycling on TAT ≤ +5°C with NP > 400 RPM. REF SPEEDS at INCR adjusts SPS stall margin for icing.',
 '/assets/aircraft/q400/ice-rain-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — Ice Detector Probes (2)',
 'Two IDPs on left and right side of front fuselage. Fully automatic with 115 VAC. Trigger threshold >0.5 mm ice. Self-deicing. ICE DETECT FAIL only when both fail.',
 14.16, 16.66, 'component', '#7dd3fc', 'idp'),
(@diagram_id, '2 — Wing Boots (5 sections per wing)',
 'Pneumatic rubber boots on wing leading edge: extension + outboard + outboard centre + inboard centre + inboard sections. Bleed-fed at 18 PSI. Sequenced via TMU.',
 41.66, 16.66, 'component', '#10b981', 'boot'),
(@diagram_id, '3 — Horizontal + Vertical Stabilizer Boots',
 'Inboard + outboard horizontal stab boots; upper + lower vertical stab boots. Pneumatically cross-connected — survives one-side leak.',
 60.83, 16.66, 'component', '#10b981', 'boot'),
(@diagram_id, '4 — Nacelle Inlet Lip Boots',
 'Pneumatic boots on each engine nacelle inlet lip. Same cycle as wing/tail boots. Part of pneumatic protection.',
 79.16, 16.66, 'component', '#10b981', 'boot'),
(@diagram_id, '5 — Bleed Source (each engine)',
 'Boot air taken from bleed port of each engine. INDEPENDENT of BLEED control switch position. Regulated to 18 ± 3 PSI before distribution.',
 14.16, 40.00, 'component', '#fbbf24', 'bleed'),
(@diagram_id, '6 — DDV + Heaters',
 'Dual Distributing Valves with integral heaters. Energised open inflates a pair of boots. Suction holds boots flat when not inflated. DDV/check-valve heaters auto-on at SAT < +5°C with MODE SELECT at OFF/SLOW/FAST; permanent ON in MANUAL.',
 35.83, 40.00, 'component', '#a78bfa', 'ddv'),
(@diagram_id, '7 — TMU (Timer and Monitor Unit)',
 'Controls auto boot inflation sequence. SLOW = 3-min cycle / 144-sec dwell. FAST = 1-min cycle / 24-sec dwell. 6 boot combinations × 6 sec each. DE-ICE TIMER caution on TMU failure.',
 50.00, 40.00, 'component', '#22c55e', 'tmu'),
(@diagram_id, '8 — Ice Protection Panel',
 'Crew interface. AIRFRAME MODE SELECT, AIRFRAME MANUAL SELECT, BOOT AIR (NORM/ISO), PROP selector (TEST/OFF/ON), ENGINE INTAKE OPN HTR, PITOT STATIC, WINDSHIELD, WIPER, PLT SIDE WDO/HT, REF SPEEDS.',
 64.16, 40.00, 'component', '#22d3ee', 'panel'),
(@diagram_id, '9 — Propeller Blade Heaters',
 '6 blades per propeller. Electric heating element on leading 70% of each blade. 115 VAC variable-frequency bus. Both props alternate (load balance). PROPS advisory light (green) when energised.',
 80.00, 40.00, 'component', '#fbbf24', 'prop-heat'),
(@diagram_id, '10 — TMCU (per propeller)',
 'Timer Monitor Control Unit. One per propeller. Cycles heater based on TAT. Conditions: PROP ON + TAT ≤ +5°C + NP > 400 RPM. PROP TEST: 5 sec each, 30-sec cooldown.',
 14.16, 60.00, 'component', '#fbbf24', 'tmcu'),
(@diagram_id, '11 — Pitot/Static Probe Heaters',
 'Electric heaters on pilot, copilot, and standby pitot-static probes. AOA vane heaters auto-on with PITOT STATIC heaters. Switch positions: 1 / 2 / STBY.',
 35.83, 60.00, 'component', '#a78bfa', 'pitot'),
(@diagram_id, '12 — Windshield + Side Window Heat',
 'Both windshields heated electrically (WARM / NORM). Pilot side window heat (PLT SIDE WDO/HT OFF/ON). Plus engine intake flange heaters (ENGINE INTAKE OPN HTR per side).',
 50.00, 60.00, 'component', '#a78bfa', 'wsh'),
(@diagram_id, '13 — Wipers + Alternate',
 'Electrically operated windshield wipers. PARK / OFF / LOW / HIGH. Alternate pilot wiper pushbutton (guarded) drives pilot wiper at HIGH speed if normal control fails.',
 64.16, 60.00, 'component', '#22d3ee', 'wiper'),
(@diagram_id, '14 — REF SPEEDS / SPS + DEICE PRESS Gauge',
 'REF SPEEDS switch OFF / INCR — INCR informs the Stall Protection System to adjust stall margin for icing; [INCR REF SPEED] message displays below ICE DETECTED on the ED. Companion: DEICE PRESS gauge on copilot side panel reads pneumatic boot pressure (NORM = L+R average; ISO = individual side); normal 18 ± 3 PSI; DE-ICE PRESS caution if < 15 PSI.',
 80.00, 60.00, 'component', '#dc2626', 'sps');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'No icing detected. All systems healthy. ICE DETECTED extinguished. No DE-ICE PRESS / DE-ICE TIMER / ICE DETECT FAIL caution. AIRFRAME MODE SELECT at OFF. PROP selector at OFF. REF SPEEDS at OFF.',
 JSON_OBJECT()),
(@diagram_id, 'in_icing', 'Entering Icing — Crew Chant Active',
 'IDPs detected ice; ICE DETECTED message on ED. Crew chant complete: REF SPEEDS at INCR; PROPS ON (TMCU cycling, PROPS advisory illuminated); AIRFRAME MODE SELECT at FAST; ENGINE INTAKE heaters ON; DEICE PRESS within 18 ± 3 PSI; BOOT INFLATION lights cycling green.',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#10b981', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h9', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'tmu_fail', 'DE-ICE TIMER — Manual Cycling',
 'TMU has failed (DE-ICE TIMER caution). Auto sequencing lost. Crew has selected AIRFRAME MODE SELECT to MANUAL. AIRFRAME MANUAL SELECT 6-detent rotary used to fire each pair sequentially. 24-sec dwell minimum before re-firing same pair. Increased crew workload — exit from icing as practicable.',
 JSON_OBJECT(
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
