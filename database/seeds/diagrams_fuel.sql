-- =============================================================================
-- AviatorTutor — Phase 8: ATA 28 Fuel — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fuel' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Fuel — Tanks, Pumps, Transfer, Refuel, Engine Feed',
 'Schematic of the Q400 fuel system. Two integral wing tanks (No.1 left, No.2 right), each divided into surge bay (vent + recovery), main tank (storage), and collector bay (engine feed). Each collector houses scavenge ejectors, a primary ejector pump, and an AC variable-frequency auxiliary pump. NO engine crossfeed — fuel sharing is via tank-to-tank transfer through central plumbing controlled by the FUEL TRANSFER switch. Engine feed routes through FOHE (heats and filters) before the FMU. Single-point pressure refuel under No.2 nacelle (DC required) plus gravity overwing adapters. Total usable 5,318 kg; lateral imbalance limit 272 kg; FUEL LOW at ~150 kg in collector bay.',
 '/assets/aircraft/q400/fuel-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — Tank No.1 (LEFT WING)',
 'Integral left-wing fuel tank. Three bays: surge (outboard), main (storage), collector (inboard/aft). Feeds left engine + APU only. NO crossfeed.',
 14.16, 16.66, 'component', '#10b981', 'tank'),
(@diagram_id, '2 — Tank No.2 (RIGHT WING)',
 'Integral right-wing fuel tank. Mirror of tank No.1. Feeds right engine only. Same three-bay architecture.',
 60.83, 16.66, 'component', '#10b981', 'tank'),
(@diagram_id, '3 — Surge Bay (vent + recovery)',
 'Outboard end of each tank. Two outboard float vent valves + one inboard vent line + two NACA vents on bottom of wing. Recovers spilled fuel back to main tank by partial vacuum as fuel is consumed.',
 30.00, 16.66, 'component', '#7dd3fc', 'surge'),
(@diagram_id, '4 — Collector Bay (engine feed)',
 'Inboard/aft corner of each tank. Houses scavenge ejectors, primary ejector pump, AC variable-frequency aux pump. Maintains engine feed regardless of attitude. FUEL LOW at ~150 kg.',
 79.16, 16.66, 'component', '#7dd3fc', 'collector'),
(@diagram_id, '5 — Auxiliary Fuel Pump (TANK 1)',
 'AC variable-frequency aux pump in collector bay 1. Backs up primary ejector. Required ON for takeoff/landing. Auto-activates during fuel transfer (donor side).',
 14.16, 40.00, 'component', '#fbbf24', 'aux-pump'),
(@diagram_id, '6 — Auxiliary Fuel Pump (TANK 2)',
 'AC variable-frequency aux pump in collector bay 2. Mirror of TANK 1 aux. Same auto-on behaviour during transfer.',
 60.83, 40.00, 'component', '#fbbf24', 'aux-pump'),
(@diagram_id, '7 — Fuel Transfer Plumbing',
 'Central transfer plumbing. Donor tank''s aux pump pushes fuel through transfer shutoff valves to receiver tank. Auto-stop on receiver overfill. Inhibited when FUELING ON caution illuminated.',
 41.66, 40.00, 'component', '#22c55e', 'transfer'),
(@diagram_id, '8 — FUEL CONTROL TRANSFER Panel',
 'Crew interface: TANK 1 AUX PUMP switchlight, FUEL TRANSFER switch (TO TANK 1 / CENTER / TO TANK 2), TANK 2 AUX PUMP switchlight.',
 41.66, 60.00, 'component', '#22d3ee', 'panel'),
(@diagram_id, '9 — FOHE (Fuel Oil Heat Exchanger)',
 'Heats AND filters fuel before the FMU. Heating prevents ice crystal formation; filter clogging triggers FUEL FLTR BYPASS caution.',
 14.16, 60.00, 'component', '#a78bfa', 'fohe'),
(@diagram_id, '10 — Engine Feed Shutoff Valve',
 'Per engine. Closed when corresponding PULL FUEL/HYD OFF T-handle is pulled on the Fire Protection Panel. Closes fuel + hydraulic for that engine.',
 24.16, 60.00, 'component', '#dc2626', 'shutoff'),
(@diagram_id, '11 — Pressure Refuel Adapter (No.2 nacelle)',
 'Single-point pressure refuel/defuel access on rear underside of No.2 nacelle. Flush door. DC POWER REQUIRED. PRESELECT REFUEL (auto stop) or REFUEL (manual via PRECHECK/OPEN/CLOSE).',
 79.16, 60.00, 'component', '#fbbf24', 'refuel'),
(@diagram_id, '12 — Gravity Refuel Adapters',
 'Wing-mounted gravity refuel adapters on top of each wing. No DC required. Slower flow rate. Backup when pressure refuel unavailable.',
 60.83, 60.00, 'component', '#7dd3fc', 'gravity-refuel'),
(@diagram_id, '13 — MFD Fuel Page (FUEL SYS)',
 'Single source of truth. Analog dials per tank (white pointer normal, yellow on imbalance), digital quantity + temp, AUX PUMP annunciators, aux pressure-status circle (white/green), VALVE annunciator, fuel transfer triangle.',
 14.16, 80.00, 'component', '#22c55e', 'mfd'),
(@diagram_id, '14 — FQC + BALANCE Logic',
 'Fuel Quantity Computer continuously monitors lateral imbalance. Triggers yellow [BALANCE] flashing above the FUEL legend on Engine Display when imbalance >272 kg. Analog dials turn solid yellow.',
 60.83, 80.00, 'component', '#dc2626', 'fqc');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'Both tanks balanced. Aux pumps OFF in cruise (or ON for takeoff/landing as required). Total fuel matches planned remaining. No fuel cautions. FQC reports balanced. Aux pump pressure circles green when active.',
 JSON_OBJECT()),
(@diagram_id, 'transfer_to_tank2', 'Transfer Active — TANK 1 to TANK 2',
 'BALANCE detected (imbalance >272 kg) with tank 2 the lighter side. FUEL TRANSFER switch selected TO TANK 2. Donor (TANK 1) aux pump auto-illuminates ON GREEN without crew push. Transfer shutoff valve opens (MFD shows OPEN reverse-video). Triangle on fuel page points right toward receiver tank. Transfer continues until balanced or receiver high-level halts automatically.',
 JSON_OBJECT(
   'h5', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'fuel_leak', 'Suspected Fuel Leak',
 'Rapid imbalance growth combined with total fuel dropping faster than fuel flow accounts for. Run QRH FUEL LEAK. CRITICAL: do NOT initiate transfer toward the leaking side. Calculate landing fuel from actual remaining; secure affected engine if QRH directs; divert to nearest suitable.',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
