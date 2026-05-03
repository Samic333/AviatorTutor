-- =============================================================================
-- AviatorTutor — Phase 14: ATA 35 Oxygen — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'oxygen' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Oxygen — Crew Fixed, Portable, PBE',
 'Schematic of the Q400 oxygen system. Crew fixed system: 3 full-face masks (pilot, copilot, observer) on a single common cylinder in the right lower nose compartment. Green burst disc on right exterior of nose ejects on over-pressurisation. Lighted gauge on COPILOT side console. Capacity: descent to 14,000 ft in 4 minutes plus 116 minutes level at 14,000. Donning target: less than 5 seconds via inflatable harness. Regulator NORM/100%/EMER with EMER providing 100% O2 at positive pressure and purging smoke goggles. In-line pressure indicator GREEN good or RED low. Plus portable passenger O2 cylinders, PBE for both crews, and first aid O2 in cabin.',
 '/assets/aircraft/q400/oxygen-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — Crew Cylinder (right lower nose)',
 'Single common cylinder in the right LOWER nose compartment. Supplies all 3 crew masks. ON-OFF knob, pressure regulator, charging valve, pressure gauge on the cylinder itself.',
 14.16, 16.66, 'component', '#a78bfa', 'cylinder'),
(@diagram_id, '2 — Green Burst Disc',
 'On the right EXTERIOR of the nose. Ejects to relieve cylinder over-pressurisation. Pre-flight check item — missing or damaged disc indicates over-pressure event has occurred.',
 35.83, 16.66, 'component', '#22c55e', 'burst-disc'),
(@diagram_id, '3 — Cylinder Gauge',
 'Pressure gauge on the cylinder itself. Reads bottle pressure continuously.',
 50.00, 16.66, 'component', '#fbbf24', 'cyl-gauge'),
(@diagram_id, '4 — Flight-Deck Gauge (Copilot Side)',
 'Lighted oxygen pressure gauge on the COPILOT side console. Shows pressure available to masks. Continues to show BOTTLE pressure even when cylinder is OFF.',
 64.16, 16.66, 'component', '#fbbf24', 'fd-gauge'),
(@diagram_id, '5 — In-Line Pressure Indicator',
 'On the mask supply hose. GREEN with correct pressure / RED if low. If donning gives breathing difficulty or RED indicator, verify supply hose connection at mask + outlet.',
 80.00, 16.66, 'component', '#22c55e', 'in-line'),
(@diagram_id, '6 — Pilot Mask',
 'Full-face microphone-equipped mask. Stowed in cup on bulkhead behind pilot seat. Inflatable harness donning in <5 sec via red button. Diluter-demand regulator.',
 14.16, 40.00, 'component', '#7dd3fc', 'pilot-mask'),
(@diagram_id, '7 — Copilot Mask + Dual Outlet',
 'Copilot mask in stowage cup behind copilot seat. Plugs into the copilot oxygen supply line which has a DUAL outlet — the second outlet feeds the observer mask.',
 35.83, 40.00, 'component', '#7dd3fc', 'copilot-mask'),
(@diagram_id, '8 — Observer Mask',
 'Adjacent to copilot mask. Plugs into the dual outlet on copilot supply line. Used by observer; can be used by either crew member if a primary mask fails.',
 50.00, 40.00, 'component', '#7dd3fc', 'observer-mask'),
(@diagram_id, '9 — Regulator (NORM/100%/EMER)',
 'Diluter-demand regulator on each mask. Three positions: NORM (auto air/O2 mix by cabin altitude), 100% (pure O2 regardless of altitude), EMER (100% O2 at positive pressure + purges smoke goggles). EMER depletes cylinder fast.',
 64.16, 40.00, 'component', '#fbbf24', 'regulator'),
(@diagram_id, '10 — Inflatable Harness + Red Button',
 'Quick-don inflatable harness on each mask. Red button (momentary action) inflates harness with O2 pressure. Release deflates and secures around head. Donning target: less than 5 seconds.',
 80.00, 40.00, 'component', '#dc2626', 'harness'),
(@diagram_id, '11 — Portable Passenger O2 Cylinders',
 'Kept in the cabin for cabin attendant / passenger emergency O2 supply. Supplements PSU drop-down system if fitted.',
 14.16, 60.00, 'component', '#a78bfa', 'portable'),
(@diagram_id, '12 — PBE (Flight Deck + Cabin)',
 'Protective Breathing Equipment. Self-contained smoke-hood-style units. Available for flight deck crew AND cabin attendants. Used in low-oxygen environments such as cabin smoke or fire.',
 35.83, 60.00, 'component', '#dc2626', 'pbe'),
(@diagram_id, '13 — First Aid Oxygen',
 'In the passenger compartment. For medical use during normal flight (passenger illness, dizziness). Cabin attendants administer per training.',
 64.16, 60.00, 'component', '#22c55e', 'first-aid'),
(@diagram_id, '14 — System Capacity (4 + 116 min)',
 'System sized for: descent to 14,000 ft in 4 minutes + level flight at 14,000 ft for 116 minutes. Sufficient for typical Q400 emergency descent and divert profile.',
 80.00, 60.00, 'component', '#22c55e', 'capacity');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'Cylinder full and ON. All 3 masks stowed in cups. In-line indicators GREEN. Regulator at NORM (default). System passive — no O2 in use. Pre-flight checks complete; burst disc intact.',
 JSON_OBJECT()),
(@diagram_id, 'depressurisation', 'Rapid Depressurisation — Masks On',
 'Rapid depressurisation in cruise. Masks donned in <5 sec via inflatable harness. Regulator NORM (or 100% per cabin altitude). Crew comm via mask mic. Emergency descent to 14,000 ft (4 min). Cabin announcement; F/A check passenger O2; divert.',
 JSON_OBJECT(
   'h6', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h10', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'cockpit_smoke_emer', 'Cockpit Smoke — EMER Mode Active',
 'Cockpit smoke event. Both pilots'' masks ON. Regulator at EMER position — 100% O2 at positive pressure flushes contaminants from mask seal AND purges smoke goggles. Smoke goggles ON. Crew comm via mics. Run SMOKE/FUMES QRH. Manage EMER time — depletes cylinder.',
 JSON_OBJECT(
   'h9', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h10', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
