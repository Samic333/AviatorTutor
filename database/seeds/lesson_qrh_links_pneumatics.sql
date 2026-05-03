-- =============================================================================
-- AviatorTutor — Phase 15: ATA 36 Pneumatics — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'pneumatics' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'pneumatics-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'APU FIRE — Auto-Shutdown + 7-Sec Auto-Extg',
 'APU FIRE detected by loop sensor. FPP indications: FIRE light (red), MASTER WARNING + CHECK FIRE DET flash, BTL ARM amber, FUEL VALVE CLOSED white, FUEL VALVE OPEN out, EXTG segment white. APU automatically shuts down. After 7 SECONDS of FIRE detection, the extinguishing agent is automatically released; BTL ARM goes out. If auto-extg fails (BTL ARM stays on), push the guarded EXTG switchlight to manually discharge. Once bottle is discharged, APU restart is PREVENTED until bottle is replaced. Cabin and ground crew alerted as appropriate; brief F/A. ARFF on field if event is on the ramp.',
 1,
 'APU fires are usually contained quickly by the auto-extg. The 7-second window is fast enough that crew rarely needs to manually fire EXTG.',
 'FIRE red on FPP; MASTER WARNING flashing; CHECK FIRE DET flashing.',
 '7-SEC AUTO · MANUAL EXTG IF FAILED · NO RESTART AFTER DISCHARGE.',
 10),

(@lesson_id, NULL,
 'APU PWR FAIL — Internal Fault Auto-Shutdown',
 'APU FADEC detects an internal fault: overspeed/underspeed/start fail/accelerate fail/EGT overtemp/low oil press/high oil temp/failed sensor/failed valve or relay/internal. APU auto-shuts down. PWR FAIL segment (amber) on PWR switchlight. APU caution amber. FUEL VALVE CLOSED white. Action: identify the cause if possible (some are obvious — start fail vs running fault). Reselect PWR if you want to retry. If repeat fault, defer per MEL; plan operations without APU support.',
 0,
 'PWR FAIL is usually a benign ground event. APU is ground-only; flight isn''t affected. Plan the next sector with no APU if needed.',
 'PWR FAIL amber; APU caution; FUEL VALVE CLOSED.',
 'IDENTIFY · RETRY · MEL IF REPEAT.',
 20),

(@lesson_id, NULL,
 'APU GEN OHT — Starter-Generator Overheat',
 'APU starter-generator has overheated. APU automatically shuts down. GEN OHT advisory (amber) illuminates. Action: confirm shutdown; allow cooldown period; investigate cause (cooling air flow restricted? Hot ramp? Excessive load?). Reselect PWR after cooldown if APU is needed. Repeated GEN OHT events warrant MEL deferral and maintenance investigation.',
 0,
 'GEN OHT on a hot ramp is not unusual. Cooldown + retry usually resolves. Don''t fight the system.',
 'GEN OHT amber on APU control panel; APU auto-shutdown.',
 'COOLDOWN · INVESTIGATE · RETRY.',
 30),

(@lesson_id, NULL,
 'BOTTLE LOW or FAULT on APU FPP',
 'BOTTLE LOW (amber): APU fire bottle low or empty. FAULT (amber): APU fire system or FPP fault. Either condition means the APU fire protection is degraded or unavailable. Action: defer per MEL — APU operation without functional fire protection is generally not permitted. Coordinate with maintenance; plan operations without APU until bottle replenishment or system repair.',
 0,
 'BOTTLE LOW or FAULT means the APU is operationally restricted. Don''t dispatch APU-running with these illuminated.',
 'BOTTLE LOW amber; FAULT amber on FPP.',
 'MEL · DEFER · NO APU UNTIL FIX.',
 40),

(@lesson_id, NULL,
 'BL AIR Auto-De-Energizes Unexpectedly',
 'APU BL AIR switchlight de-energizes when not expected. Most likely cause: either main engine BLEED toggle is set to 1 or 2 — system designed to prevent simultaneous APU + engine bleed supply. Verify engine BLEED switch positions; if both are OFF, push BL AIR switchlight again. If still won''t open, check APU EGT — high APU EGT reduces bleed air for GEN load priority. If still won''t open with both engine BLEED switches OFF and APU EGT normal, defer per MEL.',
 0,
 'A common pre-flight pseudo-fault. The auto-de-energization is the system protecting itself, not a real fault.',
 'APU BL AIR switchlight OPEN green goes out unexpectedly.',
 'CHECK ENG BLEED · CHECK APU EGT · MEL IF PERSISTENT.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
