-- =============================================================================
-- AviatorTutor — Phase 6: ATA 26 Fire Protection — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fire-protection' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fire-protection-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'ENGINE FIRE — IN FLIGHT (Memory Items)',
 'Both ENGINE FIRE lights flash red, CHECK FIRE DET red, affected PULL FUEL/HYD OFF T-handle illuminated red, fire tone sounds. Memory items, in order: (1) Press an ENGINE FIRE light — silences tone, lights stop flashing, stay on steady. (2) PWR LVR (affected) — DISC. (3) PROP LVR (affected) — FEATHER. (4) CONDITION LVR (affected) — FUEL OFF. (5) PULL the affected T-handle — closes fuel and hydraulic shut-off valves; arms bottle squibs; ARM lights illuminate yellow. (6) EXTG SWITCH (affected) — FWD BTL. (7) Wait 30 seconds. (8) If FIRE light remains: EXTG SWITCH — AFT BTL. After memory items: refer to ENGINE FIRE non-normal. MAYDAY. Single-engine procedures.',
 1,
 'This is one of the few Q400 procedures where memory items must be executed without reading the QRH. Drill until they come out reflexively. The 30-second wait between bottles is mandatory and easy to skip under stress.',
 'ENGINE FIRE press-to-reset lights flashing + CHECK FIRE DET + T-handle illuminated + fire tone.',
 'FLASH · PRESS · PULL · EXTG · 30-sec · OTHER.',
 10),

(@lesson_id, NULL,
 'CHECK FIRE DET (Fault Without Fire)',
 'CHECK FIRE DET (red) on Caution and Warning panel illuminates without an ENGINE FIRE light flashing — typically with FAULT A or FAULT B (amber) on the Fire Protection Panel. This indicates a loop detector circuit fault — APD sensor element rupture or wiring failure — without an actual fire condition. QRH non-normal: confirm no fire indication; verify other engine indications normal; defer per MEL on landing. NOT a divert event by itself, but the affected zone has lost detection capability for the rest of the flight.',
 0,
 'A FAULT without a FIRE is a maintenance issue, not an emergency. Do not pull the T-handle. Continue the flight, brief the FO, write up the defect, defer per MEL.',
 'CHECK FIRE DET red + FAULT A or FAULT B amber. No ENGINE FIRE light. T-handle not illuminated.',
 'FAULT only · No fire · Continue · Defer.',
 20),

(@lesson_id, NULL,
 'BAGGAGE COMPARTMENT SMOKE',
 'BAGGAGE FWD or BAGGAGE AFT SMOKE/EXTG switchlight illuminates plus the appropriate ARM segment amber. Memory action: PUSH the SMOKE/EXTG switchlight on the affected compartment. Aft baggage: HRD discharges immediately, vents close automatically (INLT and OTLT CLOSED illuminate), LRD discharges automatically 7 minutes later. Forward baggage: HRD AND LRD discharge simultaneously. After bottle discharge: monitor for fire suppression, descend, MAYDAY or PAN-PAN, divert to nearest suitable airport with maintenance and emergency services. Cabin oxygen masks deployed if smoke migrates into cabin.',
 0,
 'Baggage smoke is a divert event regardless of whether it is suppressed by the bottle discharge. The fire may not be fully out — get on the ground while you have the option.',
 'BAGGAGE FWD or AFT SMOKE/EXTG switchlight illuminated + ARM segment amber.',
 'PUSH · Vents close · Divert.',
 30),

(@lesson_id, NULL,
 'LAVATORY SMOKE (Cabin Indication Only)',
 'Cabin crew calls "smoke in the lav." There is NO flight-deck indication — verify by their report. Cabin repeater lights are illuminated, the smoke detector LED is on, an audible chime is sounding through the P/A. The Potty Bottle in the waste bin is thermally activated — if the fire is in the bin, the bottle will discharge automatically when the end caps fuse. Crew action: brief cabin crew to use a portable Halon 1211 extinguisher if accessible and safe; descend; declare PAN-PAN or MAYDAY per smoke severity; divert to nearest suitable. Lavatory door isolation may help contain the source.',
 0,
 'A reported lav smoke event without flight-deck indication is real until proven otherwise. Trust the cabin crew. Do not waste time looking for a panel light that does not exist.',
 'Cabin crew report — NO flight-deck indication. Verify by cabin report and chime through P/A.',
 'Cabin says smoke · No panel light · Divert.',
 40),

(@lesson_id, NULL,
 'COCKPIT SMOKE OR FIRE',
 'Cockpit smoke or visible flame from any source — electrical, oil, hydraulic. Memory items: (1) ALL CREW oxygen masks ON, EMERGENCY position selected (100% O2 positive pressure). (2) Establish crew communication on smoke goggles. (3) If a discrete electrical source is identified, isolate by switch or breaker. (4) Use the cockpit Halon 1211 portable extinguisher — confirm gauge GREEN, pull safety catch, discharge at base of source. After memory items: refer to SMOKE / FUMES non-normal; descend; declare; divert to nearest suitable. Cabin notified.',
 1,
 'Mask-first is non-negotiable in the cockpit. Halon displaces oxygen and incapacitates crew within seconds without protected breathing. The first 10 seconds determine the outcome — they must be reflexive.',
 'Visible smoke or flame in cockpit; smell of electrical / oil / fuel; warning lights related to source.',
 'MASK 100% · Identify · Isolate · Discharge · Divert.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
