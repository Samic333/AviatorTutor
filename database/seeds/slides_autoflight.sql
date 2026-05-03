-- =============================================================================
-- AviatorTutor — Phase 3: ATA 22 Autoflight
-- 14-slide interactive lesson for the AFCS, AP/YD/FD, and Mistrim logic.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'autoflight' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'autoflight-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides
    (lesson_id, sort_order, slide_type, title, body,
     media_type, media_url, media_alt, key_point, ops_relevance, question)
VALUES
-- 1. Intro
(@lesson_id, 10, 'intro',
 'The System You Watch the Most, Touch the Least',
 'Autoflight is the system you spend most of every flight observing. It captures and tracks, climbs and descends, holds altitude in chop, captures the localiser, manages your workload so you can manage everything else. The deal you make with it is simple: it flies precisely while you keep the situational picture. The day it stops cooperating — a mistrim, an inhibit, an unexpected disengagement — is the day you find out whether you understood the contract. This lesson is that contract.',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'AFCS architecture overview diagram',
 'AP precision; pilot picture. The pilot owns the disagreement.',
 'Sim instructors will fail your AP at the worst moment by design. The right response is calm hands-on and an immediate trim assessment.',
 NULL),

-- 2. Concept — architecture
(@lesson_id, 20, 'concept',
 'AFCS Architecture — Two FGMs, Two APAUs',
 'The Automatic Flight Control System (AFCS) has two independent, identical Flight Guidance computation channels — Flight Guidance Module 1 and Flight Guidance Module 2. Each FGM can independently process Flight Director commands. For Autopilot and Yaw Damper, both FGMs are required. FGM1 sends actual commands to the AP and YD actuators (the two Autopilot Actuator Units, APAU1 and APAU2). FGM2 monitors those commands and the actuator performance — the second pair of eyes. The Flight Guidance Control Panel (FGCP) is your interface — mode buttons, target selectors, AP and YD engagement.',
 'diagram', '/assets/aircraft/q400/autoflight-flow.svg',
 'FGM-1/2 + APAU-1/2 block diagram',
 'FGM-2-FOR-1. Two FGMs (cmd + monitor) for one AP. FD only needs one.',
 'The dual-channel design means a single-point AFCS failure cannot drive a control surface unnoticed. Trust the architecture; respond to its annunciations.',
 NULL),

-- 3. Concept — engagement
(@lesson_id, 30, 'concept',
 'Engagement — What Must Be True',
 'You press AP on the FGCP. The AFCS will only engage if all of these are simultaneously true: AHRS 1 and AHRS 2 are both valid AND do not disagree; ADC 1 and ADC 2 are both valid AND do not disagree; the aircraft is airborne; the attitude is within engagement limits (roll within ±45°, pitch within ±20°); the manual pitch trim AP disconnect is not set; the AP Disengage Switches on the wheels are not selected; TCS is not failed. Engaging the AP automatically engages the Yaw Damper. YD also has a ±45° roll inhibit.',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'Engagement-conditions checklist diagram',
 '45/20-INHIBIT — roll ±45°, pitch ±20°. All ten conditions or no engagement.',
 'On a windy departure with a steep early turn, do not press AP until the turn is rolled out and pitch is settled. The system enforces the limits.',
 JSON_OBJECT(
   'prompt', 'Which of the following is an Autopilot ENGAGEMENT inhibit?',
   'options', JSON_ARRAY(
     'Aircraft pitch attitude greater than ±10°',
     'Aircraft roll attitude greater than ±45° OR pitch greater than ±20°',
     'Aircraft below 200 ft AGL',
     'Cabin altitude above 9,800 ft'
   ),
   'correct_index', 1,
   'explanation', 'AP engagement is inhibited if roll exceeds ±45° or pitch exceeds ±20°. Yaw Damper engagement is inhibited only by ±45° roll.'
 )),

