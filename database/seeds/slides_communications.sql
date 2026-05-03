-- =============================================================================
-- AviatorTutor — Phase 4: ATA 23 Communications — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'communications' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'communications-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'The Picture You Cannot Fly Without',
 'Communications is the network that turns a metal tube at FL250 into a system in an Air Traffic Control picture. ATC sees you on radar; you exist in their picture only because the transponder is awake and you are talking. The cabin trusts you because they hear you on the PA. The ground crew knows what you need because the interphone works. When comms degrades — radio goes dead, ARCDU freezes, ELT activates inadvertently — your situational picture starts shrinking. This lesson is about keeping the picture wide.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'Communications system overview',
 'Comms = your picture in the system. Lose it and you shrink.',
 'On a controlled-airspace ride, every minute without comms loses you separation, traffic, and clearances. Memorise lost-comms procedures.',
 NULL),

(@lesson_id, 20, 'concept',
 'ARMS Architecture — One Umbrella, Three Halves',
 'The Audio and Radio Management System (ARMS) is the umbrella that ties three sub-systems together. RCOM — Radio Communication and Radio Navigation Management — covers the three VHFs, optional HF, and audio integration of all NAV receivers. PACIS — Passenger Address and Communication Interphone System — handles cabin comms with CALL and EMER keys for FD↔cabin. AIS — Audio Integration System — wires audio paths between the ARCDUs, the Audio Control Panels, the speakers, headsets, and PTT/INPH circuits. Plus three discrete items not in ARMS: CVR, FDR, ELT.',
 'diagram', '/assets/aircraft/q400/communications-flow.svg',
 'ARMS umbrella block diagram',
 'ARMS-3-IN-1: RCOM + PACIS + AIS. CVR / FDR / ELT are separate.',
 'Memorising the architecture lets you reason about failures. Loss of one branch does not bring down the others.',
 NULL),

(@lesson_id, 30, 'concept',
 'ARCDU — Two Boxes, One Operating Idea',
 'There are TWO ARCDUs (Audio and Radio Control Display Units) — one for the captain, one for the FO. Each is the cockpit''s primary interface to ARMS. Active matrix LCD with coloured fonts on a black background. Each frequency display has two slots: ACTIVE (top) and PRESET (bottom). Active frequency colour = GREEN if the radio is sending valid data; WHITE if not. Preset = CYAN, and when you push the side key adjacent to the VHF label the preset highlights (black digits on cyan background) — that is the "tune window" where rotary input lands. No action for 5 seconds and the highlight reverts.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'ARCDU page layout with active/preset colour coding',
 'GREEN-VALID-WHITE-NOPE. Active green = good, white = no data.',
 'Glance at the ARCDU on every radio swap. White active means you have no data — you are talking but maybe not transmitting on the right channel.',
 JSON_OBJECT(
   'prompt', 'On the Q400 ARCDU, the active frequency is shown in WHITE digits. What does that indicate?',
   'options', JSON_ARRAY(
     'The radio is operating normally with valid data',
     'The radio is on standby',
     'Invalid data or no data is being received from the communication system',
     'A transmit is in progress'
   ),
   'correct_index', 2,
   'explanation', 'Active in GREEN = valid data. Active in WHITE = invalid or no data. Suspect the radio path or power.'
 )),

(@lesson_id, 40, 'system',
 'Active / Preset Swap — The Three-Push Rhythm',
 'Tuning a new VHF frequency on the Q400 is a three-push rhythm. (1) Push the side key adjacent to the VHF label — preset highlights black-on-cyan. (2) Use the rotary tune knob to dial in the new frequency. (3) Push the side key AGAIN — preset becomes active, active becomes preset. The frequencies SWAP. If you hesitate longer than 5 seconds at step 1, the highlight reverts back to plain cyan and you have to start over. Brief this rhythm to your FO so the swap is muscle memory in busy airspace.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'Active/preset swap sequence diagram',
 'CYAN-PRESET-5-SEC. Three pushes: highlight, dial, swap.',
 'In busy TMA, every wasted swap second is one ATC call you missed. Drill the three-push rhythm.',
 NULL),

