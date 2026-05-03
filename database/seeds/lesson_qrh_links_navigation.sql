-- =============================================================================
-- AviatorTutor — Phase 13: ATA 34 Navigation — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'navigation' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'navigation-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'AHRS FAULT — Cross-Side Source Selection',
 'AHRS fault on one side. EFIS shows flagged or invalid attitude/heading parameters on the affected PFD. Action: rotate the EFIS ATT/HDG SOURCE selector from NORM to the operative side (1 or 2). Cross-side selection illuminates a YELLOW indication on the PFD as a continuous reminder. Verify autopilot status — AFCS may degrade if its primary AHRS source is the failed unit. Run QRH AHRS non-normal. Brief the FO. Continue per QRH; consider divert based on weather, route remaining, and the loss of redundancy buffer (a second AHRS loss would be a serious event).',
 0,
 'Single AHRS loss with healthy cross-side is non-emergency. The yellow flag is a continuous reminder you''re running cross-side.',
 'Flagged or invalid attitude/heading on PFD; AHRS caution.',
 'CROSS-SIDE · YELLOW PFD · BRIEF FO · QRH.',
 10),

(@lesson_id, NULL,
 'ADC FAULT — Cross-Side + Possible IAS Mismatch',
 'ADC fault on one side. Action: rotate ADC SOURCE selector from NORM to operative side. Cross-side yellow indication on PFD. Verify airspeed against standby airspeed; possible IAS MISMATCH cascade if mismatch >17 kts (4 cautions: RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM — see Phase 7 Flight Controls). Reduce airspeed below 200 KIAS if cascade fires. Run QRH; consider divert.',
 0,
 'ADC fault often cascades to IAS MISMATCH if airspeed deviates >17 kts. Recognise the four-light pattern and the 200 KIAS rule.',
 'Flagged airspeed/altitude on PFD; ADC caution; possible IAS MISMATCH cascade.',
 'ADC SOURCE · STANDBY CHECK · 200 KIAS IF CASCADE · QRH.',
 20),

(@lesson_id, NULL,
 'EFCP MALFUNCTION — TCAS Auto / Other EFCP',
 'EFCP malfunction. Most important automatic behaviour: TCAS automatically goes to AUTO mode without crew action. Other EFCP functions may be lost — bearing pointer selection, FORMAT, WX/TERR, RANGE, DATA. Recovery: (1) Use the OTHER EFCP — each pilot has one. (2) Reset/recycle EFCP per QRH. (3) If both EFCPs fail, use ESCP MFD selectors for major reversion. Brief the FO; consider divert if workload is asymmetric in IMC.',
 0,
 'TCAS auto-AUTO is the system protecting you — traffic awareness preserved without crew action.',
 'EFCP not responding to selections; TCAS may auto-set to AUTO.',
 'OTHER EFCP · TCAS AUTO PRESERVED · ESCP IF NEEDED.',
 30),

(@lesson_id, NULL,
 'BEARING POINTER ANOMALY — Removed vs Parked',
 'Bearing pointer behaviour by anomaly. (a) <strong>Removed</strong> from PFD: VOR frequency invalid, OR an ILS frequency selected on a VOR channel. Verify ARCDU tuning vs charts; retune if wrong. (b) <strong>Parked at 90°</strong>: ADF signal/frequency invalid, OR ADF in ANT mode (loop disabled), OR ADF in TEST mode. If unintended ANT/TEST, return to ADF mode. If signal genuinely lost, cross-check via FMS or VOR.',
 0,
 'Removed pointer = VOR frequency issue. Parked-at-90 pointer = ADF source issue. Different fixes.',
 'Bearing pointer removed (VOR) or parked at 90° (ADF) on PFD.',
 'REMOVED = VOR FREQ · PARKED = ADF SIGNAL.',
 40),

(@lesson_id, NULL,
 'DME LOSS — Silent Auto-Failover',
 'One DME unit fails. Both EFIS sides automatically use the remaining DME — no crew action, no caution. Discovery on next pre-flight inspection or when DME data unexpectedly unavailable on a specific cross-channel. Action: document for maintenance. With both DMEs failed, distance information on the EFIS is lost — VOR/ILS approaches still valid via timing/charts, but RNAV procedures relying on DME may need divert.',
 0,
 'A clean DME failover is silent. The point is design redundancy. Both EFIS sides keep their distance info from the surviving DME.',
 'Single DME loss: silent. Both: no DME data on EFIS.',
 'AUTO-REROUTE · DOCUMENT · DIVERT IF BOTH.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
