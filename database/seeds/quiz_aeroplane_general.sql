-- =============================================================================
-- AviatorTutor — Phase 1: ATA 21 Aeroplane General
-- Two quizzes attached to the Aeroplane General system:
--   1) "Aeroplane General — Practice" : 25 questions, no time limit
--   2) "Aeroplane General — Type Rating Mock"  : 10 questions, 12-minute timer
--
-- Idempotent: re-running wipes prior quizzes (and their questions, via
-- CASCADE) for this system and re-inserts.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

-- ----------------------------------------------------------------------------
-- Quiz 1 — Practice
-- ----------------------------------------------------------------------------
INSERT INTO quizzes
    (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES
    (@system_id,
     'Aeroplane General — Practice',
     'Twenty-five-question practice quiz covering Q400 dimensions, fuselage architecture, wing and empennage layout, materials, cockpit equipment locations, doors, and the Q400 quirks. No time limit.',
     'practice',
     NULL,
     70,
     1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions
    (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order)
VALUES
(@quiz_practice,
 'How many shaft horsepower (SHP) does each Q400 PW150A engine produce?',
 'mcq',
 JSON_ARRAY('5000 SHP','5071 SHP','5100 SHP','4750 SHP'),
 JSON_OBJECT('correct_index', 1),
 'The PW150A is rated at 5071 SHP. The "about 5000" answer is the common student trap.',
 'easy', 10),

(@quiz_practice,
 'How many propeller blades does each Q400 engine drive?',
 'mcq',
 JSON_ARRAY('Four','Five','Six','Eight'),
 JSON_OBJECT('correct_index', 2),
 'Six-bladed propellers are part of the Q400 signature — they let the prop turn at lower RPM for the same thrust, which keeps cabin noise within the ANVS''s capability to suppress.',
 'easy', 20),

(@quiz_practice,
 'What is the maximum operating altitude of the Q400 in SAS-variant configuration?',
 'mcq',
 JSON_ARRAY('FL220','FL250','FL270','FL310'),
 JSON_OBJECT('correct_index', 1),
 'SAS-variant Q400s are approved to FL250 (25,000 ft). Other Dash 8 series have different ceilings — do not confuse them.',
 'easy', 30),

(@quiz_practice,
 'The Q400 fuselage is constructed in three main parts. Which one is unpressurised?',
 'mcq',
 JSON_ARRAY('Forward','Center','Aft','Nose only'),
 JSON_OBJECT('correct_index', 2),
 'The aft section is deliberately unpressurised. It houses the air-conditioning packs and the APU. Smoke or fire from these items vents overboard rather than into the cabin.',
 'easy', 40),

(@quiz_practice,
 'Which of the following is true of the Q400 baggage doors?',
 'mcq',
 JSON_ARRAY(
   'Open inwards, can be opened from inside or outside',
   'Open outwards, can be opened from inside or outside',
   'Open outwards, can ONLY be opened from outside',
   'Open inwards, can ONLY be opened from inside'
 ),
 JSON_OBJECT('correct_index', 2),
 'Both baggage doors open outwards and can only be opened from outside. The passenger door and the Type II/III emergency exit, by contrast, can be opened from either side.',
 'medium', 50),

(@quiz_practice,
 'On the Q400, the trailing rudder is geometrically arranged so that its deflection is:',
 'mcq',
 JSON_ARRAY(
   'Equal to the fore rudder',
   'Half the fore rudder',
   'Twice the fore rudder',
   'Independent of the fore rudder, set by the autopilot'
 ),
 JSON_OBJECT('correct_index', 2),
 'Trailing rudder deflects 2x the fore rudder. The pair gives the Q400 the rudder authority needed for engine-out handling without an oversized vertical stabiliser.',
 'medium', 60),

(@quiz_practice,
 'How many hydraulic actuators operate the Q400 rudder pair?',
 'mcq',
 JSON_ARRAY('One','Two','Three','Four — one per blade chamber'),
 JSON_OBJECT('correct_index', 1),
 'Two hydraulic actuators drive the fore + trailing rudder pair.',
 'medium', 70),

(@quiz_practice,
 'How are the Q400 elevators powered, and what allows them to be split?',
 'mcq',
 JSON_ARRAY(
   'Cable-driven; split via a mechanical disconnect lever',
   'Hydraulically operated with artificial feel; split via the pitch-disconnect system',
   'Electrically driven; split via the autopilot disconnect',
   'Hydraulically operated with no artificial feel; cannot be split in flight'
 ),
 JSON_OBJECT('correct_index', 1),
 'Elevators are hydraulically operated with artificial feel. Both normally operate together but can split via the pitch-disconnect system if a jam appears.',
 'medium', 80),

(@quiz_practice,
 'What is the wing dihedral on the Q400?',
 'mcq',
 JSON_ARRAY('1.0°','2.5°','3.5°','5.0°'),
 JSON_OBJECT('correct_index', 1),
 'The Q400 wing has 2.5° dihedral outboard of the engine nacelles.',
 'medium', 90),

(@quiz_practice,
 'What two roles do the Q400 wing spoilers serve?',
 'mcq',
 JSON_ARRAY(
   'Roll only — they have no ground role',
   'Roll in flight + ground mode (lift dump on landing)',
   'Speed brake only — they are not used for roll',
   'Yaw augmentation + ground mode'
 ),
 JSON_OBJECT('correct_index', 1),
 'Spoilers work differentially with the ailerons for roll in flight, and have a ground mode that extends them on landing to dump lift.',
 'medium', 100),

(@quiz_practice,
 'Where do the Q400 air-conditioning packs and APU live?',
 'mcq',
 JSON_ARRAY(
   'Forward unpressurised equipment deck (under the cockpit)',
   'In the wing root, between the engine nacelles',
   'Aft fuselage section, behind the aft pressure bulkhead',
   'Tailcone above the elevators'
 ),
 JSON_OBJECT('correct_index', 2),
 'Both packs and the APU live in the aft fuselage. Because that section is unpressurised, a leak or fire there vents overboard rather than into the cabin.',
 'medium', 110),

(@quiz_practice,
 'You smell electrical smoke that persists after both packs are turned off. The most likely physical source is:',
 'mcq',
 JSON_ARRAY(
   'Forward baggage compartment',
   'Galley ovens',
   'Aft fuselage — APU or pack equipment',
   'A passenger laptop'
 ),
 JSON_OBJECT('correct_index', 2),
 'Persistent electrical smoke after isolating the packs strongly implies an aft-fuselage source. The crew cannot access the aft section in flight; the response is procedural — masks 100%, run the QRH, isolate, divert.',
 'hard', 120),

(@quiz_practice,
 'Which of the following IS an emergency exit on the SAS Q400?',
 'mcq',
 JSON_ARRAY(
   'Either baggage door',
   'The forward baggage door only',
   'The aft baggage door only',
   'The Type II/III exit and the cockpit overhead exit'
 ),
 JSON_OBJECT('correct_index', 3),
 'Baggage doors open from outside only and are NOT evacuation routes. The pax door, the Type II/III exit, and the cockpit overhead exit are the routes off the aeroplane.',
 'medium', 130),

(@quiz_practice,
 'You are rolling at 60 kt for takeoff and the MFD shows DOOR — BAGGAGE caution. What is the correct action?',
 'mcq',
 JSON_ARRAY(
   'Continue the takeoff — baggage doors only open from outside',
   'Reject. Below 100 kt any door caution is a stop. Hold position; engineering inspection before any further movement',
   'Continue if no second caution appears within 5 seconds',
   'Continue but reduce takeoff thrust by 10%'
 ),
 JSON_OBJECT('correct_index', 1),
 'A door caution below 100 kt is always a reject. The cockpit cannot confirm whether the door is latched. Cost of a 60-kt reject is low; cost of an open baggage door at rotation is high.',
 'hard', 140),

(@quiz_practice,
 'The Q400 wing is best described as:',
 'mcq',
 JSON_ARRAY(
   'Mid-wing, swept, integral fuel tanks',
   'High-aspect-ratio cantilevered, joined to the upper midsection of the fuselage',
   'Low-wing, with external fuel tanks under the wingtips',
   'High wing with bracing struts to the fuselage'
 ),
 JSON_OBJECT('correct_index', 1),
 'Single, high-aspect-ratio cantilevered wing joined to the upper midsection of the fuselage. No bracing struts.',
 'easy', 150),

(@quiz_practice,
 'What composite material is used for the Q400 nose equipment bay?',
 'mcq',
 JSON_ARRAY('Fiberglass','Aramid fiber','Carbon fiber','Kevlar with titanium honeycomb'),
 JSON_OBJECT('correct_index', 1),
 'The nose equipment bay uses aramid fiber composite. The radome uses fiberglass with a honeycomb core; the bullet fairing uses a hybrid glass / aramid composite.',
 'hard', 160),

(@quiz_practice,
 'Where is the Landing Gear Emergency Extension Hand Pump Handle located?',
 'mcq',
 JSON_ARRAY(
   'Front of the overhead console',
   'Centre console near the flap lever',
   'Aft flight deck',
   'Forward equipment bay'
 ),
 JSON_OBJECT('correct_index', 2),
 'The hand pump handle lives on the aft flight deck. The Alternate Release Door and Alternate Extend Door — separate items — are at the front of the overhead area.',
 'medium', 170),

(@quiz_practice,
 'The Q400 ANVS system stands for:',
 'mcq',
 JSON_ARRAY(
   'Automatic Navigation and Vibration Suppression',
   'Active Noise and Vibration Suppression',
   'Anti-Noise Vibration Sealant',
   'Automatic Nacelle Vibration Sensor'
 ),
 JSON_OBJECT('correct_index', 1),
 'ANVS = Active Noise and Vibration Suppression. NVS INOP on the F/A panel indicates the system has failed; the cabin will be louder but no flight-safety effect.',
 'easy', 180),

(@quiz_practice,
 'TRUE or FALSE — The Q400 baggage doors can be opened from inside the cabin in case of evacuation.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Both baggage doors open from outside ONLY. They are not evacuation routes.',
 'easy', 190),

(@quiz_practice,
 'TRUE or FALSE — The aft fuselage section is pressurised and the APU compartment is sealed at cabin pressure during flight.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. The aft section is deliberately unpressurised. APU and pack compartments operate at ambient pressure so leaks vent overboard.',
 'medium', 200),

(@quiz_practice,
 'TRUE or FALSE — On the Q400 both elevators are mechanically tied together and cannot be operated independently.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Both elevators normally operate together but can be split via the pitch-disconnect system if a jam appears.',
 'medium', 210),

(@quiz_practice,
 'TRUE or FALSE — The Q400 trailing rudder is rigidly fixed to the fore rudder and they always deflect equally.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. The trailing rudder is hinged to the fore rudder and is geared so that it deflects at twice the angle of the fore rudder.',
 'medium', 220),

(@quiz_practice,
 'TRUE or FALSE — Q400 windshield panels are made of polycarbonate plastic.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. The windshield panels are laminated glass. Side window panels are a combination of laminated glass and plastic.',
 'medium', 230),

(@quiz_practice,
 'TRUE or FALSE — The Q400 spoilers extend on landing in ground mode to dump lift.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 0),
 'TRUE. Spoilers have a roll role in flight (working with ailerons) and a ground mode that extends them on landing.',
 'easy', 240),

(@quiz_practice,
 'TRUE or FALSE — The Q400 forward and aft pressure bulkheads define the pressurised cabin volume.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 0),
 'TRUE. The pressurised volume is bounded by the forward pressure bulkhead (just behind the nose) and the aft pressure bulkhead (just forward of the aft fuselage / APU compartment).',
 'easy', 250);

-- ----------------------------------------------------------------------------
-- Quiz 2 — Type Rating Mock
-- ----------------------------------------------------------------------------
INSERT INTO quizzes
    (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES
    (@system_id,
     'Aeroplane General — Type Rating Mock',
     'Ten-question mock at type-rating oral standard. Twelve-minute timer, pass score 80%. Designed to expose holes BEFORE the check ride.',
     'exam',
     12,
     80,
     1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions
    (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order)
VALUES
(@quiz_exam,
 'Quote the per-engine SHP and propeller-blade count for the Q400.',
 'mcq',
 JSON_ARRAY(
   '5000 SHP per engine, 4-blade propellers',
   '5071 SHP per engine, 6-blade propellers',
   '5100 SHP per engine, 6-blade propellers',
   '4900 SHP per engine, 5-blade propellers'
 ),
 JSON_OBJECT('correct_index', 1),
 'Memorise the exact figure: 5071 SHP per PW150A. Six-blade propellers.',
 'medium', 10),

(@quiz_exam,
 'List the three fuselage sections in order, and identify which is unpressurised.',
 'mcq',
 JSON_ARRAY(
   'Forward, Center, Aft — Forward unpressurised',
   'Forward, Center, Aft — Aft unpressurised',
   'Nose, Center, Tail — Nose unpressurised',
   'Forward, Mid, Aft — none unpressurised'
 ),
 JSON_OBJECT('correct_index', 1),
 'Forward, Center, Aft. Aft is unpressurised — APU and packs live there.',
 'medium', 20),

(@quiz_exam,
 'On a takeoff roll at 60 kt, MFD shows DOOR — BAGGAGE caution. Captain''s correct action?',
 'mcq',
 JSON_ARRAY(
   'Continue — baggage doors are outside-only, the door cannot have opened',
   'Reject below 100 kt; hold position; do not move until ground inspection confirms door state',
   'Continue if a second caution does not appear in 5 seconds',
   'Reject only above 80 kt'
 ),
 JSON_OBJECT('correct_index', 1),
 'Below 100 kt, ANY door caution is a reject. The cockpit cannot confirm latch state.',
 'hard', 30),

(@quiz_exam,
 'Describe the Q400 rudder configuration, including the trailing-to-fore deflection ratio.',
 'mcq',
 JSON_ARRAY(
   'Single rudder, single hydraulic actuator',
   'Fore + trailing rudders. Trailing deflects 2x the fore. Two hydraulic actuators',
   'Fore + trailing rudders. Trailing deflects 0.5x the fore. One actuator',
   'Single rudder, dual hydraulic actuators'
 ),
 JSON_OBJECT('correct_index', 1),
 'Fore rudder + trailing rudder. Trailing rudder is geometrically arranged to deflect at twice the angle of the fore rudder. Two hydraulic actuators operate the pair.',
 'hard', 40),

(@quiz_exam,
 'How are the elevators normally operated, and what allows independent operation in case of jam?',
 'mcq',
 JSON_ARRAY(
   'Mechanically linked; cannot be separated',
   'Hydraulically operated with artificial feel. Both operate together; pitch-disconnect system splits them on jam',
   'Electrically operated; split by autopilot disconnect',
   'Cable-driven; split by mechanical breakout'
 ),
 JSON_OBJECT('correct_index', 1),
 'Hydraulic with artificial feel. Pitch-disconnect splits the system if one elevator jams.',
 'medium', 50),

(@quiz_exam,
 'Where is the Landing Gear Emergency Extension Hand Pump located?',
 'mcq',
 JSON_ARRAY(
   'Forward overhead console',
   'Centre pedestal',
   'Aft flight deck',
   'Cabin forward galley'
 ),
 JSON_OBJECT('correct_index', 2),
 'Aft flight deck. The Alternate Release Door and Alternate Extend Door — separate items — sit at the front of the overhead area.',
 'medium', 60),

(@quiz_exam,
 'What is the maximum operating altitude of the SAS-variant Q400?',
 'mcq',
 JSON_ARRAY('FL220','FL250','FL270','FL310'),
 JSON_OBJECT('correct_index', 1),
 'FL250 (25,000 ft). Do not confuse with other Dash 8 variants.',
 'easy', 70),

(@quiz_exam,
 'TRUE or FALSE — Both Q400 baggage doors can be opened from inside the cabin during an evacuation.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. Outside-only. They are not evacuation routes.',
 'easy', 80),

(@quiz_exam,
 'List one composite material and the location it is used in on the Q400.',
 'mcq',
 JSON_ARRAY(
   'Aramid in the radome',
   'Fiberglass in the nose equipment bay',
   'Aramid fiber in the nose equipment bay',
   'Carbon fiber in the wing-to-fuselage fairings'
 ),
 JSON_OBJECT('correct_index', 2),
 'Aramid fiber is used in the nose equipment bay. Radome is fiberglass / honeycomb. Wing-fuselage fairings are fiberglass.',
 'hard', 90),

(@quiz_exam,
 'Why is the aft fuselage section unpressurised — name two operational reasons.',
 'mcq',
 JSON_ARRAY(
   'Saves weight and noise — the section does not need to carry pressure-load',
   'Lets pack-failure or APU-fire vent overboard rather than into the cabin, AND avoids structural pressure-load on the swept-up tail-cone supporting empennage',
   'It is pressurised — this premise is wrong',
   'Required for static-pressure ports on the empennage'
 ),
 JSON_OBJECT('correct_index', 1),
 'Two reasons: smoke / fire / pack-leak isolation, and structural simplification of the swept tail-cone region. Both are real engineering rationales for the design.',
 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id)        AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN
        (SELECT id FROM quizzes WHERE system_id = @system_id))         AS questions_inserted;
