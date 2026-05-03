-- =============================================================================
-- AviatorTutor — Phase 2 (ATA 21 Air Conditioning & Pressurization) interactive
-- diagram with 14 hotspots and 3 states (Normal / CABIN PRESS warn / Pack fail).
-- SVG at public/assets/aircraft/q400/air-cond-press-flow.svg.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'air-cond-press' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams
    (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES
    (@system_id,
     'Q400 ECS + Pressurization Flow',
     'Interactive flow diagram: hot bleed sources (left) feed two ACMs sharing a single primary + secondary heat exchanger; conditioned air is distributed to the cabin (~75%) and flight deck (~25%); recirculation fan loops cabin air back through a filter behind the aft class-C baggage compartment; the aft outflow valve regulates cabin pressure with two safety valves (aft and forward) as backup. Click any numbered hotspot to read its brief; toggle states to see how the diagram changes during a CABIN PRESS warning or a pack failure.',
     '/assets/aircraft/q400/air-cond-press-flow.svg',
     NULL,
     1,
     'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots
    (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group)
VALUES
(@diagram_id,
 '1 — No.1 Engine Bleed',
 'Hot bleed air tapped from the No.1 engine (PW150A). Modulated by the Nacelle Shut-Off Valve under ECU control. Flow rate set by the BLEED selector (MIN required for take-off).',
 10.42, 21.33, 'component', '#f59e0b', 'sources'),

(@diagram_id,
 '2 — No.2 Engine Bleed',
 'Hot bleed air from the No.2 engine. Pairs with No.1 to balance flow under ECU regulation. Either or both may supply the packs depending on BLEED switch positions.',
 10.42, 34.66, 'component', '#f59e0b', 'sources'),

(@diagram_id,
 '3 — APU Bleed',
 'APU-supplied bleed for ground operations and as an in-flight backup source. APU bleed flow is NOT controlled by the BLEED selector knob — it follows an internal ECU schedule.',
 10.42, 48.00, 'component', '#fbbf24', 'sources'),

(@diagram_id,
 '4 — Ground A/C Connection',
 'External ground air supply. Located on the right aft fuselage at fuselage station X 860.00. Latched door, 8-inch industry-standard fitting. A flapper-style check valve at the distribution-system junction prevents reverse flow if the cabin is pressurised.',
 10.42, 61.33, 'component', '#22c55e', 'sources'),

(@diagram_id,
 '5 — ACM 1',
 'Air-cycle machine #1: compressor + turbine that drops bleed air temperature through expansion. Lives in the aft equipment bay (unpressurised). Pneumatic pack inlet FCSOV defaults OPEN on single ECU channel failure (continued ops); CLOSED on dual channel failure (ECS stops).',
 35.42, 40.00, 'component', '#22d3ee', 'pack'),

(@diagram_id,
 '6 — ACM 2',
 'Air-cycle machine #2: identical to ACM 1. Sharing the heat exchangers with ACM 1 means single-pack operation runs at 70% selected flow with the recirc fan at LOW speed; dual-pack runs at full performance with recirc HIGH.',
 46.25, 40.00, 'component', '#22d3ee', 'pack'),

(@diagram_id,
 '7 — Shared Primary + Secondary HX',
 'Single dual-stage heat exchanger that BOTH ACMs feed through. The Q400 design saves weight versus two complete heat exchangers and gives a larger heat-rejection surface during single-pack operation.',
 40.83, 51.66, 'component', '#fbbf24', 'pack'),

(@diagram_id,
 '8 — Distribution Manifold',
 'Mixes pack-conditioned air with recirculated cabin air, then splits the resulting flow approximately 25 percent to the flight deck and 75 percent to the cabin. Flow split is achieved by the ECU digital channels (left handles flight-deck temp; right handles cabin temp).',
 66.66, 43.33, 'component', '#7dd3fc', 'distribution'),

(@diagram_id,
 '9 — Recirc Fan + Filter',
 'Recirculation fan with a filter mounted behind the AFT class-C baggage compartment. Starts at LOW speed when RECIRC is selected (current inrush limit), then auto-switches to HIGH. Speed adapts to operating conditions through ECU.',
 66.66, 56.66, 'component', '#7dd3fc', 'distribution'),

(@diagram_id,
 '10 — Flight Deck',
 'Receives approximately 25 percent of total ECS flow. Flight deck temperature is controlled by the FLT COMP TEMP selector on the AIR CONDITIONING panel. Five LCD displays + avionics rack are cooled separately by the avionics extraction loop (3 fans, fully automatic).',
 80.00, 21.66, 'component', '#22c55e', 'cabin'),

(@diagram_id,
 '11 — Cabin (58–72 pax)',
 'Receives approximately 75 percent of total ECS flow. Cabin temperature is set by the CABIN TEMP selector (or by the cabin-attendant panel when the cockpit selector is in F/A position). Maximum cabin-to-ambient differential is 5.5 PSI; CABIN PRESS warning fires above 9,800 ft cabin altitude.',
 85.00, 35.00, 'component', '#22c55e', 'cabin'),

(@diagram_id,
 '12 — Forward Safety Valve (FSV)',
 'On the forward pressure bulkhead. Selector on the copilot''s side console has only NORMAL or OPEN — cannot be modulated. For progressive bleed use the FWD OUTFLOW knob on the CPC panel. For rapid dump: lift guard, set OPEN.',
 75.00, 25.83, 'component', '#dc2626', 'safety'),

(@diagram_id,
 '13 — Aft Outflow Valve',
 'PRIMARY pressure regulator. Located on the aft pressure dome. Modulated continuously by the Cabin Pressure Controller (CPC) under AUTO mode; opened or closed by AUTO-MAN-DUMP toggle (DECR/INCR) under MAN mode. On the ground with power levers below 60° = fully OPEN.',
 95.00, 35.00, 'component', '#22d3ee', 'pressurization'),

(@diagram_id,
 '14 — Aft Safety Valve',
 'Backup release on the aft pressure dome. Opens on the ground when at least one engine is at idle OR the APU is operating. Backs up the aft outflow on the ground and provides an independent emergency release path in flight.',
 95.00, 45.00, 'component', '#fbbf24', 'safety');

-- ---------------------------------------------------------------------------
-- States
-- ---------------------------------------------------------------------------
INSERT INTO diagram_states
    (diagram_id, state_name, state_label, description, hotspot_overrides)
VALUES
(@diagram_id,
 'normal',
 'Normal Operations',
 'Both packs running, recirc fan at HIGH speed, AUTO pressurisation mode, scheduled cabin altitude versus aircraft altitude. Default colour scheme: amber for hot bleed sources, cyan for ACM/pack components, blue for distribution, green for occupied volumes, red for the forward safety valve emergency dump.',
 JSON_OBJECT()),

(@diagram_id,
 'cabin_press_warn',
 'CABIN PRESS Warning — > 9,800 ft Cabin Altitude',
 'Cabin altitude has exceeded 9,800 ft and the CABIN PRESS warning has fired. The diagram pulses the cabin and outflow valve red and highlights the forward safety valve as the rapid-dump option. Memory items: oxygen masks 100%, EMERGENCY DESCENT, transponder 7700, advise ATC.',
 JSON_OBJECT(
   'h11', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h13', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h12', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 )),

(@diagram_id,
 'pack_fail',
 'Pack Failure — Dual ECU Channel Loss',
 'Both digital channels of the ECU have failed. The pack inlet FCSOV defaults to CLOSED, the ACMs shut off, and ECS supply ends. The diagram pulses the ACMs and FCSOV red. Crew action: emergency ram-air ventilation, descend to FL100 or below, divert to the nearest suitable airport.',
 JSON_OBJECT(
   'h5', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#f59e0b', 'pulse', false),
   'h8', JSON_OBJECT('color_hex', '#f59e0b', 'pulse', false)
 ));

SELECT
    @diagram_id AS diagram_id,
    (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
    (SELECT COUNT(*) FROM diagram_states   WHERE diagram_id = @diagram_id) AS states_inserted;
