-- =============================================================================
-- AviatorTutor — Phase 16: ATA 61 Propeller — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'propeller' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'propeller-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'ENGINE FAILURE — Autofeather Sequence',
 'Engine failure on takeoff with autofeather armed. Trigger: ONE engine torque drops below 25% OR Np drops below 816 (80%) for at least 3 SECONDS. Sequence: A/F ARM light goes out, AUX FEATHER PUMP energized, prop feathers automatically, FADEC of operating engine receives UPTRIM command (extra power). Crew action: confirm autofeather worked (prop feathered, drag low, yaw manageable); maintain directional control; identify "engine X failed, autofeather, UPTRIM active"; clean up per ENGINE FAILURE on TAKEOFF QRH; declare; return for landing or divert. The 3-second confirm window is intentional — autofeather doesn''t fire on transient torque dips.',
 1,
 'Autofeather + UPTRIM is the difference between a controllable single-engine climb-out and a wrestling match. The system handles the prop and the engine power; the crew handles the aircraft.',
 'Sudden torque drop on one engine; A/F ARM light goes out; prop feathers; UPTRIM TRQ on ED.',
 'CONFIRM AUTOFEATHER · MAINTAIN CONTROL · IDENTIFY · QRH · DECLARE.',
 10),

(@lesson_id, NULL,
 'MANUAL FEATHER — Without Autofeather',
 'Engine failure with autofeather OFF (or autofeather failed to fire). Action: select Condition Lever to FEATHER on the failed engine, OR push the corresponding alternate feather switchlight (#1 ALT FTHR or #2 ALT FTHR). Verify prop feathered: low drag, low Np, yaw moment manageable. Run ENGINE FAILURE non-normal QRH; identify; cleanup; UPTRIM is not automatic (no autofeather) — power management on the operating engine is manual.',
 0,
 'Manual feather is slower than autofeather. Practise the CL movement to FEATHER as a reflex on every recurrent.',
 'Engine failed with autofeather OFF or failed to fire; prop windmilling; yaw moment present.',
 'CL TO FEATHER OR ALT FTHR · VERIFY · QRH.',
 20),

(@lesson_id, NULL,
 'PROPELLER OVERSPEED — 1071 / 1122',
 'Prop RPM cycling around 1071 RPM: hydraulic OSG is dropping HP oil supply. Investigate cause — stuck servo valve at fine pitch, PEC fault, or electrical supply issue. Stable governing at 1071 until cause removed. Prop RPM exceeding 1122 RPM with FADEC NP overspeed action: FMU cuts fuel, engine power drops. Investigate; do not assume PEC is healthy. Run QRH PROP OVERSPEED non-normal. With sustained OSG action, plan a power management strategy that stays well clear of demanded RPM.',
 0,
 'OSG cycling at 1071 is the system protecting you. Don''t fight it — investigate. The PEC + servo valve + electrical chain is where the fault lives.',
 'Prop RPM holding at ~1071 RPM despite higher CL demand; or 1122 RPM FADEC fuel-cut event.',
 'INVESTIGATE PEC/SERVO/ELEC · POWER MANAGEMENT · QRH.',
 30),

(@lesson_id, NULL,
 'BETA WARNING HORN IN FLIGHT',
 'Beta warning horn sounds in flight. The PLA gate has been raised and PLA pushed below Flight Idle in flight — a forbidden operation. Action: IMMEDIATELY move PLA back above the Flight Idle gate. Confirm horn stops. Verify prop responding to PLA at or above Flight Idle. Run QRH if the event was prolonged or aircraft control was affected. WARNING from AOM: NEVER move power levers below Flight Idle in flight. The detent + horn + 16° hard stop are the layered defences against this — but the captain must reflexively move PLA back if it crosses the gate.',
 1,
 'Beta horn in flight is a loud, unmistakable warning. The reflex is one motion: PLA back above the gate, now.',
 'Beta warning horn sounds in flight; PROPELLER GROUND RANGE lights may illuminate.',
 'PLA ABOVE GATE NOW · CONFIRM HORN STOPS · NEVER BELOW FLT IDLE IN FLIGHT.',
 40),

(@lesson_id, NULL,
 'AUTOFEATHER FAIL TO ARM',
 'Pre-takeoff: AUTOFEATHER switchlight ON, SELECT illuminated, but ARM does not illuminate as torque builds and PLA advances beyond 60°. Action: verify both engine torques actually >50% and both PLAs actually >60° via ED indications. If conditions met but ARM still off, autofeather has failed to arm — significant operational restriction. Defer per MEL — typically reduces takeoff weight or prohibits dispatch with autofeather inoperative for certain runways/temperatures. Discuss with dispatch; consider ground re-test or maintenance before next sector.',
 0,
 'Autofeather is the system that saves a busy V1 cut. Don''t dispatch without it unless company SOP and MEL explicitly allow.',
 'Pre-takeoff or takeoff roll: A/F SELECT on but A/F ARM not illuminating despite torque + PLA conditions met.',
 'VERIFY CONDITIONS · MEL CHECK · DEFER OR MAINTENANCE.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
