-- =============================================================================
-- AviatorTutor — Phase 12: ATA 33 Lighting — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'lighting' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'lighting-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'POSITION LIGHT FAILURE — Silent Failover',
 'Position lights have a primary + secondary at each location (green right wingtip, red left wingtip, white aft of bullet fairing). When POSN switch ON, both primary AND secondary illuminate; after ~1 second, the secondary lights go off but stay ARMED via an electronic switch unit. If a primary fails, the related armed secondary automatically illuminates — the failure is silent in the cockpit (no caution). Action: no in-flight action required. Document for maintenance on landing. Both primary AND secondary failed on the same side: required equipment for night ops; defer per MEL or plan day-only ops; consider divert if discovered during night sector.',
 0,
 'A position-light failover is a non-event in flight. The system is designed to handle it. The cockpit panel never indicates a single bulb failure.',
 'ATC may report seeing a light flicker; cockpit panel shows no caution.',
 'PRI-SEC-1-SEC-ARM · SILENT · DOCUMENT.',
 10),

(@lesson_id, NULL,
 'TAXI LIGHT WILL NOT ILLUMINATE',
 'Taxi light is on the steerable section of the nose gear and is INHIBITED unless the gear is locked down. After landing or during taxi-out, if the taxi light won''t come on: verify gear is locked down (no red unsafe, no amber HANDLE, three green LEFT/NOSE/RIGHT). If gear is locked down but taxi light won''t illuminate, defer per MEL. Use landing lights for ground taxi if company SOP allows.',
 0,
 'A common pre-flight fault that crews mistake for a switch problem. Gear must be locked DOWN, not just selected DN.',
 'Taxi light switch ON but no illumination on the ground.',
 'GEAR LOCKED DOWN · MEL IF FAULT.',
 20),

(@lesson_id, NULL,
 'EMER LIGHTS ARM CHECK FAIL',
 'Pre-flight check of EMER LIGHTS shows that ARM does not produce the expected response (e.g. test indicates battery pack not charged or fault). The emergency egress chain may not auto-illuminate on AC power loss. Action: defer per MEL. Significant impact on night ops — without emergency lighting, evacuation in the dark becomes much harder. Consider whether the dispatch is for day-only ops; check MEL conditions for night.',
 0,
 'EMER LIGHTS at ARM is a quiet item — pre-flight check is the only routine touch. A failed ARM check is a significant operational item.',
 'EMER LIGHTS test fails; battery pack low or fault detected.',
 'MEL · NIGHT-OPS RESTRICTION.',
 30),

(@lesson_id, NULL,
 'AC POWER LOSS — EMER LIGHTS Auto-On',
 'In flight, all AC power is lost (e.g. dual generator failure). With EMER LIGHTS at ARM, the cabin emergency egress chain auto-illuminates from emergency battery packs without crew action. Cockpit dome light remains operative (BATTERY PWR bus). Storm lights are inoperative (L SECONDARY bus). Crew action: per the AC POWER LOSS QRH (Phase 5 Electrical), declare PAN-PAN, descend, divert. Brief F/A on the cabin lighting state. Cabin attendants may already be following emergency procedures from the auto-illumination.',
 0,
 'When AC is lost in flight, the cabin lighting transition is one of the first signs the F/A sees. They may begin emergency cabin procedures without prompting.',
 'All AC power lost; cabin emergency lighting on.',
 'EMER ON · DOME ON · STORM OFF · F/A AWARE.',
 40),

(@lesson_id, NULL,
 'INFORMATION SIGN ANOMALY',
 'Common anomalies: NO SMOKING signs do not auto-on with gear DN (auto-logic broken); FASTEN BELTS chime not sounding through PA; lavatory RETURN TO SEAT not illuminating; lavatory OCCUPIED indicator not working with latch in OCCUPIED. Action: verify cockpit FASTEN BELTS / NO SMOKING switches functional; verify LAVATORY LTS membrane on. Defer per MEL. Most are dispatchable but need maintenance for night sectors or international ops where signs are required equipment.',
 0,
 'Information sign anomalies are mostly maintenance items. The auto-logic items (NO SMOKING with gear DN, OCCUPIED with latch) are the most common.',
 'Sign behaviour deviates from expected (auto-logic not firing, chime missing, etc.).',
 'CHECK SWITCH · CHECK MEMBRANE · MEL.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
