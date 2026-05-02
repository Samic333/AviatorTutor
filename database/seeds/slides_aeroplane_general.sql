-- =============================================================================
-- AviatorTutor — Phase 1: ATA 21 Aeroplane General
-- 14-slide interactive lesson for the Q400 Aeroplane General overview.
--
-- Idempotent: re-running wipes prior slides for this lesson and re-inserts.
-- Run AFTER lesson_aeroplane_general.sql so the lesson row exists.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1);
SET @lesson_id := (
    SELECT id FROM lessons
    WHERE system_id = @system_id
      AND slug = 'aeroplane-general-overview'
    LIMIT 1
);

SELECT
    @system_id AS resolved_system_id,
    @lesson_id AS resolved_lesson_id;

-- Wipe any prior slides for this lesson so the seed can be re-run safely.
DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides
    (lesson_id, sort_order, slide_type, title, body,
     media_type, media_url, media_alt, key_point, ops_relevance, question)
VALUES
-- 1. Intro
(@lesson_id, 10, 'intro',
 'Why "Aeroplane General" Comes First',
 'Before we open any single system, we have to know what we are sitting in. Aeroplane General is the briefing every other lesson depends on. Where are the bulkheads. Where are the doors. Where do the emergency hand pump and the smoke goggles live. What is pressurised and what is not. By the end of these fourteen slides you should be able to walk around the Q400 and explain it to a new first officer without looking at a manual — because in the cockpit at 0500 you will not have the time to look.',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Q400 architecture overview diagram',
 'Aeroplane General = the room before the systems. Know the room first.',
 'Every type-rating sim ride starts with "tell me about the aeroplane." A confident, structured answer here sets the tone for the whole check.',
 NULL),

-- 2. Concept — numbers
(@lesson_id, 20, 'concept',
 'The Q400 in Numbers — Memorise These Six',
 'Six numbers that come up over and over. Span 28.42 m, length 32.83 m. Two PW150A turboprop engines, 5071 SHP each, driving six-bladed propellers. Approved to FL250 (25,000 ft) in the SAS variant. Maximum take-off mass goes up to 29,257 kg in the High Gross Mass configuration. The aeroplane is two-pilot certified and seats 58 to 72 passengers plus two cabin crew. These numbers anchor every weight, performance, and SOP question.',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Q400 dimensions diagram (28.42 m span, 32.83 m length)',
 '28-by-33, 5-0-7-1, six blades, FL250, 29,257 kg.',
 'Quote these by memory in any tech interview. The 5071 SHP figure separates a candidate who studied the book from one who said "about 5000."',
 NULL),

-- 3. Concept — fuselage architecture
(@lesson_id, 30, 'concept',
 'Three-Section Fuselage — Forward, Center, Aft',
 'The fuselage is built in three main parts. The Forward section holds the flight deck and the forward baggage compartment on the right side. The Center section is the passenger cabin — constant cross-section with a slightly flattened bottom. The Aft section is unpressurised and is swept up; it houses the air-conditioning packs and the APU, and provides access to the empennage support structure. The forward and aft pressure bulkheads are what make the Center section pressurisable and the Aft section is deliberately not.',
 'diagram', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Fuselage three-section diagram with bulkheads',
 'F-C-A. Forward, Center, Aft. APU and packs in the un-pressurised tail.',
 'A pressurisation question often hides here: "is the APU compartment pressurised?" The answer is no — that is one reason an APU fire is contained.',
 JSON_OBJECT(
   'prompt', 'Where on the Q400 do the air-conditioning packs and the APU live, and is that area pressurised?',
   'options', JSON_ARRAY(
     'Forward section, pressurised',
     'Center section, pressurised',
     'Aft section, unpressurised',
     'Aft section, pressurised'
   ),
   'correct_index', 2,
   'explanation', 'Both packs and the APU live in the aft fuselage. The aft section sits behind the aft pressure bulkhead and is deliberately unpressurised — that lets a pack failure or an APU fire vent overboard rather than into the cabin.'
 )),

-- 4. System — forward section
(@lesson_id, 40, 'system',
 'Forward Section — Flight Deck, Nose, and the Baggage Door Quirk',
 'The Forward section runs from the radome to the bulkhead aft of the flight crew seats. The nose, in front of the forward pressure bulkhead, holds the nose-wheel well, an unpressurised equipment deck, and the weather radar. The flight deck has laminated-glass windshields and a glass-plus-plastic side window. The forward baggage compartment is on the right forward side. Important quirk: both baggage doors open outward and can ONLY be opened from the outside — the passenger door and the Type II/III emergency exit can be opened from either side.',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Forward section layout with baggage door highlight',
 'BAGS-OUT-ONLY. Baggage doors: outside-only. Pax door + Type II/III: both sides.',
 'On a security walk-around the baggage doors should never be openable from inside the cabin — confirm visually before pushback. A baggage door ajar warning during taxi means stopping, not continuing.',
 NULL),

