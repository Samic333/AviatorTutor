-- =============================================================================
-- AviatorTutor — Phase 12: ATA 33 Lighting — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'lighting' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Lighting — Practice', 'Twenty-five-question practice quiz on Q400 lighting: cockpit, cabin, exterior, emergency. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'What three categories does Q400 lighting split into?',
 'mcq', JSON_ARRAY('Inside, outside, emergency','Interior, exterior, emergency','Cockpit, cabin, exterior','LED, fluorescent, incandescent'),
 JSON_OBJECT('correct_index', 1),
 'Interior / Exterior / Emergency.',
 'easy', 10),
(@quiz_practice, 'What voltage and dimming method is used for cockpit panel/instrument lighting?',
 'mcq', JSON_ARRAY('28 VDC fixed','115 VAC variable','5 VDC variable, disc-shaped lamps in Plexiglas','12 VDC LED'),
 JSON_OBJECT('correct_index', 2),
 'Variable 5 VDC, disc-shaped lamps embedded in Plexiglas.',
 'medium', 20),
(@quiz_practice, 'Which bus powers the cockpit DOME light?',
 'mcq', JSON_ARRAY('L Secondary','BATTERY PWR (operates without BATTERY MASTER)','Right Main DC','Standby ESS bus'),
 JSON_OBJECT('correct_index', 1),
 'BATTERY PWR — works without BATTERY MASTER. Mnemonic: DOME-BATTERY-NO-MASTER.',
 'hard', 30),
(@quiz_practice, 'What is the purpose of the STORM lights, and which bus powers them?',
 'mcq', JSON_ARRAY('Cruise illumination; Right ESS bus','Night-vision recovery after lightning flash; L SECONDARY bus','Approach lighting; Left Main bus','Galley illumination; Battery bus'),
 JSON_OBJECT('correct_index', 1),
 'Night-vision recovery after lightning. L SECONDARY bus.',
 'hard', 40),
(@quiz_practice, 'What does the PILOTS FLT PNL knob control?',
 'mcq', JSON_ARRAY('Pilot side console + ICP1 + Standby Instruments','Both PFDs','Cabin overhead lighting','Anti-collision lights'),
 JSON_OBJECT('correct_index', 0),
 'Pilot side + ICP1 + Standby Instruments.',
 'medium', 50),
(@quiz_practice, 'What does the COPILOTS FLT PNL knob control?',
 'mcq', JSON_ARRAY(
   'Copilot side console + ICP2 + LANDING GEAR selector panel + GPWS/Hyd panel',
   'Just the copilot side console',
   'Both EFIS panels',
   'Master Warning + Caution lights'
 ),
 JSON_OBJECT('correct_index', 0),
 'Copilot side + ICP2 + LDG GEAR selector panel + GPWS/Hyd panel.',
 'hard', 60),
(@quiz_practice, 'How many fluorescent overhead and sidewall fixtures does the cabin have?',
 'mcq', JSON_ARRAY('15 + 15','21 + 21','30 + 30','21 overhead, 30 sidewall'),
 JSON_OBJECT('correct_index', 1),
 '21 overhead + 21 sidewall. Mnemonic: 21-OVER-21-SIDE.',
 'medium', 70),
(@quiz_practice, 'How many reading lights does each Passenger Service Unit (PSU) contain?',
 'mcq', JSON_ARRAY('1','2','3','4'),
 JSON_OBJECT('correct_index', 1),
 '2 reading lights per PSU, gated by PSU ON/OFF.',
 'medium', 80),
(@quiz_practice, 'What auto-logic causes the NO SMOKING signs to illuminate?',
 'mcq', JSON_ARRAY(
   'Cockpit switch only',
   'Auto-on when landing gear selector to DN',
   'Auto-on with FASTEN BELTS',
   'Auto-on at 10000 ft AGL'
 ),
 JSON_OBJECT('correct_index', 1),
 'NO SMOKING auto-on with gear selector DN. Mnemonic: NO-SMOKE-GEAR-DN.',
 'hard', 90),
(@quiz_practice, 'When the FASTEN BELTS switch is on, what other indications occur?',
 'mcq', JSON_ARRAY(
   'Only the front of cabin signs',
   'Front + each PSU signs + low chime through PA + lavatory RETURN TO SEAT illuminates',
   'Cabin lights dim automatically',
   'NO SMOKING also illuminates'
 ),
 JSON_OBJECT('correct_index', 1),
 'Signs at front + PSU + chime + lavatory RETURN TO SEAT.',
 'medium', 100),
(@quiz_practice, 'How is lavatory occupancy detected and indicated?',
 'mcq', JSON_ARRAY(
   'Smoke detector triggers OCCUPIED',
   'LAVATORY LTS membrane arms; lavatory latch in OCCUPIED activates fluorescent + OCCUPIED indicator at F/A seat',
   'Pressure sensor on the door',
   'OCCUPIED is automatic, no membrane required'
 ),
 JSON_OBJECT('correct_index', 1),
 'LAVATORY LTS arms; latch OCCUPIED activates. F/A indicator illuminates.',
 'hard', 110),