-- 4. System — AP INHIBIT vs AP FAIL
(@lesson_id, 40, 'system',
 'AP INHIBIT vs AP FAIL — External vs Internal',
 'When you press AP and it does not engage, the PFD tells you why. If the inhibit comes from OUTSIDE the AFCS (AHRS / ADU monitoring trip, not airborne, attitude exceeded, GA selected, TCS active, manual pitch-trim AP disconnect set, stall-warning AP disconnect from SPM 1 or 2, AP disengagement warnings still active) — the message is AP INHIBIT. If the inhibit comes from an INTERNAL AFCS failure — the message is AP FAIL. If the failure also inhibits the YD, the message is AP/YD FAIL. INHIBIT means "fix the external condition"; FAIL means "AFCS is broken".',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'AP INHIBIT vs AP FAIL decision diagram',
 'AP-INHIBIT-OUT, AP-FAIL-IN. INHIBIT = check external; FAIL = AFCS broken.',
 'On a sim ride: identify the cause from the message before mashing the AP button. AP INHIBIT means look for the cause; AP FAIL is a non-normal.',
 NULL),

-- 5. System — auto pitch trim
(@lesson_id, 50, 'system',
 'Automatic Pitch Trim — Two Speeds, One Threshold',
 'When the AP is engaged, the AFCS auto-trims pitch to keep the AP servo torque near zero — so when you disengage, the trim is correct and there is no surprise. Trim runs at HIGH speed below 180 KCAS (handles flap/gear movements, accelerations, decelerations); LOW speed above 180 KCAS (precision for cruise). The FCECU prioritises manual pitch trim from the wheel switch over AFCS commands — so if you trim while the AP is engaged, the AP DISENGAGES. AP Pitch Trim is also disabled when TCS is active. Outside of AP engagement, the AFCS still auto-trims when flaps are transitioned (Flap Auto Pitch Trim).',
 'animation', '/assets/aircraft/q400/autoflight-flow.svg',
 'Pitch trim speed-schedule diagram',
 '180-FAST-LOW. Below 180 = HIGH; above 180 = LOW. Manual trim = AP off.',
 'A pilot trimming through the AP because the trim "feels off" is creating an unexpected disengagement. If the pitch feels off, disengage cleanly first, then trim.',
 JSON_OBJECT(
   'prompt', 'You trim pitch using the elevator-trim switch on the control wheel while the AP is engaged at 220 KCAS. What happens?',
   'options', JSON_ARRAY(
     'AP keeps flying; AFCS auto-trim absorbs your manual input',
     'AP DISENGAGES — manual pitch trim with AP engaged is treated as an override',
     'Trim command is ignored until AP is disengaged',
     'AP enters TCS mode for 5 seconds'
   ),
   'correct_index', 1,
   'explanation', 'Manual pitch trim with AP engaged is an override. The AP DISENGAGES. Plan disengagement deliberately and trim with hands on the wheel.'
 )),

-- 6. System — Roll Mistrim
(@lesson_id, 60, 'system',
 'Roll Mistrim — Annunciation Only, No Automatic Trim',
 'When the AP is engaged, each FGM monitors the AP roll-servo torque. If torque exceeds a threshold, the FGMs command an amber MISTRIM [TRIM L WING DN] or MISTRIM [TRIM R WING DN] message on the PFD. The Q400 has NO automatic roll trim — the AFCS does not correct roll mistrim. The AP does NOT disengage automatically when the message appears. The crew action: disengage AP, trim laterally to remove the mistrim, re-engage AP. Critical: if you simply punch AP DIS without re-trimming, you may get a sudden roll transient that surprises you in turbulence or near the ground.',
 'video', '/assets/aircraft/q400/autoflight-flow.svg',
 'Roll Mistrim message + crew workflow',
 'ROLL-NO-AUTO. Mistrim = annunciation only. Disengage cleanly after trimming.',
 'On a long sector with crosswind asymmetric loading, mistrim creeps in slowly. Brief the FO: "If we see Mistrim, we disengage with hands on, trim, re-engage."',
 JSON_OBJECT(
   'prompt', 'A MISTRIM [TRIM L WING DN] message appears in cruise with AP engaged. Correct sequence?',
   'options', JSON_ARRAY(
     'AP will auto-trim; ignore the message',
     'Disengage AP cleanly with hands on, trim until message clears, re-engage',
     'Press AP DIS immediately to fly manually',
     'Apply opposite rudder to centre the ball'
   ),
   'correct_index', 1,
   'explanation', 'Mistrim is annunciation only — there is no automatic roll trim. Disengage with hands ON the wheel (anticipate a transient), trim until the message clears, then re-engage AP.'
 )),

