-- =============================================================================
-- AviatorTutor — Phase 17: ATA 71 Powerplant — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'powerplant' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'powerplant-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'ENGINE FAILURE on TAKEOFF — Autofeather + UPTRIM',
 'Engine failure on takeoff with autofeather armed. Detection: ONE engine torque drops below 25% OR Np below 816 (80%) for ≥3 SECONDS. Autofeather (Phase 16) feathers the prop. The PEC issues an UPTRIM command to the OPERATING engine FADEC, raising max power from 4,580 SHP NTOP to 5,071 SHP MTOP for a brief period — automatic safety margin. Crew action: maintain directional control via rudder + aileron; identify "engine X failed, autofeather, UPTRIM active"; verify A/F ARM out, UPTRIM TRQ on ED, MTOP rating displayed; declare; run ENGINE FAILURE on TAKEOFF QRH; clean up; return for landing or divert.',
 1,
 'Autofeather + UPTRIM is the design margin for V1 cuts. The system handles both prop and engine power; the crew handles aircraft control.',
 'Sudden torque drop on one engine; A/F ARM out; UPTRIM TRQ on ED; MTOP rating.',
 'CONFIRM AUTOFEATHER · CONFIRM UPTRIM · CONTROL · QRH · DECLARE.',
 10),

(@lesson_id, NULL,
 'ENGINE FIRE — FMU Fuel Shutoff via T-Handle',
 'Engine fire procedure (Phase 6 chant): FLASH · PRESS · PULL · EXTG · 30-sec · OTHER. The PULL FUEL/HYD OFF handle is the key action — pulling it activates a DEDICATED fuel shutoff switch in the Fuel Metering Unit (FMU). This is a different fuel-cutoff path from the normal CL FUEL OFF. The fire handle path also closes the hydraulic shutoff valve and arms the engine fire bottles. Once T-handle is pulled, EXTG switch fires the chosen bottle. Wait 30 seconds; if FIRE light persists, EXTG to the OTHER bottle. After memory items: declare MAYDAY; single-engine procedures.',
 1,
 'The FMU dedicated fuel shutoff is faster and surer than the CL path on a fire — designed for time-critical events.',
 'Engine FIRE light flashing; CHECK FIRE DET; PULL FUEL/HYD OFF T-handle illuminated.',
 'FLASH · PRESS · PULL · EXTG · 30-SEC · OTHER.',
 20),

(@lesson_id, NULL,
 'PMA FAILURE — ESS Bus Alternate',
 'PMA failure in flight. Indication: FADEC fault code or annunciation depending on operator. The aeroplane essential power buses provide ALTERNATE electrical power to the FADEC — no interruption to engine control. Engine continues to run normally on ESS power. Crew action: run QRH PMA non-normal if applicable. Defer per MEL on landing. PMA is on the accessory gearbox driven by NH; failure could indicate gearbox or PMA-specific fault. Investigate post-flight.',
 0,
 'PMA failure is a non-event in flight — ESS bus has it covered. Maintenance write-up only.',
 'FADEC PMA fault code; possible PMA caution per operator.',
 'ESS BUS ALTERNATE · ENGINE NORMAL · MEL.',
 30),

(@lesson_id, NULL,
 'ENGINE OVER-TORQUE',
 'Engine torque exceeds rated maximum. Indication: TRQ display shows >100% (above operational limit). Recorded by EMS. Action: PUSH the EVENT MARKER pushbutton — captures EMS data 2 minutes BEFORE the event + 1 minute AFTER, supporting maintenance investigation. If above V1 in takeoff, continue takeoff with the over-torque (likely transient); maintenance reviews data post-flight. If below V1, captain may reject the takeoff if discomfort with situation. Reduce power to NTOP or below if able. Investigate cause post-flight via EMS data — engine may need inspection or removal depending on duration and magnitude.',
 0,
 'Over-torque is rarely catastrophic in flight, but the EMS data + EVENT MARKER are critical for the maintenance decision afterward.',
 'TRQ display above rated max; possible engine vibration; possible torque bug yellow.',
 'EVENT MARKER · CONTINUE OR REJECT · LET MAINTENANCE DECIDE.',
 40),

(@lesson_id, NULL,
 'BYPASS DOOR FAULT',
 'Bypass door fails to open in icing/precipitation/contaminated-runway conditions. Risk: solids ingestion damaging the engine. Or bypass door fails to close when conditions clear — increased fuel consumption + degraded engine performance. Action: confirm switchlight position vs actual door position via secondary indication if available. Plan around: avoid icing if door won''t open; accept performance loss if door won''t close. Consider divert if conditions ahead are severe. Defer per MEL post-flight.',
 0,
 'Bypass door fault forces a route/altitude/timing change rather than an emergency. Plan around it.',
 'Switchlight position not matching expected door state; possible engine performance anomaly.',
 'IDENTIFY · PLAN · AVOID OR DIVERT · MEL.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