(@lesson_id, 50, 'system',
 'VHF Test — Listen for the Noise',
 'To test a VHF radio, push the VHF side key to enter that VHF page; push the EXP (expand) key to display the VHF particular page; push the side key adjacent to the TEST legend. The system disables the audio module automatic squelch for ONE SECOND. If the receiver is operational you hear noise on the headset / speaker. The ARCDU shows TEST in reverse video (black on green) and a GREEN SQL with a diagonal line through it on the second line of the VHF area to indicate squelch is open. Test ends after 1 second; legend returns to white. NO automatic pass/fail indication — your ear is the test result.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'VHF test procedure flow',
 'VHF · EXP · TEST. 1 second of noise = good radio.',
 'Pre-flight test of all three VHFs is part of the cockpit setup. Skipping it costs you a clearance read-back failure later.',
 NULL),

(@lesson_id, 60, 'system',
 'VHF1 Standby Control & Display Unit — The Backup',
 'When both ARCDUs are unusable, VHF1 still has a backup tuning interface — the Standby Control and Display Unit on the side console. A 3-position rotary OFF / ON / TEST. ON powers the standby unit; TEST disables receiver squelch — listen for background noise to confirm receiver operational. The display shows ACTIVE frequency in the top portion and PRESET in the bottom portion. A "Tx" annunciator illuminates each time the mic is keyed and RF output is detected. With this backup you can keep VHF1 alive — primary lost-comms recovery — even with both ARCDUs dead.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'VHF1 Standby Control & Display Unit panel',
 'VHF1 STBY: backup tune for VHF1 only. OFF/ON/TEST.',
 'Lost-comms scenario brief: "If both ARCDUs go, switch the VHF1 standby ON, tune 121.5, declare." Practice this once a year minimum.',
 JSON_OBJECT(
   'prompt', 'Both Q400 ARCDUs are dead in flight. How can VHF1 still be tuned and used?',
   'options', JSON_ARRAY(
     'It cannot — both ARCDUs are required for any VHF',
     'Via the VHF1 Standby Control and Display Unit on the side console',
     'Via the FMS comms page',
     'Via the cabin attendant panel'
   ),
   'correct_index', 1,
   'explanation', 'The VHF1 Standby Control and Display Unit is a dedicated backup that keeps VHF1 alive even with both ARCDUs unusable. Switch it ON, tune frequency, transmit.'
 )),

(@lesson_id, 70, 'system',
 'Audio Panels — Control Wheels, Side Panels, and the Observer',
 'PTT is wired to several stations. Pilot control wheel: a 3-position spring-loaded PTT/INPH switch — PTT transmits on the radio selected by the ARCDU mic/interphone selector and mutes flight-deck speakers by 6 dB to prevent feedback; INPH connects the mic to the cabin interphone (mic/interphone selector must be SERV/INT or PA). Copilot side panel: identical XMIT/INPH switch. Steering panel: PTT only (no INPH); mute logic same. Observer Audio Control Panel: 13 audio knobs (full counter-clockwise = OFF), mechanically interlocked transmitter keys (only one selected at a time), INT/RAD switch (3-position spring-to-center).',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'Audio panel layout — control wheels, side panel, observer',
 'PTT mutes speakers 6 dB. Observer keys are mechanically interlocked.',
 'On a noisy flight deck, the 6 dB mute prevents an ATC call from feeding back into your own headset. Use the speakers when the aircraft is quiet enough.',
 NULL),