-- 5. System — wings
(@lesson_id, 50, 'system',
 'Wings — High, Cantilevered, and Why That Matters',
 'A single high-aspect-ratio cantilevered wing joins the upper midsection of the fuselage. Inside it: integral fuel tanks, the engine nacelles, and the main landing gear mounting structure. Outboard of the engine nacelles the wing tapers and has 2.5 degrees of dihedral. Pneumatic deicer boots cover the leading edges of the centre wing and the section outboard of the landing lights. Control surfaces on the wing: conventional ailerons working with differential lateral-control spoilers on the upper skin, and single-slotted flaps from the fuselage to inboard of the ailerons. The spoilers also have a ground mode — they extend on landing to dump lift.',
 'diagram', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Wing detail with ailerons, flaps, spoilers labelled',
 'High wing, 2.5° dihedral, integral tanks, spoilers do roll AND lift-dump.',
 'The high-wing design changes propeller-arc clearance and ground clearance. Operating from a domed runway or one with edge slope makes the down-going wing a real concern.',
 NULL),

-- 6. System — empennage
(@lesson_id, 60, 'system',
 'Empennage — The Q400 Split Rudder',
 'The empennage carries a horizontal stabiliser with separate left and right elevators, and a vertical stabiliser with a fore rudder and a trailing rudder. The Q400 is unusual: the trailing rudder is hinged to the trailing edge of the fore rudder, and is geometrically arranged to deflect at twice the angle of the fore rudder. Two hydraulic actuators drive the rudder pair. Both elevators normally operate together but can split via the pitch-disconnect system if a jam appears. Elevators are hydraulically operated with artificial feel; hydraulic actuators handle pitch trim. The vertical stabiliser and rearmost fuselage are constructed as one composite-leading-edge piece, capped with a composite bullet fairing.',
 'animation', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Empennage animation showing fore + trailing rudder deflection',
 'FORE+TRAIL = TWO-FOR-ONE. Trailing rudder deflects 2x the fore rudder.',
 'Asymmetric rudder behaviour shows up in V1-cut training and during one-engine-inop manoeuvring. Knowing which rudder you are commanding helps you interpret what the airplane is doing in the rudder.',
 JSON_OBJECT(
   'prompt', 'On the Q400 the trailing rudder is geometrically arranged so that its deflection is:',
   'options', JSON_ARRAY(
     'Equal to the fore rudder',
     'Half the fore rudder',
     'Twice the fore rudder',
     'Independent of the fore rudder, set by the autopilot'
   ),
   'correct_index', 2,
   'explanation', 'The trailing rudder is hinged to the fore rudder and is geared so that it deflects at twice the fore-rudder angle. This gives the Q400 the rudder authority it needs for engine-out handling without an oversized fin.'
 )),

-- 7. System — aft section
(@lesson_id, 70, 'system',
 'Aft Section — APU, Packs, and Why It Stays Unpressurised',
 'The aft section sits behind the aft pressure bulkhead, swept up to support the empennage. Inside it: both air-conditioning packs and the APU. The section is deliberately unpressurised — a leak, a pack fire, or an APU compartment fire vents overboard rather than into the cabin. There is internal access for inspection and maintenance. Because pressurisation stops at the aft pressure bulkhead, the cabin can stay sealed even if the aft fuselage is opened up after a hard landing or a tail strike.',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Aft fuselage section with APU and pack locations',
 'Aft = Unpressurised. APU fire = vents overboard.',
 'After a tail-strike inspection, do not assume the aft fuselage is structurally stressed in the same way as the cabin. Different pressure regime, different inspection criteria.',
 NULL),

-- 8. Normal op — structure & materials
(@lesson_id, 80, 'normal_op',
 'Materials — Aluminium Primary, Composites Where It Matters',
 'The airframe primary structure is high-strength aluminium alloy. Steel is used in the landing gear and selected airframe components; titanium and other aluminium alloys appear elsewhere. Magnesium is used in selected interior regions of the flight deck, cabin and engine. Composite panels live in specific places: radome (fiberglass / honeycomb core), nose equipment bay (aramid fiber), wing-to-fuselage fairings (fiberglass), tailcone (aramid; titanium when APU-equipped), bullet fairing (hybrid glass / aramid), dorsal fin (hybrid glass / aramid), stabiliser leading edges (aramid covered by rubber de-ice boots), and ice protection panels.',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Materials map showing aluminium primary + composite panel locations',
 'Aluminium body, aramid edges, fiberglass radome, titanium tailcone (with APU).',
 'A "small composite ding" near a leading edge is never small — composites can have hidden delamination. Defer to engineering for any visible damage to a composite panel.',
 NULL),

