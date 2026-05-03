-- =============================================================================
-- AviatorTutor — Phase 3: ATA 22 Autoflight — lesson sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'autoflight' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'autoflight-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — AFCS, AP, YD, FD as One System',
 '<p>The Q400 Automatic Flight Control System (AFCS) bundles the Flight Director (FD), Autopilot (AP), Yaw Damper (YD), Automatic Pitch Trim, Roll Mistrim Annunciation, Flight Guidance Control and Flight Mode Annunciation into a single integrated package. The cockpit interface is the Flight Guidance Control Panel (FGCP) on the glareshield; the back-end processors are two Flight Guidance Modules (FGM1 + FGM2) feeding two Autopilot Actuator Units (APAU1 + APAU2). FGM1 commands the actuators, FGM2 monitors them.</p>
<p>Engaging AP automatically engages YD. YD survives without AP. Both FGMs are required for AP and YD; a single FGM is enough to drive a Flight Director.</p>',
 'overview', 10),

(@lesson_id, 'Components — AFCS Hardware and Cockpit Controls',
 '<ul>
  <li><strong>2 × Flight Guidance Modules (FGMs):</strong> independent computation channels. FGM1 commands the actuators; FGM2 monitors. Both required for AP/YD.</li>
  <li><strong>2 × Autopilot Actuator Units (APAUs):</strong> drive pitch + roll servos under FGM1 commands.</li>
  <li><strong>Flight Guidance Control Panel (FGCP):</strong> on the glareshield. AP and YD pushbuttons; mode buttons (HDG, NAV, APPR, BC, IAS, VS, VNAV, ALT, ALT SEL); HSI SEL; NAV SOURCE selector; STBY; course / heading / altitude / IAS / VS rotary knobs.</li>
  <li><strong>2 × AP Disengage Warning Lights:</strong> on the glareshield. Red AP DISENG segments — flash for AUTOMATIC disengagement; do NOT flash for manual disengagement.</li>
  <li><strong>AP Disengage Switches (AP DIS):</strong> one on each control wheel. Disengage AP, reset disengage warnings, reset YD disengage PFD annunciation.</li>
  <li><strong>TCS pushbutton:</strong> on each control wheel. Touch Control Steering — pilot can override AP without disengaging.</li>
  <li><strong>FCECU (Flight Control Electronic Control Unit):</strong> takes pitch trim commands from the AFCS; prioritises manual trim from the wheel switch over AFCS commands.</li>
  <li><strong>Flight Mode Annunciator (FMA):</strong> top of the PFD. Lateral / Vertical armed and active modes. White = armed; Green = active.</li>
</ul>',
 'components', 20),

(@lesson_id, 'Operation — How the AFCS Behaves',
 '<p><strong>Flight Director:</strong> two independent FD channels. In normal (non-Dual) mode, both FGMs use the SAME side data as the PFD selection. In Dual FD mode, FGM1 uses #1 sensors and FGM2 uses #2 sensors. Selection is via the HSI SEL pushbutton, NAV SOURCE switches, EFIS ATT/HDG SOURCE, and EFIS ADC SOURCE (on the ESCP).</p>
<p><strong>Autopilot Pitch Trim:</strong> active when AP engaged. Trims to keep AP servo torque near zero. Two speeds: HIGH below 180 KCAS, LOW above 180 KCAS. Manual pitch trim from the wheel disengages the AP. Disabled when TCS is active.</p>
<p><strong>Flap Auto Pitch Trim:</strong> active when AP NOT engaged and flaps are transitioning. Same FCECU command path.</p>
<p><strong>Roll Mistrim Annunciation:</strong> each FGM monitors AP roll-servo torque. Above threshold → amber MISTRIM message on PFD. AP does NOT auto-disengage. There is NO automatic roll trim function.</p>
<p><strong>Yaw Damper:</strong> commands the rudder for stability augmentation. Engagement inhibited at ±45° roll; disengagement of YD also disengages AP.</p>
<p><strong>Go Around Mode:</strong> activated by GA switches on the power levers. Lateral target: Wings Level. All armed modes disarmed. Deactivated by other vertical mode selection, AP engagement, STBY/HSI SEL, or ADC/AHRS source change.</p>',
 'operation', 30),

