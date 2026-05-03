-- =============================================================================
-- AviatorTutor — Phase 12: ATA 33 Lighting — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'lighting' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Lighting — Interior, Exterior, Emergency',
 'Schematic of the Q400 lighting system. Three categories: interior (cockpit panel/instrument 5 VDC variable, dome on BATTERY PWR, storm on L SECONDARY, utility, map, circuit breaker, cabin overhead/sidewall 21+21 fluorescent, reading per PSU, signs, lavatory, baggage), exterior (landing — outboard approach + inboard flare, taxi on steerable nose gear with gear-down inhibit, position green right red left white aft with primary plus secondary auto-failover, anti-collision upper plus lower, recognition red, wing/engine inspection, logo), and emergency (3-position lever-locked switch ON ARM OFF; battery packs power egress chain).',
 '/assets/aircraft/q400/lighting-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — Cockpit Panel Lighting (5 VDC)',
 'Variable-intensity 5 VDC. Disc-shaped lamps in Plexiglas. Knobs: Overhead Console, Glareshield (drives clocks), Fwd Centre, Aft Centre, plus per-pilot FLT PNL knobs.',
 14.16, 16.66, 'component', '#fbbf24', 'panel'),
(@diagram_id, '2 — Dome (BATTERY PWR)',
 'Cockpit dome light. BATTERY PWR bus — operates without BATTERY MASTER. Standalone 2-position switch.',
 35.83, 16.66, 'component', '#fbbf24', 'dome'),
(@diagram_id, '3 — Storm/Dome (L SECONDARY)',
 'Storm lights for night-vision recovery after lightning flash. L SECONDARY bus. 3-position switch: STORM / STORM-DOME / OFF.',
 50.00, 16.66, 'component', '#a78bfa', 'storm'),
(@diagram_id, '4 — Utility + Map + W/S Wiper',
 '2 ceiling utility lights (one each pilot) + 1 observer utility. Map lights below side windows. W/S WIPER ICE DETECT pushbutton illuminates wiper spigot.',
 64.16, 16.66, 'component', '#22d3ee', 'utility'),
(@diagram_id, '5 — Circuit Breaker Panel Lights',
 'White floodlights above each side''s circuit breaker panels. Separate toggle switch per pilot. Avionics + variable-frequency CB panels also lit.',
 80.00, 16.66, 'component', '#7dd3fc', 'cb-light'),
(@diagram_id, '6 — Cabin Overhead + Sidewall (21+21)',
 '21 fluorescent overhead + 21 fluorescent sidewall. F/A panel membranes: CABIN OVERHD, CABIN SIDEWALL, DIM OVERHD, DIM SIDEWALL. Single dimmer on C/A panel dims all main cabin together.',
 14.16, 40.00, 'component', '#22c55e', 'cabin'),
(@diagram_id, '7 — Information Signs + Auto-Logic',
 'NO SMOKING + FASTEN BELTS at front and each PSU. Lavatory RETURN TO SEAT. Low chime on PA when signs activate. NO SMOKING auto-on with gear selector to DN.',
 35.83, 40.00, 'component', '#fbbf24', 'signs'),
(@diagram_id, '8 — Lavatory + OCCUPIED',
 'LAVATORY LTS membrane arms 2 fluorescent + 2 lamps. Lavatory latch OCCUPIED activates fluorescent + OCCUPIED indicator at F/A seat.',
 50.00, 40.00, 'component', '#a78bfa', 'lav'),
(@diagram_id, '9 — Reading + PSU + Signs',
 '2 reading lights per PSU. Pushbutton each. Gated by PSU ON/OFF membrane on F/A panel. PSU TEST membrane verifies operation.',
 64.16, 40.00, 'component', '#22d3ee', 'psu'),
(@diagram_id, '10 — Forward Door + Boarding',
 '4 step lights on door risers (Left Main bus). 2 boarding lights — lower threshold + forward boarding (Battery bus). Forward C/A panel BOARDING membrane.',
 80.00, 40.00, 'component', '#7dd3fc', 'door'),
(@diagram_id, '11 — Landing Lights (4)',
 '2 per wing on leading edge outboard of nacelles. Outboard = APPROACH; inboard = FLARE (angled DOWN for flare). EXTERIOR LIGHTS panel switches.',
 14.16, 60.00, 'component', '#fbbf24', 'landing'),
(@diagram_id, '12 — Taxi Light (Inhibit)',
 'On steerable section of nose gear. Shines in direction nose gear points. Inhibited unless landing gear is LOCKED DOWN.',
 35.83, 60.00, 'component', '#fbbf24', 'taxi'),
(@diagram_id, '13 — Position + Anti-Coll + Recognition',
 'Position: GREEN right wingtip / RED left wingtip / WHITE aft of vertical stab bullet. Each = primary + secondary; ~1-sec arm delay; auto-failover. Anti-collision upper (bullet) + lower (fuselage). Recognition red on top fuselage centreline forward of wings.',
 64.16, 60.00, 'component', '#22c55e', 'position'),
(@diagram_id, '14 — Emergency Lighting (3-Pos)',
 'EMER LIGHTS switch: 3-position lever-locked. ON / ARM / OFF. ARM = auto-illuminate on AC loss. Battery packs power egress chain: ceiling, reflective floor markings, locator signs, exit signs, egress light per exit.',
 80.00, 60.00, 'component', '#dc2626', 'emerg');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'EMER LIGHTS at ARM. All cockpit + cabin + exterior lighting per phase of flight. Position lights primary illuminated; secondaries armed. No anomalies.',
 JSON_OBJECT()),
(@diagram_id, 'gear_dn_no_smoke', 'Gear DN — NO SMOKING Auto-Logic',
 'Landing gear selector moved to DN. NO SMOKING signs auto-illuminate even if cockpit switch is OFF. Low chime through PA. Cabin attendants alerted; pre-landing reminder. Taxi light remains inhibited until gear locked down.',
 JSON_OBJECT(
   'h7', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h12', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'ac_power_loss', 'AC Power Loss — EMER LIGHTS Auto-On',
 'All AC power lost. Emergency lighting auto-illuminates from battery packs (EMER LIGHTS at ARM). Cabin egress chain (ceiling, floor markings, locator, exit, egress) on. Cockpit dome (BATTERY PWR) remains. Storm lights (L SECONDARY) gone.',
 JSON_OBJECT(
   'h2', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h3', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