-- 7. System — disengage annunciations
(@lesson_id, 70, 'system',
 'Disengagement — Auto vs Manual, Lights and Tones',
 'The AFCS distinguishes how it disengaged. AUTOMATIC disengagement: two red AP DISENG segments on the glareshield FLASH; the PFD shows amber AP DISENGAGED (or AP/YD DISENGAGED) FLASHING; a continuous aural tone sounds. The flashing continues until the crew acknowledges by pressing AP DIS on either control wheel. After acknowledgement the PFD message stays steady for 5 seconds, then clears. MANUAL disengagement (you pressed AP DIS deliberately): amber PFD message for 5 seconds — no flashing, no aural tone. Both AP Disengage Switches lit means the disengagement was caused by FGM power-source failure.',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'Disengage annunciation logic diagram',
 'FLASH-RED-AUTO. Flashing = automatic; steady amber 5-sec = manual.',
 'A red flashing glareshield in cruise is a hands-on event — fly the aircraft first, then read the PFD. Brief the FO that the aural tone is not a distraction; it is the headline.',
 NULL),

-- 8. Normal op — FMA colours
(@lesson_id, 80, 'normal_op',
 'FMA Colours — White Armed, Green Active',
 'The Flight Mode Annunciator at the top of the PFD is the single source of truth for what the AFCS is doing. The colour convention is universal across the Q400: WHITE = a mode is ARMED (waiting for capture). GREEN = a mode is ACTIVE (currently flying). LATERAL armed examples: VOR APP, LNAV, HDGINT (waiting). LATERAL active: HDG, NAV (in capture phase or track phase). VERTICAL armed: ALT SEL (armed for capture). VERTICAL active: VS, IAS, ALT, VNAV (in PATH / FLIGHT LEVEL CHANGE / ALTITUDE CAPTURE / ALTITUDE HOLD submodes). When a mode transitions from white to green, you have just captured.',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'FMA layout with armed/active colour examples',
 'WHITE-ARM-GREEN-LIVE. White waits; green flies.',
 'Brief the FO: "white to green is the call." Anytime the FMA changes colour, callout the new mode so both pilots are aligned.',
 NULL),

-- 9. Normal op — cruise scan
(@lesson_id, 90, 'normal_op',
 'Cruise Scan — What You Watch on the AFCS',
 'In cruise the AFCS scan is brief but disciplined. (1) FMA top-of-PFD: lateral / vertical / armed modes — ARE THE EXPECTED MODES ACTIVE? (2) Selected targets — heading bug, course, altitude, speed — DO THEY MATCH THE PLAN? (3) Trim indicator — pitch trim moving slowly is normal; rapid runaway is not. (4) Mistrim message line — clear or showing TRIM L/R WING DN? (5) AP green annunciation — solid green AP and YD on the PFD. Run this scan every 10 minutes minimum, and any time something visual or auditory changes. The scan catches the trend before the warning fires.',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'Cruise scan checklist diagram',
 '5-item AFCS cruise scan: FMA · targets · trim · mistrim · AP/YD.',
 'A disciplined cruise scan turns most "AP gotchas" into routine adjustments. The scan also keeps the flying pilot mentally engaged on a long sector.',
 NULL),

