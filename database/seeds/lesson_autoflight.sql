-- =============================================================================
-- AviatorTutor — Phase 3 (ATA 22 Autoflight) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'autoflight' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM lessons
 WHERE system_id = @system_id
   AND slug = 'autoflight-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Autoflight — AFCS, AP/YD/FD, Pitch Trim, Mistrim, and the FMA',
     'autoflight-overview',
     'overview',
     'Two Flight Guidance Modules (FGMs) — one commanding, one monitoring — feed two Autopilot Actuator Units (APAUs) that drive the controls under crew oversight. AP engages with YD; YD survives without AP. Pitch trim is automatic and speed-scheduled at 180 KCAS; roll mistrim is annunciated, never auto-corrected. Disengage discipline is your protection against trim transients you do not want to fly through.',
     '<p>Autoflight is the system you spend the most time observing and the least time flying with — but the second it disagrees with you, you have to be on top of it. This lesson covers the architecture (two FGMs, two APAUs, dual-channel command/monitor logic), the engagement and inhibit conditions, the difference between AP INHIBIT (external) and AP FAIL (internal), the two-speed automatic pitch trim, the no-auto-trim-on-roll behaviour of the Mistrim warning, and the FMA colour conventions you scan in cruise.</p>',
     JSON_ARRAY(
       'Two Flight Guidance Modules (FGM1 commands, FGM2 monitors) plus two Autopilot Actuator Units (APAUs)',
       'AP engagement automatically engages the Yaw Damper. YD failure disengages the AP. Engaging AP requires both FGMs working',
       'AP engagement inhibited if roll attitude exceeds ±45° or pitch attitude exceeds ±20°. YD engagement inhibited at ±45° roll',
       'AP INHIBIT = external cause (AHRS/ADC failure, attitude excursion, GA, TCS, stall-warning disconnect, etc.)',
       'AP FAIL = internal AFCS failure. AP/YD FAIL if YD also affected',
       'Automatic Pitch Trim runs at HIGH speed below 180 KCAS, LOW speed above. Manual pitch trim with AP engaged disengages the AP',
       'Roll Mistrim message is annunciation-only; the AFCS does NOT auto-trim roll. Pilot must disengage AP, trim manually, re-engage',
       'FMA colours: WHITE = armed mode, GREEN = active mode',
       'Auto disengagement = red flashing AP DISENG glareshield lights + amber PFD message + continuous aural tone. Manual disengagement = amber PFD only, 5 seconds, no flash'
     ),
     JSON_ARRAY(
       'Two FGMs are required for AP and YD operation. Single FGM is enough only for the FD.',
       'TCS (Touch Control Steering) lets the pilot override the AP without disengaging — but pitch-trim function is disabled while TCS is active.',
       'Reset auto-disengagement warnings using the AP DIS switch on either control wheel BEFORE attempting re-engagement.',
       'Roll mistrim with a large servo torque means a significant control-wheel force will appear at AP disengagement. Brief the disengagement.',
       'Go Around mode commands Wings Level laterally; deactivated by any other vertical mode selection or AP re-engagement.'
     ),
     JSON_ARRAY(
       'Engagement attitude limits: roll ±45°, pitch ±20° — students often quote ±30° from non-Q400 jets.',
       'Pitch trim speed schedule: HIGH below 180 KCAS, LOW above. Reverse this and you get the wrong answer.',
       'Roll mistrim: there is NO automatic roll trim. Common mistake is to assume both axes auto-trim.',
       'Automatic vs manual disengagement: only AUTO is announced by the red glareshield lights AND the aural tone. Manual is PFD-only, 5 seconds.',
       'AP DISENG switches reset warnings — and also reset the YD disengage PFD annunciation.'
     ),
     10,
     1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'autoflight-overview';