(@quiz_practice, 'How are the wing landing lights grouped?',
 'mcq', JSON_ARRAY(
   '4 lights total, all approach',
   '2 per wing: outboard = APPROACH, inboard = FLARE (angled DOWN)',
   '4 per wing for redundancy',
   '1 per wing'
 ),
 JSON_OBJECT('correct_index', 1),
 '2 per wing: outboard approach, inboard flare. Total 4. Mnemonic: OUT-APPROACH-IN-FLARE.',
 'medium', 120),
(@quiz_practice, 'Where is the taxi light located, and what is the inhibit condition?',
 'mcq', JSON_ARRAY(
   'On the wing leading edge; no inhibit',
   'On the steerable section of the nose gear; inhibited unless gear is LOCKED DOWN',
   'On the fuselage; inhibited above 10 kts',
   'On the engine nacelle; inhibited in flight'
 ),
 JSON_OBJECT('correct_index', 1),
 'Taxi light on steerable nose gear. Inhibited unless gear locked down. Mnemonic: TAXI-LOCK-DOWN.',
 'medium', 130),
(@quiz_practice, 'What colours are the position lights, and where are they located?',
 'mcq', JSON_ARRAY(
   'Green left, red right, white aft',
   'Green right wingtip, red left wingtip, white aft of vertical stab bullet fairing',
   'Both white wingtips, red on tail',
   'Red right, green left, white forward'
 ),
 JSON_OBJECT('correct_index', 1),
 'Standard: green right, red left, white aft. Mnemonic: GREEN-RIGHT-RED-LEFT-WHITE-AFT.',
 'easy', 140),
(@quiz_practice, 'What is the position-light primary/secondary failover logic?',
 'mcq', JSON_ARRAY(
   'Both stay on continuously',
   'POSN ON: both illuminate; ~1 sec later secondaries go off but ARMED; if primary fails, secondary auto-on',
   'Secondary illuminates only on the ground',
   'Secondary lights flash; primary stay solid'
 ),
 JSON_OBJECT('correct_index', 1),
 'Both on then secondary off after ~1 sec but armed. Primary fail → secondary auto-on. Mnemonic: PRI-SEC-1-SEC-ARM.',
 'hard', 150),
(@quiz_practice, 'What does the EMER LIGHTS switch do at ARM?',
 'mcq', JSON_ARRAY(
   'Lights stay off; only manual ON works',
   'Auto-illuminate emergency egress chain on AC power loss; battery packs power',
   'Disables emergency lights',
   'Same as OFF'
 ),
 JSON_OBJECT('correct_index', 1),
 'ARM = auto-illuminate on AC loss. Battery packs power. ARM is normal flight setting. Mnemonic: EMER-3-POS-ARM.',
 'hard', 160),
(@quiz_practice, 'How many EMER LIGHTS switch positions are there?',
 'mcq', JSON_ARRAY('2 (ON/OFF)','3 (ON/ARM/OFF), lever-locked','4 with TEST','5 with separate egress + signs'),
 JSON_OBJECT('correct_index', 1),
 '3-position lever-locked.',
 'medium', 170),
(@quiz_practice, 'How many baggage compartment dome lights are installed?',
 'mcq', JSON_ARRAY(
   '1 forward + 1 aft',
   '1 forward + 2 aft',
   '2 forward + 2 aft',
   '3 forward + 3 aft'
 ),
 JSON_OBJECT('correct_index', 1),
 'Forward 1 dome / aft 2 dome. Auto-on with door unlocked. Mnemonic: 1-FWD-2-AFT-BAG.',
 'medium', 180),
(@quiz_practice, 'How many forward passenger door step lights, and on which bus?',
 'mcq', JSON_ARRAY(
   '2 lights on Battery bus',
   '4 step lights on Left Main bus',
   '6 lights on Battery bus',
   '4 on Right Main bus'
 ),
 JSON_OBJECT('correct_index', 1),
 '4 step lights on left main bus. Plus 2 boarding lights on Battery bus.',
 'hard', 190),
(@quiz_practice, 'TRUE or FALSE — A single primary position light failure illuminates a cockpit caution.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Failover is silent. Secondary auto-illuminates. Caution does not appear. Mnemonic: PRI-SEC-1-SEC-ARM.',
 'medium', 200),
(@quiz_practice, 'What does the W/S WIPER ICE DETECT pushbutton do?',
 'mcq', JSON_ARRAY(
   'Activates the windshield wipers',
   'Illuminates a light above the glareshield that shines on the wiper spigot for ice check',
   'Tests the ice detection probes',
   'Inhibits the wiper motor'
 ),
 JSON_OBJECT('correct_index', 1),
 'Illuminates the wiper-spigot ice-detect light.',
 'medium', 210),
(@quiz_practice, 'TRUE or FALSE — Storm lights operate without AC power.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. Storm lights on L SECONDARY bus require AC. Dome on BATTERY PWR works without AC.',
 'hard', 220),
