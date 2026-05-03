-- =============================================================================
-- AviatorTutor — Phase 3: ATA 22 Autoflight — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'autoflight' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'autoflight-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'AP / YD AUTOMATIC DISENGAGEMENT — Crew Recovery',
 'On automatic AP disengagement (red flashing glareshield AP DISENG segments + flashing amber PFD AP DISENGAGED + continuous aural tone), the crew action sequence is: (1) hands ON the controls — fly the aircraft; (2) press AP DIS on either control wheel to acknowledge — stops flashing, silences aural; (3) cross-check attitude, altitude, heading; (4) identify the cause from the PFD message and any associated cautions; (5) attempt re-engagement only after warnings reset and the cause is identified or cleared.',
 0,
 'Automatic disengagement is loud and bright by design — the cockpit demands attention. Train your hands to take the controls before your mouth speaks. The QRH supports the diagnostic step; the memory step is the muscle memory.',
 'Red flashing AP DISENG segments on glareshield + flashing amber AP DISENGAGED on PFD + continuous aural tone.',
 'Hands · Acknowledge · Cross-check · Re-engage.',
 10),

(@lesson_id, NULL,
 'AP PITCH TRIM FAIL',
 'Automatic pitch trim is broken (input monitor, output monitor, or AFCS failure). The flashing yellow message becomes steady after 5 seconds. The crew must keep up with manual pitch trim during configuration changes (flap/gear), accelerations, decelerations, and capture phases. Anticipate larger pitch transients at AP disengagement because trim may not have followed the aircraft.',
 0,
 'Long sectors with frequent flap or gear movement become high-workload events. Brief the FO: "I will call for trim during configuration changes; we will hand-fly the approach."',
 'Yellow AP PITCH TRIM FAIL on PFD. Pitch transients during configuration changes that the AP appears to fight.',
 'Manual trim plan · Brief hand-flown approach.',
 20),

(@lesson_id, NULL,
 'YD NOT CENTERED',
 'The yaw damper is not at its zero reference. Flashing yellow message becomes steady. Possible causes: rudder trim not centred, lingering rudder input, internal YD reference offset. QRH actions: confirm rudder pedals neutral, check rudder trim setting, cycle YD if QRH directs. If YD does not return to centre, plan for hand-flown approach with extra rudder discipline.',
 0,
 'Yaw damper is a stability augmentation system — its loss is uncomfortable but not dangerous in calm air. It becomes critical in crosswind landing, gusty conditions, and one-engine-inop manoeuvring.',
 'Yellow YD NOT CENTERED on PFD. Possible accompanying yaw / rudder feel anomaly.',
 'Pedals neutral · Trim centred · Re-engage.',
 30),

(@lesson_id, NULL,
 'L FD FAIL or R FD FAIL',
 'One Flight Director channel has failed. Flashing yellow then steady. The OTHER FD channel still drives its PFD bars. The AP and YD use FGM data redundantly and may continue to operate. Crew action: brief hand-flown approach if both FDs are unavailable; if only one, the other side flies the FD-driven approach. The QRH addresses degraded approach planning and minima.',
 0,
 'Single FD failure is operationally manageable. Dual FD failure is hand-flown only. Confirm with the QRH whether the failed FD also affects approved approach minima.',
 'Yellow L FD FAIL or R FD FAIL on the corresponding PFD.',
 'Brief hand-fly · Check approach minima.',
 40),

(@lesson_id, NULL,
 'MISTRIM (Pitch / Roll) — Disengagement Discipline',
 'Roll Mistrim — TRIM L/R WING DN — is annunciation only. The AP does not auto-disengage; there is no automatic roll trim. Pitch Mistrim (less common) appears on AP PITCH TRIM FAIL. QRH discipline: with hands ON the wheel anticipating a transient, disengage AP cleanly. Trim laterally (or pitch) until the message clears. Then decide whether to re-engage AP for the rest of the flight or hand-fly to landing.',
 0,
 'NEVER take a Mistrim into a low-altitude AP disengagement. Disengage cleanly in cruise. The cost of letting the AP disengage on its own near the ground is a roll transient with no margin to recover.',
 'Amber MISTRIM [TRIM L WING DN] or MISTRIM [TRIM R WING DN] on PFD. Possible control-wheel offset.',
 'Hands on · Disengage cleanly · Trim · Re-engage in cruise only.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