-- 10. Abnormal — auto disengagement
(@lesson_id, 100, 'abnormal',
 'Automatic Disengagement — Crew Recovery Sequence',
 'You hear the aural tone, you see flashing red AP DISENG segments on the glareshield, and the PFD shows flashing amber AP DISENGAGED. Sequence: HANDS ON the controls (fly the aircraft); PRESS AP DIS on either wheel to acknowledge (this stops the flashing and aural); cross-check attitude, altitude, heading; assess what changed (a system caution, a stall warning, a power-source loss, a TCS hold-too-long). Re-engagement is INHIBITED while the warnings are active — that is by design. After acknowledgement and cross-check, attempt re-engagement. If it inhibits or fails, run the AFCS QRH non-normal.',
 'video', '/assets/aircraft/q400/autoflight-flow.svg',
 'Auto-disengagement crew recovery flow',
 'Hands · Acknowledge · Cross-check · Re-engage (or QRH).',
 'The aural tone is designed to grab attention on the worst day. Train your hands to come to the controls before your mouth says anything.',
 JSON_OBJECT(
   'prompt', 'You experience an automatic AP disengagement at FL220 in cruise. The PFD message and glareshield lights are flashing. After taking the controls, what is the next action?',
   'options', JSON_ARRAY(
     'Re-engage AP immediately',
     'Press AP DIS on either control wheel to acknowledge — clears the flashing and silences the aural tone',
     'Reduce thrust and disconnect the autothrottle',
     'Switch to MAN pressurisation mode'
   ),
   'correct_index', 1,
   'explanation', 'Press AP DIS on either control wheel. Re-engagement is inhibited while warnings are active. After acknowledgement, cross-check attitude/heading/altitude, identify the cause, then attempt re-engagement.'
 )),

-- 11. Abnormal — AFCS PFD failure messages
(@lesson_id, 110, 'abnormal',
 'AFCS Failure Messages — What the PFD Tells You',
 'Each AFCS failure has a dedicated yellow PFD message that flashes for 5 seconds then becomes steady. Most are not resettable except by removing the failure or by AFCS itself failing. AFCS FAIL — the whole system is gone. AP PITCH TRIM FAIL — automatic pitch trim is broken; manual trim must keep up. YD NOT CENTERED — the yaw damper is not at its zero reference. L FD FAIL or R FD FAIL — one Flight Director channel failed; the other still drives its FD. AFCS CONTROLLER INOP — the FGCP is unresponsive. AUTO TRIM FAIL — covers Flap Auto Pitch Trim path. Each requires a QRH non-normal.',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'PFD failure-message catalogue',
 'AFCS FAIL · AP PITCH TRIM FAIL · YD NOT CENTERED · FD FAIL · CTRL INOP · AUTO TRIM FAIL.',
 'Memorise these six messages. On a check ride the instructor may set up any one and your job is to identify and run the correct QRH.',
 NULL),

-- 12. QRH connection — AP/YD failures
(@lesson_id, 120, 'qrh',
 'QRH Connection: AP / YD Failures and Mistrim Recovery',
 'AFCS QRH non-normals cluster into three groups. (1) AP DISENGAGE — the aircraft is now hand-flown; cross-check attitude, trim out the residual mistrim, plan whether to re-engage. (2) AP / YD FAIL or AFCS FAIL — accept loss of automation; brief approach for hand-flown ILS or VOR; consider crew rotation on a long sector. (3) MISTRIM (any axis) — disengage AP, trim manually, re-engage. None of these are time-critical at altitude. They become time-critical close to the ground or in IMC — that is when the discipline of an early diagnosis pays off.',
 'image', '/assets/aircraft/q400/autoflight-flow.svg',
 'AFCS QRH non-normal cluster diagram',
 'AP off = fly. AFCS fail = brief hand-flown. Mistrim = disengage, trim, re-engage.',
 'Practice these in the sim until the response is automatic. AFCS abnormals during line operations are rare; the muscle memory must come from training.',
 NULL),

