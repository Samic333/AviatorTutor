-- =============================================================================
-- AviatorTutor — Phase 1: ATA 21 Aeroplane General
-- 36 flashcards covering numbers, fuselage architecture, surfaces, materials,
-- cockpit equipment locations, doors, and the Q400 quirks.
--
-- Idempotent: re-running wipes prior cards for this system and re-inserts.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM flashcards WHERE system_id = @system_id;

INSERT INTO flashcards
    (system_id, front, back, hint, difficulty, tags)
VALUES
-- ----- Numbers (8) -----
(@system_id,
 'How many shaft horsepower does each Q400 PW150A engine produce?',
 '5071 SHP per engine.',
 'Mnemonic: 5-0-7-1.',
 'easy',
 JSON_ARRAY('aeroplane-general','engines','numbers')),

(@system_id,
 'How many propeller blades does the Q400 have on each engine?',
 'Six blades per propeller.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','propeller','numbers')),

(@system_id,
 'What is the maximum operating altitude for a Q400 in SAS-variant configuration?',
 'FL250 (25,000 ft).',
 'NOT FL270 — that is some other Dash 8 series.',
 'easy',
 JSON_ARRAY('aeroplane-general','limits','numbers')),

(@system_id,
 'What is the wing span of the Q400?',
 '28.42 m (93 ft 3 in).',
 'Mnemonic: 28-by-33.',
 'easy',
 JSON_ARRAY('aeroplane-general','dimensions','numbers')),

(@system_id,
 'What is the overall length of the Q400?',
 '32.83 m (107 ft 9 in).',
 'Mnemonic: 28-by-33.',
 'easy',
 JSON_ARRAY('aeroplane-general','dimensions','numbers')),

(@system_id,
 'What is the diameter of the Q400 propeller?',
 '4.12 m (13 ft 6 in). Clears the fuselage by about 1.1 m.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','propeller','dimensions')),

(@system_id,
 'What is the minimum pavement width for a 180° turn at 70° nose-wheel steering?',
 '25.7 m (84 ft 5 in) — without backing up.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','dimensions','operations')),

(@system_id,
 'What is the High Gross Mass MTOW for the Q400 SAS variant?',
 '29,257 kg (High Gross Mass). Basic = 27,987 kg, Intermediate = 28,998 kg.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','limits','mass')),

-- ----- Fuselage architecture (5) -----
(@system_id,
 'Name the three main sections of the Q400 fuselage.',
 'Forward, Center, Aft.',
 'Mnemonic: F-C-A.',
 'easy',
 JSON_ARRAY('aeroplane-general','fuselage','architecture')),

(@system_id,
 'Which fuselage section is unpressurised, and what major components live there?',
 'The Aft section is unpressurised. It houses both air-conditioning packs and the APU.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','fuselage','pressurisation')),

(@system_id,
 'Where is the forward baggage compartment?',
 'On the right forward part of the fuselage, in the Forward section.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','baggage','fuselage')),

(@system_id,
 'Where is the aft baggage compartment?',
 'Forward of the aft pressure bulkhead, in the Center section (still pressurised).',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','baggage','fuselage')),

(@system_id,
 'What is in the Forward section in front of the forward pressure bulkhead?',
 'The nose: nose-wheel well, an unpressurised equipment deck, and the weather radar radome.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','nose','fuselage')),

-- ----- Doors (4) -----
(@system_id,
 'Can the Q400 baggage doors be opened from inside or outside?',
 'OUTSIDE ONLY. Both baggage doors open outwards and can only be opened from outside.',
 'Mnemonic: BAGS-OUT-ONLY.',
 'easy',
 JSON_ARRAY('aeroplane-general','doors','security')),

(@system_id,
 'Can the passenger compartment door and the Type II/III exit be opened from inside, outside, or both?',
 'BOTH sides. Passenger door and the Type II/III exit can be opened from either inside or outside.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','doors','emergency')),

(@system_id,
 'You see a "DOOR — BAGGAGE" caution at 60 kt on takeoff roll. Action?',
 'REJECT below 100 kt for any door caution. Hold position. Do not move until physically inspected by an engineer.',
 NULL,
 'hard',
 JSON_ARRAY('aeroplane-general','doors','reject','captain-decisions')),

(@system_id,
 'Are the baggage doors a viable evacuation route?',
 'NO. Baggage doors open from outside only and are NOT evacuation exits. Use pax door, Type II/III exit, or cockpit overhead exit.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','doors','emergency','evacuation')),

-- ----- Empennage (5) -----
(@system_id,
 'Describe the Q400 rudder configuration.',
 'A fore rudder hinged to the rear vertical-stabiliser spar AND a trailing rudder hinged to the trailing edge of the fore rudder.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','empennage','rudder')),

(@system_id,
 'What is the geometric ratio between the trailing rudder and the fore rudder deflection?',
 'The trailing rudder deflects at TWICE the angle of the fore rudder.',
 'Mnemonic: FORE+TRAIL = TWO-FOR-ONE.',
 'medium',
 JSON_ARRAY('aeroplane-general','empennage','rudder','ratios')),