-- 9. Normal op — cockpit equipment & locations
(@lesson_id, 90, 'normal_op',
 'Cockpit Equipment — Where the Important Things Live',
 'On the flight deck, locate without looking: the Caution & Warning Panel above the glareshield centre, the Standby Compass forward of it, smoke goggles at both seats, life-vest stowage at both seats, the emergency escape rope storage, and the emergency exit overhead. Behind the seats on the aft flight deck: the Landing Gear Emergency Extension HAND PUMP HANDLE, the fire axe, two fire extinguishers, flashlights, the Protective Breathing Equipment (PBE), and the Weight & Balance Manual. The Landing Gear Alternate Release Door and Alternate Extend Door are at the front of the overhead area — the fast-access path for a gear that will not come down on hydraulics.',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Aft flight deck equipment layout — handpump, axe, PBE, extinguishers',
 'PBE, Axe, Pump, Extinguisher — back-of-the-bus must-knows.',
 'On every first-of-day brief, physically point to the hand pump handle and the PBE. If you cannot find them in daylight, you will not find them in smoke.',
 NULL),

-- 10. Abnormal — Aft section access during flight
(@lesson_id, 100, 'abnormal',
 'When the Aft Section Becomes the Problem — Smoke and Fumes',
 'Smoke or fumes that smell electrical and persist after pulling pack inputs almost always trace back to the aft fuselage — that is where the packs and the APU sit. The cockpit cannot crew-access the aft section in flight. The crew responses are: don oxygen masks 100%, run the SMOKE / FUMES / FIRE QRH, isolate the suspect bus, consider APU shutdown if the APU is the suspect, and plan to land at the nearest suitable airport. Do not attempt to investigate the aft section in flight. After landing, evacuate before opening the section for engineering inspection.',
 'video', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Aft-fuselage smoke procedure briefing video',
 'Aft smoke = Mask 100%, QRH, isolate, divert. Do NOT investigate in flight.',
 'A persistent smell with no caution light is still a smoke event. The Q400 SMOKE / FUMES procedures are run on suspicion, not on confirmation.',
 JSON_OBJECT(
   'prompt', 'In flight, you smell electrical smoke that persists after both packs are turned off. The most likely physical source is:',
   'options', JSON_ARRAY(
     'A laptop in a passenger seat — pull it and continue',
     'The forward baggage compartment — the bags are smouldering',
     'The aft fuselage — APU or a component near the packs',
     'The galley ovens — turn galley power off and continue'
   ),
   'correct_index', 2,
   'explanation', 'Persistent electrical smoke after isolating the packs strongly implies an aft-fuselage source. The APU and pack equipment live there and the area is unpressurised. The crew cannot access it in flight — so the answer is procedure-driven: oxygen, QRH, isolate, divert.'
 )),

-- 11. Operational — emergency exits & evacuation
(@lesson_id, 110, 'operational',
 'Emergency Exits — Type II/III, Pax Door, and the "Outside-Only" Rule',
 'On the SAS Q400 the routes off the aeroplane are: the main passenger compartment door (left, forward), one Type II/III emergency exit, and the cockpit emergency exit overhead. The passenger door and the Type II/III exit can be opened from either inside or outside — that is intentional, so an outside rescuer or an inside survivor can both work the door. The two baggage doors open outward and only from the outside — they are NOT evacuation routes. Cabin crew brief these every flight.',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Q400 SAS evacuation routes — pax door, Type II/III, cockpit overhead',
 'Two cabin exits + 1 cockpit overhead. Baggage doors are NEVER an exit.',
 'Pre-flight, run a quick cross-cabin sweep with the senior cabin crew member: "exits, slides, fire, smoke, comms." Five words, every flight, before doors close.',
 NULL),

-- 12. QRH connection — landing gear emergency extension
(@lesson_id, 120, 'qrh',
 'QRH Connection: Landing Gear Manual Extension via the Hand Pump',
 'The QRH "LANDING GEAR FAILS TO EXTEND" or "DUAL HYDRAULIC FAIL" non-normals call for manual gear extension. The Aeroplane General lesson is where you locate the kit: the Alternate Release and Alternate Extend doors are at the front of the overhead area; the hand pump handle is at the aft flight deck. The procedure is physically demanding — pumping the gear down takes time and the FO has to hold the handle in long, slow strokes. Brief this BEFORE every approach where any hydraulic abnormal exists. Pump early. Verify three greens. Brief a manual-disconnect taxi (no nose-wheel steering on hand-pump-only extension).',
 'image', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'QRH page: gear emergency extension hand pump',
 'Pump handle = aft flight deck. Pump EARLY. Three greens or go around.',
 'In a sim ride or check, fingertip-locate the hand pump handle and the Alternate Extend door BEFORE the instructor asks. That muscle memory wins the gear-emergency item every time.',
 NULL),

