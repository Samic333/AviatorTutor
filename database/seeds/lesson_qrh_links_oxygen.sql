-- =============================================================================
-- AviatorTutor — Phase 14: ATA 35 Oxygen — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'oxygen' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'oxygen-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'RAPID DEPRESSURISATION — Memory Items',
 'Rapid depressurisation in cruise. Memory items in order: (1) Oxygen masks ON immediately — don in less than 5 seconds via inflatable harness. (2) Regulator at NORM (or 100% if cabin altitude high). (3) Crew comm via mask mic + audio connector. (4) Initiate emergency descent toward 14,000 ft (system sized for descent in 4 min). (5) Cabin announcement; F/A check passenger O2. (6) Run QRH RAPID DEPRESSURISATION non-normal. (7) Plan divert to nearest suitable airport. Mask donning under 5 seconds is the survival item — useful consciousness time at altitudes above FL250 is measured in seconds.',
 1,
 'Mask first, talk after. The 5-second don is the survival window. Drill it in every recurrent.',
 'Sudden cabin altitude rise, ear pop, mist, ringing alarm.',
 'MASKS ON · NORM/100% · COMM · DESCEND · DIVERT.',
 10),

(@lesson_id, NULL,
 'COCKPIT SMOKE / FIRE — EMER Mode',
 'Cockpit smoke or fire event. Memory items: (1) Oxygen masks ON immediately. (2) Regulator to EMER position — 100% O2 at slight positive pressure. The positive pressure flushes contaminants out around the mask seal AND purges smoke from any smoke goggles. (3) Smoke goggles ON if not already. (4) Establish crew comm via mask mics. (5) If a discrete electrical source is identified, isolate by switch or breaker. (6) Use cockpit Halon 1211 portable extinguisher (Phase 6) per Halon procedure — confirm gauge GREEN, pull safety, discharge at base. (7) Run QRH SMOKE/FUMES non-normal. CAUTION: keeping the regulator in EMER continuously will deplete the cylinder. Return to NORM or 100% as soon as the smoke event is contained.',
 1,
 'Mask first, EMER second, goggles third. The positive pressure flushes the goggles and the seal — that''s the design intent of EMER.',
 'Visible smoke or smell of electrical / oil / fuel; warning lights related to source.',
 'MASK ON · EMER · GOGGLES · ISOLATE · DISCHARGE · MANAGE EMER TIME.',
 20),

(@lesson_id, NULL,
 'CYLINDER LOSS / RED IN-LINE INDICATOR',
 'In-line pressure indicator on supply hose is RED, OR breathing difficulty after donning mask. Action: (1) Verify supply hose is connected at both the mask end and the outlet. The most common cause is a disconnected hose. (2) If hose is connected and indicator still RED, switch the mask to another outlet (cross-compatible). (3) If still RED on multiple outlets, the cylinder may be off, depleted, or faulted — verify cylinder shutoff knob is OPEN; check cylinder gauge for pressure. (4) If cylinder confirmed lost / depleted, no fixed crew oxygen available — descend to 14,000 ft or below; divert. Use observer mask if a primary mask is the issue.',
 0,
 'A red in-line indicator is fixable in seconds — usually a hose. Don''t panic; check the obvious first.',
 'In-line pressure indicator RED; breathing difficulty after mask don.',
 'CHECK HOSE · TRY OTHER OUTLET · CYLINDER VALVE · DESCEND IF CYLINDER LOST.',
 30),

(@lesson_id, NULL,
 'CABIN SMOKE / FIRE — F/A Use of PBE',
 'Cabin smoke or fire event. Cabin attendants don PBE units (Protective Breathing Equipment) for low-oxygen environment work. Crew uses portable Halon 1211 extinguishers (3 in cabin per Phase 6) for active firefighting. Portable passenger O2 cylinders supplement the PSU drop-down system if needed. F/A coordinates with flight deck via interphone. Flight crew runs QRH SMOKE/FUMES; descends; declares MAYDAY/PAN-PAN per severity; diverts to nearest suitable. Brief cabin via PA. Cockpit crew may need oxygen masks on if smoke migrates forward.',
 0,
 'Cabin smoke is the F/A''s show, with flight-deck support. PBE for the F/A; oxygen masks for the flight deck.',
 'Cabin attendant calls smoke/fire event; smoke detector lavatory chime through PA.',
 'PBE · HALON · PORTABLE O2 · COMM · DIVERT.',
 40),

(@lesson_id, NULL,
 'BURST DISC EJECTED (Pre-Flight Discovery)',
 'Pre-flight walk-around: green burst disc on right exterior of nose is missing or visibly compromised. This indicates a cylinder over-pressurisation event has occurred. The cylinder safety relief has activated, venting the cylinder to atmosphere. Action: maintenance write-up. The crew oxygen system is presumed inoperative until inspected and recharged. Do not dispatch with crew O2 system inoperative — required equipment for the flight envelope. Escalate to dispatch and maintenance.',
 0,
 'A pre-flight catch of a missing burst disc saves a sortie. Train your eye to find the disc on every walk-around.',
 'Green burst disc on right exterior of nose missing or damaged during walk-around inspection.',
 'WRITE UP · NO DISPATCH · MAINTENANCE.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
