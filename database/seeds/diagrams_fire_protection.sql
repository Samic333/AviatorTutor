-- =============================================================================
-- AviatorTutor — Phase 6: ATA 26 Fire Protection — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fire-protection' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Fire Protection — Detection, Indication, Extinguishing',
 'System schematic of the Q400 fire protection architecture. Six engine-nacelle APDs (three per engine: PEZ, LEZ, MWW) plus one APU APD feed the Control Amplifier. Four smoke detectors (2 aft baggage, 1 fwd, 1 lavatory) supply the smoke-detection paths. Two dual-port engine fire bottles in the LEFT wing root deliver up to two shots into either engine via the EXTG switch and PULL T-handle. Two HRD bottles plus one shared LRD bottle protect the baggage compartments — AFT delays the LRD by 7 min; FWD fires both simultaneously. Lavatory waste-bin Potty Bottle is thermally fused, no wiring. Four Halon 1211 portables (1 cockpit + 3 cabin) for hand use.',
 '/assets/aircraft/q400/fire-protection-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — ENGINE 1 APDs (PEZ + LEZ + MWW)',
 'Three Advanced Pneumatic Detectors in the No.1 engine nacelle. Helium-filled sensor tubes; integrity switch (fault on rupture) and alarm switch (fire on heat). Series wiring to the Control Amplifier.',
 8.33, 16.66, 'component', '#fbbf24', 'apd'),
(@diagram_id, '2 — ENGINE 2 APDs (PEZ + LEZ + MWW)',
 'Three APDs in the No.2 engine nacelle. Same architecture as engine 1. Six engine APDs total.',
 25.00, 16.66, 'component', '#fbbf24', 'apd'),
(@diagram_id, '3 — APU APD',
 'Single APD in the APU compartment. Detection logic per OM-B 12.19.4. Seven APDs total on the airframe.',
 41.66, 16.66, 'component', '#fbbf24', 'apd'),
(@diagram_id, '4 — Smoke Detectors (4 total)',
 'Four smoke detectors: 2 in aft baggage compartment (front and rear of compartment), 1 in forward baggage compartment, 1 in lavatory. Mnemonic: 2-2-1-1-SMOKE.',
 60.83, 16.66, 'component', '#a78bfa', 'smoke'),
(@diagram_id, '5 — Control Amplifier',
 'System brain. Performs detection and extinguishing monitoring, BIT functions, drives advisory lights on the Fire Protection Panel, drives CHECK FIRE DET on the Caution and Warning panel, sounds optional fire tone. Loss does NOT cause complete loss of detection.',
 50.00, 35.00, 'component', '#22c55e', 'controller'),
(@diagram_id, '6 — Fire Protection Panel',
 'Centre overhead. ENGINE 1/2 sections (FAULT A/B, BOTTLE ARM, EXTG switch, T-handle, FUEL/HYD valve lights, BTL LOW), TEST DETECTION switch, BAGGAGE FWD and AFT sections (SMOKE/EXTG switchlights, ARM and LOW segments, INLT and OTLT CLOSED).',
 80.00, 35.00, 'component', '#22c55e', 'panel'),
(@diagram_id, '7 — FWD Engine Fire Bottle',
 'Dual-port pressurised bottle in LEFT wing root, FWD position. Plumbed to discharge into either engine''s PEZ + LEZ + MWW zones. First shot for an engine fire.',
 8.33, 56.66, 'component', '#dc2626', 'engine-bottle'),
(@diagram_id, '8 — AFT Engine Fire Bottle',
 'Dual-port pressurised bottle in LEFT wing root, AFT position. Second shot for either engine. Two shots per engine total.',
 25.00, 56.66, 'component', '#dc2626', 'engine-bottle'),
(@diagram_id, '9 — FWD Baggage HRD Bottle',
 'High-Rate Discharge bottle dedicated to forward baggage. Discharges with the LRD simultaneously on FWD SMOKE/EXTG switchlight push.',
 41.66, 56.66, 'component', '#dc2626', 'baggage-bottle'),
(@diagram_id, '10 — AFT Baggage HRD Bottle',
 'High-Rate Discharge bottle dedicated to aft baggage. Discharges immediately on AFT SMOKE/EXTG switchlight push; LRD follows automatically 7 minutes later.',
 60.83, 56.66, 'component', '#dc2626', 'baggage-bottle'),
(@diagram_id, '11 — Shared LRD Bottle (Aft Equip Bay)',
 'Single Low-Rate Discharge bottle in the aft equipment bay. Serves BOTH baggage compartments. Slow-soak prevents reignition. AFT: 7-min auto-delay. FWD: simultaneous with HRD.',
 80.00, 56.66, 'component', '#dc2626', 'lrd'),
(@diagram_id, '12 — Lavatory Potty Bottle',
 'Inside the waste-bin cabinet. Thermally activated — fusible end-cap seals melt at the set point; dual outlets discharge into the bin. NO electrical interface, NO cockpit indication, no manual discharge.',
 14.16, 76.66, 'component', '#dc2626', 'lav-bottle'),
(@diagram_id, '13 — PULL FUEL/HYD OFF T-Handle',
 'Red T-handle on the Fire Protection Panel, one per engine. PULL closes fuel + hydraulic shut-off valves AND arms the bottle squibs (yellow ARM lights). EXTG switch then fires the chosen bottle.',
 41.66, 76.66, 'component', '#22d3ee', 'control'),
(@diagram_id, '14 — Halon 1211 Portable Extinguishers (4)',
 '1 in flight compartment + 3 in passenger compartment. Effective on electrical, oil, and fuel fires. Non-corrosive, non-toxic, will not freeze. Gauge: GREEN serviceable / YELLOW overcharge / RED recharge. Cockpit use REQUIRES 100% O2 mask EMERGENCY position.',
 73.33, 76.66, 'component', '#7dd3fc', 'portable');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'All detectors healthy. No FAULT, no FIRE, no SMOKE, no BTL LOW. Pre-flight TEST DETECTION cycle complete. Cabin crew has run lavatory smoke-detector self-test. Four portable extinguishers aboard with gauges in GREEN. Default colour scheme on all hotspots.',
 JSON_OBJECT()),
(@diagram_id, 'engine_fire', 'Engine 1 Fire — Bottle Shots Available',
 'No.1 engine APDs detect fire. ENGINE FIRE press-to-reset lights flash; CHECK FIRE DET red; ENGINE 1 PULL FUEL/HYD OFF T-handle illuminated red; fire tone sounds. Crew memory items: PRESS to silence, PULL the T-handle, EXTG switch FWD BTL, wait 30 sec, OTHER if persists. Both wing-root bottles available for two shots into engine 1.',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h5', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h13', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 )),
(@diagram_id, 'aft_baggage_smoke', 'Aft Baggage Smoke — HRD Now, LRD in 7 Min',
 'Aft baggage smoke detector triggers. AFT ARM amber illuminates; AFT BAGGAGE SMOKE/EXTG switchlight illuminates; vent valves close (INLT/OTLT CLOSED white). Crew pushes the switchlight: AFT HRD bottle discharges immediately. LRD bottle automatically discharges 7 minutes later.',
 JSON_OBJECT(
   'h4', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h10', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h11', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
