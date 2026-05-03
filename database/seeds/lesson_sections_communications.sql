-- =============================================================================
-- AviatorTutor — Phase 4: ATA 23 Communications — lesson sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'communications' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'communications-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — One Umbrella, Three Halves',
 '<p>The Audio and Radio Management System (ARMS) is the architectural umbrella that ties together the Q400 communications package. Three sub-systems live under it: <strong>RCOM</strong> (Radio Communication and Radio Navigation Management) covers the three VHFs, optional HF, and audio integration of all NAV receivers; <strong>PACIS</strong> (Passenger Address and Communication Interphone System) handles cabin comms and the FD↔cabin CALL/EMER paths; <strong>AIS</strong> (Audio Integration System) wires audio paths between ARCDUs, audio control panels, speakers, headsets, and PTT/INPH circuits. Three discrete items not in ARMS but related: CVR, FDR, ELT.</p>',
 'overview', 10),

(@lesson_id, 'Components — ARCDUs, Standby Tuner, Audio Panels, ELT',
 '<ul>
  <li><strong>2 × ARCDU (Audio and Radio Control Display Unit):</strong> ARCDU 1 captain side, ARCDU 2 FO side. Active matrix LCD. Coloured fonts on black background. Active frequency green/white; preset cyan/highlighted-cyan.</li>
  <li><strong>VHF1 Standby Control and Display Unit:</strong> dedicated backup tuner for VHF1 only. 3-position rotary OFF / ON / TEST. Independent of ARCDUs.</li>
  <li><strong>Three VHF Comms (VHF 1, 2, 3):</strong> third VHF (Thompson EVR 76) is dedicated to ACARS data — no voice.</li>
  <li><strong>Optional HF:</strong> long-range comms.</li>
  <li><strong>Control wheel PTT/INPH switches:</strong> 3-position spring-loaded to centre on each control wheel.</li>
  <li><strong>Copilot side panel XMIT/INPH switch.</strong></li>
  <li><strong>Steering panel PTT switch:</strong> momentary action.</li>
  <li><strong>Observer Audio Control Panel:</strong> 13 audio knobs; mechanically-interlocked transmitter keys; INT/RAD switch (3-position spring-to-centre).</li>
  <li><strong>ELT:</strong> Kannad 406 MHz with COSPAS/SARSAT, plus 121.5 / 243 MHz. Remote switch ON / ARMED / RESET & TEST.</li>
  <li><strong>SSCVR:</strong> Solid-state Cockpit Voice Recorder. Records last 2 hours.</li>
  <li><strong>FDR:</strong> Flight Data Recorder. Tested via cockpit GND TEST switch when on the ground.</li>
  <li><strong>ACARS:</strong> data link via 3rd VHF; half-size printer (Allied Signal PTA-45B) below copilot side console.</li>
</ul>',
 'components', 20),

