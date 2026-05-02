-- =============================================================================
-- AviatorTutor — Phase 1 (ATA 21 Aeroplane General) interactive diagram
-- One diagram with 14 hotspots and 3 states (Normal / Door Caution / Aft Smoke).
-- The SVG image lives at public/assets/aircraft/q400/aeroplane-general-flow.svg
-- and the on-page DiagramEngine overlays this hotspot data on top of it.
--
-- Idempotent: re-running wipes prior diagrams (and via CASCADE all hotspots
-- and states) for this system and re-inserts.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1);

SELECT @system_id AS resolved_system_id;

-- Wipe any prior diagrams for this system (cascades to hotspots + states).
DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams
    (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES
    (@system_id,
     'Q400 Architecture — Side Profile',
     'Interactive side profile of the Bombardier Dash 8-Q400 showing the three fuselage sections, high-mounted wing with PW150A nacelle and six-blade propeller, fore + trailing rudder split, key emergency-equipment locations, and pressurisation boundaries. Click any numbered hotspot to read the component brief; toggle states to see the airframe behaviour during a door caution or an aft-fuselage smoke event.',
     '/assets/aircraft/q400/aeroplane-general-flow.svg',
     NULL,
     1,
     'schematic');

SET @diagram_id := LAST_INSERT_ID();

-- ---------------------------------------------------------------------------
-- Hotspots (14)
-- x_pct / y_pct are percentages of the SVG viewBox (1200 × 600).
-- The DiagramEngine renders these as overlays on top of the static SVG.
-- ---------------------------------------------------------------------------
INSERT INTO diagram_hotspots
    (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group)
VALUES
(@diagram_id,
 '1 — Nose / Weather Radar Radome',
 'Forward-most section, in front of the forward pressure bulkhead. Houses the nose-wheel well, an unpressurised equipment deck, and the weather radar. Radome is fiberglass with honeycomb core.',
 8.33, 52.00,
 'component',
 '#22d3ee',
 'forward'),

(@diagram_id,
 '2 — Forward Pressure Bulkhead',
 'Bounds the forward edge of the pressurised volume. Forward of this line: nose, equipment bay, radome — all unpressurised. Aft of this line: flight deck and cabin — pressurised.',
 20.00, 63.30,
 'note',
 '#22d3ee',
 'forward'),

(@diagram_id,
 '3 — Flight Deck',
 'Two-pilot transport-category cockpit. Laminated-glass windshields. Caution &amp; Warning panel above glareshield. Centre console carries Emergency Brake, Control Lock, Flap selector, Power and Condition levers. Aft flight deck holds the LANDING GEAR EMERGENCY EXTENSION HAND PUMP HANDLE — locate it eyes-closed.',
 15.00, 50.66,
 'component',
 '#22d3ee',
 'forward'),

(@diagram_id,
 '4 — Forward Baggage Door',
 'Right forward fuselage. Opens OUTWARDS and ONLY from the outside. NOT an evacuation route. A baggage-door caution below 100 kt on takeoff = REJECT.',
 17.92, 57.66,
 'component',
 '#fbbf24',
 'doors'),

(@diagram_id,
 '5 — Passenger Door',
 'Left forward fuselage. Opens from EITHER side. Primary evacuation route. Cabin crew brief checks and access on every flight.',
 22.42, 54.66,
 'component',
 '#22c55e',
 'doors'),

(@diagram_id,
 '6 — Engine Nacelle (PW150A) + 6-Blade Propeller',
 'PW150A turboprop, 5071 SHP, driving a six-bladed propeller (4.12 m diameter). Main landing gear stows under the nacelle. The high-mounted wing keeps the prop arc clear of ground debris.',
 35.00, 46.66,
 'component',
 '#22d3ee',
 'wing'),

(@diagram_id,
 '7 — High Wing (2.5° dihedral outboard)',
 'Single high-aspect-ratio cantilevered wing. Integral fuel tanks. Pneumatic deicer boots on leading edges. 2.5° dihedral outboard of the engine nacelles.',
 45.00, 49.16,
 'component',
 '#22d3ee',
 'wing'),

(@diagram_id,
 '8 — Aileron + Outer Spoilers',
 'Conventional aileron for lateral control + differential lateral-control spoilers on the upper wing skin. Spoilers also have a ground mode that extends them on landing to dump lift.',
 55.83, 49.33,
 'component',
 '#22d3ee',
 'wing'),

(@diagram_id,
 '9 — Type II/III Emergency Exit',
 'Mid-cabin emergency exit. Opens from EITHER side. Cabin crew briefs operating instructions to able-bodied passengers seated in the row.',
 42.25, 54.66,
 'component',
 '#22c55e',
 'doors'),

(@diagram_id,
 '10 — Aft Pressure Bulkhead',
 'Aft edge of the pressurised volume. Forward of this line: cabin pressurised. Aft of this line: APU, packs, empennage support — all unpressurised.',
 61.66, 63.30,
 'note',
 '#22d3ee',
 'aft'),

(@diagram_id,
 '11 — Air Conditioning Packs',
 'Both A/C packs live in the unpressurised aft fuselage. Pack failure smells venting overboard rather than into the cabin. Recirculation fan and pack inputs are isolatable from the cockpit.',
 68.33, 53.66,
 'component',
 '#fbbf24',
 'aft'),

(@diagram_id,
 '12 — APU',
 'Auxiliary Power Unit, also in the unpressurised aft fuselage. APU compartment fire is contained because the section is unpressurised — it vents overboard. APU shutdown is a memory step in many smoke / fumes procedures.',
 75.83, 53.66,
 'component',
 '#fbbf24',
 'aft'),

(@diagram_id,
 '13 — Fore + Trailing Rudder',
 'Q400 split-rudder configuration. The trailing rudder is hinged to the trailing edge of the fore rudder and is geared to deflect at TWICE the angle of the fore rudder. Two hydraulic actuators drive the pair. Composite leading edges with two-chamber pneumatic deicer boots.',
 90.83, 36.66,
 'component',
 '#22d3ee',
 'empennage'),

(@diagram_id,
 '14 — Horizontal Stabilizer + Bullet Fairing',
 'Fixed-incidence horizontal stabiliser at the top of the vertical stabiliser. Composite leading edges with pneumatic deicer boots. Composite "bullet fairing" caps the junction. L and R elevators are hydraulically operated with artificial feel; pitch-disconnect splits them on jam.',
 87.08, 28.66,
 'component',
 '#22d3ee',
 'empennage');

-- ---------------------------------------------------------------------------
-- States (3) — Normal, Door Caution, Aft Smoke Event
-- hotspot_overrides JSON applies per-state visual emphasis on top of the
-- baseline hotspot definitions.
-- ---------------------------------------------------------------------------
INSERT INTO diagram_states
    (diagram_id, state_name, state_label, description, hotspot_overrides)
VALUES
(@diagram_id,
 'normal',
 'Normal Operations',
 'All systems nominal. Pressurisation schedule maintained, doors latched, no cautions. The diagram shows the airframe in its default colour scheme — green for forward and exits, blue for cabin and structural items, amber-hatched for the unpressurised aft section.',
 JSON_OBJECT()),

(@diagram_id,
 'door_caution',
 'Door Caution — Below 100 kt on Takeoff Roll',
 'A DOOR or BAGGAGE DOOR caution illuminates during the takeoff roll. Below 100 kt this is a REJECT — the airframe is highlighted at the suspect doors so the crew can visualise which latches to confirm physically once stopped.',
 JSON_OBJECT(
   'h4', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h5', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h9', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 )),

(@diagram_id,
 'aft_smoke',
 'Smoke / Fumes — Aft Fuselage Source',
 'Persistent electrical smoke after isolating the packs implies an aft-fuselage source (APU or pack equipment). The aft section is highlighted red and the affected components are pulsed. The cockpit cannot access this section in flight; the response is procedural — masks 100%, run the QRH, isolate, divert.',
 JSON_OBJECT(
   'h11', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h12', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h10', JSON_OBJECT('color_hex', '#f59e0b', 'pulse', false)
 ));

SELECT
    @diagram_id AS diagram_id,
    (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
    (SELECT COUNT(*) FROM diagram_states   WHERE diagram_id = @diagram_id) AS states_inserted;
