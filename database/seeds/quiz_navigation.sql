-- =============================================================================
-- AviatorTutor — Phase 13: ATA 34 Navigation — quizzes.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'navigation' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Navigation — Practice', 'Twenty-five-question practice quiz on Q400 navigation systems. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many VHF NAV receivers does the Q400 have?',
 'mcq', JSON_ARRAY('1','2','3','4'),
 JSON_OBJECT('correct_index', 1),
 '2 VHF NAV receivers, each handling VOR + LOC + GS + DME + Marker.',
 'easy', 10),
(@quiz_practice, 'What is the VOR frequency range and channel spacing?',
 'mcq', JSON_ARRAY(
   '108.00–117.95 MHz, 25 kHz spacing',
   '108.00–117.95 MHz, 50 kHz EVEN spacing',
   '108.10–111.95 MHz, 50 kHz odd spacing',
   '118.00–137.00 MHz, 8.33 kHz spacing'
 ), JSON_OBJECT('correct_index', 1),
 'VOR 108.00–117.95 MHz, 50 kHz EVEN. LOC 108.10–111.95, 50 kHz ODD. Mnemonic: VOR-EVEN-LOC-ODD.',
 'hard', 20),
(@quiz_practice, 'What is the Localizer frequency range?',
 'mcq', JSON_ARRAY('108.00–117.95 even','108.10–111.95 odd, 50 kHz','118–137 every 25 kHz','108.10–112.00 even'),
 JSON_OBJECT('correct_index', 1),
 'LOC 108.10–111.95 MHz, 50 kHz ODD. GS auto-paired.',
 'medium', 30),
(@quiz_practice, 'What colours and labels are used on the EFIS marker beacon indicators?',
 'mcq', JSON_ARRAY(
   'OUT red, MID green, INN blue',
   'OUT blue, MID amber, INN white',
   'All white',
   'OUT amber, MID white, INN blue'
 ),
 JSON_OBJECT('correct_index', 1),
 'OUT BLUE / MID AMBER / INN WHITE. Mnemonic: OUT-BLUE-MID-AMBER-INN-WHITE.',
 'easy', 40),
(@quiz_practice, 'How is marker beacon sensitivity selected?',
 'mcq', JSON_ARRAY('Auto only','HI or LO from ARCDU on centre console','Always HI','Cockpit panel switch'),
 JSON_OBJECT('correct_index', 1),
 'HI or LO from ARCDU.',
 'medium', 50),
(@quiz_practice, 'How are DME signals distributed between pilot and copilot EFIS?',
 'mcq', JSON_ARRAY(
   'Both sides use DME1 only',
   'Pilot EFIS uses DME1; Copilot EFIS uses DME2; loss → both sides auto-use remaining',
   'Pilot uses DME1+DME2; copilot uses standby only',
   'No cross-side feed'
 ),
 JSON_OBJECT('correct_index', 1),
 'DME-CROSS-USE.',
 'hard', 60),
(@quiz_practice, 'How many ADF receivers on the Q400, and what are the four modes?',
 'mcq', JSON_ARRAY(
   '1 with ADF/ANT/TEST',
   '2 with ADF/ANT/BFO/TEST',
   '2 with ADF/AUX/TEST/IDENT',
   '4 with various modes'
 ),
 JSON_OBJECT('correct_index', 1),
 '2 ADF; modes ADF/ANT/BFO/TEST. Mnemonic: 4-MODE-ADF.',
 'medium', 70),
(@quiz_practice, 'In what conditions does the ADF bearing pointer park at 90° relative bearing?',
 'mcq', JSON_ARRAY(
   'Always',
   'In ANT mode, TEST mode, or invalid signal/frequency',
   'Only in ADF mode with valid signal',
   'On the ground only'
 ),
 JSON_OBJECT('correct_index', 1),
 'ANT, TEST, or invalid signal. Mnemonic: ADF-PARKS-90.',
 'hard', 80),
