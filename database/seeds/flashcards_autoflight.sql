-- =============================================================================
-- AviatorTutor — Phase 3: ATA 22 Autoflight — 36 flashcards.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'autoflight' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM flashcards WHERE system_id = @system_id;

INSERT INTO flashcards (system_id, front, back, hint, difficulty, tags) VALUES
-- Architecture (5)
(@system_id, 'How many Flight Guidance Modules (FGMs) does the Q400 AFCS have, and what does each do?',
 'Two FGMs. FGM1 commands the AP and YD actuators; FGM2 monitors the commands and actuator performance. Both are required for AP/YD; one is enough for the FD.',
 'Mnemonic: FGM-2-FOR-1.', 'easy', JSON_ARRAY('autoflight','architecture','fgm')),
(@system_id, 'How many Autopilot Actuator Units (APAUs) on the Q400?',
 'Two: APAU1 and APAU2. Driven by FGM1 commands, monitored by FGM2.',
 NULL, 'easy', JSON_ARRAY('autoflight','architecture','actuator')),
(@system_id, 'How many AP Disengage Warning Lights are on the Q400 glareshield?',
 'Two — one on the captain''s side and one on the FO''s side. Red AP DISENG segments. Both lit means FGM power-source failure caused the disengagement.',
 NULL, 'medium', JSON_ARRAY('autoflight','glareshield','disengage')),
(@system_id, 'Where are the AP Disengage Switches (AP DIS) located?',
 'On each control wheel — pilot and copilot. Functions: disengage AP, reset AP disengage warnings, reset YD disengage PFD annunciation.',
 NULL, 'easy', JSON_ARRAY('autoflight','controls','disengage')),
(@system_id, 'What does engaging the Q400 AP automatically engage?',
 'The Yaw Damper. AP engagement also engages YD. YD failure disengages AP.',
 NULL, 'easy', JSON_ARRAY('autoflight','engagement','yd')),

-- Engagement conditions (5)
(@system_id, 'List the AP engagement attitude limits.',
 'Roll within ±45 degrees, pitch within ±20 degrees.',
 'Mnemonic: 45/20-INHIBIT.', 'medium', JSON_ARRAY('autoflight','engagement','limits')),
(@system_id, 'What is the YD engagement attitude limit?',
 'Roll within ±45 degrees only. (No pitch limit for YD engagement, unlike AP.)',
 NULL, 'medium', JSON_ARRAY('autoflight','engagement','yd','limits')),
(@system_id, 'Name three things that must be true (about AHRS/ADC) for AP engagement.',
 'AHRS 1 and AHRS 2 both valid AND do not disagree. ADC 1 and ADC 2 both valid AND do not disagree. (One pair invalid or disagreeing → AP cannot engage.)',
 NULL, 'medium', JSON_ARRAY('autoflight','engagement','sensors')),
(@system_id, 'Can the Q400 AP be engaged on the ground?',
 'NO. The aircraft must be airborne. "Aeroplane is airborne" is one of the engagement conditions.',
 NULL, 'easy', JSON_ARRAY('autoflight','engagement','ground')),
(@system_id, 'What happens to the AP if a manual pitch trim input is made on the wheel switch while AP is engaged?',
 'The AP DISENGAGES. Manual pitch trim is treated as a deliberate override.',
 NULL, 'medium', JSON_ARRAY('autoflight','pitch-trim','disengage')),

-- AP INHIBIT vs AP FAIL (4)
(@system_id, 'You press AP and the PFD shows "AP INHIBIT". What category of cause is that?',
 'EXTERNAL — outside the AFCS. Sources: AHRS/ADU monitoring trip, not airborne, attitude exceeded, GA active, TCS active, manual pitch-trim AP disconnect, stall-warning AP disconnect, AP disengagement warnings still active.',
 'Mnemonic: AP-INHIBIT-OUT.', 'medium', JSON_ARRAY('autoflight','inhibit','messages')),
(@system_id, 'You press AP and the PFD shows "AP FAIL". What category of cause is that?',
 'INTERNAL — an AFCS failure prevented engagement. If YD is also affected, the message is "AP/YD FAIL".',
 'Mnemonic: AP-FAIL-IN.', 'medium', JSON_ARRAY('autoflight','fail','messages')),
