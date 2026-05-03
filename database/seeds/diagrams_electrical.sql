-- =============================================================================
-- AviatorTutor — Phase 5: ATA 24 Electrical Power — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'electrical' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 EPGDS — Generators, TRUs, Buses, EPCU',
 'Block diagram of the Electrical Power Generation and Distribution System. Two engine-driven AC generators (115 VAC variable frequency) feed two TRUs that produce 28 VDC. Two engine-driven DC starter/generators directly produce 28 VDC. Three NiCad batteries (40 Ahr main, 40 Ahr aux, 17 Ahr standby) plus an APU starter/generator in the tail cone provide additional DC sources. The EPCU manages bus tie contactors automatically. Bus fault protection: 5-second tolerance window before generator trip and battery contactor lockout.',
 '/assets/aircraft/q400/electrical-flow.svg', NULL, 1, 'electrical');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — AC GEN 1',
 'Engine-driven AC generator. Output: 115 VAC variable frequency. Powers the left variable-frequency bus and feeds TRU 1.',
 10.00, 21.66, 'component', '#fbbf24', 'ac-gen'),
(@diagram_id, '2 — AC GEN 2',
 'Engine-driven AC generator. Same output as AC GEN 1. Powers the right variable-frequency bus and feeds TRU 2.',
 25.00, 21.66, 'component', '#fbbf24', 'ac-gen'),
(@diagram_id, '3 — TRU 1',
 'Transformer Rectifier Unit. Converts 115 VAC from AC GEN 1 to 28 VDC for DC bus 1. Primary in-flight DC contributor.',
 10.00, 35.83, 'component', '#a78bfa', 'tru'),
(@diagram_id, '4 — TRU 2',
 'Transformer Rectifier Unit. Converts 115 VAC from AC GEN 2 to 28 VDC for DC bus 2.',
 25.00, 35.83, 'component', '#a78bfa', 'tru'),
(@diagram_id, '5 — DC GEN 1 (Starter/Generator)',
 'Engine-driven DC starter/generator. Doubles as engine starter. Output 28 VDC. Sized primarily for engine starting; in flight TRUs handle most DC load.',
 45.00, 21.66, 'component', '#22d3ee', 'dc-gen'),
(@diagram_id, '6 — DC GEN 2 (Starter/Generator)',
 'Engine-driven DC starter/generator. Mirror of DC GEN 1.',
 61.66, 21.66, 'component', '#22d3ee', 'dc-gen'),
(@diagram_id, '7 — EPCU (Electrical Power Control Unit)',
 'The system brain. Monitors all sources and buses. Manages bus tie contactors automatically. On bus fault: 5-second tolerance window, then TRIP signal to GCU and battery contactor lockout. Coordinated with per-generator GCUs.',
 53.33, 40.00, 'component', '#22c55e', 'epcu'),
(@diagram_id, '8 — DC BUS 1 (Main)',
 'Primary DC distribution bus 1. Normally fed by DC GEN 1 + TRU 1. EPCU re-configures bus ties on any source failure.',
 14.16, 57.50, 'component', '#7dd3fc', 'bus'),
(@diagram_id, '9 — ESS Bus',
 'Essential bus. Powers flight-critical loads. Survives multiple source failures.',
 57.50, 57.50, 'component', '#22c55e', 'bus'),
(@diagram_id, '10 — ESS Standby Bus (17 Ahr)',
 'Powered by the 17 Ahr standby battery as last-resort source. Carries the absolute minimum essential loads (key flight instruments, key avionics).',
 79.16, 57.50, 'component', '#22c55e', 'bus'),
(@diagram_id, '11 — MAIN BATTERY (40 Ahr NiCad)',
 'Primary 40 Amp-hour NiCad battery. Located in the forward fuselage with the AUX + STBY batteries. Nominal 24 VDC under load (20 cells × 1.2 VDC). No-load ~28 VDC. Diode-isolated from STBY during engine start.',
 11.66, 70.83, 'component', '#dc2626', 'battery'),
(@diagram_id, '12 — STBY BATTERY (17 Ahr NiCad)',
 'Standby 17 Amp-hour NiCad battery in the forward fuselage. Smaller capacity but smaller load on its dedicated ESS standby bus — survives longest in a battery-only scenario.',
 41.66, 70.83, 'component', '#dc2626', 'battery'),
(@diagram_id, '13 — APU Starter/Generator',
 'Located in the tail cone section. Supplies 28 VDC to essential main and secondary DC buses on the ground. Connects to the right main feeder bus via a contactor — same path used for APU starting.',
 59.16, 70.83, 'component', '#a78bfa', 'apu'),
(@diagram_id, '14 — External Power Receptacles',
 'DC GPU on LEFT side of forward fuselage; AC GPU on RIGHT side of forward fuselage near nose cone. AC PPU monitors AC quality before allowing power onto the variable-frequency buses. While DC ext is connected, EPCU inhibits engine-driven generator connections to main buses.',
 79.16, 70.83, 'component', '#22c55e', 'external');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'All four DC sources operational. Each source on its own dedicated bus. Default colour scheme on all hotspots. EPCU monitoring; no faults.',
 JSON_OBJECT()),
(@diagram_id, 'bus_fault', 'DC BUS Fault — 5-Second Window',
 'A main bus fault has been detected. The EPCU has isolated the affected bus by preventing bus tie closures. The DC BUS caution light is illuminated. If the fault persists past 5 seconds, the EPCU will TRIP the affected generator (h5 or h6) and lock out the affected battery contactor (h11).',
 JSON_OBJECT(
   'h7', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h5', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 )),
(@diagram_id, 'battery_only', 'Battery-Only Operation',
 'All four DC sources lost (extreme failure scenario). The 40 Ahr main and aux batteries serve essential buses; the 17 Ahr standby covers the ESS standby bus longest. Load-shed per QRH; nearest suitable airport. Time-critical.',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h5', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h11', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h12', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