-- 13. Scenario — captain decision
(@lesson_id, 130, 'scenario',
 'Captain Decision: Door Warning at 60 kt on Takeoff Roll',
 'Setup: rolling takeoff, accelerating through 60 knots, MFD pops a "DOOR" caution. CAS: BAGGAGE DOOR. The FO calls "Door". Wind is 5 knots, runway is 2400 m, V1 is 110.\n\nQuestion: do you reject? The door cannot have just opened — baggage doors ONLY open from the outside. The caution is most likely an indication issue (microswitch / locked-flag sensor). But you do not know that from the cockpit. You have plenty of stopping margin at 60 kt. The right call is: REJECT BELOW 100 KT for any door / cabin-pressure caution. After stopping, hold position, request an engineer, do not move the airplane until the door is physically inspected.',
 'animation', '/assets/aircraft/q400/aeroplane-general-flow.svg',
 'Reject decision animation: door caution at 60 kt',
 'Below 100 kt, a door caution = REJECT. No exceptions. Inspect before moving.',
 'The reject decision is a captain-only call. Brief it so the FO knows it is coming: "below 100 kt, any door, cabin, or engine caution, we stop."',
 JSON_OBJECT(
   'prompt', 'Rolling at 60 kt on takeoff in a Q400, MFD shows "DOOR — BAGGAGE" caution. What is the correct action?',
   'options', JSON_ARRAY(
     'Continue — baggage doors only open from outside, so it must be a sensor fault',
     'Continue if the runway is below 1800 m, otherwise reject',
     'Reject below 100 kt for any door caution; hold position; request engineering inspection before any further movement',
     'Reject only if a second caution appears in the next 5 seconds'
   ),
   'correct_index', 2,
   'explanation', 'A door caution below 100 kt is always a reject in a Q400. Even though physical opening is unlikely (baggage doors are outside-only), the cockpit cannot confirm whether the door is latched. The cost of rejecting at 60 kt is low; the cost of not knowing is open at rotation. After stopping, do not move until the door has been physically inspected on the ground.'
 )),

-- 14. Revision — recap
(@lesson_id, 140, 'revision',
 'Lesson Recap: Aeroplane General in 60 Seconds',
 'Recap the foundation:\n  • Q400 = high-wing twin turboprop, two PW150A engines (5071 SHP each), six-bladed props, FL250 ceiling.\n  • Numbers: 28.42 m span, 32.83 m length, MTOW up to 29,257 kg High Gross Mass, 58–72 pax.\n  • Fuselage: Forward (flight deck + fwd baggage), Center (cabin), Aft (UNPRESSURISED — APU + packs).\n  • Wing: high, cantilever, 2.5° dihedral, integral fuel tanks, single-slotted flaps, spoilers do roll AND ground lift-dump.\n  • Empennage: fore + trailing rudder, trailing deflects 2x the fore. Elevators can split via pitch disconnect.\n  • Materials: aluminium primary, aramid leading edges, fiberglass radome, titanium tailcone (with APU).\n  • Doors: baggage = outside-only; pax door + Type II/III = both sides; cockpit overhead = emergency exit.\n  • Hand pump for gear emergency extension lives on the aft flight deck.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'F-C-A · 28-by-33 · 5-0-7-1 · BAGS-OUT-ONLY · FORE+TRAIL=2x · APU=AFT-UNPRESS',
 'Run these six mnemonics on every recurrent. They are the spine of any Aeroplane General oral. Drill them.',
 NULL);

-- Re-apply difficulty visibility after reseed (Phase 2 columns).
-- Beginner mode skips the abnormal/qrh/scenario slides; Intermediate adds them.
-- Advanced sees everything including the scenario gate.
UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');

-- Treat the materials slide as intermediate-plus (lots of detail, low daily-ops impact).
UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND title = 'Materials — Aluminium Primary, Composites Where It Matters';

-- Captain Decision is advanced-only — it is the stretch question.
UPDATE IGNORE lesson_slides
   SET show_beginner     = 0,
       show_intermediate = 0
 WHERE lesson_id = @lesson_id
   AND title = 'Captain Decision: Door Warning at 60 kt on Takeoff Roll';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
