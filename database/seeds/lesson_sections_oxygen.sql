-- =============================================================================
-- AviatorTutor — Phase 14: ATA 35 Oxygen — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'oxygen' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'oxygen-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Fixed, Portable, PBE',
 '<p>The Q400 oxygen system is a layered defence. There is a fixed crew system for the flight deck (3 full-face masks for pilot, copilot, observer), a portable passenger oxygen system for the cabin (cylinders kept in the cabin for emergency use), Protective Breathing Equipment (PBE) for both flight crew and cabin attendants for use in low-oxygen environments such as cabin fires or smoke, and first-aid oxygen kept in the passenger compartment for medical use. The system is sized to handle the standard Q400 depressurisation profile: a 4-minute descent to 14,000 ft followed by 116 minutes at 14,000 ft of level flight.</p>',
 'overview', 10),

(@lesson_id, 'Components — Cylinder, Masks, Regulator, Indicators',
 '<ul>
  <li><strong>Crew oxygen cylinder:</strong> single common cylinder in the right lower nose compartment.</li>
  <li><strong>Burst disc:</strong> green, on the right exterior of the nose. Ejects on over-pressurisation (visible from outside as a maintenance/safety indicator).</li>
  <li><strong>Crew masks:</strong> 3 full-face microphone-equipped masks (pilot + copilot + observer). Stowed in cups on bulkhead behind pilot/copilot seats.</li>
  <li><strong>Observer mask supply:</strong> dual outlet on the copilot oxygen supply line.</li>
  <li><strong>Mask outlets:</strong> cross-compatible — masks can be plugged into another outlet if one fails.</li>
  <li><strong>Regulator:</strong> diluter-demand, on each mask. Three-position knob: NORM / 100% / EMER.</li>
  <li><strong>Inflatable harness:</strong> on each mask, controlled by a red harness inflation button (momentary action).</li>
  <li><strong>In-line pressure indicator:</strong> on the mask supply hose. GREEN good / RED low.</li>
  <li><strong>Cylinder pressure gauge:</strong> on the cylinder itself.</li>
  <li><strong>Flight-deck pressure gauge:</strong> lighted, on the COPILOT side console.</li>
  <li><strong>Cylinder ON-OFF knob:</strong> on the cylinder. Off → mask supply atmospheric; gauge still reads bottle.</li>
  <li><strong>Pressure regulator + relief valve + charging valve:</strong> on the cylinder.</li>
  <li><strong>Quick disconnect fitting:</strong> on the supply line.</li>
  <li><strong>Portable passenger O2 cylinders:</strong> kept in the cabin for cabin attendant/passenger use.</li>
  <li><strong>PBE units:</strong> Protective Breathing Equipment for flight deck crew and cabin attendants. For low-O2 environments.</li>
  <li><strong>First aid O2:</strong> in passenger compartment for medical use.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — Donning, Regulator Modes, Indicators',
 '<h4>Donning</h4>
<ul>
  <li>Mask in stowage cup on bulkhead behind pilot/copilot seat.</li>
  <li>Pull mask out by inflatable harness; press red harness inflation button — harness inflates with O2 pressure to fit over head.</li>
  <li>Adjust over face; release button to deflate harness around head — secure fit.</li>
  <li>Less than 5 seconds donning time target.</li>
</ul>
<h4>Regulator modes</h4>
<ul>
  <li><strong>NORM:</strong> automatic air/oxygen mixture. Mix varies with cabin altitude. Default for normal supplemental use at altitude.</li>
  <li><strong>100%:</strong> regulator supplies 100% oxygen regardless of cabin altitude. Used when air contamination is a concern.</li>
  <li><strong>EMER:</strong> 100% oxygen at slight POSITIVE pressure. Used for cabin smoke / fire — positive pressure flushes contaminants out around the mask seal. Also purges smoke from smoke goggles. CAUTION: keeping in EMER continuously will DEPLETE the cylinder.</li>
</ul>
<h4>Pressure indicators</h4>
<ul>
  <li><strong>Cylinder gauge:</strong> on the cylinder itself. Continuous reading.</li>
  <li><strong>Flight-deck gauge:</strong> on COPILOT side console, lighted. Shows pressure available to masks.</li>
  <li><strong>In-line pressure indicator:</strong> on supply hose. GREEN with correct pressure; RED if low.</li>
  <li>Cylinder turned OFF: available mask pressure reduced to atmospheric; flight-deck gauge continues to show BOTTLE pressure.</li>
</ul>
<h4>Outlet failover</h4>
<ul>
  <li>Masks plug into outlets. If an outlet fails, plug the mask into a working outlet.</li>
  <li>Single mask failure: observer mask can be used by either crew member.</li>
</ul>
<h4>Smoking</h4>
<ul>
  <li>Smoking is NOT permitted when oxygen is in use (any mode). Pure O2 + ignition source = fire risk.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Cruise, Top-of-Descent',
 '<h4>Pre-flight</h4>
<ul>
  <li>Verify cylinder pressure on the flight-deck gauge — within company minimum.</li>
  <li>Check 3 masks in stowage; in-line pressure indicators GREEN.</li>
  <li>Test mask donning briefly per company SOP — pull, press red button, fit, release.</li>
  <li>Verify regulator at NORM (default for normal flight).</li>
  <li>Brief F/A on cabin O2 layout, PBE locations, first aid O2 location.</li>
</ul>
<h4>Cruise</h4>
<ul>
  <li>System is passive — pressurisation handles cabin altitude. O2 not in use.</li>
  <li>Periodic check (e.g. once per leg): cylinder gauge ≥ company minimum, in-line indicators GREEN.</li>
</ul>
<h4>Top-of-descent</h4>
<ul>
  <li>O2 system remains passive unless an event has occurred. Routine descent does not require O2 use.</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — Depressurisation, Smoke, Mask Failures',
 '<ul>
  <li><strong>Depressurisation (rapid):</strong> oxygen masks ON immediately. Regulator: 100% or NORM per cabin altitude. Initiate emergency descent to 14,000 ft within 4 minutes (system capacity). Cabin announcement; F/A check passenger O2; brief approach.</li>
  <li><strong>Cockpit smoke / fire:</strong> oxygen masks ON, regulator EMER position — 100% O2 at positive pressure, purges smoke goggles. Smoke goggles ON. Crew comm via mask mic + audio connector. Run QRH SMOKE / FUMES non-normal. Note: EMER depletes cylinder fast — manage time.</li>
  <li><strong>Loss of cylinder:</strong> no fixed crew oxygen. Single point of failure. Plan immediate descent to safe altitude (14,000 ft or below); divert as needed.</li>
  <li><strong>Mask failure (one):</strong> use observer mask. Plug into available outlet (cross-compatible).</li>
  <li><strong>Outlet failure:</strong> plug mask into another outlet. Designed for cross-compatibility.</li>
  <li><strong>In-line indicator RED:</strong> verify supply hose connection at the mask + outlet. If still red, switch to another outlet.</li>
  <li><strong>Burst disc ejected (visible during pre-flight inspection):</strong> cylinder over-pressurisation event has occurred. Maintenance write-up; do not dispatch with crew O2 system inoperative.</li>
  <li><strong>EMER mode left on:</strong> cylinder will deplete. Return to NORM or 100% as soon as the smoke/fire event is contained.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Gauges, Indicators, Burst Disc',
 '<ul>
  <li><strong>Cylinder pressure gauge:</strong> on the cylinder. Reads bottle pressure continuously.</li>
  <li><strong>Flight-deck oxygen pressure gauge:</strong> lighted, on COPILOT side console. Shows pressure available to masks. Note: also shows bottle pressure if cylinder is off.</li>
  <li><strong>In-line pressure indicator:</strong> on supply hose. GREEN good / RED low.</li>
  <li><strong>Mask pressure indicator (red/green):</strong> mask-mounted indicator. Red = low oxygen pressure; green = minimum pressure available.</li>
  <li><strong>Green burst disc:</strong> right side exterior of nose. Ejected = over-pressurisation event has occurred.</li>
  <li><strong>Regulator knob (rotary):</strong> NORM / 100% / EMER. Detents at each position.</li>
  <li><strong>Harness inflation button (red, momentary):</strong> on regulator. Push to inflate; release to deflate.</li>
  <li><strong>Cylinder ON-OFF knob:</strong> at the cylinder.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Crew masks total:</strong> 3 (pilot + copilot + observer).</li>
  <li><strong>Crew oxygen cylinder count:</strong> 1 (single common).</li>
  <li><strong>Cylinder location:</strong> right LOWER nose compartment.</li>
  <li><strong>Burst disc location:</strong> right exterior of nose. Colour: GREEN.</li>
  <li><strong>Flight-deck gauge location:</strong> COPILOT side console (lighted).</li>
  <li><strong>System capacity — descent:</strong> to 14,000 ft in 4 minutes.</li>
  <li><strong>System capacity — level flight:</strong> 116 minutes at 14,000 ft.</li>
  <li><strong>Donning time target:</strong> less than 5 seconds.</li>
  <li><strong>Regulator positions:</strong> 3 (NORM / 100% / EMER).</li>
  <li><strong>Observer mask supply outlet type:</strong> dual outlet on copilot oxygen supply line.</li>
  <li><strong>EMER mode pressure:</strong> 100% O2 at slight POSITIVE pressure.</li>
  <li><strong>Pressure indicator colours:</strong> GREEN good / RED low.</li>
  <li><strong>Mask outlet compatibility:</strong> cross-compatible — any mask can plug into any outlet.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>3-MASKS-1-CYLINDER</strong> — 3 crew masks (pilot/copilot/observer) on 1 single cylinder.</li>
  <li><strong>14000-IN-4-LEVEL-116</strong> — descent to 14,000 in 4 min + level flight at 14,000 for 116 min.</li>
  <li><strong>5-SEC-DON</strong> — mask donning target less than 5 seconds via inflatable harness.</li>
  <li><strong>NORM-100-EMER</strong> — 3 regulator positions: NORM (mix), 100% (pure), EMER (positive + smoke goggle purge).</li>
  <li><strong>EMER-DEPLETES</strong> — keeping regulator in EMER continuously will deplete the cylinder.</li>
  <li><strong>RIGHT-NOSE-CYLINDER</strong> — cylinder in RIGHT LOWER nose compartment.</li>
  <li><strong>GREEN-BURST-RIGHT</strong> — green burst disc on RIGHT exterior of nose.</li>
  <li><strong>COPILOT-CONSOLE-GAUGE</strong> — flight-deck gauge on COPILOT side console.</li>
  <li><strong>OFF-ATMO-GAUGE-BOTTLE</strong> — cylinder OFF reduces mask pressure to atmospheric, but gauge still reads bottle.</li>
  <li><strong>NO-SMOKE-O2</strong> — smoking NOT permitted when oxygen in use.</li>
  <li><strong>OBSERVER-DUAL-COPILOT</strong> — observer mask plugs into DUAL outlet on copilot supply line.</li>
  <li><strong>PBE-FLT-CABIN</strong> — Protective Breathing Equipment for both flight deck and cabin attendants.</li>
 </ol>
<p>Smoke chant: <em>"MASK ON · EMER · 100% positive · GOGGLES PURGE."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
