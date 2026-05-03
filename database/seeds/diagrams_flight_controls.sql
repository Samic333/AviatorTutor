-- =============================================================================
-- AviatorTutor — Phase 7: ATA 27 Flight Controls — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'flight-controls' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Flight Controls — PFCS, Roll, Pitch, Yaw, Flaps, SPS',
 'System schematic of Q400 flight controls. Ailerons (mechanical/cable, ±17°) plus 4 spoilers (2 inboard No.1 hyd, 2 outboard No.2 hyd) provide roll. Each elevator has 3 PCUs (No.1 outboard active, No.2 centre active, No.3 inboard standby). Rudder has 2 PCUs (RUD 1 lower, RUD 2 upper) under FCECU airspeed-scheduled authority. Five flap gates 0/5/10/15/35 driven by FPU on No.1 hyd. Aileron CONTROL LOCK gust lock; rudder/elevators gust-protected by trapped hydraulic fluid. Stall Protection System with 2 SPMs, stick shakers + pusher. Cascading IAS mismatch logic when ADU 1 and ADU 2 differ by more than 17 kts.',
 '/assets/aircraft/q400/flight-controls-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — Left Aileron (mechanical)',
 'Left aileron with geared tab. Mechanically/cable driven from copilot wheel via ATCU. Deflection ±17°. NOT part of PFCS.',
 8.33, 16.66, 'component', '#22d3ee', 'aileron'),
(@diagram_id, '2 — Right Aileron + Trim Tab',
 'Right aileron with geared tab AND a ground-adjustable trim tab. Same mechanical control as left. Aileron trim via ATCU + electric trim actuator on Left ESS bus.',
 25.00, 16.66, 'component', '#22d3ee', 'aileron'),
(@diagram_id, '3 — Inboard Spoilers (No.1 hyd)',
 'Two inboard spoilers (one per wing) on No.1 hydraulic system. Always active across the airspeed envelope. ROLL SPLR INBD HYD caution if No.1 below 900 PSI or SPLR1 PUSH OFF pressed.',
 41.66, 16.66, 'component', '#fbbf24', 'spoiler-in'),
(@diagram_id, '4 — Outboard Spoilers (No.2 hyd)',
 'Two outboard spoilers (one per wing) on No.2 hydraulic system. FCECU disables ABOVE 170 KIAS, re-enables BELOW 165 KIAS. SPLR OUTBD caution if disable/enable logic fails or IAS mismatch.',
 60.83, 16.66, 'component', '#fbbf24', 'spoiler-out'),
(@diagram_id, '5 — Elevators (3 PCUs each)',
 'Two elevator surfaces. Each has 3 PCUs: outboard (No.1 hyd active), centre (No.2 hyd active), inboard (No.3 hyd STANDBY). Standby auto-activates on No.1 or No.2 fail; manual via HYD #3 ISOL VLV.',
 80.00, 16.66, 'component', '#a78bfa', 'elevator'),
(@diagram_id, '6 — Rudder (2 PCUs)',
 'Single rudder driven by 2 PCUs — RUD 1 (LOWER) and RUD 2 (UPPER). FCECU reduces hydraulic pressure as airspeed rises to limit authority. AFM 4.18.12: only ONE PUSH OFF at a time.',
 8.33, 40.00, 'component', '#a78bfa', 'rudder'),
(@diagram_id, '7 — FCECU (system brain)',
 'Flight Control Electronic Control Unit. Regulates rudder authority and elevator feel by airspeed; manages PCU redundancy; prioritises pitch trim signals (pilot > copilot > AP); watches IAS mismatch >17 kts → 4-light cascade.',
 50.00, 40.00, 'component', '#22c55e', 'fcecu'),
(@diagram_id, '8 — PFTU + Pitch Trim Actuators',
 'Pitch Feel and Trim Units (2, in vertical stab) + 2 pitch trim actuators on top. Provide artificial column feel scaled by airspeed and normal acceleration. 3-second rule trip.',
 80.00, 40.00, 'component', '#a78bfa', 'pftu'),
(@diagram_id, '9 — Flaps (5 gates 0/5/10/15/35)',
 'Two single-slotted Fowler flaps per wing. FCU monitors and controls. FPU drives via No.1 hyd. 4 actuators per wing (2 per flap). Bi-directional no-backs lock against aero forces. Auto-trim 15–35° range.',
 14.16, 60.00, 'component', '#7dd3fc', 'flaps'),
(@diagram_id, '10 — ROLL DISC handle',
 'Centre pedestal. Engaged: spring-loaded in. Disengaged: pulled out and rotated 90°. Splits aileron / spoiler systems — pilot keeps spoilers, copilot keeps ailerons.',
 35.83, 60.00, 'component', '#dc2626', 'roll-disc'),
(@diagram_id, '11 — Pitch Disconnect handle',
 'Left side of centre console. Pulled out and rotated 90°: clutch disengages pilot and copilot control columns. Pilot with the FREE column has pitch control.',
 50.00, 60.00, 'component', '#dc2626', 'pitch-disc'),
(@diagram_id, '12 — CONTROL LOCK lever (gust)',
 'Aileron gust lock on power quadrant ahead of power levers. FORWARD = OFF. AFT = ON (locks ailerons at neutral, restricts power-lever travel). Spring-loaded; trigger holds AFT position.',
 64.16, 60.00, 'component', '#22d3ee', 'gust-lock'),
(@diagram_id, '13 — Yaw Damper (±4.5°)',
 'Yaw damper actuator. ±4.5° max rudder authority. Gets inputs from Flight Guidance Modules No.1 AND No.2 — needs both for engagement. Disengages on stall warning.',
 79.16, 60.00, 'component', '#a78bfa', 'yaw-damper'),
(@diagram_id, '14 — SPS (2 SPMs + Pusher)',
 'Stall Protection System. Two Stall Protection Modules (SPM1, SPM2). Inputs: AoA, flap position, Mach, torque, icing. Stick shaker per pilot column + shared stick pusher (uses average of AOA1 + AOA2). Daily test: TEST1 + TEST2 each held >10 sec.',
 50.00, 80.00, 'component', '#fbbf24', 'sps');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'All control surfaces healthy. Above 165 KIAS only inboard spoilers operate; below 165 both. FCECU regulating rudder and pitch feel; yaw damper engaged. No PFCS cautions.',
 JSON_OBJECT()),
(@diagram_id, 'roll_jam', 'Roll Jam — ROLL DISC Engaged',
 'Roll control jam detected. ROLL DISC handle pulled out and rotated 90°. Aileron and spoiler systems separated. Pilot retains SPOILERS only; copilot retains AILERONS only. Pilot with the unjammed wheel has roll control.',
 JSON_OBJECT(
   'h10', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h1', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h3', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h4', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 )),
(@diagram_id, 'ias_mismatch', 'IAS Mismatch — Four-Light Cascade',
 'Airspeed #1 and Airspeed #2 differ by more than ±17 kts. FCECU illuminates RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM cautions simultaneously. Crew action: REDUCE BELOW 200 KIAS, identify bad ADU using standby + GPS groundspeed, run QRH.',
 JSON_OBJECT(
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h4', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h5', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
