-- =============================================================================
-- AviatorTutor — Phase 7 (ATA 27 Flight Controls) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'flight-controls' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'flight-controls-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Flight Controls — PFCS, Spoilers, Trim, Gust Locks, SPS',
     'flight-controls-overview',
     'overview',
     'Ailerons (mechanical, cable-driven, ±17°) for roll plus 4 spoilers (2 inboard No.1 hyd / 2 outboard No.2 hyd) that disable above 170 KIAS and re-enable below 165 KIAS. Two elevators each with 3 PCUs (No.1 outboard active, No.2 centre active, No.3 inboard standby). Rudder with TWO PCUs — RUD 1 PUSH OFF (lower) and RUD 2 PUSH OFF (upper) — only ONE may be pushed at a time per AFM 4.18.12. FCECU regulates rudder authority and elevator feel as a function of airspeed. Roll disconnect (in + 90°) and pitch disconnect (in + 90°) split jammed surfaces. Five flap gates: 0°/5°/10°/15°/35° (No.1 hyd, FCU + FPU). Aileron gust lock via CONTROL LOCK lever; rudder/elevators gust-protected by trapped hydraulic fluid. Stall Protection System: two SPMs, stick shaker + pusher, daily TEST1/TEST2 cycles >10 sec each. IAS mismatch >17 kts cascades RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM cautions.',
     '<p>Flight controls on the Q400 are a layered system — primary surfaces (ailerons + elevators + rudder) for the three axes, spoilers as a roll assistant and ground lift-dump device, secondary controls (flaps) for high-lift, and a Stall Protection System layered on top to keep the aeroplane out of trouble at the low end of the envelope. The defining design choice is that ailerons are mechanical / cable-driven while elevators, spoilers, and rudder are hydraulically powered — making them the Powered Flight Control Surfaces (PFCS). The FCECU is the brain that regulates rudder authority, elevator feel, and trim rates as airspeed varies, and that handles the redundancy when a PCU jams or a hydraulic system fails. Knowing what fails to what — and how to disconnect a jammed wheel or column — is the captain''s job.</p>',
     JSON_ARRAY(
       'Primary surfaces: ailerons (roll), elevators (pitch), rudder (yaw). Secondary: flaps. Spoilers = roll assist + ground lift dump',
       'PFCS = Powered Flight Control Surfaces: elevators + spoilers + rudder (hydraulic). Ailerons are MECHANICAL/cable, NOT hydraulic',
       'Ailerons: 1 per wing, deflection ±17° (handwheel turns 70° L/R), each has a geared tab; right aileron has a ground-adjustable trim tab',
       'Pilot''s wheel = SPOILERS; copilot''s wheel = AILERONS. Normally interconnected; ROLL DISC handle (pull + 90°) splits them on a jam',
       'Spoilers: 4 total (2 inboard + 2 outboard). Inboard = No.1 hyd; Outboard = No.2 hyd. 4 LVDTs feed FCECU + IFC for MFD display',
       'Spoiler airspeed logic: above 170 KIAS only INBOARD active (FCECU disables outboard); below 165 KIAS BOTH active. SPLR OUTBD caution if mismatch outside 150–185 KIAS',
       'Spoiler ground mode (lift dump): three conditions — FLIGHT/TAXI in FLIGHT, both power levers below FLT IDLE +12°, WOW on both main gear',
       'Elevators: 2 surfaces, each with 3 PCUs — outboard (No.1 hyd, active), centre (No.2 hyd, active), inboard (No.3 hyd, STANDBY). HYD #3 ISOL VLV pushbutton manually activates inboard; auto-activates on No.1 or No.2 fail',
       'Rudder: TWO PCUs — RUD 1 PUSH OFF = LOWER PCU; RUD 2 PUSH OFF = UPPER PCU. AFM 4.18.12: only ONE may be pushed at a time',
       'FCECU regulates rudder authority by reducing PCU hydraulic pressure as airspeed increases. ADUs supply airspeed',
       'Yaw damper authority: ±4.5° max. Needs BOTH Flight Guidance Modules No.1 and No.2',
       'Pitch trim priority order (FCECU): pilot > copilot > autopilot. Pitch trim rate: HIGH speed below 150 KIAS, LOW speed above 250 KIAS',
       'Pitch trim 3-second rule: if a manual pitch trim command persists >3 sec, ELEVATOR TRIM SHUTOFF switchlight illuminates and aural warning sounds',
       'Flap auto-pitch trim: active 15°–35° range only, AP off, airspeed <180 KIAS, no manual trim. Nose-down on extension, nose-up on retraction',
       'Flaps: 5 gates 0°/5°/10°/15°/35°. No.1 hyd. FCU controls, FPU drives, 4 actuators per wing (2 per flap), bi-directional no-backs',
       'Gust locks: ailerons via CONTROL LOCK lever (FWD = OFF, AFT = ON). Lever restricts power-lever travel when ON. Rudder + elevators gust-protected by trapped hydraulic fluid',
       'Stall Protection: 2 SPMs (SPM1, SPM2). Pre-flight TEST1 and TEST2 each held >10 sec. Stick shaker disengages AP; stick pusher uses average of AOA1 + AOA2',
       'IAS MISMATCH = Airspeed #1 ≠ Airspeed #2 by more than ±17 kts → triggers RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM cautions. Reduce airspeed below 200 KIAS'
     ),
     JSON_ARRAY(
       'Roll disconnect: ROLL DISC handle pulled out and rotated 90°. Pilot keeps SPOILERS only; copilot keeps AILERONS only. Pilot with the unjammed wheel has roll control.',
       'Pitch disconnect: handle pulled out and rotated 90° splits the two control columns. The pilot with the free column has pitch control.',
       'Rudder PCU jam: push the corresponding RUD 1 or RUD 2 PUSH OFF switchlight to depressurise that PCU. NEVER push both — AFM 4.18.12. If both pressed inadvertently, push the NON-jammed side again to recover.',
       'Above 170 KIAS the FCECU disables outboard spoilers. If they fail to disable above 185 KIAS or fail to enable below 150 KIAS, SPLR OUTBD caution illuminates.',
       'Elevator HYD #3: standby. Auto-activates on No.1 or No.2 fail; manual activation via HYD #3 ISOL VLV illuminates ELEVATOR PRESS caution if both No.1 and No.2 are healthy.',
       'IAS mismatch >17 kts triggers FOUR cautions simultaneously (RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM) — a cascade pattern to recognise instantly.',
       'Aileron gust lock CONTROL LOCK lever ON restricts power-lever travel — you cannot advance to takeoff thrust with the lock engaged. Forced reminder.',
       'Stick pusher uses AVERAGE of AOA1 and AOA2 inputs. A single AOA disagreement degrades pusher confidence.'
     ),
     JSON_ARRAY(
       'Ailerons are MECHANICAL/cable, NOT hydraulic — easy trap to lump them with the PFCS.',
       'Spoilers: INBOARD = No.1 hyd; OUTBOARD = No.2 hyd. Easy to swap.',
       'Spoiler airspeed thresholds: 170 disables outboard, 165 re-enables (NOT a single number). Caution at 150 / 185 boundaries.',
       'Elevator PCU mapping: outbd = No.1, ctr = No.2, inbd standby = No.3. Students confuse the inboard vs outboard mapping.',
       'RUD PUSH OFF: only ONE at a time. Pressing both inadvertently RE-PRESSURISES the previously-pushed PCU.',
       'Roll disconnect: PILOT keeps spoilers (NOT ailerons). Copilot keeps ailerons. Common to swap.',
       'Pitch trim 3-second rule produces an aural and the SHUTOFF switchlight — ELEVATOR TRIM SHUTOFF, not "PITCH TRIM SHUTOFF".',
       'Flap auto-trim: only between 15° and 35°, AP OFF, <180 KIAS. Easy trap to think it works at all flap positions.',
       'Yaw damper authority: ±4.5° (not ±5° or ±3°). Needs BOTH FGMs.',
       'IAS mismatch threshold: ±17 kts (not ±15 or ±20). Memorise.',
       'Gust lock CONTROL LOCK lever direction: FWD = OFF, AFT = ON. Easy to invert.',
       'Stall warning pre-flight test: TEST1 then TEST2, each held >10 SECONDS. Quick taps fail the test.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'flight-controls-overview';
