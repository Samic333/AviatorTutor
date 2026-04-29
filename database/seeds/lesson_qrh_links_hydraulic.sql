-- =============================================================================
-- AviatorTutor — Phase 4 seed
-- QRH cross-references for the Q400 Hydraulic Power lesson (ATA 29).
-- Links three QRH sections to the hydraulic lesson so the slide player's
-- qrh-type slides render structured excerpts with memory-item flags.
--
-- Idempotent: re-running wipes prior links for this lesson and re-inserts.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE ata_code = 'ATA29' LIMIT 1);
SET @lesson_id := (
    SELECT id FROM lessons
    WHERE system_id = @system_id
    ORDER BY sort_order, id
    LIMIT 1
);

-- Wipe prior links for this lesson so the seed is idempotent.
DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links
    (lesson_id, slide_id, qrh_section_title, qrh_excerpt,
     memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order)
VALUES
(@lesson_id, NULL,
 'HYD 1 PRESS LO — Single System Failure',
 'Pressure on the No.1 hydraulic system has dropped below limits. Loss of No.1 disables nose-wheel steering, nose-gear extension, normal brakes (use alternate), and removes No.1 power from the affected flight-control surfaces — No.3 standby continues to power the elevators.',
 0,
 'Land at the nearest suitable airport. Brief a longer landing roll (alternate brakes only) and a manual-disconnect taxi (no nose-wheel steering). No memory items — run the QRH in order.',
 'EICAS caution: HYD 1 PRESS LO. Pressure gauge dropping below 1800 PSI.',
 'No memory items. Run the book.',
 10),

(@lesson_id, NULL,
 'HYD 2 PRESS LO — Single System Failure',
 'Pressure on the No.2 hydraulic system has dropped below limits. Loss of No.2 disables normal brakes, normal gear retraction, and removes No.2 power from the assigned flight-control surfaces — No.3 standby continues to power the elevators.',
 0,
 'Land at the nearest suitable airport. Plan for accumulator-pressure brakes and a possible single-application stop. No memory items — run the QRH in order.',
 'EICAS caution: HYD 2 PRESS LO. Pressure gauge dropping below 1800 PSI.',
 'No memory items. Run the book.',
 20),

(@lesson_id, NULL,
 'HYD 1 + HYD 2 PRESS LO — Dual Hydraulic Loss',
 'Pressure lost on BOTH No.1 and No.2 main hydraulic systems. No.3 standby continues to power the elevators. Landing gear must be extended manually with the EMERGENCY HAND PUMP. Braking is by accumulator pressure only — limited applications, no anti-skid.',
 1,
 'MAYDAY. Land at the nearest suitable runway, extra-long preferred. Lower the gear EARLY using the hand pump — pumping takes time and physical effort. Brief the FO that brakes are accumulator-only with limited applications.',
 'Both HYD 1 PRESS LO and HYD 2 PRESS LO illuminated. No.3 STBY pressurised. Cockpit physical-cue: HYD 3 indication on EICAS.',
 'Hand pump EARLY · Accumulator brakes · Nearest suitable',
 30);