(@system_id,
 'How many hydraulic actuators operate the Q400 rudder?',
 'Two hydraulic actuators operate the rudder pair.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','empennage','rudder','hydraulics')),

(@system_id,
 'How are the Q400 elevators normally operated, and what allows them to be split?',
 'Both elevators normally operate together. The pitch-disconnect system allows them to be split if a jam is detected.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','empennage','elevator')),

(@system_id,
 'How are the Q400 elevators powered, and how is trim achieved?',
 'Hydraulically operated with artificial feel. Hydraulic actuators are used for elevator trim.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','empennage','elevator','hydraulics')),

-- ----- Wing (4) -----
(@system_id,
 'Describe the Q400 wing layout.',
 'Single, high-aspect-ratio, cantilevered wing joined to the upper midsection of the fuselage.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','wing','architecture')),

(@system_id,
 'What is the Q400 wing dihedral?',
 '2.5° outboard of the engine nacelles.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','wing','dimensions')),

(@system_id,
 'What does the Q400 wing contain?',
 'Integral fuel tanks, engine nacelles, main gear mounting structures, ailerons, flaps, and spoilers.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','wing','components')),

(@system_id,
 'What two roles do the Q400 wing spoilers serve?',
 'Roll mode in flight (working differentially with the ailerons) and ground mode on landing (extending to dump lift).',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','wing','spoilers')),

-- ----- Materials (4) -----
(@system_id,
 'What is the Q400 primary airframe structure made of?',
 'High-strength aluminium alloys.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','materials','structure')),

(@system_id,
 'Where is steel used in the Q400 airframe?',
 'Landing gear and certain airframe components.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','materials','structure')),

(@system_id,
 'What composite is used for the Q400 nose equipment bay?',
 'Aramid fiber.',
 NULL,
 'hard',
 JSON_ARRAY('aeroplane-general','materials','composites')),

(@system_id,
 'What composite is used for the Q400 wing-to-fuselage fairings?',
 'Fiberglass.',
 NULL,
 'hard',
 JSON_ARRAY('aeroplane-general','materials','composites')),

-- ----- Cockpit equipment locations (5) -----
(@system_id,
 'Where is the Landing Gear Emergency Extension Hand Pump Handle located?',
 'On the aft flight deck.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','cockpit','emergency-equipment','landing-gear')),

(@system_id,
 'Where are the Landing Gear Alternate Release Door and Alternate Extend Door located?',
 'Both at the front of the overhead area in the flight deck.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','cockpit','emergency-equipment','landing-gear')),

(@system_id,
 'Name the seven aft-flight-deck items every crew should locate without looking.',
 'Hand pump handle, fire axe, two fire extinguishers, flashlights, Protective Breathing Equipment (PBE), Weight & Balance Manual, observer''s seat.',
 NULL,
 'hard',
 JSON_ARRAY('aeroplane-general','cockpit','emergency-equipment')),

(@system_id,
 'Where are the smoke goggles stowed in the Q400 flight deck?',
 'At both pilot and copilot seats — listed as items 22 in the flight-deck equipment layout.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','cockpit','emergency-equipment')),

(@system_id,
 'What does the Standby Compass back up, and where is it located?',
 'Backs up primary heading on AHRS power loss; located on the glareshield centre area.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','cockpit','indications')),

-- ----- Q400 quirks (5) -----
(@system_id,
 'What is ANVS on the Q400?',
 'Active Noise and Vibration Suppression — a system that reduces cabin noise and vibration. Failure shows on the F/A panel as NVS INOP.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','anvs','cabin')),

(@system_id,
 'Where is the towing switch power source on the Q400, and what is its CB rating?',
 'Power is taken directly from the main battery (behind DC CONTROL panel) via a 7.5 A circuit breaker, fed to a point behind the EXTERIOR LIGHTS panel.',
 NULL,
 'hard',
 JSON_ARRAY('aeroplane-general','towing','electrical')),

(@system_id,
 'What is the towing switch position discipline?',
 'Standard 2-position guarded switch. Towing personnel flip the guard to TOWING during towing, then return to NORMAL with guard closed.',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','towing','operations')),

(@system_id,
 'What is the Q400 minimum operating crew?',
 'Two pilots. Plus optional flight observer position. SAS variant adds two cabin crew.',
 NULL,
 'easy',
 JSON_ARRAY('aeroplane-general','crew','limits')),

(@system_id,
 'What controls live on the centre console (Figure 12.1-11)?',
 'Emergency Brake Lever, Control Lock Lever, Flap Selector Lever, Elevator Trim Indicator, two Power Levers (#1, #2), two Condition Levers (#1, #2).',
 NULL,
 'medium',
 JSON_ARRAY('aeroplane-general','cockpit','controls'));

SELECT COUNT(*) AS flashcards_inserted FROM flashcards WHERE system_id = @system_id;
