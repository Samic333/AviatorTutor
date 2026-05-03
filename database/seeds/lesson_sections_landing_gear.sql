-- =============================================================================
-- AviatorTutor — Phase 11: ATA 32 Landing Gear — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'landing-gear' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'landing-gear-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Architecture and Hydraulic Allocation',
 '<p>The Q400 landing gear is a tricycle dual-wheel retractable installation: mains aft into the nacelles, nose forward into the nose section. Operation is electrically controlled and hydraulically powered. <strong>No.2 hydraulic system</strong> drives gear extension, retraction, and nosewheel steering. <strong>No.1 hydraulic system</strong> drives the multi-disc anti-skid brakes. The PTU (Power Transfer Unit) backs up No.2. The PSEU (Proximity Sensor Electronics Unit) is the brain — it monitors weight-on-wheels and gear positions, and provides signals to the ASCU (Anti Skid Control Unit) for brake logic. Two independent extension paths exist: normal hydraulic, and alternate extension via INHIBIT switch + RELEASE door + EXTENSION door + hand pump. Two independent brake paths exist: normal (No.1, anti-skid) and emergency/park (No.2 or accumulator, no anti-skid, no differential). Steering modes are layered by airspeed and switch position.</p>',
 'overview', 10),

(@lesson_id, 'Components — Gear, Doors, Brakes, Steering',
 '<ul>
  <li><strong>Tricycle dual-wheel gear:</strong> 2 mains + nose. All retractable.</li>
  <li><strong>Main gear retraction direction:</strong> AFT into the nacelles.</li>
  <li><strong>Nose gear retraction direction:</strong> FORWARD into the nose section.</li>
  <li><strong>Hydraulic gear doors:</strong> nose forward door (closes hydraulically after retract), main aft doors (close hydraulically after retract).</li>
  <li><strong>Mechanical doors:</strong> aft nose doors linked to nose gear, forward main doors linked to main gear. Open/close with the gear.</li>
  <li><strong>PSEU:</strong> Proximity Sensor Electronics Unit. Monitors WOW + gear position; controls sequencing valves; supplies signals to ASCU and FCECU for spoiler/brake logic.</li>
  <li><strong>PTU:</strong> Power Transfer Unit. Backup hydraulic to No.2 system. Activated when flaps move out of 0° (per Phase 7 flight controls).</li>
  <li><strong>Anti-skid multiple-disc brakes:</strong> one unit per main wheel. No.1 hydraulic system.</li>
  <li><strong>ASCU:</strong> Anti Skid Control Unit. Modulates brake pressure to prevent wheel lock-up.</li>
  <li><strong>EMERG BRAKE lever:</strong> on engine-control quadrant. Operates against a spring; pull-back proportional to braking. PARK detent at full back.</li>
  <li><strong>Park brake accumulator:</strong> charged from No.2 hyd. ~6 applications when fully charged. 500 PSI minimum before engine start.</li>
  <li><strong>Park brake hand pump:</strong> right main wheel well. Increases accumulator pressure manually.</li>
  <li><strong>Steering Hand Control (tiller):</strong> pilot side console. Self-centering. Moves nosewheel ±70° (low-speed taxi).</li>
  <li><strong>Rudder pedals (steering):</strong> ±8° nosewheel deflection (high-speed taxi, T-O, landing roll).</li>
  <li><strong>SCU:</strong> Steering Control Unit. Receives commands from tiller/rudder, drives steering motor and actuator. Triggers passive caster mode on fault or angle >70°.</li>
  <li><strong>Nose gear ground lock control handle:</strong> on fuselage. IN flush = unlocked. OUT not rotated = downlock disengaged. OUT rotated CW = downlock engaged. Used for towing/maintenance.</li>
  <li><strong>Alternate gear extension components:</strong> LANDING GEAR INHIBIT switch (overhead, guarded), MAIN LANDING GEAR ALTERNATE RELEASE door (overhead), MAIN L/G RELEASE handle (behind that door), LANDING GEAR ALTERNATE EXTENSION door (floor), NOSE L/G RELEASE handle, hand pump socket (floor) + pump handle (behind copilot seat), DOWNLOCK VERIFICATION toggle.</li>
  <li><strong>Tire fill pressure gauges:</strong> customer option. Integral with inflation valve. Dial face with shaded proper-inflation band including 5% tolerance.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — Sequences and Logic',
 '<h4>Retraction sequence</h4>
<ol>
  <li>Selector lever to UP (LOCK RELEASE held). No.2 hyd applied to retract side.</li>
  <li>Nose forward + main aft hydraulic doors open.</li>
  <li>Mains retract aft; nose retracts forward.</li>
  <li>Aft nose doors (mech-linked) close with retracting nose. Forward main doors (mech-linked) close with retracting mains.</li>
  <li>After gear up, forward nose door closes hydraulically; aft main doors close hydraulically.</li>
  <li>Indications: red unsafe and amber HANDLE light during retraction; green LEFT/NOSE/RIGHT extinguish; amber door advisories illuminate; once gear locked up + doors closed, all advisories extinguish.</li>
  <li>Gear held mechanically by uplocks; hydraulic pressure removed.</li>
</ol>
<h4>Extension sequence</h4>
<ol>
  <li>Selector to DN (LOCK RELEASE held). No.2 hyd applied to extend side via solenoid selector valve.</li>
  <li>Hydraulic doors open. Mains and nose extend.</li>
  <li>Hydraulic forward nose and aft main doors close after gear is down + locked.</li>
  <li>Indications: red unsafe + amber HANDLE during extension; amber door advisories on; when gear down + locked, red + HANDLE extinguish; green LEFT/NOSE/RIGHT illuminate; when doors close, door advisories extinguish.</li>
  <li>Continuous hyd pressure on the gear when down + locked, but PRIMARY DOWNLOCK is by mechanical overcenter locks.</li>
</ol>
<h4>Nosewheel steering modes</h4>
<ul>
  <li><strong>Low-speed taxi:</strong> hand control / tiller, ±70°. STEERING switch at STEERING.</li>
  <li><strong>High-speed taxi / T-O / landing roll:</strong> rudder pedals only, ±8°.</li>
  <li><strong>Auto-centering:</strong> nosewheel auto-centers before retraction.</li>
  <li><strong>Passive caster:</strong> ±120°. Triggered by angle >70°, SCU fault, or STEERING OFF. Differential braking + power for directional control.</li>
  <li><strong>Reverse taxi:</strong> STEERING ON, but NO tiller or pedal input.</li>
</ul>
<h4>Anti-skid braking</h4>
<ul>
  <li>ANTI SKID switch ON: arms above 10 kts wheel speed.</li>
  <li>Self-test prevented above 17 kts wheel speed.</li>
  <li>TEST on ground: INBD/OUTBD ANTISKID caution lights for 6 sec.</li>
  <li>TEST in air, gear extended/locked: caution lights for 3 sec.</li>
  <li>5-sec brake delay if wheels haven''t spun up after WOW; immediately cancelled when wheel speed >35 kts.</li>
</ul>
<h4>Alternate gear extension</h4>
<ol>
  <li>Set LANDING GEAR INHIBIT switch to INHIBIT — isolates all hyd from gear.</li>
  <li>Open MAIN LANDING GEAR ALTERNATE RELEASE door (overhead). Mechanically opens bypass valve.</li>
  <li>Pull MAIN L/G RELEASE handle fully — releases main doors + uplocks. Mains free-fall (may not fully extend).</li>
  <li>Open LANDING GEAR ALTERNATE EXTENSION door (floor). Operates MLG alternate selector valve.</li>
  <li>Pull NOSE L/G RELEASE handle — nose gear free-falls; airflow assists.</li>
  <li>If MLG not down/locked, insert hand pump handle (from behind copilot seat) into socket; pump until handle is stiff.</li>
  <li>Set ALTERNATE DOWNLOCK VERIFICATION switch to AFT — three green downlock verification lights illuminate to confirm.</li>
  <li>Leave BOTH doors fully open after extension.</li>
</ol>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Taxi, T-O, Landing Roll',
 '<h4>Pre-flight</h4>
<ul>
  <li>Walk-around: tire pressure (visual via gauges if fitted), wheel/brake assembly, gear pin in place if required by ops.</li>
  <li>Park brake set, accumulator pressure ≥ 500 PSI on PK BRK indicator (PSA on MFD).</li>
  <li>Confirm gear handle DN; LEFT/NOSE/RIGHT green; no amber DOOR or HANDLE.</li>
  <li>ANTI SKID switch ON and TEST: caution lights cycle for 6 sec then extinguish.</li>
  <li>Confirm STEERING switch STEERING (forward taxi) once towbar disconnected.</li>
</ul>
<h4>Taxi</h4>
<ul>
  <li>Hand-control tiller for tight turns (±70°). Rudder pedals for runway-line corrections.</li>
  <li>Differential braking + power if needed.</li>
</ul>
<h4>Take-off roll</h4>
<ul>
  <li>STEERING via rudder pedals only (±8°). No tiller above ~30 kts ground speed (per company SOP).</li>
  <li>Gear UP after positive rate of climb: hold LOCK RELEASE, select UP. Verify red unsafe momentarily; doors and gear cycle; all advisories extinguish.</li>
</ul>
<h4>Approach + landing</h4>
<ul>
  <li>Gear DN before glidepath intercept (per SOP). Verify three green; no red; no amber DOOR/HANDLE.</li>
  <li>ANTI SKID confirmed ON.</li>
  <li>After touchdown, brake pedals as required. Anti-skid modulates. 5-sec delay only if wheels haven''t spun up.</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — Failures, Alt Extension, NOSE STEERING',
 '<ul>
  <li><strong>LDG GEAR INOP caution:</strong> hydraulic sequencing valve fault or PSEU unable to control. Run alternate gear extension.</li>
  <li><strong>Loss of No.2 hydraulic pressure:</strong> primary gear extension impossible. Run alternate extension. Note: nosewheel steering is also lost — taxi via differential braking.</li>
  <li><strong>Gear indication failure:</strong> red unsafe and/or green down-and-locked don''t agree with selector. Use ALTERNATE DOWNLOCK VERIFICATION switch to confirm via three green floor lights.</li>
  <li><strong>NOSE STEERING caution:</strong> SCU fault with STEERING ON, OR hyd pressure detected with STEERING OFF. Switch to caster mode if SCU fault; investigate hyd-detected case.</li>
  <li><strong>Anti-skid fault (INBD/OUTBD ANTISKID caution):</strong> ASCU has detected fault. Brake pressure no longer modulated on the affected side. Run QRH; brief approach for asymmetric or longer landing roll.</li>
  <li><strong>Park brake low (PK BRK indicator below 500 PSI):</strong> hand pump in right main wheel well, or run SPU + PTU on AC.</li>
  <li><strong>EMERG BRAKE use:</strong> pull lever back proportional to required braking. Note no differential, no anti-skid. Accumulator gives ~6 applications.</li>
  <li><strong>Steering loss in flight:</strong> minimal impact. After landing, taxi via differential braking + power.</li>
  <li><strong>Tow with STEERING ON:</strong> can damage SCU if towbar applies torque. Always confirm STEERING OFF before towing.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Lights, Tones, Switches',
 '<ul>
  <li><strong>Gear advisory lights (LANDING GEAR control panel):</strong> L./N./R. DOOR amber (door open); LEFT/NOSE/RIGHT green (down + locked); LEFT/NOSE/RIGHT red (unsafe); HANDLE amber (handle vs gear disagree).</li>
  <li><strong>LANDING GEAR selector lever:</strong> two-position (UP / DN). LOCK RELEASE button held to enable.</li>
  <li><strong>HORN MUTE/TEST switch:</strong> two-position momentary. TEST sounds tone over speakers; MUTE silences tone in case-3 conditions only.</li>
  <li><strong>Gear warning tone:</strong> three trigger groups (see Operation). Only case 3 (single-engine fail <156 KIAS) may be muted.</li>
  <li><strong>STEERING switch:</strong> STEERING / OFF.</li>
  <li><strong>NOSE STEERING caution:</strong> SCU fault with switch ON, or hyd pressure detected with switch OFF.</li>
  <li><strong>ANTI SKID switch:</strong> ON / TEST momentary. INBD / OUTBD ANTISKID caution lights.</li>
  <li><strong>EMERG BRAKE lever:</strong> proportional pull. PARK detent at full back.</li>
  <li><strong>PARKING BRAKE caution:</strong> illuminates when EMERG BRAKE lever at PARK detent.</li>
  <li><strong>PK BRK indicator:</strong> on PSA of the MFD. Park brake accumulator pressure.</li>
  <li><strong>LDG GEAR INOP caution:</strong> hydraulic sequencing valve fault or PSEU control loss.</li>
  <li><strong>LANDING GEAR INHIBIT switch:</strong> INHIBIT / NORMAL. Guarded.</li>
  <li><strong>ALTERNATE DOWNLOCK VERIFICATION switch:</strong> AFT activates 3 green floor downlock verification lights.</li>
  <li><strong>MAIN L/G RELEASE handle:</strong> spring-loaded to stowed position. Pulled fully out releases main doors + uplocks.</li>
  <li><strong>NOSE L/G RELEASE handle:</strong> spring-loaded to stowed. Pulled fully out releases nose doors + uplock.</li>
  <li><strong>NOSE GEAR GND LOCK CONTROL HANDLE:</strong> IN flush = unlocked / OUT not rotated = disengaged / OUT rotated CW = engaged.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Hydraulic source for gear:</strong> No.2 (extension/retraction + nosewheel steering).</li>
  <li><strong>Hydraulic source for normal brakes:</strong> No.1 (anti-skid multi-disc).</li>
  <li><strong>Hydraulic source for emergency/park brake:</strong> No.2 or accumulator.</li>
  <li><strong>Park brake accumulator capacity:</strong> approximately 6 applications fully charged.</li>
  <li><strong>Park brake minimum pressure before engine start:</strong> 500 PSI.</li>
  <li><strong>Nosewheel steering — hand control:</strong> ±70° (low-speed taxi).</li>
  <li><strong>Nosewheel steering — rudder pedals:</strong> ±8° (high-speed taxi, T-O, landing roll).</li>
  <li><strong>Nosewheel passive caster mode:</strong> ±120°.</li>
  <li><strong>Anti-skid arming:</strong> wheel speed > 10 kts.</li>
  <li><strong>Anti-skid self-test prevented:</strong> wheel speed > 17 kts.</li>
  <li><strong>Anti-skid 5-sec brake delay cancellation:</strong> wheel speed > 35 kts.</li>
  <li><strong>Anti-skid TEST duration on ground:</strong> 6 seconds.</li>
  <li><strong>Anti-skid TEST duration in air:</strong> 3 seconds (gear extended).</li>
  <li><strong>Gear warning tone airspeed threshold:</strong> KIAS < 156.</li>
  <li><strong>Gear warning tone RA threshold:</strong> < 1053 ft (321 m) if RA valid.</li>
  <li><strong>Gear warning tone flap threshold:</strong> > 8.5° (case 1).</li>
  <li><strong>Engine torque threshold (case 1):</strong> < 50% on either engine.</li>
  <li><strong>PLA threshold (RATING detent):</strong> below RATING detent.</li>
  <li><strong>PLA threshold (FLT IDLE +12°):</strong> below FLT IDLE +12°.</li>
  <li><strong>Tire pressure gauge band:</strong> shaded "proper inflation +5% tolerance" on dial face.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>N2-GEAR-N1-BRAKES</strong> — No.2 hyd for gear + steering; No.1 hyd for normal brakes; No.2/accumulator for emergency/park.</li>
  <li><strong>NOSE-FWD-MAINS-AFT</strong> — nose retracts forward; mains retract aft.</li>
  <li><strong>70-HAND-8-PEDAL-120-CASTER</strong> — steering: tiller ±70°, pedals ±8°, caster ±120°.</li>
  <li><strong>3-DOOR-ALT-EXTEND</strong> — alternate gear extension: INHIBIT switch + RELEASE door + EXTENSION door + hand pump.</li>
  <li><strong>156-KIAS-1053-RA</strong> — gear warning tone airspeed and RA thresholds.</li>
  <li><strong>3-TONE-GROUPS-MUTE-3</strong> — three trigger groups for gear warning tone; ONLY case 3 (single-engine fail) may be muted.</li>
  <li><strong>10-17-35-BRAKE</strong> — anti-skid arming 10 kts; self-test prevented above 17 kts; 5-sec delay cancels at 35 kts.</li>
  <li><strong>5-SEC-BRAKE-DELAY</strong> — 5-sec brake delay if wheels haven''t spun up after WOW.</li>
  <li><strong>6-APP-500-PSI-PARK</strong> — accumulator gives ~6 applications; minimum 500 PSI park brake before start.</li>
  <li><strong>FREE-FALL-PUMP-LOCK</strong> — alternate extension: gear free-falls; hand-pump completes MLG downlock.</li>
  <li><strong>BOTH-DOORS-OPEN-AFTER</strong> — both alt-extension doors must be left FULLY OPEN after.</li>
  <li><strong>STEERING-ON-NO-INPUT-REVERSE</strong> — reverse taxi: STEERING ON, NO tiller or pedal input.</li>
  <li><strong>3-GREEN-FLOOR-LIGHTS</strong> — alternate downlock verification: 3 green floor lights confirm downlock.</li>
 </ol>
<p>Gear-fail chant: <em>"INHIBIT · RELEASE main · EXTEND nose · pump · verify 3 green floor."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
