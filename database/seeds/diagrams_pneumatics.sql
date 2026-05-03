-- =============================================================================
-- AviatorTutor — Phase 15: ATA 36 Pneumatics — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'pneumatics' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Pneumatics — APU + Bleed Air System',
 'Schematic of the Q400 APU and bleed-air system. APU is a gas turbine driving a 28 VDC starter-generator in a titanium tailcone with firewall (replaces composite tailcone) accessed by 2 clamshell doors on the bottom. Air intake on right rear of fuselage. Exhaust through ejector and upwards-pointing outlet at aft tailcone. APU CANNOT be operated in flight. Fuel from left wing collector bay. Starter cuts at half operating speed. Battery start: 100% → ~20 V; 50% → ~18 V. APU bleed valve supplies ECS and holds CPCS aft safety valve open; auto-de-energizes when engine BLEED selected. Auto fire detection + 7-second auto-extg. No restart once bottle discharged. Limits: composite duct removed 30°C/ISA+25; Louvre installed 21°C.',
 '/assets/aircraft/q400/pneumatics-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — APU (Titanium Tailcone)',
 'Gas turbine engine driving 28 VDC starter-generator. Located in titanium tailcone with firewall (replaces standard composite tailcone). Cannot operate in flight.',
 14.16, 16.66, 'component', '#a78bfa', 'apu'),
(@diagram_id, '2 — Clamshell Access Doors',
 'Two clamshell-type doors on the BOTTOM of the tailcone. For ground servicing.',
 35.83, 16.66, 'component', '#7dd3fc', 'doors'),
(@diagram_id, '3 — Air Intake (Right Rear)',
 'Screened inlet duct on the right rear of the fuselage. Optional louvered cover for snow/sleet protection during long turnarounds.',
 50.00, 16.66, 'component', '#22d3ee', 'inlet'),
(@diagram_id, '4 — Exhaust (Aft Tailcone)',
 'Through exhaust ejector + upwards-pointing outlet at aft end of titanium tailcone. Directs hot exhaust away from ground crew.',
 64.16, 16.66, 'component', '#fbbf24', 'exhaust'),
(@diagram_id, '5 — APU FADEC',
 'Full Authority Digital Engine Control. Controls APU start sequence, normal operation, and malfunction monitoring. Auto-managed; crew interface via switchlights.',
 80.00, 16.66, 'component', '#22c55e', 'fadec'),
(@diagram_id, '6 — Starter-Generator (28 VDC)',
 'Gearbox-mounted 28 VDC starter-generator. Starter stays engaged until APU reaches HALF operating speed. External AC/DC inhibits APU GEN output.',
 14.16, 40.00, 'component', '#22c55e', 'starter-gen'),
(@diagram_id, '7 — APU Fuel Shutoff Valve',
 'At left wing collector bay end. Opens on PWR push. Closes on: PWR off, fire, EXTG pushed, aircraft in flight (4 conditions).',
 35.83, 40.00, 'component', '#dc2626', 'fuel-valve'),
(@diagram_id, '8 — APU Bleed Valve',
 'Opens via BL AIR switchlight. Supplies ECS + holds CPCS aft safety valve open. Auto-de-energizes when engine BLEED selected. Reduces if APU EGT high (priority to GEN load).',
 50.00, 40.00, 'component', '#22d3ee', 'bleed-valve'),
(@diagram_id, '9 — Check Valves (APU + Wing)',
 'APU check valve + wing duct check valves. Prevent APU bleed from entering engine bleed supply (including airframe de-icing).',
 64.16, 40.00, 'component', '#a78bfa', 'check-valves'),
(@diagram_id, '10 — APU Control Panel (overhead)',
 'PWR / START / GEN / BL AIR switchlights. PWR arms only on 3 conditions: ground + no fire + EXTG not selected. GEN OHT amber advisory.',
 80.00, 40.00, 'component', '#fbbf24', 'control-panel'),
(@diagram_id, '11 — APU Fire Loop Sensor',
 'Loop sensor along tailcone above APU. Continuous monitoring whenever right ESS 28 VDC bus energised. Senses fire/overheat.',
 14.16, 60.00, 'component', '#dc2626', 'loop'),
(@diagram_id, '12 — APU Fire Bottle (Stainless)',
 'Stainless steel fire extinguisher bottle + distribution tubing. Auto-releases 7 seconds after FIRE detected. Once discharged, NO RESTART until bottle replaced.',
 35.83, 60.00, 'component', '#dc2626', 'bottle'),
(@diagram_id, '13 — APU Fire Protection Panel (FPP)',
 'On overhead console. Indicators: FIRE red, BTL ARM amber, FUEL VALVE OPEN green / CLOSED white, EXTG switchlight (guarded), BOTTLE LOW amber, FAULT amber. FIRE TEST pushbutton.',
 64.16, 60.00, 'component', '#fbbf24', 'fpp'),
(@diagram_id, '14 — APU Starter Batteries (2×40 Ahr)',
 '2×40 Ahr NiCad (the same MAIN + AUX of the EPGDS). Battery start drops bus voltage: 100% charge → ~20 V; 50% → ~18 V (risk of brown-out).',
 80.00, 60.00, 'component', '#dc2626', 'batteries');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Ground Operations',
 'APU operating on ground. PWR RUN green, GEN ON green supplying 28 VDC, BL AIR OPEN green supplying ECS. No FIRE. No FAULT. Aircraft stationary. Valves open per system state.',
 JSON_OBJECT()),
(@diagram_id, 'apu_fire_auto_extg', 'APU FIRE — 7-Second Auto-Extg',
 'APU fire detected by loop sensor. FIRE red on FPP, MASTER WARNING + CHECK FIRE DET flashing, BTL ARM amber, fuel valve auto-closed (FUEL VALVE CLOSED white), APU auto-shut down. After 7 seconds, extinguishing agent automatically released. BTL ARM goes out. Once bottle discharged, no APU restart until bottle replaced.',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h11', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h12', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h13', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'aircraft_in_flight', 'Aircraft In Flight — APU Auto-Stopped',
 'Aircraft becomes airborne. Weight-on-wheels signal removed. APU shutoff valve auto-closes (1 of 4 close conditions). APU stops. APU is a ground-only asset; no in-flight backup electrical or bleed. In-flight redundancy is via engine GENs + TRUs + batteries (Phase 5 Electrical).',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