(@lesson_id, 80, 'system',
 'ELT — Three Frequency, 5–7 G Inertia, ARMED by Default',
 'The Q400 carries a three-frequency ELT — Kannad 406 MHz with COSPAS/SARSAT, plus 121.5 and 243 MHz. The ELT remote switch on the cockpit panel has three positions, spring-loaded AWAY from RESET & TEST: ON manually activates (overrides automatic inertia switch); ARMED is the operating position — automatic via inertia switch between 5 and 7 G LONGITUDINAL acceleration; RESET & TEST is momentary, used to reset an inadvertent activation and re-arm. The ELT monitor light: one long flash every 3 seconds = normal; a series of short flashes = fault. After any heavy landing or perceived deceleration, glance at the monitor light.',
 'animation', '/assets/aircraft/q400/communications-flow.svg',
 'ELT remote switch and monitor-light states',
 '5-7-G-ARMED. Flash every 3 sec = OK. Short flashes = fault.',
 'Inadvertent activations after a hard taxi happen. After landing always check the ELT light state on the post-flight scan.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'ACARS and CVR — Data and the Black Box',
 'ACARS (Aircraft Communications Addressing and Reporting System) lives on a dedicated THIRD VHF Comm (Thompson EVR 76). Independent system, independent antenna, NO voice capability. The 3rd VHF is tuned by ACARS itself — you cannot voice-key it. ACARS handles down-linked aircraft data (OOOI position, etc.) and up-linked clearances, weather, gate assignments. Half-size data printer (Allied Signal PTA-45B) under the forward side console copilot side. The Solid-State CVR (SSCVR) records the LAST TWO HOURS of flight crew communications, flight deck area microphone, PA announcements, and clock data. Powered by aircraft electrical system.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'ACARS + CVR layout',
 '3RD-VHF-DATA · 2-HOUR-CVR. ACARS = data only, dedicated 3rd VHF.',
 'After any incident, the CVR captures the last 2 hours. Pull the CB to preserve the recording before you taxi in if the event is sensitive.',
 NULL),

(@lesson_id, 100, 'abnormal',
 'ARCDU Failure — How to Survive on the Backup',
 'A single ARCDU failure: the surviving ARCDU still controls all radios. The pilot whose ARCDU is dead reads the surviving unit and uses the wheel PTT/INPH normally. Both ARCDUs failed: VHF1 Standby Control and Display Unit becomes your lifeline — switch it ON, tune VHF1, declare with ATC, brief the FO that we are now single-radio. NAV audio integration is impaired with both ARCDUs out — brief approaches accordingly. CVR / FDR continue to record electrically; you do not lose the recordings just because the cockpit panel went dark.',
 'video', '/assets/aircraft/q400/communications-flow.svg',
 'ARCDU failure decision tree',
 'Single ARCDU = use the other. Both = VHF1 STBY only.',
 'Whenever you take a Q400 type rating, the simulator will fail an ARCDU. Have the recovery sequence on muscle memory.',
 JSON_OBJECT(
   'prompt', 'Both ARCDUs fail in flight. How does the crew maintain ATC communications?',
   'options', JSON_ARRAY(
     'Use the FMS comms page',
     'Use the VHF1 Standby Control and Display Unit on the side console — tune VHF1, declare with ATC',
     'Use ACARS to send a text-mode position report',
     'Switch to passenger-address-only mode and continue'
   ),
   'correct_index', 1,
   'explanation', 'VHF1 Standby Control and Display Unit is the backup. Switch ON, tune VHF1 (e.g., 121.5 or current ATC), declare. ACARS is data-only and has no voice capability.'
 )),

