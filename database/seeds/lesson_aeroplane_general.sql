-- =============================================================================
-- AviatorTutor — Phase 1 (ATA 21 Aeroplane General) lesson row.
-- Creates the overview lesson for system_id = aeroplane-general so the
-- subsequent slide / section / flashcard / quiz / qrh / diagram seeds can
-- attach to it.
--
-- Idempotent: re-running upserts the lesson by (system_id, slug).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1);

-- Bail loudly if the system row is missing — the seed writer (Claude) is
-- expected to have verified this in advance.
SELECT @system_id AS resolved_system_id;

-- Idempotent wipe: dropping the lesson cascades to lesson_sections,
-- lesson_slides, lesson_qrh_links, user_slide_progress, etc.
-- Safe to re-run on a freshly imported deploy with no real user data yet.
DELETE FROM lessons
 WHERE system_id = @system_id
   AND slug = 'aeroplane-general-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Aeroplane General — Q400 Architecture & Cockpit Familiarization',
     'aeroplane-general-overview',
     'overview',
     'High-wing twin turboprop, two PW150A engines (5071 SHP each), six-bladed propellers, three-section fuselage, fore-and-trailing split rudder. The "everything you should know before you sit in the seat" lesson — dimensions, structure, cockpit layout, emergency-equipment locations, and the Q400 quirks that catch line crews.',
     '<p>This is the foundation lesson for the Q400. It is the briefing the rest of the systems depend on — you cannot meaningfully discuss flight controls, hydraulics, or pressurization without first knowing where the surfaces are, where the bulkheads are, and where the emergency hand pump lives. The slide deck that follows walks you through the airframe in the same order you would walk around it on a pre-flight: dimensions and engines, fuselage architecture forward to aft, wings, empennage, structural materials, cockpit equipment, then the abnormal and emergency-equipment items every crew member must locate without looking.</p>',
     JSON_ARRAY(
       'Two PW150A turboprops, 5071 SHP each, six-bladed propellers',
       'Wing span 28.42 m, length 32.83 m, MTOW up to 29,257 kg (High Gross Mass)',
       'Three-section fuselage: Forward (flight deck + fwd baggage), Center (cabin), Aft (unpressurised, A/C packs + APU)',
       'Approved to FL250 (25,000 ft) in SAS versions, two-pilot crew, 58 to 72 pax + 2 cabin crew',
       'Empennage uses fore-and-trailing rudder — trailing rudder geometric ratio is 2:1 vs fore',
       'Baggage doors open outward and ONLY from outside; passenger door and Type II/III exit open from either side',
       'Elevators can split via pitch-disconnect system; both are hydraulically operated with artificial feel'
     ),
     JSON_ARRAY(
       'Fuselage = Forward / Center / Aft. APU and air-con packs live in the unpressurised aft section.',
       'The emergency-extension landing-gear hand pump handle is on the aft flight deck — locate it eyes-closed in turbulence.',
       'ANVS (Active Noise & Vibration Suppression) is a Q400 signature; failure shows on the F/A panel as NVS INOP.',
       'The fore rudder is hinged to the rear vertical-stabiliser spar, the trailing rudder is hinged to the fore rudder. Two hydraulic actuators operate the pair.',
       'Composites (aramid / fiberglass / hybrid glass-aramid) live in the radome, nose equipment bay, wing-fuselage fairings, tailcone, bullet fairing, dorsal fin, and stabiliser leading edges.',
       'Skydrol-class fluid is used in the hydraulic system (covered in detail in ATA 29) — never assume a leak is harmless to wiring or composites.'
     ),
     JSON_ARRAY(
       'Q400 SHP per engine — students often quote 5000 SHP. The book number is 5071 SHP.',
       'Approved ceiling — Q400 SAS variant is FL250, NOT FL270 like some other Dash 8 series.',
       'Baggage door direction — exam may ask "from inside or outside?". Answer: outside ONLY.',
       'Rudder uniqueness — trailing rudder deflection is 2x the fore rudder, NOT equal.',
       'Aft fuselage pressurisation — it is UNPRESSURISED, even though the APU and packs live there.'
     ),
     10,
     1);

SELECT
    id AS resolved_lesson_id,
    title
FROM lessons
WHERE system_id = @system_id AND slug = 'aeroplane-general-overview';