(@quiz_practice, 'What audio cue indicates a valid station in ADF BFO mode?',
 'mcq', JSON_ARRAY('Continuous 400 Hz','Intermittent 1000 Hz','Voice ID','No audio cue'),
 JSON_OBJECT('correct_index', 1),
 'BFO 1000 Hz intermittent. Mnemonic: BFO-1000-HZ.',
 'hard', 90),
(@quiz_practice, 'What positions does the EFCP Bearing 1 selector have?',
 'mcq', JSON_ARRAY(
   '4: VOR/ADF/FMS/AUX',
   '5: OFF/VOR1/ADF1/FMS1/AUX1',
   '5: NORM/VOR/ADF/ILS/MAN',
   '3: VOR/ADF/FMS'
 ),
 JSON_OBJECT('correct_index', 1),
 '5-position: OFF/VOR/ADF/FMS/AUX. Mnemonic: BRG-5-POS.',
 'medium', 100),
(@quiz_practice, 'What does the bearing pointer do when set to VOR with an invalid frequency or ILS frequency?',
 'mcq', JSON_ARRAY(
   'Parks at 90°',
   'Removed from PFD',
   'Flashes red',
   'Continues to point at last valid bearing'
 ),
 JSON_OBJECT('correct_index', 1),
 'VOR pointer is REMOVED if frequency invalid or ILS freq selected. ADF parks at 90° on invalid.',
 'medium', 110),
(@quiz_practice, 'What does the FMS bearing pointer do when FMS is operating?',
 'mcq', JSON_ARRAY(
   'Points to destination airport',
   'Points to next waypoint in the active flight plan',
   'Parks at 90°',
   'Always 0° (north)'
 ),
 JSON_OBJECT('correct_index', 1),
 'FMS bearing → next waypoint. Mnemonic: WPT-FMS-PT.',
 'medium', 120),
(@quiz_practice, 'EFCP FORMAT pushbutton — what does push-and-hold for 1 second do?',
 'mcq', JSON_ARRAY(
   'Cycles ARC mode sources',
   'Switches to FULL mode (360° north-up, A/C centred). Note: WX radar disabled in FULL mode',
   'Activates plan view',
   'Resets the EFCP'
 ),
 JSON_OBJECT('correct_index', 1),
 'Push+hold 1 sec → FULL mode. WX disabled in FULL. Mnemonic: FORMAT-1-SEC-FULL.',
 'hard', 130),
(@quiz_practice, 'At what range does TCAS show continuous traffic on the EFIS?',
 'mcq', JSON_ARRAY('20 nm','40 nm or less','80 nm','160 nm'),
 JSON_OBJECT('correct_index', 1),
 'Continuous at 40 nm or less. Mnemonic: TCAS-40-NM.',
 'medium', 140),
(@quiz_practice, 'What happens to TCAS mode if the EFCP malfunctions?',
 'mcq', JSON_ARRAY(
   'TCAS goes off',
   'TCAS automatically goes to AUTO mode',
   'TCAS reverts to standby',
   'TCAS continues continuous mode'
 ),
 JSON_OBJECT('correct_index', 1),
 'EFCP fault → TCAS auto-AUTO. Preserves traffic awareness.',
 'hard', 150),
(@quiz_practice, 'What ranges does the EFCP RANGE selector offer, and what is the default?',
 'mcq', JSON_ARRAY(
   '5/10/20/40/80; default 20',
   '10/20/40/80/160/240 nm; default 40',
   '20/50/100/200/400; default 100',
   'Continuous variable; no default'
 ),
 JSON_OBJECT('correct_index', 1),
 '6 positions; default 40 nm. Mnemonic: RANGE-6-DEFAULT-40.',
 'medium', 160),
(@quiz_practice, 'What does the EFCP DATA pushbutton offer?',
 'mcq', JSON_ARRAY(
   '10 nearest nav aids / 10 nearest airports / both / off (default)',
   'Only nearest nav aids',
   'Only nearest airports',
   'No data display'
 ),
 JSON_OBJECT('correct_index', 0),
 '4-cycle: 10 nav aids → 10 airports → both → off. Push and HOLD 1 sec removes all.',
 'hard', 170),