(@lesson_id, 'Normal — Cruise Scan and Mode Discipline',
 '<h4>Cruise scan (every 10 minutes minimum)</h4>
<ol>
  <li><strong>FMA:</strong> lateral / vertical / armed modes — match the plan?</li>
  <li><strong>Selected targets:</strong> heading bug, course, altitude, speed/VS — match the clearance?</li>
  <li><strong>Pitch trim indicator:</strong> moving slowly is normal; rapid runaway is not.</li>
  <li><strong>Mistrim line:</strong> clear, or showing TRIM L/R WING DN?</li>
  <li><strong>AP / YD annunciations:</strong> solid green AP and YD on the PFD.</li>
</ol>
<h4>Mode discipline</h4>
<ul>
  <li>White → Green is the call. Anytime the FMA changes colour, callout the new mode.</li>
  <li>Brief mode changes before initiating: "approach mode armed, NAV NAV active."</li>
  <li>If the FMA shows a mode you did not expect, identify why before continuing.</li>
  <li>Disengage decisions are deliberate. Press AP DIS with hands on the controls, never as an afterthought.</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — Failures, Inhibits, and Disengagements',
 '<ul>
  <li><strong>AP INHIBIT (PFD message):</strong> external cause. AHRS/ADC monitoring trip, attitude excursion, GA active, TCS active, manual pitch-trim AP disconnect, stall-warning AP disconnect, AP disengagement warnings still active. Fix the external condition; then engage.</li>
  <li><strong>AP FAIL (PFD message):</strong> internal AFCS failure. AP/YD FAIL if YD also affected. Run the AFCS QRH non-normal.</li>
  <li><strong>Automatic AP disengagement:</strong> red AP DISENG glareshield segments FLASH; PFD shows flashing amber AP DISENGAGED; continuous aural tone. Crew action: hands on controls, press AP DIS to acknowledge (silences tone, stops flash), cross-check, identify cause, attempt re-engagement.</li>
  <li><strong>Manual AP disengagement:</strong> amber PFD message for 5 seconds, NO flashing, NO aural. Pressing AP DIS does not affect this message.</li>
  <li><strong>Both AP DISENG segments lit:</strong> disengagement was caused by FGM power-source failure.</li>
  <li><strong>Roll Mistrim:</strong> AP does NOT auto-disengage. Crew must disengage, trim manually, re-engage.</li>
  <li><strong>Manual pitch trim with AP engaged:</strong> AP DISENGAGES (designed override).</li>
  <li><strong>YD failure:</strong> AP also disengages. Verify YD status before AP re-engagement attempt.</li>
  <li><strong>AFCS PFD failure messages:</strong> AFCS FAIL · AP PITCH TRIM FAIL · YD NOT CENTERED · L FD FAIL · R FD FAIL · AFCS CONTROLLER INOP · AUTO TRIM FAIL — flash yellow 5 seconds then steady; most non-resettable.</li>
</ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — FMA, Glareshield, and PFD',
 '<h4>FMA colours</h4>
<ul>
  <li><strong>WHITE:</strong> mode is ARMED — waiting for capture.</li>
  <li><strong>GREEN:</strong> mode is ACTIVE — currently flying.</li>
  <li><strong>AMBER:</strong> failure or warning message (e.g. AP DISENGAGED, MISTRIM, AFCS FAIL).</li>
</ul>
<h4>Lateral modes</h4>
<ul>
  <li>Armed (white): VOR APP, LNAV, HDGINT, etc.</li>
  <li>Active (green): HDG, NAV, LNAV (capture / track / over-station phases).</li>
  <li>Wings Level (Go Around lateral submode).</li>
</ul>
<h4>Vertical modes</h4>
<ul>
  <li>Armed (white): ALT SEL, etc.</li>
  <li>Active (green): VS, IAS, ALT, VNAV (Path / FL Change / Alt Capture / Alt Hold submodes via FMS).</li>
</ul>
<h4>Disengagement annunciations</h4>
<ul>
  <li><strong>Glareshield AP DISENG segments:</strong> red, flashing for automatic disengagement only.</li>
  <li><strong>PFD AP DISENGAGED message:</strong> amber, flashing for auto / steady 5-sec for manual.</li>
  <li><strong>Aural tone:</strong> continuous until acknowledged (auto only).</li>
</ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers and Boundaries',
 '<ul>
  <li><strong>AP engagement attitude limits:</strong> roll ±45°, pitch ±20°.</li>
  <li><strong>YD engagement attitude limit:</strong> roll ±45°.</li>
  <li><strong>AP Pitch Trim speed schedule:</strong> HIGH below 180 KCAS, LOW above 180 KCAS.</li>
  <li><strong>FGM count required for AP / YD:</strong> 2 (both required).</li>
  <li><strong>FGM count required for FD only:</strong> 1.</li>
  <li><strong>Manual pitch trim with AP engaged:</strong> disengages AP (always).</li>
  <li><strong>YD failure:</strong> always disengages AP.</li>
  <li><strong>Auto-disengagement aural tone:</strong> continuous until acknowledged via AP DIS on either control wheel.</li>
  <li><strong>Manual disengagement PFD message duration:</strong> 5 seconds steady.</li>
</ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>FGM-2-FOR-1</strong> — 2 FGMs (one commands, one monitors) for one AP. FD only needs 1 FGM.</li>
  <li><strong>45/20-INHIBIT</strong> — roll ±45°, pitch ±20° = AP engagement inhibits. YD only ±45° roll.</li>
  <li><strong>AP-INHIBIT-OUT, AP-FAIL-IN</strong> — AP INHIBIT = external cause; AP FAIL = internal AFCS failure.</li>
  <li><strong>180-FAST-LOW</strong> — pitch trim HIGH speed below 180 KCAS, LOW speed above.</li>
  <li><strong>ROLL-NO-AUTO</strong> — Mistrim message only; NO automatic roll trim function.</li>
  <li><strong>FLASH-RED-AUTO</strong> — flashing red glareshield + flashing amber PFD + aural = AUTOMATIC disengagement. Steady amber 5-sec PFD only = MANUAL.</li>
  <li><strong>WHITE-ARM-GREEN-LIVE</strong> — FMA colour convention: armed white, active green.</li>
</ol>
<p>Auto-disengagement chant: <em>"Hands · Acknowledge · Cross-check · Re-engage."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