(@lesson_id, 'Operation — Normal Use of the ARMS',
 '<h4>ARCDU operation</h4>
<ul>
  <li>Page architecture: VHF, NAV, audio panels, etc., accessed via side keys.</li>
  <li>Tuning rhythm: side key (highlight preset) → rotary (dial frequency) → side key (swap active/preset).</li>
  <li>5-second timeout: if you stop after the first push and do nothing for 5 seconds, the highlight reverts to plain cyan.</li>
  <li>VHF testing: VHF side key → EXP → TEST side key. 1-second squelch-disable; listen for noise.</li>
</ul>
<h4>Audio path</h4>
<ul>
  <li>PTT routes mic audio to the radio selected on the ARCDU mic/interphone selector.</li>
  <li>PTT mutes flight-deck speakers by 6 dB to prevent feedback.</li>
  <li>INPH routes mic audio to the interphone system; selector must be in SERV/INT or PA position.</li>
  <li>Observer transmitter keys are mechanically interlocked — only one selected at a time.</li>
</ul>
<h4>ACARS</h4>
<ul>
  <li>Down-link: aircraft data (OOOI, position, performance) via dedicated 3rd VHF.</li>
  <li>Up-link: clearances, weather, gate assignments, free-text messages.</li>
  <li>Tuning: ACARS tunes the 3rd VHF itself; no crew action.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Cockpit Setup and Cruise Discipline',
 '<h4>Pre-flight</h4>
<ul>
  <li>Test all three VHFs (VHF · EXP · TEST). Listen for noise on each.</li>
  <li>Verify ELT remote switch in ARMED. Monitor light off (or one long flash every 3 sec at most).</li>
  <li>Test FDR on the ground via GND TEST switch (FLT DATA RECORDER caution should turn on then go out — that confirms the test pass).</li>
  <li>Confirm ARCDU mic/interphone selectors in correct positions per SOP.</li>
</ul>
<h4>Cruise</h4>
<ul>
  <li>Frequency swap rhythm: highlight → dial → swap. Three pushes.</li>
  <li>Cross-check active frequency colour: GREEN = good. White = investigate.</li>
  <li>Service interphone available for cabin coordination via PACIS CALL key.</li>
  <li>Observer Audio Control Panel for jumpseat passenger if applicable.</li>
</ul>
<h4>Post-flight</h4>
<ul>
  <li>Confirm ELT monitor light status. If activated, RESET & TEST momentarily; tech-log entry.</li>
  <li>If incident occurred, pull CVR CB before taxi-in to preserve last 2 hours.</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — ARCDU Loss, Lost Comms, ELT Misfire',
 '<ul>
  <li><strong>Single ARCDU failure:</strong> surviving ARCDU still tunes all radios. Flight crew shares one panel. Defer to QRH.</li>
  <li><strong>Both ARCDUs failed:</strong> VHF1 Standby Control and Display Unit becomes the radio. Switch ON, tune VHF1, declare. NAV audio integration impaired — brief approaches.</li>
  <li><strong>VHF1 Standby Display fail:</strong> use VHF2 only via working ARCDU; if ARCDUs also gone, comms is degraded — declare.</li>
  <li><strong>Lost comms (squawk 7600):</strong> follow lost-comms procedure per local AIM/SOP. Filed route → cleared route → destination. Approach as last expected.</li>
  <li><strong>ELT inadvertent activation:</strong> monitor light flashing 4-second cycle. RESET & TEST momentarily — light goes out. Advise tower; tech log.</li>
  <li><strong>ELT real distress:</strong> set to ON to override inertia switch. Monitor light flashing 4-second cycle confirms transmission.</li>
  <li><strong>FDR caution illuminated in flight:</strong> brief that FDR data is incomplete; engineering on landing.</li>
</ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Lights, Colours, and Annunciators',
 '<ul>
  <li><strong>ARCDU active frequency:</strong> green (valid) or white (invalid/no data).</li>
  <li><strong>ARCDU preset frequency:</strong> cyan (default), black-on-cyan (highlighted/tune window — 5-second timeout).</li>
  <li><strong>ARCDU TEST:</strong> reverse video — black on green during test sequence.</li>
  <li><strong>SQL annunciation during VHF test:</strong> green SQL with diagonal line through it (squelch open).</li>
  <li><strong>VHF1 Standby Tx annunciator:</strong> illuminates each time mic keyed AND RF output detected.</li>
  <li><strong>Steering panel ground crew connection:</strong> FWD (amber) = ground crew at DC external; AFT (amber) = refuel/defuel panel or aft connection.</li>
  <li><strong>ELT monitor light:</strong> ONE long flash every 3 sec = normal armed; series of short flashes = fault; flash every 4 sec = activated.</li>
  <li><strong>FLT DATA RECORDER caution light:</strong> off when recording normally; on briefly during GND TEST and goes out when test passes.</li>
  <li><strong>PTT speaker mute indication:</strong> 6 dB drop in flight-deck speaker output during transmission (audible only).</li>
</ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers and Boundaries',
 '<ul>
  <li><strong>ARCDU preset highlight timeout:</strong> 5 seconds (no action → reverts to plain cyan).</li>
  <li><strong>VHF test duration:</strong> 1 second of squelch-disabled noise per test cycle.</li>
  <li><strong>PTT speaker mute:</strong> 6 dB during transmission (anti-feedback).</li>
  <li><strong>ELT inertia switch:</strong> longitudinal acceleration between 5 and 7 G triggers automatic activation.</li>
  <li><strong>ELT monitor light normal flash:</strong> ONE long flash every 3 seconds.</li>
  <li><strong>SSCVR recording duration:</strong> last 2 hours (continuous loop).</li>
  <li><strong>ELT frequencies:</strong> 121.5, 243, and 406 MHz (Kannad with COSPAS/SARSAT).</li>
  <li><strong>ACARS voice capability:</strong> none (data only on dedicated 3rd VHF).</li>
  <li><strong>Lost-comms transponder code:</strong> 7600.</li>
</ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>ARMS-3-IN-1</strong> — RCOM + PACIS + AIS under one umbrella. CVR/FDR/ELT separate.</li>
  <li><strong>GREEN-VALID-WHITE-NOPE</strong> — ARCDU active frequency green = valid data; white = invalid/no data.</li>
  <li><strong>CYAN-PRESET-5-SEC</strong> — preset cyan; highlight (black-on-cyan) is the tune window; 5-second timeout.</li>
  <li><strong>VHF · EXP · TEST</strong> — 3-step VHF test sequence; 1-second squelch-disable.</li>
  <li><strong>5-7-G-ARMED</strong> — ELT inertia trigger 5–7 G longitudinal.</li>
  <li><strong>3-SEC-FLASH-NORMAL</strong> — ELT monitor light: 1 long flash every 3 sec = normal armed; short flashes = fault.</li>
  <li><strong>3RD-VHF-DATA</strong> — ACARS uses dedicated 3rd VHF; no voice capability.</li>
  <li><strong>2-HOUR-CVR</strong> — SSCVR records last 2 hours.</li>
  <li><strong>7600-LOST</strong> — lost-comms transponder code is 7600.</li>
</ol>
<p>Lost-comms chant: <em>"Standby ON · 121.5 · 7600 · Filed route."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
