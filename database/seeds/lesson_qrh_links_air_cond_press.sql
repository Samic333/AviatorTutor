-- =============================================================================
-- AviatorTutor — Phase 2 (ATA 21 Air Conditioning & Pressurization) QRH links
-- Five QRH cross-references linked to the lesson so the slide player's
-- qrh-type slide renders structured excerpts with memory-item flags.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'air-cond-press' LIMIT 1);
SET @lesson_id := (
    SELECT id FROM lessons
    WHERE system_id = @system_id
      AND slug = 'air-cond-press-overview'
    LIMIT 1
);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links
    (lesson_id, slide_id, qrh_section_title, qrh_excerpt,
     memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order)
VALUES
(@lesson_id, NULL,
 'CABIN ALTITUDE WARNING (>9,800 ft)',
 'Memory items in order: (1) OXYGEN MASKS — ON, 100%; (2) CREW COMMS — ESTABLISHED; (3) EMERGENCY DESCENT — INITIATE; (4) TRANSPONDER — 7700; (5) ATC — ADVISE. Then run the QRH for the secondary actions: PACK switches as required, BLEED switches as required, CPC mode confirmation, FSV selector if rapid depressurisation needed. Land at the nearest suitable airport. Continued flight at FL100 or below only if cabin altitude can be re-established at safe levels per QRH guidance.',
 1,
 'Time-critical above FL200. The first 30 seconds determine outcome. Brief the memory items on every recurrent until they are automatic. Cabin altitude warning is one of the very few Q400 events where you act on memory FIRST and read the QRH SECOND.',
 'CABIN PRESS warning light (red). Cabin altitude indicator above 9,800 ft. Possible smell of cold dry air, ear pressure changes, oxygen-mask indicator changes for cabin crew.',
 'Mask · 100% · Descend · 7700 · ATC.',
 10),

(@lesson_id, NULL,
 'PACK FAIL — Single Pack Loss in Flight',
 'A single pack failure leaves the second pack handling cabin and flight-deck flow at 70% of selected flow with the recirculation fan at low speed. QRH actions: confirm failed pack OFF; verify the running pack indications; expect slightly elevated cabin temperature on warm days; brief approach for normal landing. No immediate diversion required unless temperature management becomes operationally limiting.',
 0,
 'Continue to destination if comfortable. Communicate with cabin crew so they can manage passenger comfort. Note: a single-pack ferry is normal MEL territory but a pack failure in flight needs a tech-log entry and engineering attention on arrival.',
 'PACK 1 (or PACK 2) caution. Pack inlet flow / temperature gauge anomaly. Cabin temperature drift.',
 'Single = continue. Dual = ram air + descend.',
 20),

(@lesson_id, NULL,
 'PACKS FAIL — Dual Pack Loss',
 'Both packs lost. ECS supply ends; cabin pressurisation cannot be maintained at altitude. QRH calls for: (1) immediate descent to FL100 or below; (2) emergency RAM AIR ventilation selection; (3) BLEED switches as appropriate; (4) check for smoke/fumes (dual-pack loss often correlates with an aft-fuselage event); (5) PAN-PAN minimum, MAYDAY if any other system is involved; (6) divert to nearest suitable.',
 1,
 'A "land at the nearest suitable airport" event. Crew oxygen if cabin altitude is climbing. Brief cabin crew explicitly that ram-air ventilation will be cooler and noisier than normal. Confirm passenger oxygen state if cabin altitude exceeds 14,000 ft.',
 'Both PACK 1 and PACK 2 cautions. ECS flow / cabin temperature gauges fall to ambient indications. Cabin altitude trending up.',
 'Ram air · Descend FL100 · Nearest suitable.',
 30),

(@lesson_id, NULL,
 'CPC FAULT — AUTO Mode Failure (Switch to MAN)',
 'AUTO mode self-test detected a controller fault, OR the FAULT alert light remained on after power-up. QRH switches the AUTO-MAN-DUMP toggle to MAN. The crew now schedules cabin pressure manually using DECR (open outflow / cabin altitude up) or INCR (close outflow / cabin altitude down). Cabin altitude, DIFF PSI, and rate-of-change must be monitored continuously. If MAN also fails, the FWD OUTFLOW knob bleeds pressure progressively through the FSV; the FORWARD SAFETY VALVE selector dumps it fully.',
 0,
 'Workload jumps significantly in MAN mode. Brief the FO that pressurisation is now a continuous task, not an automatic one. On a long sector, consider crew rotation of the pressurisation watch. Smaller diff targets are safer if the controller behaviour is uncertain.',
 'FAULT alert light on the CPC panel persistent after power-up self-test. Unexpected cabin altitude / DIFF PSI behaviour with AUTO selected.',
 'AUTO out · MAN in · Watch the three gauges.',
 40),

(@lesson_id, NULL,
 'SMOKE / FUMES — ECS / Pack Source Suspected',
 'Persistent smoke or fumes that smell electrical or "machinery hot" with possible source in the aft fuselage (where the packs and APU live). Sequence: oxygen masks 100%, smoke goggles on, comms confirmed, RECIRC fan OFF, pack inputs as per QRH, suspected bus isolation, possible APU OFF, divert nearest suitable. Aft fuselage is unpressurised and not crew-accessible in flight — response is procedural, not investigative.',
 1,
 'Run on suspicion, not on confirmation. The "near suitable" decision is captain-only and should be made within 60 seconds of mask donning. Cabin crew briefed via interphone with a pre-agreed code. Time-critical: smoke in cockpit degrades quickly.',
 'Persistent smell with no caution light, OR pack/recirc-related cautions combined with smell, OR APU caution combined with smell.',
 'Mask · Goggles · Recirc OFF · Isolate · NEAREST SUITABLE.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
