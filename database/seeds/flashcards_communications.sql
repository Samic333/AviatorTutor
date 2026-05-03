-- =============================================================================
-- AviatorTutor — Phase 4: ATA 23 Communications — 36 flashcards.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'communications' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM flashcards WHERE system_id = @system_id;

INSERT INTO flashcards (system_id, front, back, hint, difficulty, tags) VALUES
-- ARMS architecture (5)
(@system_id, 'What does ARMS stand for, and what three sub-systems live under it?',
 'Audio and Radio Management System. Sub-systems: RCOM (Radio Communication and Radio Navigation Management), PACIS (Passenger Address and Communication Interphone System), AIS (Audio Integration System).',
 'Mnemonic: ARMS-3-IN-1.', 'easy', JSON_ARRAY('communications','arms','architecture')),
(@system_id, 'How many ARCDUs does the Q400 have, and where are they located?',
 'TWO. ARCDU 1 captain side, ARCDU 2 FO side. Each is the cockpit''s primary interface to ARMS.',
 NULL, 'easy', JSON_ARRAY('communications','arcdu','location')),
(@system_id, 'Are the CVR, FDR, and ELT part of ARMS?',
 'NO. They are separate (but related) communication-equipment items. ARMS = RCOM + PACIS + AIS.',
 NULL, 'medium', JSON_ARRAY('communications','arms','architecture')),
(@system_id, 'On the Q400, what is the active frequency colour for VALID data?',
 'GREEN. (White means invalid or no data.)',
 'Mnemonic: GREEN-VALID-WHITE-NOPE.', 'easy', JSON_ARRAY('communications','arcdu','colours')),
(@system_id, 'On the Q400, what is the preset frequency colour normally?',
 'CYAN. When highlighted (black digits on cyan background) the preset becomes the tune window / scratch pad.',
 NULL, 'easy', JSON_ARRAY('communications','arcdu','colours')),

-- ARCDU operation (5)
(@system_id, 'How long can the preset frequency stay highlighted (tune window) without action?',
 '5 seconds. Then the highlight reverts back to plain cyan and you have to start over.',
 'Mnemonic: CYAN-PRESET-5-SEC.', 'medium', JSON_ARRAY('communications','arcdu','timeout')),
(@system_id, 'Describe the three-push rhythm to swap a VHF active and preset frequency.',
 '(1) Push side key adjacent to VHF label — preset highlights. (2) Rotate tune knob to dial new frequency. (3) Push side key again — preset becomes active, active becomes preset.',
 NULL, 'medium', JSON_ARRAY('communications','arcdu','tuning')),
(@system_id, 'How do you initiate a VHF test on the Q400 ARCDU?',
 'Push the VHF side key, then EXP key, then the side key adjacent to the TEST legend. Test runs for 1 second with squelch disabled.',
 NULL, 'medium', JSON_ARRAY('communications','arcdu','test')),
(@system_id, 'How long does the VHF test last, and how do you confirm success?',
 '1 second. The squelch is disabled so you LISTEN for noise — that confirms receiver operational. There is NO automatic pass/fail indication.',
 NULL, 'medium', JSON_ARRAY('communications','vhf','test')),
(@system_id, 'During a VHF test, what does the green SQL with a diagonal line through it indicate?',
 'Squelch is currently disabled — the audio module automatic squelch circuit is open so the receiver noise can be heard.',
 NULL, 'hard', JSON_ARRAY('communications','vhf','test','indications')),

-- VHF1 Standby (3)
(@system_id, 'What does the VHF1 Standby Control and Display Unit do?',
 'Backup tuning and display for VHF1 only. Used when both ARCDUs are unusable. Independent system, independent panel on the side console.',
 NULL, 'medium', JSON_ARRAY('communications','vhf1-standby','backup')),
(@system_id, 'What positions does the VHF1 Standby Control and Display Unit rotary switch have?',
 'Three positions: OFF / ON / TEST. ON powers the standby panel. TEST disables receiver squelch — listen for noise to verify radio operational.',
 NULL, 'medium', JSON_ARRAY('communications','vhf1-standby','controls')),
(@system_id, 'What is the Tx annunciator on the VHF1 Standby Control and Display Unit, and when does it illuminate?',
 'Indicates TRANSMIT. Illuminates each time the microphone is keyed AND a RF output is detected from the radio.',
 NULL, 'hard', JSON_ARRAY('communications','vhf1-standby','indications')),

-- Audio panels (5)
(@system_id, 'What positions does the control wheel PTT/INPH switch have, and what is its spring behaviour?',
 'Three positions: PTT (transmit), CENTRE (idle), INPH (interphone). Spring-loaded to centre.',
 NULL, 'easy', JSON_ARRAY('communications','ptt','controls')),
(@system_id, 'When PTT is pressed on the Q400, what happens to the flight-deck speakers?',
 'They mute by 6 dB to prevent feedback into the open mic.',
 NULL, 'medium', JSON_ARRAY('communications','ptt','speakers')),
(@system_id, 'For INPH (interphone) mode to work, where must the mic/interphone selector be positioned on the ARCDU?',
 'In the SERV/INT or PA position.',
 NULL, 'medium', JSON_ARRAY('communications','inph','interphone')),
(@system_id, 'What is special about the transmitter keys on the Observer Audio Control Panel?',
 'They are mechanically interlocked — only one transmitter key can be selected at a time.',
 NULL, 'hard', JSON_ARRAY('communications','observer','interlock')),
