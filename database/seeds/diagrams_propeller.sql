-- =============================================================================
-- AviatorTutor — Phase 16: ATA 61 Propeller — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'propeller' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Propeller — PEC, PCU, OSG, Autofeather',
 'Schematic of the Q400 propeller system. 6-blade composite propeller per engine, counterweighted (natural moment toward HIGH pitch in flight). PEC dual-channel commands PCU two-stage servo valve metering HP engine oil to fine/coarse pitch chambers. Layered overspeed: hydraulic OSG at 1071 RPM, electronic FADEC at 1122 RPM (hyd locked out in reverse). Flight fine stop 16° hard / 16.5° soft. Beta range PLA below Flight Idle (660 RPM NP gov), reverse 660-950 RPM with 1500 SHP max. GBE valve locks OSG on ground beta. Synchrophasing reduces cabin noise (not at takeoff). Autofeather ARM both torque >50% + PLA >60°; TRIGGER one torque <25% OR Np <816 for 3 sec → feather + UPTRIM operating engine.',
 '/assets/aircraft/q400/propeller-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — 6-Blade Composite Propeller',
 'Six composite blades per engine. Counterweighted — natural twisting moment toward HIGH PITCH in flight. Around flat pitch the moment is small; at negative blade angle (reverse), it acts toward NEGATIVE pitch.',
 14.16, 16.66, 'component', '#a78bfa', 'blades'),
(@diagram_id, '2 — PEC (dual-channel)',
 'Propeller Electronic Control. Dual-channel microprocessor in each engine nacelle. Manages governing, beta, reverse, autofeather, AUPC, UPTRIM. Commands PCU.',
 35.83, 16.66, 'component', '#22c55e', 'pec'),
(@diagram_id, '3 — PCU (Pitch Control Unit)',
 'Hydromechanical, electrically commanded by PEC. Two-stage servo valve meters HP engine oil to fine/coarse pitch chambers. In event of electronic control malfunction, PCU controls minimum blade pitch in flight.',
 50.00, 16.66, 'component', '#fbbf24', 'pcu'),
(@diagram_id, '4 — HP Pump + OSG',
 'High-Pressure PCU pump driven from reduction gearbox. Provides HP oil. Propeller Overspeed Governor: independent flyweight design. Hydraulic OSG drops HP at ~1071 RPM (105%). LOCKED OUT in reverse.',
 64.16, 16.66, 'component', '#22c55e', 'osg'),
(@diagram_id, '5 — Auxiliary Feather Pump',
 '28 VDC electrical motor + external gear pump. Independent feathering source. Used for autofeather, alternate feather, manual feather, and maintenance.',
 80.00, 16.66, 'component', '#dc2626', 'feather-pump'),
(@diagram_id, '6 — Counterweighted Blades',
 'Each blade has a counterweight phased around the root sleeve. Natural twisting moment in flight is COARSE-SEEKING (toward HIGH pitch). HP loss in flight → autocoarsen to safe windmill.',
 14.16, 40.00, 'component', '#a78bfa', 'counterweight'),
(@diagram_id, '7 — Pitch Change Cylinder',
 'Receives HP oil from PCU. Has FINE pitch chamber and COARSE pitch chamber. Servo valve directs oil to balance counterweight effort.',
 35.83, 40.00, 'component', '#7dd3fc', 'cylinder'),
(@diagram_id, '8 — FADEC NP Overspeed (~1122 RPM)',
 'Electronic overspeed protection layer. Signals FMU to reduce fuel at ~1122 RPM. Reduces engine power, drops prop RPM. Restores fuel below threshold. Primary protection in reverse.',
 50.00, 40.00, 'component', '#fbbf24', 'fadec-os'),
(@diagram_id, '9 — Hydraulic OSG (~1071 RPM)',
 'Hydraulic overspeed protection. Drops HP supply at ~1071 RPM (105%). Counterweight effort coarsens blades. RPM drops, OSG reconnects. Stable governing at 1071. LOCKED OUT in reverse.',
 64.16, 40.00, 'component', '#dc2626', 'hyd-os'),
(@diagram_id, '10 — GBE Valve (Ground Beta Enable)',
 'Locks out OSG hydraulic section during ground beta operations. Prevents transient overspeed at flat pitch from interfering with pitch control. GBE failure caught by scheduled OSG test.',
 80.00, 40.00, 'component', '#fbbf24', 'gbe'),
(@diagram_id, '11 — Flight Fine Stop',
 'Minimum blade angle in flight (constant-speed mode): 16° HARD hydraulic stop. 16.5° SOFT PEC stop while PLA at/above Flight Idle. Below 16° requires PLA <Flight Idle + WOW. PROPELLER GROUND RANGE lights illuminate.',
 14.16, 60.00, 'component', '#fbbf24', 'fine-stop'),
(@diagram_id, '12 — Beta Range / Reverse',
 'Beta: PLA below Flight Idle, ground only. NP gov 660 RPM via FADEC. Reverse: closed-loop 660-950 RPM, 1500 SHP max. Beta horn sounds if PLA below Flight Idle in flight.',
 35.83, 60.00, 'component', '#22d3ee', 'beta-rev'),
(@diagram_id, '13 — Synchrophase + MPU',
 'PEC enters synchrophase mode in flight when both prop speeds within predetermined difference. MPU signals time phase between master/slave props. CLA position determines phase demand. NOT active at takeoff.',
 64.16, 60.00, 'component', '#a78bfa', 'sync'),
(@diagram_id, '14 — Autofeather System',
 'AUTOFEATHER switchlight ON for takeoff. ARM: both torque >50% + both PLA >60°. TRIGGER: one torque <25% OR Np <816 (80%) for ≥3 SEC. Actions: A/F ARM out, AUX FEATHER PUMP on, prop feathers, UPTRIM operating engine FADEC.',
 80.00, 60.00, 'component', '#dc2626', 'autofeather');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Constant Speed Operation',
 'In-flight constant speed mode. CL set to 850/900/1020 RPM. PEC governing via PCU servo valve, balancing counterweight effort against demanded RPM. OSG active (1071 RPM threshold not reached). Synchrophasing active. Autofeather typically OFF in cruise.',
 JSON_OBJECT()),
(@diagram_id, 'autofeather_trigger', 'Engine Failure — Autofeather Trigger',
 'Engine 2 failed on takeoff. Autofeather armed (both torque >50%, both PLA >60° during takeoff roll). One engine torque dropped below 25% AND Np below 816 for 3 seconds. Autofeather TRIGGERS: A/F ARM goes out, AUX FEATHER PUMP energized, prop 2 auto-feathers, FADEC of engine 1 receives UPTRIM command. Crew runs ENGINE FAILURE on TAKEOFF non-normal QRH.',
 JSON_OBJECT(
   'h5', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'osg_action', 'OSG Hydraulic Action — 1071 RPM Cycling',
 'Servo valve stuck or PEC fault causing prop RPM to climb above demanded. Hydraulic OSG drops HP supply at ~1071 RPM. Counterweight effort coarsens blades, RPM drops. OSG reconnects. Stable governing at 1071 RPM until cause removed. Investigate PEC, servo, electrical supply. Plan power management to stay clear of demanded RPM.',
 JSON_OBJECT(
   'h9', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h3', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