-- 13. Scenario — captain decision
(@lesson_id, 130, 'scenario',
 'Captain Decision: MISTRIM in Turbulence on Approach',
 'Setup: descending through 5,000 ft on a vectored approach to runway 06L. Light to moderate chop. Suddenly an amber MISTRIM [TRIM R WING DN] appears on the PFD. AP and YD are engaged. The control wheel feels slightly off-centre. Wx is breaking up; you are configured Flap 5, gear up. Glide-slope intercept in 6 nm.\n\nQuestion: continue with AP and accept some lateral imprecision, or disengage now and hand-fly? Decision: disengage AP CLEANLY with hands ON the wheel BEFORE the GS intercept. A roll transient at GS capture would be ugly. Trim to clear the message, brief the FO that hands-on is now the plan, and consider whether to also disconnect the FD if the PFD bars are distracting. A clean hand-flown approach beats an automated approach with a known mistrim.',
 'animation', '/assets/aircraft/q400/autoflight-flow.svg',
 'Mistrim-on-approach decision tree',
 'Mistrim near ground = disengage early, hand-fly, brief.',
 'Mistrim is annunciation only — but a 5-degree roll transient at 200 ft AGL is what kills you. Disengage in cruise; never on short final.',
 JSON_OBJECT(
   'prompt', 'On a vectored ILS at 5,000 ft an amber MISTRIM [TRIM R WING DN] appears with AP engaged. Best decision before glide-slope intercept?',
   'options', JSON_ARRAY(
     'Continue automated; mistrim is annunciation only',
     'Disengage AP cleanly with hands on, trim until message clears, decide whether to hand-fly the approach',
     'Press AP DIS immediately at glide-slope intercept',
     'Switch to MAN pressurisation and continue'
   ),
   'correct_index', 1,
   'explanation', 'Disengage cleanly with hands ON the controls anticipating the roll transient. Trim laterally until the message clears. Brief the FO. A clean hand-flown approach beats an automated approach with a known mistrim.'
 )),

-- 14. Revision — recap
(@lesson_id, 140, 'revision',
 'Lesson Recap: Autoflight in 60 Seconds',
 'Recap:\n  • AFCS = 2 FGMs (FGM1 commands, FGM2 monitors) + 2 APAUs.\n  • Engaging AP also engages YD; YD failure disengages AP.\n  • Engagement inhibits: roll ±45°, pitch ±20°. YD only ±45° roll.\n  • AP INHIBIT = external cause. AP FAIL = internal AFCS failure. AP/YD FAIL if YD also.\n  • Auto pitch trim: HIGH below 180 KCAS, LOW above. Manual trim with AP engaged disengages AP.\n  • Roll Mistrim: annunciation only — NO auto roll trim. Disengage, trim, re-engage.\n  • Disengagement: AUTO = flashing red glareshield + flashing PFD + aural; MANUAL = steady amber PFD 5 sec.\n  • FMA colours: WHITE armed, GREEN active.\n  • PFD failure messages: AFCS FAIL, AP PITCH TRIM FAIL, YD NOT CENTERED, L/R FD FAIL, CONTROLLER INOP, AUTO TRIM FAIL.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'FGM-2-FOR-1 · 45/20-INHIBIT · 180-FAST-LOW · ROLL-NO-AUTO · FLASH-RED-AUTO · WHITE-ARM-GREEN-LIVE',
 'Six mnemonics that cover every Autoflight oral question. Drill them before recurrents.',
 NULL);

UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');

UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND title IN ('AP INHIBIT vs AP FAIL — External vs Internal',
                 'AFCS Failure Messages — What the PFD Tells You');

UPDATE IGNORE lesson_slides
   SET show_beginner = 0,
       show_intermediate = 0
 WHERE lesson_id = @lesson_id
   AND title = 'Captain Decision: MISTRIM in Turbulence on Approach';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