(@system_id, 'List four external sources that can cause an AP INHIBIT message.',
 '(1) AHRS or ADU monitoring trip. (2) Attitude exceeds engagement limits. (3) GA switch selected. (4) TCS active. (Plus: not airborne, AP DIS pressed, stall-warning AP disconnect, manual pitch-trim AP disconnect, AP disengagement warnings active.)',
 NULL, 'hard', JSON_ARRAY('autoflight','inhibit','sources')),
(@system_id, 'TRUE or FALSE — AP INHIBIT and AP FAIL mean the same thing.',
 'FALSE. AP INHIBIT = external condition prevents engagement (fix the condition). AP FAIL = internal AFCS failure (run the QRH).',
 NULL, 'easy', JSON_ARRAY('autoflight','inhibit','fail')),

-- Pitch trim (5)
(@system_id, 'What are the two AP Pitch Trim speeds, and at what KCAS threshold do they switch?',
 'HIGH speed below 180 KCAS; LOW speed above 180 KCAS. High handles flap/gear motion + accel/decel; low gives precision in cruise.',
 'Mnemonic: 180-FAST-LOW.', 'medium', JSON_ARRAY('autoflight','pitch-trim','speeds')),
(@system_id, 'When is AP Pitch Trim DISABLED while the AP is engaged?',
 'When TCS (Touch Control Steering) is active. The pilot is overriding the AP — auto-trim stays out of the way.',
 NULL, 'hard', JSON_ARRAY('autoflight','pitch-trim','tcs')),
(@system_id, 'What is Flap Auto Pitch Trim?',
 'AFCS automatically trims pitch when the AP is NOT engaged AND flaps are transitioning. Same FCECU command path as AP Pitch Trim.',
 NULL, 'medium', JSON_ARRAY('autoflight','pitch-trim','flaps')),
(@system_id, 'Why does the AFCS continuously trim pitch while the AP is engaged?',
 'To keep AP servo torque near zero, so when the pilot disengages the AP there is no surprise pitch transient. The trim follows the airframe.',
 NULL, 'medium', JSON_ARRAY('autoflight','pitch-trim','rationale')),
(@system_id, 'TRUE or FALSE — The Q400 has automatic ROLL trim like it has automatic PITCH trim.',
 'FALSE. There is NO automatic roll trim. Roll Mistrim is annunciation only.',
 'Mnemonic: ROLL-NO-AUTO.', 'medium', JSON_ARRAY('autoflight','roll','mistrim')),

-- Mistrim (3)
(@system_id, 'What does a MISTRIM [TRIM L WING DN] message on the PFD indicate?',
 'AP roll-servo torque has exceeded threshold and the AFCS is detecting persistent control-wheel mistrim. The aircraft would have a left-wing-down tendency at AP disengagement. Trim L (left) wing down means trim INPUT to bring the L wing DOWN.',
 NULL, 'hard', JSON_ARRAY('autoflight','mistrim','indications')),
(@system_id, 'When the Roll Mistrim message appears, does the AP automatically disengage?',
 'NO. Mistrim is annunciation only. AP stays engaged. Crew action: disengage AP CLEANLY (hands on wheel, anticipate transient), trim laterally until message clears, re-engage AP.',
 NULL, 'medium', JSON_ARRAY('autoflight','mistrim','crew-action')),
(@system_id, 'Why is it dangerous to ignore a Mistrim message and let the AP disengage on its own later?',
 'Disengagement could happen at low altitude or in IMC, producing a roll transient when you have no margin. Always disengage CLEANLY in cruise with hands on the wheel — never on short final.',
 NULL, 'hard', JSON_ARRAY('autoflight','mistrim','captain-decisions')),

-- Disengagement annunciations (5)
(@system_id, 'Describe the cockpit indications for AUTOMATIC AP disengagement.',
 '(1) Red AP DISENG segments on the glareshield FLASH. (2) PFD shows flashing amber AP DISENGAGED (or AP/YD DISENGAGED). (3) Continuous aural tone. All three until acknowledged.',
 'Mnemonic: FLASH-RED-AUTO.', 'medium', JSON_ARRAY('autoflight','disengage','automatic')),
