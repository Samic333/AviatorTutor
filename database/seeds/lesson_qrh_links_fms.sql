-- =============================================================================
-- AviatorTutor — Phase 18: ATA 22B FMS — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fms' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fms-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'POSITION UNCERTAIN — Revert to Raw Nav',
 'Position Uncertain message on FPCDU + possibly PFD. Three conditions met: GPS lost AND DME inputs insufficient AND no other valid position sensor. Action: revert to RAW NAV — Bearing 1 to VOR1 with appropriate radial; manually tune via ARCDU; cross-check Bearing pointer + DME against expected position. Run QRH POSITION UNCERTAIN non-normal. Consider divert to a station with adequate ground-based nav coverage. Note: in remote oceanic / polar / desert ops where ground nav is sparse, this can become an emergency requiring careful divert planning.',
 0,
 'Position Uncertain in covered area is annoying. In a remote area it''s a real divert event. Plan the raw-nav fallback before departure.',
 'POSITION UNCERTAIN message on FPCDU; possible PFD indication.',
 'RAW NAV · CROSS-CHECK · QRH · CONSIDER DIVERT.',
 10),

(@lesson_id, NULL,
 'GPS LOSS — Sensor Watchdog Reversion',
 'GPS lost in flight. Sensor Watchdog automatically reverts FMS position to next-best source: scanning DME (if 2+ stations in range), then VOR/DME (single station), then AHRS+ADC dead reckoning. Transition is silent — no Position Uncertain unless all sources degrade. Position accuracy may drop slightly. Action: cross-check raw nav more frequently — every 10 min instead of 30. If you''re in a remote area without DME coverage, prepare for Position Uncertain — brief divert plan now. Run QRH GPS LOSS non-normal. Document for maintenance on landing.',
 0,
 'GPS loss with good DME coverage is non-event. Without DME (remote ops), it''s a build-up to Position Uncertain.',
 'GPS not available indication; FMS reverts to scanning DME source.',
 'INCREASED CROSS-CHECK · MONITOR FOR POS UNCERTAIN · DOCUMENT.',
 20),

(@lesson_id, NULL,
 'NCU FAULT — Single FMS Loss',
 'NCU (Navigation Computer Unit) fault. On dual-FMS aircraft, swap to the other FMS. On single-FMS aircraft (the Q400 standard build), FMS is unavailable. Crew reverts to raw nav (VOR/ADF) for navigation. Defer per MEL on landing — significant operational restriction (can the aircraft be dispatched IFR for the next sector?). Coordinate with dispatch. The aircraft remains airworthy with raw nav, but workload increases substantially without FMS.',
 0,
 'On a single-FMS Q400, NCU loss reduces you to raw nav. Doable but high workload — divert if conditions warrant.',
 'FMS not responding to FPCDU inputs; NAV/FMS source flag on PFD.',
 'OTHER FMS IF DUAL · RAW NAV IF SINGLE · MEL DEFER.',
 30),

(@lesson_id, NULL,
 'DATABASE EXPIRED — Pre-Dispatch',
 'Pre-flight check: navigation database AIRAC cycle has expired (effective date past). Cannot dispatch IFR with expired database — IFR navigation requires current data. Action: update database via DTU before flight. Coordinate with maintenance for cartridge availability. The dual-cycle architecture means the next-cycle data may already be in the FMS — swap cycles via FPCDU if the new cycle has become effective. If new cycle not yet loaded, full database update required.',
 0,
 'Pre-dispatch catch of an expired database saves a sortie. Verify AIRAC effective dates on every leg.',
 'FPCDU initialisation page shows expired AIRAC cycle dates.',
 'CHECK CYCLE · SWAP IF NEXT AVAILABLE · DTU UPDATE IF NEEDED.',
 40),

(@lesson_id, NULL,
 'HIGH-LATITUDE TRUE HEADING SWITCH',
 'Operating at high latitudes (typically near 70°N/S or 80°N/S threshold). FMS automatically switches its heading reference from MAGNETIC to TRUE. The FMS course displayed becomes a TRUE course; standby compass remains magnetic — disagreement between FMS and standby is NORMAL. Brief the FO. ATC clearances may be issued in TRUE in polar regions; otherwise convert to magnetic for VOR/ADF cross-checks. Once back in middle latitudes, FMS auto-switches back to magnetic.',
 0,
 'Polar ops on the Q400 are unusual but not zero (high-latitude UN missions, ferry flights). Brief the TRUE switch on any planned high-latitude leg.',
 'FMS at high latitude with course displayed in TRUE; standby compass disagreement.',
 'TRUE on FMS · MAGNETIC on standby · NORMAL · BRIEF FO.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
