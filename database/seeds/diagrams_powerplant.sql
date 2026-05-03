-- =============================================================================
-- AviatorTutor — Phase 17: ATA 71 Powerplant — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'powerplant' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Powerplant — PW150A Architecture + Controls',
 'Schematic of the Q400 PW150A powerplant. Two-spool architecture: NL axial + NL turbine; NH centrifugal + NH turbine; two-stage power turbine on independent shaft drives reduction gearbox to 6-blade Dowty R408 propeller. Accessory gearbox driven by NH: oil pumps, HP fuel pump, PMA, DC starter-gen. PMA active above NH 20%. 4,580 SHP NTOP; 5,071 SHP MTOP UPTRIM. Five core ratings (NTOP/MTOP/MCP/MCL/MCR + RDC TOP). 2 HBOVs (LP+HP) for surge margin. Bypass doors at intake (icing/precip/bird/contam). RDC NP LDG 15-sec window. EVENT MARKER 2 min before + 1 min after. PROPELLER GROUND RANGE lights at 10°.',
 '/assets/aircraft/q400/powerplant-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — PW150A Engine',
 '2 Pratt & Whitney PW150A turboprops per aircraft. Drive Dowty R408 6-blade composite propellers via reduction gearbox. 4,580 SHP NTOP; 5,071 SHP MTOP UPTRIM brief on engine failure.',
 14.16, 16.66, 'component', '#fbbf24', 'engine'),
(@diagram_id, '2 — NL Axial Compressor + NL Turbine',
 'Low-pressure 1st-stage AXIAL compressor. Single-stage NL turbine on shared shaft drives it.',
 35.83, 16.66, 'component', '#22c55e', 'nl'),
(@diagram_id, '3 — NH Centrifugal + NH Turbine',
 'High-pressure 2nd-stage CENTRIFUGAL compressor. Single-stage NH turbine on shared shaft drives it. Drives accessory gearbox.',
 50.00, 16.66, 'component', '#22c55e', 'nh'),
(@diagram_id, '4 — Power Turbine + Reduction Gearbox',
 'Two-stage power turbine on independent third shaft. Connects to reduction gearbox at front. Reduction gearbox drives propeller.',
 64.16, 16.66, 'component', '#7dd3fc', 'pt'),
(@diagram_id, '5 — Accessory Gearbox',
 'Mounted on top of engine. Driven by NH compressor. Operates: oil pressure pump, oil scavenge pump, HP fuel pump, PMA (Permanent Magnet Alternator), DC starter-generator.',
 80.00, 16.66, 'component', '#a78bfa', 'accessory'),
(@diagram_id, '6 — Power Lever (PLA)',
 'Per engine. Forward range: drives FADEC for power. Beta/reverse: drives prop blade angle via PEC. Positions: MAX REV / DISC / FLT IDLE / forward / rating detent.',
 14.16, 40.00, 'component', '#22d3ee', 'pla'),
(@diagram_id, '7 — Condition Lever (CL)',
 'Per engine. Drives PEC for: prop RPM in forward, ratings, manual feather, fuel on/off. Positions: FUEL OFF / START & FEATHER / MIN 850 / 900 / MAX 1020.',
 35.83, 40.00, 'component', '#22d3ee', 'cl'),
(@diagram_id, '8 — FADEC',
 'Full Authority Digital Engine Control. Receives PLA + ambient + sensors + NPT + remote engine failure (UPTRIM). Controls fuel via FMU; protects engine via NH overspeed circuitry. PMA primary; ESS bus alternate.',
 50.00, 40.00, 'component', '#22c55e', 'fadec'),
(@diagram_id, '9 — Engine Display (ED)',
 'Shows TRQ %, PROP RPM, NH %, ITT °C, NL %, Fuel Flow (hundreds kg/hr), Oil temp + pressure, Engine Rating Mode (green): NTOP/MTOP/MCP/MCL/MCR/RDC TOP. Bleed status white/yellow.',
 64.16, 40.00, 'component', '#fbbf24', 'ed'),
(@diagram_id, '10 — Engine Control Panel',
 'Pushbuttons: MTOP, EVENT MARKER, RDC NP LDG, MCL, MCR, RDC TOP TRQ DEC, RDC TOP TRQ RESET. Plus AUTOFEATHER switchlight (Phase 16).',
 80.00, 40.00, 'component', '#22d3ee', 'control-panel'),
(@diagram_id, '11 — PMA (NH > 20%)',
 'Permanent Magnet Alternator on accessory gearbox. PRIMARY electrical for FADEC. Independent coils per channel. Active when NH > 20%. Below 20%, ESS bus alternate (start + PMA fail).',
 14.16, 60.00, 'component', '#a78bfa', 'pma'),
(@diagram_id, '12 — HBOVs (LP + HP)',
 'Handling Bleed-Off Valves. 2 per engine: one LP + one HP. Bleed compressor air for surge margin during start, steady state, transient. FADEC controls automatically.',
 35.83, 60.00, 'component', '#7dd3fc', 'hbov'),
(@diagram_id, '13 — Bypass Doors',
 'At each engine nacelle intake. Manually selected via ICE PROTECTION panel switchlights. Open in: icing / heavy precipitation / bird activity / contaminated runways. Prevent solids ingestion.',
 64.16, 60.00, 'component', '#fbbf24', 'bypass'),
(@diagram_id, '14 — UPTRIM (Engine Failure)',
 'On engine failure during takeoff (Phase 16 autofeather), the PEC issues an UPTRIM command to the OPERATING engine''s FADEC, raising max power from 4,580 NTOP to 5,071 SHP MTOP. Automatic safety margin.',
 80.00, 60.00, 'component', '#dc2626', 'uptrim');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'Both engines operating normally. PMA active (NH > 20%). FADEC controlling per PLA. PEC governing per CL. NTOP or MCP rating during takeoff per bleed selection. ED showing rating, torque, NH, ITT, NL, fuel flow, oil. Bypass doors per conditions. Autofeather typically OFF in cruise; ON for takeoff.',
 JSON_OBJECT()),
(@diagram_id, 'engine_failure_uptrim', 'Engine Failure on Takeoff — UPTRIM',
 'Engine 2 failed during takeoff. Autofeather (Phase 16) feathered prop 2. PEC issued UPTRIM command to engine 1 FADEC. Engine 1 power rises automatically from 4,580 NTOP toward 5,071 SHP MTOP. ED shows UPTRIM TRQ + MTOP rating on engine 1. Crew runs ENGINE FAILURE on TAKEOFF QRH.',
 JSON_OBJECT(
   'h14', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h9', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'pma_failure', 'PMA Failure — ESS Bus Alternate',
 'PMA failed in flight. Aeroplane essential power buses provide alternate electrical to FADEC. No interruption to engine control; engine continues to run normally. Crew runs QRH PMA non-normal if applicable; defer per MEL on landing.',
 JSON_OBJECT(
   'h11', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
