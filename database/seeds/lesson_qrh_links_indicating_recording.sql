-- =============================================================================
-- AviatorTutor — Phase 10: ATA 31 Indicating & Recording — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'indicating-recording' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'indicating-recording-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'SINGLE DU FAILURE — Reversion via ESCP/EFCP',
 'A single Display Unit fails (goes black or shows AVAIL in centre). Action: confirm failure (verify brightness, attempt re-power if procedure allows). Use the ESCP MFD rotary or EFCP brightness control to reroute content. To replace a failed PFD: ESCP MFDx selector to PFD — primary flight content appears on the adjacent MFD. To replace a failed MFD: use the other MFD for system pages, accept the loss of dual system-page presentation. The failed DU shows AVAIL (white) in centre to confirm reversion is available. After reversion: brief the FO on the new pages-by-DU mapping; document the failure; consider divert if workload exceeds the current crew capacity (especially in IMC).',
 0,
 'A DU failure is a workload event. The reversion path must be reflexive — drill it in the sim. The five-DU interchangeable architecture is the design choice that keeps a DU failure from being an emergency.',
 'A DU goes black or shows AVAIL (white) in centre.',
 'CONFIRM · ROUTE VIA ESCP/EFCP · BRIEF FO · CONSIDER DIVERT.',
 10),

(@lesson_id, NULL,
 'ESCP POWER LOSS — MFD2 Selector Dies',
 'ESCP power loss: MFD1 selector remains operative — you can still rotate through PFD/NAV/SYS/ENG and the MFD1 will respond. MFD2 selector does NOT operate — drops to its default page. Practical handling: (1) MFD1 becomes your master reversion path. (2) MFD2 is locked on its current display (typically SYS or NAV). (3) System pushbuttons (ELEC/ENG/FUEL/DOORS/ALL) still work for MFD1 if MFD1 is set to SYS. (4) Press-and-hold a system pushbutton to display the page on the ED in composite format. After action: brief the FO on the asymmetric display set; consider divert.',
 0,
 'ESCP power loss is unusual but creates an asymmetric cockpit. The MFD2 lock is a Q400 design quirk — easy to forget under stress. Drill the recovery in the sim.',
 'ESCP power loss; MFD2 stops responding to selector inputs while MFD1 still does.',
 'MFD1 MASTER · MFD2 LOCKED · ED HOLD-PRESS FOR PAGES.',
 20),

(@lesson_id, NULL,
 'AHRS or ADC SOURCE FAULT (One Side)',
 'AHRS or ADC fault on one side. Crew must switch to the other side''s source: EFIS ATT/HDG SOURCE selector to the healthy side; ADC SOURCE selector to the healthy side. Cross-side source selection illuminates YELLOW on the PFD as a continuous reminder. Continue with the cross-side data; brief the FO; run QRH; consider divert based on remaining workload, weather, and any compounding faults. A single AHRS or ADC loss with healthy cross-side is non-emergency but should be documented.',
 0,
 'Cross-side AHRS/ADC selection is a yellow flag on your PFD — easy to miss if you don''t scan colour discipline. Brief the FO so both pilots see the cross-side indicator.',
 'EFIS ATT/HDG fault or ADC fault on one side; flagged or invalid PFD parameters.',
 'CROSS-SIDE SOURCE · YELLOW PFD · CONTINUE OR DIVERT.',
 30),

(@lesson_id, NULL,
 'STALL WARNING / PUSHER FAULTS',
 'Stall warning faults can present as: (1) #1 or #2 STALL SYST FAIL — one channel of the SPS has failed. The other channel and the pusher logic continue. (2) PUSHER SYST FAIL — caused by either a system fault OR by manual disable via the STICK PUSHER SHUT-OFF switchlight. The stick shaker (warning) function continues; the pusher action is off. Action: confirm whether the pusher is disabled by intent (someone pushed the switchlight) or by fault. If by fault, run QRH; consider exit from icing if applicable. Never re-enable a pusher that the crew intentionally turned off without crew agreement.',
 0,
 'A stick pusher with a fault is an SPS system that no longer guarantees protection at the low-speed end. In icing or near min speeds, this is a divert candidate.',
 '#1 or #2 STALL SYST FAIL caution; PUSHER SYST FAIL caution; "OFF" annunciation on the SHUT-OFF switchlight.',
 'CONFIRM PUSHER STATE · QRH · EXIT ICING IF APPLICABLE.',
 40),

(@lesson_id, NULL,
 'MASTER WARNING / MASTER CAUTION DISCIPLINE',
 'Master Warning (flashing red) demands immediate action. Master Caution (flashing amber) demands awareness + subsequent action. Both reset by pushing the corresponding switchlight on the glareshield (dual switchlights — either pilot can reset). CRITICAL: pushing the switchlight resets THE FLASH (audio + flashing stop). The underlying caution/warning panel light remains illuminated steady if the fault persists. Reset is not the same as "fixed." After reset, eyes go to the C/W panel to identify the source; the page on the MFD/ED gives the system-level view. Run QRH for the identified condition.',
 0,
 'The reset-vs-cleared distinction trips up new captains. The reset only silences the cockpit; the underlying fault is still there until you see the C/W panel light extinguish.',
 'Master Warning red flashing or Master Caution amber flashing.',
 'PUSH RESETS FLASH · UNDERLYING LIGHT PERSISTS · FIND THE FAULT.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