(@quiz_practice, 'How many AHRS units, ADCs, AHCPs, flux valves, and remote memory modules?',
 'mcq', JSON_ARRAY(
   'All single (1 each)',
   '2 of each: 2 AHRS units, 2 ADCs, 2 AHCPs, 2 flux valves, 2 RMMs',
   '3 of each',
   '2 AHRUs but only 1 of everything else'
 ),
 JSON_OBJECT('correct_index', 1),
 '2 of each. Mnemonic: 2-AHRU-2-ADC-2-FLUX-2-RMM.',
 'hard', 180),
(@quiz_practice, 'What sensors does the AHRU use?',
 'mcq', JSON_ARRAY(
   'Laser ring gyros only',
   'Vertical and directional gyros + accelerometers',
   'GPS + altimeter only',
   'Magnetic compass only'
 ),
 JSON_OBJECT('correct_index', 1),
 'Vertical + directional gyros + accelerometers. Note: NOT laser ring gyros (that''s IRS).',
 'hard', 190),
(@quiz_practice, 'How is cross-side AHRS source selected, and how is cross-side selection indicated?',
 'mcq', JSON_ARRAY(
   'Auto only',
   'EFIS ATT/HDG SOURCE selector NORM/1/2; YELLOW indication on PFD when cross-side',
   'Master Caution flashes',
   'No cross-side capability'
 ),
 JSON_OBJECT('correct_index', 1),
 'EFIS ATT/HDG SOURCE selector. Yellow PFD indication.',
 'medium', 200),
(@quiz_practice, 'What does TERRAIN INHIBIT switchlight inhibit?',
 'mcq', JSON_ARRAY(
   'All GPWS modes',
   'TAD and TCF only — other GPWS modes remain active',
   'TCAS only',
   'Weather radar'
 ),
 JSON_OBJECT('correct_index', 1),
 'TAD + TCF only. Other GPWS modes (1-5) remain active.',
 'hard', 210),
(@quiz_practice, 'What does GPWS FLAP OVERRIDE inhibit?',
 'mcq', JSON_ARRAY(
   'All GPWS modes',
   'GPWS mode 4B only — permits 0° flap landings',
   'TAD + TCF',
   'BELOW G/S aural'
 ),
 JSON_OBJECT('correct_index', 1),
 'Mode 4B only. 0° flap landings.',
 'hard', 220),
(@quiz_practice, 'TRUE or FALSE — In FULL format mode the weather radar image is displayed.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 1),
 'FALSE. WX is NOT displayed in FULL format. Only ARC mode shows weather radar.',
 'hard', 230),
(@quiz_practice, 'You set Bearing 2 to ADF2 and the pointer parks at 90°. What does this most likely indicate?',
 'mcq', JSON_ARRAY(
   'Normal operation',
   'ADF signal or frequency invalid OR system in ANT/TEST mode',
   'EFCP malfunction',
   'Cross-side source active'
 ),
 JSON_OBJECT('correct_index', 1),
 'ADF parks at 90° on invalid signal/frequency or in ANT/TEST modes.',
 'medium', 240),
(@quiz_practice, 'TRUE or FALSE — Loss of one DME silently reroutes both EFIS sides to the remaining DME without crew action.',
 'true_false', JSON_ARRAY('True','False'), JSON_OBJECT('correct_index', 0),
 'TRUE. Silent auto-failover. Document for maintenance.',
 'medium', 250);

INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id, 'Navigation — Type Rating Mock', 'Ten-question mock at type-rating oral standard. Twelve-minute timer.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'VOR vs Localizer frequency ranges and spacings.',
 'mcq', JSON_ARRAY(
   'VOR even / LOC odd, both 108–112',
   'VOR 108.00–117.95 even 50 kHz / LOC 108.10–111.95 odd 50 kHz',
   'Same range, different parity',
   'VOR odd / LOC even'
 ), JSON_OBJECT('correct_index', 1),
 'VOR-EVEN-LOC-ODD.',
 'hard', 10),