(@system_id, 'How are the headphone audio knobs on the Observer Audio Control Panel turned OFF?',
 'Set the knob to the FULL counter-clockwise position. Each of the 13 knobs controls audio volume of its applicable source.',
 NULL, 'medium', JSON_ARRAY('communications','observer','controls')),

-- Steering panel + ground crew (3)
(@system_id, 'What do the FWD and AFT segments of the Ground Crew Connection annunciator indicate?',
 'FWD (amber) = ground crew connected at DC external connection point. AFT (amber) = ground crew connected at REFUEL/DEFUEL panel or aft aircraft connection point.',
 'Mnemonic: FORE-AFT-AMBER.', 'medium', JSON_ARRAY('communications','ground-crew','annunciators')),
(@system_id, 'Where on the Q400 is the steering panel PTT switch, and what is its action?',
 'On the steering handwheel. Momentary action — press to transmit. Same speaker-mute logic (6 dB) as the control wheel PTT.',
 NULL, 'medium', JSON_ARRAY('communications','ptt','steering')),
(@system_id, 'On taxi with ground crew connected, you observe FWD segment amber lit. What does that tell you?',
 'Ground crew is connected at the DC external connection point. Confirm comms with the ground crew before any movement.',
 NULL, 'easy', JSON_ARRAY('communications','ground-crew','operations')),

-- ELT (5)
(@system_id, 'What three frequencies does the Q400 Kannad ELT transmit on?',
 '121.5 MHz, 243 MHz, and 406 MHz (with COSPAS/SARSAT).',
 NULL, 'easy', JSON_ARRAY('communications','elt','frequencies')),
(@system_id, 'At what longitudinal G threshold does the Q400 ELT inertia switch automatically activate?',
 'Between 5 and 7 G longitudinal acceleration.',
 'Mnemonic: 5-7-G-ARMED.', 'medium', JSON_ARRAY('communications','elt','inertia')),
(@system_id, 'What three positions does the ELT remote switch have, and what is the spring behaviour?',
 'ON / ARMED / RESET & TEST. Spring-loaded AWAY from RESET & TEST (which is momentary).',
 NULL, 'medium', JSON_ARRAY('communications','elt','controls')),
(@system_id, 'What does the Q400 ELT monitor light look like in NORMAL armed condition?',
 'ONE long flash every 3 seconds.',
 'Mnemonic: 3-SEC-FLASH-NORMAL.', 'medium', JSON_ARRAY('communications','elt','indications')),
(@system_id, 'After a hard landing the ELT monitor light is flashing every 4 seconds. What action?',
 'Inadvertent activation. Set ELT remote switch to RESET & TEST momentarily. Monitor light goes out. Advise ATC; tech log entry.',
 NULL, 'hard', JSON_ARRAY('communications','elt','recovery')),

-- ACARS + CVR (5)
(@system_id, 'What does ACARS stand for?',
 'Aircraft Communications, Addressing and Reporting System.',
 NULL, 'easy', JSON_ARRAY('communications','acars','definition')),
(@system_id, 'Which VHF Comm does ACARS use, and is voice transmission supported on it?',
 'The dedicated 3rd VHF Comm (Thompson EVR 76). NO voice capability — data only. The 3rd VHF is tuned by ACARS itself.',
 'Mnemonic: 3RD-VHF-DATA.', 'medium', JSON_ARRAY('communications','acars','3rd-vhf')),
(@system_id, 'What is the printer model used with ACARS, and where is it located?',
 'Allied Signal PTA-45B half-size data printer. Mounted just below the forward side console on the copilot side.',
 NULL, 'hard', JSON_ARRAY('communications','acars','printer')),
(@system_id, 'How long does the Q400 SSCVR record?',
 'The last 2 hours of audio (continuous loop).',
 'Mnemonic: 2-HOUR-CVR.', 'easy', JSON_ARRAY('communications','cvr','duration')),
(@system_id, 'What four data streams does the Q400 SSCVR record?',
 'All flight crew communications, the flight deck area microphone, PA announcements, and clock data.',
 NULL, 'medium', JSON_ARRAY('communications','cvr','sources')),

-- Lost comms + scenarios (5)
(@system_id, 'Both ARCDUs fail in flight. How does the crew maintain ATC comms?',
 'Switch the VHF1 Standby Control and Display Unit ON, tune VHF1 to current ATC or 121.5, declare with ATC.',
 NULL, 'hard', JSON_ARRAY('communications','arcdu','failure')),
(@system_id, 'What is the lost-comms transponder code?',
 '7600.',
 'Mnemonic: 7600-LOST.', 'easy', JSON_ARRAY('communications','transponder','lost-comms')),
(@system_id, 'After an incident in cruise, what should be done with the CVR to preserve evidence?',
 'Pull the CVR circuit breaker BEFORE taxi-in to preserve the last 2 hours of recording. (Otherwise the loop will overwrite during taxi.)',
 NULL, 'hard', JSON_ARRAY('communications','cvr','incident')),
(@system_id, 'What is the test procedure for the Flight Data Recorder while on the ground?',
 'Set GND TEST switch (with the RED-OFF-WHITE A/COL switch on the Exterior Lights Panel set to OFF). FLT DATA RECORDER caution light comes on. Light goes out = test passes.',
 NULL, 'hard', JSON_ARRAY('communications','fdr','test')),
(@system_id, 'A real ELT distress case requires what setting?',
 'ELT remote switch to ON. Overrides the automatic inertia switch. Monitor light flashes every 4 seconds confirming transmission.',
 NULL, 'hard', JSON_ARRAY('communications','elt','distress'));

SELECT COUNT(*) AS flashcards_inserted FROM flashcards WHERE system_id = @system_id;