(@quiz_practice, 'What is the role of the single dimmer control on the C/A panel?',
 'mcq', JSON_ARRAY(
   'Dim only overhead lights',
   'Dim ALL main cabin lights together',
   'Dim only the lavatory',
   'Dim sidewall only'
 ),
 JSON_OBJECT('correct_index', 1),
 'Single dimmer dims all main cabin lights together — useful for night transitions.',
 'medium', 230),
(@quiz_practice, 'How many utility lights are on the flight deck?',
 'mcq', JSON_ARRAY(
   '1 — only pilot side',
   '2 swivel-ball (one each pilot) plus 1 observer = 3 total',
   '4 evenly spaced on the ceiling',
   'No utility lights — only map lights'
 ),
 JSON_OBJECT('correct_index', 1),
 '2 pilot ceiling utility + 1 observer utility = 3 total.',
 'hard', 240),
(@quiz_practice, 'The recognition light is what colour and where?',
 'mcq', JSON_ARRAY('White on bullet fairing','Red on top fuselage centreline forward of wings','Green on left wing','Blue on tail'),
 JSON_OBJECT('correct_index', 1),
 'Red recognition light on top fuselage centreline, forward of wings.',
 'hard', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Lighting — Type Rating Mock', 'Ten-question mock at type-rating oral standard. Twelve-minute timer.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'Quote landing-light grouping.',
 'mcq', JSON_ARRAY('All approach','Outboard approach + inboard flare (angled down) — 2 per wing','Inboard approach + outboard flare','1 per wing'),
 JSON_OBJECT('correct_index', 1),
 'OUT-APPROACH-IN-FLARE.',
 'medium', 10),
(@quiz_exam, 'Position-light failover logic.',
 'mcq', JSON_ARRAY('No failover','Both on then secondary off after ~1 sec but ARMED; primary fail → secondary auto-on','Secondary always on','No primary lights'),
 JSON_OBJECT('correct_index', 1),
 'PRI-SEC-1-SEC-ARM.',
 'hard', 20),
(@quiz_exam, 'Taxi light inhibit condition.',
 'mcq', JSON_ARRAY('Above 30 kts only','Inhibited unless gear is LOCKED DOWN','In flight','Battery only'),
 JSON_OBJECT('correct_index', 1),
 'TAXI-LOCK-DOWN.',
 'medium', 30),
(@quiz_exam, 'NO SMOKING auto-logic.',
 'mcq', JSON_ARRAY('No auto-logic','Auto-on when gear selector to DN','Auto-on at 10000 AGL','Auto-on with FASTEN BELTS'),
 JSON_OBJECT('correct_index', 1),
 'NO-SMOKE-GEAR-DN.',
 'medium', 40),
(@quiz_exam, 'EMER LIGHTS at ARM behaviour.',
 'mcq', JSON_ARRAY('Stays off','Auto-illuminate egress chain on AC loss; battery packs power','Same as OFF','Manual only'),
 JSON_OBJECT('correct_index', 1),
 'EMER-3-POS-ARM.',
 'hard', 50),
(@quiz_exam, 'Cabin fluorescent count.',
 'mcq', JSON_ARRAY('21+21','15+15','30+30','21 overhead + 30 sidewall'),
 JSON_OBJECT('correct_index', 0),
 '21-OVER-21-SIDE.',
 'medium', 60),
(@quiz_exam, 'Dome light bus + special property.',
 'mcq', JSON_ARRAY('L SEC; BATTERY MASTER req','BATTERY PWR; works without BATTERY MASTER','Right Main; engines req','Standby ESS only'),
 JSON_OBJECT('correct_index', 1),
 'DOME-BATTERY-NO-MASTER.',
 'hard', 70),
(@quiz_exam, 'Position-light colours by location.',
 'mcq', JSON_ARRAY('GREEN LEFT/RED RIGHT/WHITE AFT','GREEN RIGHT/RED LEFT/WHITE AFT of bullet','BLUE/WHITE/RED','RED/WHITE/RED'),
 JSON_OBJECT('correct_index', 1),
 'GREEN-RIGHT-RED-LEFT-WHITE-AFT.',
 'easy', 80),
(@quiz_exam, 'AC power lost in flight with EMER LIGHTS at ARM. Cabin emergency lighting?',
 'mcq', JSON_ARRAY('Stays off — manual ON needed','Auto-illuminates from battery packs','Only egress lights','Only ceiling lights'),
 JSON_OBJECT('correct_index', 1),
 'Auto-on. Whole egress chain (ceiling, floor, locator, exit, egress per door).',
 'hard', 90),
(@quiz_exam, 'Lavatory fluorescent activation logic.',
 'mcq', JSON_ARRAY(
   'Always on with cabin overhead',
   'LAVATORY LTS membrane arms + latch OCCUPIED activates + F/A indicator',
   'Door switch only',
   'PSU pushbutton'
 ),
 JSON_OBJECT('correct_index', 1),
 'LAV-OCCUPIED-LATCH.',
 'hard', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