(@lesson_id, 110, 'abnormal',
 'ELT Inadvertent Activation — Recognition and Reset',
 'After a heavier-than-normal landing or a hard taxi-bump the inertia switch (5–7 G longitudinal) may trigger the ELT. Recognition: ELT monitor light flashing every 4 seconds (instead of every 3); possible 121.5 noise on guard frequency. Action: set the ELT remote switch to RESET & TEST (momentary) — re-arms the ELT and the monitor light goes out. Inform ATC that an inadvertent activation occurred and was reset. Make a tech-log entry. After ANY hard landing or G-event, this check is part of the post-flight scan.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'ELT inadvertent activation recovery',
 'Hard landing → check ELT light → if active, RESET & TEST → tech log.',
 'On busy airfields the controller will hear your inadvertent ELT before you do. Make the post-flight ELT check a habit.',
 NULL),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Lost Comms and ELT Operations',
 'Lost-comms QRH non-normal cluster. (1) Single ARCDU loss: continue normally, log defect. (2) Dual ARCDU loss: VHF1 standby up, declare on 121.5 if no current ATC freq, hand-fly remaining of approach with reduced situational awareness on NAV audio. (3) Total comms loss: squawk 7600, follow lost-comms route per filed flight plan, last cleared route then to destination, expected approach time per ATIS. (4) ELT activation post-incident: memorise ON / ARMED / RESET & TEST. Inadvertent → RESET. Real distress → ON to override inertia and confirm transmission.',
 'image', '/assets/aircraft/q400/communications-flow.svg',
 'QRH lost comms + ELT cluster',
 '7600 = lost comms. ELT ON for distress, RESET for inadvertent.',
 'Practice the 7600 squawk in cruise to know exactly where the panel button is. The transponder code is in the QRH but it should be in your fingertips.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Inadvertent ELT After a Hard Landing in Wilson, Nairobi',
 'Setup: Wilson runway 14, Q400 lands firm in a gust. Roll-out is fine. Taxiing in, ATC on tower frequency calls "Echo-Alpha-Bravo, we are receiving an ELT signal on guard, please verify your ELT status." You glance at the ELT monitor light — flashing every 4 seconds. The light is INDICATING activation.\n\nQuestion: do you continue taxi or stop? Decision: complete taxi-in safely, then on stand BEFORE shutdown, set ELT remote to RESET & TEST momentarily — confirm light goes out. Advise tower "ELT reset, no distress." Make a tech-log entry. Confirm with engineering that the inertia switch threshold has not changed. Continue post-flight as normal. The cost of NOT resetting an inadvertent ELT is search-and-rescue crews scrambling — not just an embarrassment but real resources spent.',
 'animation', '/assets/aircraft/q400/communications-flow.svg',
 'Inadvertent ELT decision tree',
 'Hard landing → ATC calls → check light → RESET on stand → tech log.',
 'Tower will catch this before you do on a busy airfield. Be the captain who handled it cleanly, not the one who left it transmitting all evening.',
 JSON_OBJECT(
   'prompt', 'After a firm landing the ELT monitor light is flashing every 4 seconds and tower confirms guard signal. Correct sequence?',
   'options', JSON_ARRAY(
     'Stop the aircraft on the taxiway and reset',
     'Continue taxi to stand; on stand BEFORE shutdown set ELT to RESET & TEST momentarily; advise tower; tech log',
     'Ignore — ELT will reset itself after 5 minutes',
     'Switch ELT to ON to confirm transmission'
   ),
   'correct_index', 1,
   'explanation', 'Continue safely to stand. On stand before shutdown, RESET & TEST (momentary) clears the inadvertent activation. Advise tower; tech log entry; engineering review threshold.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Communications in 60 Seconds',
 'Recap:\n  • ARMS = RCOM + PACIS + AIS. CVR / FDR / ELT separate.\n  • TWO ARCDUs primary; VHF1 Standby Control & Display Unit backs up VHF1.\n  • ARCDU colours: ACTIVE green (valid) / white (invalid). PRESET cyan; highlighted for tune window; 5-sec timeout.\n  • VHF test: VHF · EXP · TEST. 1 sec, listen for noise.\n  • PTT mutes speakers 6 dB. Observer transmitter keys mechanically interlocked.\n  • Ground crew connection annunciators: FWD amber (DC ext), AFT amber (refuel/defuel or aft).\n  • ELT: 3 freq (121.5/243/406 MHz Kannad COSPAS/SARSAT). Inertia trigger 5–7 G longitudinal. Monitor light: 1 long flash every 3 sec = normal.\n  • ELT switch: ON (manual) / ARMED (auto) / RESET & TEST (re-arm momentary).\n  • ACARS = dedicated 3rd VHF, data only, no voice.\n  • CVR = SSCVR, last 2 hours.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'ARMS-3-IN-1 · GREEN-VALID-WHITE-NOPE · CYAN-PRESET-5-SEC · 5-7-G-ARMED · 3RD-VHF-DATA · 2-HOUR-CVR',
 'Six mnemonics carry every comms oral. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