(@quiz_exam, 'Marker beacon colour mapping by location.',
 'mcq', JSON_ARRAY(
   'OUT red / MID green / INN blue',
   'OUT blue / MID amber / INN white',
   'All white',
   'OUT amber / MID blue / INN white'
 ), JSON_OBJECT('correct_index', 1),
 'OUT-BLUE-MID-AMBER-INN-WHITE.',
 'easy', 20),
(@quiz_exam, 'EFCP Bearing 1 selector — number of positions and labels.',
 'mcq', JSON_ARRAY(
   '4: VOR/ADF/FMS/AUX',
   '5: OFF/VOR1/ADF1/FMS1/AUX1',
   '3: VOR/FMS/AUX',
   '6 with TCAS'
 ), JSON_OBJECT('correct_index', 1),
 'BRG-5-POS.',
 'medium', 30),
(@quiz_exam, 'FORMAT pushbutton — what is push-and-hold 1 sec behaviour?',
 'mcq', JSON_ARRAY(
   'Resets EFCP',
   'Switches to FULL 360° north-up; WX radar disabled in FULL',
   'Cycles bearing pointers',
   'Activates plan view'
 ), JSON_OBJECT('correct_index', 1),
 'FORMAT-1-SEC-FULL.',
 'hard', 40),
(@quiz_exam, 'TCAS continuous range and EFCP fault behaviour.',
 'mcq', JSON_ARRAY(
   '20 nm; off on EFCP fail',
   '40 nm or less; auto-AUTO mode on EFCP fail',
   '80 nm; manual',
   'Continuous always'
 ), JSON_OBJECT('correct_index', 1),
 'TCAS-40-NM. Auto-AUTO on EFCP fault.',
 'hard', 50),
(@quiz_exam, 'ADF parks at 90° in which conditions?',
 'mcq', JSON_ARRAY(
   'In ADF mode with valid signal',
   'In ANT mode, TEST mode, or invalid signal/frequency',
   'Always',
   'Only in BFO mode'
 ), JSON_OBJECT('correct_index', 1),
 'ANT/TEST/invalid. Mnemonic: ADF-PARKS-90.',
 'medium', 60),
(@quiz_exam, 'AHRS architecture (count of each component).',
 'mcq', JSON_ARRAY(
   'Single AHRS, dual AHRU',
   '2 AHCPs / 2 AHRUs / 2 flux valves / 2 RMMs',
   '4 AHRS for redundancy',
   '1 of each (no cross)'
 ), JSON_OBJECT('correct_index', 1),
 '2 of each.',
 'medium', 70),
(@quiz_exam, 'You set Bearing 1 to VOR1 with a valid frequency tuned. The pointer is REMOVED. Most likely?',
 'mcq', JSON_ARRAY(
   'ADF signal lost',
   'VOR frequency invalid OR ILS frequency selected',
   'EFCP malfunction',
   'AHRS fault'
 ), JSON_OBJECT('correct_index', 1),
 'VOR pointer removed = invalid freq or ILS freq.',
 'hard', 80),
(@quiz_exam, 'TERRAIN INHIBIT (white switchlight) inhibits what?',
 'mcq', JSON_ARRAY(
   'All GPWS modes',
   'TAD + TCF only — other GPWS modes remain active',
   'TCAS only',
   'WX radar'
 ), JSON_OBJECT('correct_index', 1),
 'TAD + TCF only.',
 'hard', 90),
(@quiz_exam, 'Loss of DME1: how is data rerouted on both EFIS sides?',
 'mcq', JSON_ARRAY(
   'Pilot loses all DME data',
   'Both EFIS sides auto-use remaining DME (DME2). Silent failover',
   'Manual reroute via ARCDU only',
   'Standby DME used'
 ), JSON_OBJECT('correct_index', 1),
 'DME-CROSS-USE.',
 'medium', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
