-- =============================================================================
-- AviatorTutor — Phase 1 (ATA 21 Aeroplane General) QRH cross-references
-- Links four QRH non-normals to the Aeroplane General lesson so the slide
-- player's qrh-type slide renders structured excerpts with memory-item flags.
--
-- Idempotent: re-running wipes prior links for this lesson and re-inserts.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1);
SET @lesson_id := (
    SELECT id FROM lessons
    WHERE system_id = @system_id
      AND slug = 'aeroplane-general-overview'
    LIMIT 1
);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links
    (lesson_id, slide_id, qrh_section_title, qrh_excerpt,
     memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order)
VALUES
(@lesson_id, NULL,
 'REJECTED TAKEOFF — Door / Cabin Caution Below 100 kt',
 'Below 100 kt: REJECT for any door, cabin, fire, engine, or configuration caution. Above 100 kt: continue to V1 unless engine fire or aircraft is unable / unsafe to fly. After rejecting: hold position on the runway, advise tower, request engineering inspection. Do not move the aeroplane until door/cabin state is physically confirmed.',
 1,
 'Captain memory-item discipline. Below 100 kt the cost of stopping is low; the cost of unknown door state at rotation is high. The crew brief before every takeoff names this threshold so the FO knows the call is coming.',
 'EICAS / MFD: DOOR caution, BAGGAGE DOOR caution, or CABIN PRESS caution during takeoff acceleration.',
 'Below 100 kt, any caution = STOP. Above, only fire or unsafe = STOP.',
 10),

(@lesson_id, NULL,
 'LANDING GEAR FAILS TO EXTEND — Manual Extension via Hand Pump',
 'If the landing gear fails to extend on the normal selector, the QRH directs to: (1) confirm hydraulic state per HYD checklists, (2) open the LANDING GEAR ALTERNATE EXTEND DOOR at the forward overhead area, (3) operate the EMERGENCY EXTENSION HAND PUMP HANDLE on the aft flight deck, (4) verify three greens, (5) brief manual-disconnect taxi (no nose-wheel steering on hand-pump-only extension).',
 0,
 'Pump EARLY. The hand pump requires significant physical effort and time. Do not delay starting it until the FAF — go around, climb to a hold, run the procedure, then re-approach with three greens confirmed.',
 'Gear handle DOWN, no green lights, or red light on indicator. Hydraulic abnormal cautions present.',
 'Pump handle = aft flight deck. Pump EARLY. Three greens or go around.',
 20),

(@lesson_id, NULL,
 'SMOKE / FUMES — Unidentified Source',
 'On any persistent or unidentified smoke / fumes: oxygen masks 100%, smoke goggles on, crew comms confirmed, source-isolation per QRH (recirculation fan OFF, packs as required, suspected bus isolation, APU OFF if suspected). If the source remains unidentified after isolation, treat as aft-fuselage origin (APU / pack equipment) — the cockpit cannot access this section in flight. Plan immediate descent and divert to the nearest suitable airport.',
 1,
 'Q400 smoke / fumes is run on suspicion, not on confirmation. The "near suitable" decision is captain-only and should be made within 60 seconds of mask donning. Cabin crew is briefed via interphone using a pre-agreed code so the cabin is prepared.',
 'Persistent electrical smell, smoke from any vent, eye-irritation in cockpit, or BUS / GEN / APU caution combined with smell.',
 'Mask 100% · Goggles · Isolate · NEAREST SUITABLE.',
 30),

(@lesson_id, NULL,
 'EVACUATION ON GROUND — Q400 SAS Configuration',
 'On the SAS Q400, evacuation routes are: (a) main passenger compartment door (left, forward), (b) Type II/III emergency exit, (c) cockpit overhead emergency exit. Baggage doors are NOT evacuation routes — they open from outside only. Cabin crew evacuate after the captain''s order; pilots evacuate via the cockpit overhead exit only after the cabin is clear or if the cabin route is unusable.',
 0,
 'Captain''s evac order is communicated via PA AND interphone. The cockpit overhead exit is the last resort for the flight crew — use the pax door if the main cabin path is clear. Brief evacuation chain on every first-of-day flight.',
 'Fire indication post-stop, structural damage post-RTO, smoke in cabin, or runway-excursion incident.',
 'Pax door · Type II/III · Cockpit overhead.  NEVER baggage doors.',
 40);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
