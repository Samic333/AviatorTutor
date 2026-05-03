-- =============================================================================
-- AviatorTutor — Phase 11: ATA 32 Landing Gear — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'landing-gear' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Landing Gear — Architecture, Steering, Brakes, Alt Extension',
 'Schematic of the Q400 landing gear. Tricycle dual-wheel retractable; mains aft into nacelles, nose forward into nose section. No.2 hyd drives gear extension/retraction and nosewheel steering. No.1 hyd drives anti-skid multi-disc brakes. PSEU monitors and controls. Steering modes: tiller ±70° low-speed, pedals ±8° high-speed, caster ±120° passive. Anti-skid arms above 10 kts wheel speed; self-test prevented above 17 kts; 5-sec brake delay cancels at 35 kts. Alternate gear extension via INHIBIT switch + RELEASE door + EXTENSION door + hand pump. EMERG BRAKE on No.2 hyd or accumulator (~6 applications, 500 PSI minimum before start).',
 '/assets/aircraft/q400/landing-gear-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — Nose Gear (retracts FORWARD)',
 'Tricycle dual-wheel nose gear. Retracts forward into the nose section. Steerable via SCU (No.2 hyd). Auto-centers before retraction. Mechanical aft doors linked to gear motion; hydraulic forward door.',
 14.16, 16.66, 'component', '#7dd3fc', 'gear-nose'),
(@diagram_id, '2 — Left Main Gear (retracts AFT)',
 'Dual-wheel main gear with multi-disc anti-skid brakes (No.1 hyd). Retracts aft into engine nacelle. Mechanical forward door; hydraulic aft door.',
 41.66, 16.66, 'component', '#7dd3fc', 'gear-main'),
(@diagram_id, '3 — Right Main Gear (retracts AFT)',
 'Mirror of left main. Same architecture; same hydraulic feeds.',
 60.83, 16.66, 'component', '#7dd3fc', 'gear-main'),
(@diagram_id, '4 — PSEU',
 'Proximity Sensor Electronics Unit. Monitors WOW, gear position, lift-dump signals. Provides signals to ASCU + FCECU. Brain of the system. LDG GEAR INOP caution illuminates on PSEU control loss.',
 80.00, 16.66, 'component', '#22c55e', 'pseu'),
(@diagram_id, '5 — No.2 Hydraulic — Gear + Steering',
 'Drives gear extension/retraction AND nosewheel steering. PTU backup. Loss of No.2 = primary gear extension impossible (alt extension required) AND no nosewheel steering.',
 14.16, 40.00, 'component', '#a78bfa', 'hyd2'),
(@diagram_id, '6 — Steering Hand Control + Rudder Pedals',
 'Tiller on pilot side console: ±70° low-speed taxi. Rudder pedals: ±8° high-speed/T-O/landing roll. Auto-centers before retract. Caster ±120° passive.',
 35.83, 40.00, 'component', '#22d3ee', 'steering'),
(@diagram_id, '7 — SCU + Steering Motor',
 'Steering Control Unit. Receives commands; drives steering motor and actuator. NOSE STEERING caution: SCU fault with switch ON, OR hyd pressure detected with switch OFF.',
 50.00, 40.00, 'component', '#22c55e', 'scu'),
(@diagram_id, '8 — No.1 Hydraulic — Anti-Skid Brakes',
 'Drives normal anti-skid multi-disc brakes (one unit per main wheel). ASCU modulates. Loss of No.1 = no normal anti-skid braking; EMERG BRAKE remains via No.2/accumulator.',
 64.16, 40.00, 'component', '#dc2626', 'hyd1'),
(@diagram_id, '9 — ASCU + Anti-Skid Logic',
 'Anti Skid Control Unit. Arms above 10 kts wheel speed. Self-test prevented above 17 kts. 5-sec brake delay cancels at 35 kts. INBD/OUTBD ANTISKID caution on fault.',
 80.00, 40.00, 'component', '#fbbf24', 'ascu'),
(@diagram_id, '10 — EMERG BRAKE Lever + Accumulator',
 'EMERG BRAKE on engine-control quadrant. Proportional pull. PARK detent at full back. No.2 hyd or accumulator (~6 applications, 500 PSI min). NO differential, NO anti-skid.',
 14.16, 60.00, 'component', '#dc2626', 'emerg'),
(@diagram_id, '11 — INHIBIT Switch (overhead, guarded)',
 'Step 1 of alternate extension. INHIBIT position isolates all hyd from the gear system. Triggers LDG GEAR INOP caution if not already on.',
 35.83, 60.00, 'component', '#fbbf24', 'inhibit'),
(@diagram_id, '12 — MAIN L/G RELEASE Door + Handle (overhead)',
 'Step 2-3. Open door (opens bypass valve mechanically); pull MAIN L/G RELEASE handle fully (releases main doors + uplocks; mains free-fall). Door MUST stay open after.',
 50.00, 60.00, 'component', '#fbbf24', 'main-release'),
(@diagram_id, '13 — NOSE L/G RELEASE Door + Hand Pump (floor)',
 'Step 4-6. Open floor door (opens MLG alt selector valve); pull NOSE L/G RELEASE handle fully (nose free-falls); if MLG not down/locked, pump until handle stiff. Door MUST stay open after.',
 64.16, 60.00, 'component', '#fbbf24', 'nose-release'),
(@diagram_id, '14 — DOWNLOCK VERIFICATION + 3 Floor Lights',
 'Step 7. ALTERNATE DOWNLOCK VERIFICATION switch to AFT activates 3 green floor downlock verification lights. Independent confirmation of gear downlock for alt extension or indication failure.',
 80.00, 60.00, 'component', '#22c55e', 'downlock');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'Gear DN selected, three green LEFT/NOSE/RIGHT, no amber DOOR or HANDLE, no red unsafe. ANTI SKID ON; STEERING ON for taxi. Park brake set with accumulator at >500 PSI.',
 JSON_OBJECT()),
(@diagram_id, 'ldg_gear_inop', 'LDG GEAR INOP — Alternate Extension',
 'PSEU control loss or hydraulic sequencing valve fault. LDG GEAR INOP caution illuminated. Alternate extension sequence required: INHIBIT switch + MAIN RELEASE door/handle + NOSE RELEASE door/handle + hand pump if needed + 3 floor downlock lights confirm. Both doors LEFT OPEN after.',
 JSON_OBJECT(
   'h4', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h11', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h12', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h13', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 )),
(@diagram_id, 'no2_hyd_loss', 'No.2 Hydraulic Loss — Gear + Steering Out',
 'Loss of No.2 hyd pressure. Primary gear extension impossible — alternate extension required. Nosewheel steering also lost — taxi via differential braking. Normal brakes (No.1) and EMERG BRAKE (accumulator) still available.',
 JSON_OBJECT(
   'h5', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h11', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
