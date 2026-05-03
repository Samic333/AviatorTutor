-- =============================================================================
-- AviatorTutor — Phase 8: ATA 28 Fuel — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fuel' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fuel-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'BALANCE — Lateral Imbalance >272 KG',
 'Yellow [BALANCE] message flashes above the FUEL legend on the Engine Display; both analog quantity dials change to solid yellow pointers. Triggered when Fuel Quantity Computer detects a tank-quantity difference of more than 272 kg. Action: confirm not a leak (compare burn rate against fuel-on-board math; verify symmetric engine torque/fuel flow), then select FUEL TRANSFER switch toward the lighter tank. Donor tank''s aux pump auto-activates (ON segment turns green without push). Transfer shutoff valves open (MFD VALVE OPEN). Triangle on fuel page points toward the receiver tank. Transfer continues until deselected or until receiver high-level sensor halts. After balance restored: deselect to CENTER; valves close; analog dials return to white pointers; BALANCE message extinguishes.',
 0,
 'A clean balance correction is a 3-minute exercise. The trap is reaching for transfer reflexively without first verifying the burn-rate math — which would be wrong on a leak. Always verify burn-rate symmetry before transfer.',
 'Yellow [BALANCE] flashing above FUEL legend on ED + both analog tank dials yellow.',
 'CONFIRM NOT LEAK · TRANSFER toward lighter · auto-aux · monitor · stop at zero.',
 10),

(@lesson_id, NULL,
 'TANK FUEL LOW — Three-Condition Caution',
 '#1 or #2 TANK FUEL LOW caution illuminates when ALL THREE conditions are met: park brake OFF + collector bay below approximately 150 kg + related engine running. Action: cross-check actual remaining fuel against planned fuel-on-board for the route remaining; verify no leak signature (rapid imbalance, asymmetric burn); confirm aux pump pressure-status circle GREEN on the affected side. Run QRH FUEL LOW non-normal. Single FUEL LOW with planned remaining matching expectation is monitor-only. FUEL LOW with quantity faster than planned = potential leak; consider divert to nearest suitable. Both FUEL LOWs simultaneously with low total fuel = land at NEAREST suitable airport.',
 0,
 'FUEL LOW is your gate to commit on a long sector. If actual remaining is LESS than planned remaining at this point, the math is unfavourable — divert.',
 '#1 or #2 TANK FUEL LOW caution + collector bay reading <~150 kg on MFD fuel page.',
 'PARK BRAKE OFF · 150 KG · ENG RUN — verify burn vs FOB · divert if unfavourable.',
 20),

(@lesson_id, NULL,
 'ENG FUEL PRESS — Engine Pump Inlet Low',
 '#1 or #2 ENG FUEL PRESS caution: engine driven pump inlet pressure has dropped below preset limit. The primary ejector pump is not delivering adequate boost. Action: select the corresponding TANK x AUX PUMP switchlight ON. AC variable-frequency aux pump kicks in and restores boost; caution typically clears within seconds; aux pump pressure-status circle on MFD turns from white-fill to green-fill. If caution does NOT clear with aux pump on, consider: filter clogging (look for FUEL FLTR BYPASS), fuel quality issue, primary pump failure. Run QRH non-normal; brief approach for sustained-aux-pump landing; consider divert if sustained.',
 0,
 'A clean AUX-pump rescue is a non-event — push and confirm green, continue. A sustained ENG FUEL PRESS with aux ON is a divert candidate.',
 '#1 or #2 ENG FUEL PRESS caution; aux pump pressure circle white-fill on affected side.',
 'AUX PUMP ON · GREEN circle · continue or QRH if sustained.',
 30),

(@lesson_id, NULL,
 'FUEL LEAK — Recognising the Pattern',
 'Indications: rapidly growing lateral imbalance + total quantity dropping faster than fuel flow accounts for + visible streaks (if observable from cabin or ramp). [BALANCE] message earlier than expected. CRITICAL: do NOT initiate fuel transfer toward the leaking tank — transferring fuel into a leak just feeds the leak. Action: identify the leaking side using burn-rate vs quantity math (the side with the faster-than-expected burn is the leaker); run QRH FUEL LEAK; secure the affected engine if QRH directs; calculate landing fuel based on actual remaining (not planned); declare PAN-PAN or MAYDAY per severity; divert to nearest suitable. Cross-check tank quantity vs total fuel-on-board math at the start of every cruise hour — if they don''t add up, you have a leak.',
 1,
 'The captain who reflexively reaches for the transfer switch on a real leak makes the situation worse. Burn-rate math FIRST, transfer never. This is one of the few fuel events that ends the sector immediately.',
 'Imbalance growing faster than asymmetric burn justifies; total quantity dropping faster than total fuel flow indicates; possible visible streaks.',
 'BURN VS FOB MATH · DO NOT TRANSFER · QRH FUEL LEAK · secure engine · divert.',
 40),

(@lesson_id, NULL,
 'FUELING ON — In-Flight Caution',
 'FUELING ON caution illuminated in flight: indicates the refuel/defuel access door is open (or the access-door sensor has failed). While illuminated, tank-to-tank fuel transfer is INHIBITED entirely. Action: visually verify door closure if accessible (engineering check on next ground stop); run QRH FUELING ON IN FLIGHT non-normal; consider divert if a sensor fault cannot be confirmed and route ahead requires transfer for balance. If genuine door-open in flight, consider drag/structural risk and treat as airframe-integrity event. The most common in-flight FUELING ON is a sensor fault — confirm with maintenance on landing.',
 0,
 'A FUELING ON caution that you cannot transfer past is a hidden time-bomb on a long sector — you lose the option to balance fuel. Plan accordingly.',
 'FUELING ON caution illuminated in flight; cannot initiate fuel transfer.',
 'TRANSFER INHIBITED · verify door · QRH · divert if balance becomes critical.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