(@system_id, 'Describe the cockpit indications for MANUAL AP disengagement.',
 'PFD shows amber AP DISENGAGED (or AP/YD DISENGAGED) STEADY for 5 seconds, then clears. NO flashing. NO aural tone. NO glareshield flashing.',
 NULL, 'medium', JSON_ARRAY('autoflight','disengage','manual')),
(@system_id, 'How does the crew acknowledge an automatic AP disengagement?',
 'Press AP DIS on either control wheel. Stops the flashing, silences the aural tone, and (after acknowledgement) the PFD message stays steady for 5 seconds.',
 NULL, 'easy', JSON_ARRAY('autoflight','disengage','acknowledgement')),
(@system_id, 'When are BOTH AP Disengage Warning Lights illuminated?',
 'When the AP disengagement was caused by failure of one of the two FGM power sources.',
 NULL, 'hard', JSON_ARRAY('autoflight','disengage','power-fail')),
(@system_id, 'Can you re-engage the AP while disengagement warnings are still active?',
 'NO. AP engagement is INHIBITED while AP disengagement visual warnings are active. Reset the warnings (press AP DIS on either wheel) before attempting re-engagement.',
 NULL, 'medium', JSON_ARRAY('autoflight','disengage','re-engage')),

-- FMA + modes (5)
(@system_id, 'What does WHITE indicate on the FMA?',
 'A mode is ARMED — waiting for capture. (e.g., LNAV armed, ALT SEL armed.)',
 'Mnemonic: WHITE-ARM-GREEN-LIVE.', 'easy', JSON_ARRAY('autoflight','fma','colours')),
(@system_id, 'What does GREEN indicate on the FMA?',
 'A mode is ACTIVE — currently flying. (e.g., HDG green, ALT green, VNAV PATH green.)',
 NULL, 'easy', JSON_ARRAY('autoflight','fma','colours')),
(@system_id, 'List the four VNAV active submodes on the Q400 FMA.',
 'PATH, FLIGHT LEVEL CHANGE, ALTITUDE CAPTURE, ALTITUDE HOLD. Submode is determined by the EIS directly from the FMS.',
 NULL, 'hard', JSON_ARRAY('autoflight','vnav','submodes')),
(@system_id, 'In Go Around mode, what is the FD lateral submode?',
 'Wings Level — commands zero roll attitude. All other lateral armed modes are disarmed.',
 NULL, 'medium', JSON_ARRAY('autoflight','go-around','lateral')),
(@system_id, 'Name four ways Go Around mode can be DEACTIVATED.',
 '(1) Activating any other vertical mode (manually or automatically). (2) Engaging the AP. (3) Selecting STBY or HSI SEL. (4) Changing the selected ADC or AHRS source.',
 NULL, 'hard', JSON_ARRAY('autoflight','go-around','deactivation')),

-- AFCS PFD failure messages (4)
(@system_id, 'What does the PFD message AFCS FAIL indicate, and how is it displayed?',
 'The whole AFCS has failed. Flashing yellow for 5 seconds, then steady yellow. Non-resettable.',
 NULL, 'medium', JSON_ARRAY('autoflight','failure-messages')),
(@system_id, 'What does AP PITCH TRIM FAIL mean and what must the crew do?',
 'Automatic pitch trim is broken. The crew must keep up with manual pitch trim. Flashing yellow 5 sec then steady. Not resettable except if AFCS itself fails or the failure is no longer present.',
 NULL, 'hard', JSON_ARRAY('autoflight','pitch-trim','failure')),
(@system_id, 'What does YD NOT CENTERED mean?',
 'The yaw damper is not at its zero reference. Flashing yellow for 5 seconds, then steady yellow. Crew action: review the QRH and check rudder trim.',
 NULL, 'hard', JSON_ARRAY('autoflight','yd','failure')),
(@system_id, 'List the seven yellow PFD failure messages an AFCS can display.',
 'AFCS FAIL · AP PITCH TRIM FAIL · YD NOT CENTERED · L FD FAIL · R FD FAIL · AFCS CONTROLLER INOP · AUTO TRIM FAIL. All flash yellow for 5 sec then steady; most non-resettable.',
 NULL, 'hard', JSON_ARRAY('autoflight','failure-messages','catalogue'));

SELECT COUNT(*) AS flashcards_inserted FROM flashcards WHERE system_id = @system_id;
