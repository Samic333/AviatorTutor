-- =============================================================================
-- AviatorTutor — Phase 10: ATA 31 Indicating & Recording — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'indicating-recording' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'indicating-recording-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — EIS, Sub-systems, Reversion',
 '<p>The Q400 cockpit visual interface is the Electronic Instrument System (EIS), built around five identical interchangeable LCD Display Units. The EIS divides into two sub-systems: <strong>EFIS</strong> (Electronic Flight Instrument System) using the four EFIS DUs (PFD1 + MFD1 + MFD2 + PFD2) for primary flight and navigation, and <strong>ESID</strong> (Engine and System Integrated Displays) using the three system DUs (MFD1 + MFD2 + ED) for engine and aircraft system parameters. The MFDs are shared between EFIS and ESID. Reversion logic is sophisticated: any DU can be selected to display the content of another, and the ESCP system pushbuttons let you call up specific system pages. The Central Warning System layers on top with Master Warning + Master Caution + a Warning Tone Generator, plus GPWS, TCAS, and stall warning. Recording is two clocks driving a Flight Data Recorder and a Cockpit Voice Recorder, synchronised on real time.</p>',
 'overview', 10),

(@lesson_id, 'Components — Display Units, Control Panels, Recorders',
 '<ul>
  <li><strong>Display Units (DU):</strong> 5 total — PFD1, MFD1, ED, MFD2, PFD2. All IDENTICAL and INTERCHANGEABLE.</li>
  <li><strong>EFIS:</strong> 4 DUs (PFD1 + MFD1 + MFD2 + PFD2). Primary flight + navigation.</li>
  <li><strong>ESID:</strong> 3 DUs (MFD1 + MFD2 + ED). Engine + system pages.</li>
  <li><strong>EFCP1 / EFCP2:</strong> EFIS Control Panels, one per side. EFCP1 controls PFD1 + MFD1; EFCP2 controls PFD2 + MFD2.</li>
  <li><strong>ESCP:</strong> Engine and System Integrated Display Control Panel. One shared. Controls MFD1 + MFD2 + ED. MFD1/MFD2 4-position rotary (PFD/NAV/SYS/ENG), system pushbuttons (ELEC/ENG/FUEL/DOORS/ALL), ED brightness.</li>
  <li><strong>ICP1 / ICP2:</strong> Index Control Panels, one per side.</li>
  <li><strong>FDPS:</strong> Flight Data Processing System. 5 modules in 2 IFCs (one per side). Acquires/distributes flight data; computes warning tones.</li>
  <li><strong>IFC:</strong> Integrated Flight Cabinet. 2 in the avionics rack. Contains the FDPS modules and Flight Guidance Modules.</li>
  <li><strong>AHRS:</strong> Attitude Heading Reference System. One per side. Interfaces with all EFIS DUs.</li>
  <li><strong>ADC:</strong> Air Data Computer. One per side. Interfaces with all EFIS DUs.</li>
  <li><strong>Standby instruments:</strong> Integrated Electronic Standby Instrument (IESI), Standby ADS, Standby AHRS.</li>
  <li><strong>Two digital clocks:</strong> No.1 (pilot side) directly to CVR + to FDR via FDPS. No.2 (copilot side) to FDR via FDPS only.</li>
  <li><strong>FDR:</strong> Flight Data Recorder. Records via FDPS. Auto-switches from clock No.1 to No.2 on No.1 failure.</li>
  <li><strong>CVR:</strong> Cockpit Voice Recorder. Real-time stamped from clock No.1 directly.</li>
  <li><strong>WTG:</strong> Warning Tone Generator. Computes warning aurals.</li>
  <li><strong>Stick shaker motors:</strong> on forward side of pilot/copilot control columns.</li>
  <li><strong>GPWS LANDING FLAP SELECT switch:</strong> 10° / 15° / 35° (rotary green selectors).</li>
  <li><strong>GPWS FLAP OVERRIDE switchlight:</strong> alternate action, amber cross-hatch.</li>
  <li><strong>TERRAIN INHIBIT switchlight:</strong> alternate action, white. Inhibits TAD + TCF.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — Display Logic, Reversion, Color Rules',
 '<h4>Display selection</h4>
<ul>
  <li>EFCP1 controls PFD1 and MFD1 brightness + EFIS modes; EFCP2 controls PFD2 and MFD2 in mirror.</li>
  <li>ESCP MFD1 selector: PFD / NAV / SYS / ENG. ESCP MFD2 selector: same options.</li>
  <li>SYS mode + system pushbutton (ELEC/ENG/FUEL/DOORS) shows that page on the upper area of the MFD.</li>
  <li>ALL pushbutton cycles ENG → FUEL → DOORS → ELEC → ENG …</li>
  <li>Press-and-hold a system pushbutton with both MFDs failed/not at SYS: ED shows that system page in composite format. Release returns ED to engine display.</li>
</ul>
<h4>Reversion after ESCP power loss</h4>
<ul>
  <li><strong>MFD1 selector:</strong> remains operative — still responds to PFD/NAV/SYS/ENG selection.</li>
  <li><strong>MFD2 selector:</strong> NOT operative — drops to default page.</li>
</ul>
<h4>Color logic</h4>
<ul>
  <li><strong>RED:</strong> warning. Immediate action required.</li>
  <li><strong>YELLOW:</strong> caution. Awareness + subsequent action.</li>
  <li><strong>WHITE:</strong> actual parameter, status, advisory, scales, AFCS armed modes, bearing pointer 1, units.</li>
  <li><strong>GREEN:</strong> active controlling modes/functions, AFCS active modes, passed test, bearing pointer 2.</li>
  <li><strong>CYAN:</strong> pilot-SELECTABLE parameters — Hdg, Crs, Alt, speed/torque bugs, baro/DH.</li>
  <li><strong>MAGENTA:</strong> TCAS proximate/other traffic, VOR/ILS/DME data, FMS data, flight director commands.</li>
</ul>
<h4>Display attributes</h4>
<ul>
  <li><strong>FLASHING:</strong> 1 Hz, 50% duty cycle. Time-limited (usually 5 sec) or until crew action.</li>
  <li><strong>REVERSE VIDEO:</strong> change in operating state NOT pilot-initiated. Black on coloured rectangle. Time-limited.</li>
  <li><strong>BRACKETS:</strong> [LIKE THIS] = required crew action or instruction.</li>
</ul>
<h4>Recording sync</h4>
<ul>
  <li>No.1 clock: directly to CVR (real-time stamp) + FDR via FDPS.</li>
  <li>No.2 clock: FDR via FDPS only.</li>
  <li>FDR normally records No.1; auto-switches to No.2 on No.1 fail.</li>
  <li>Real time on both FDR and CVR provides post-event synchronisation.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Cruise, Display Discipline',
 '<h4>Pre-flight</h4>
<ul>
  <li>All 5 DUs powered up, no failure messages.</li>
  <li>EFIS pre-flight checks per company SOP — confirm AHRS aligned, ADC valid, FMS initialised.</li>
  <li>Stall Warning test: STALL WARN switch TEST 1 (>10 sec) — confirm L shaker, #1 STALL SYST FAIL + PUSHERSYSTFAIL caution illuminate, RA increases above 500 ft on pilot PFD. Then TEST 2 — same on R shaker / channel 2.</li>
  <li>T/O warning horn test: TEST switchlight on glareshield. Check horn sounds with engine running for any of the 5 trigger conditions.</li>
  <li>GPWS test: PULL UP GPWS TEST switch — observe full warning chain (GPWS C/W light, FLAP OVERRIDE annunciator, BELOW G/S, GLIDESLOPE aural, PULL UP GPWS TEST annunciators after 2 sec, PULL UP aural twice, cycles through all aurals).</li>
</ul>
<h4>Cruise scan</h4>
<ol>
  <li>EFIS displays clean, no flagged parameters.</li>
  <li>No Master Warning / Master Caution illuminated.</li>
  <li>FMS / FMA / GPS displays match flight plan.</li>
  <li>ED engine page within green band, no yellow/red exceedance.</li>
  <li>Background: clocks running; FDR / CVR running (no faults indicated).</li>
</ol>',
 'normal', 40),

(@lesson_id, 'Abnormal — DU Failure, Reversion, Warning Logic',
 '<ul>
  <li><strong>Single DU failure (e.g. PFD1 black):</strong> all 5 DUs are interchangeable. Use ESCP/EFCP reversion to display PFD content on MFD1. PFD information appears on adjacent MFD; AVAIL (white) shows in centre of failed PFD.</li>
  <li><strong>ESCP power loss:</strong> MFD1 selector remains operative. MFD2 selector LOSES function — cannot reselect from default page. Plan around this — MFD1 becomes the master reversion path.</li>
  <li><strong>Both MFDs failed or none at SYS:</strong> system pushbuttons (ELEC/ENG/FUEL/DOORS/ALL) still work — press-and-HOLD shows page on ED in composite format; release returns to engine display.</li>
  <li><strong>AHRS or ADC failure (one side):</strong> EFIS ATT/HDG SOURCE or ADC SOURCE selector reverses to the healthy side. Cross-side source selection illuminates yellow on PFD.</li>
  <li><strong>Master Warning (flashing red):</strong> immediate action required. Push the Master Warning switchlight to reset the flash; the underlying caution/warning panel light remains steady if the fault persists. Identify and act per QRH.</li>
  <li><strong>Master Caution (flashing amber):</strong> awareness + subsequent action. Same reset logic. Identify per panel/MFD; run QRH non-normal as required.</li>
  <li><strong>STICK PUSHER SHUT-OFF (manual disable):</strong> push the OFF switchlight to disable the pusher — PUSHER SYST FAIL caution illuminates. Stall warning shaker still functions.</li>
  <li><strong>Stall warning fault (#1 or #2 STALL SYST FAIL):</strong> one channel of the SPS has failed. The other channel and pusher logic continue. Run QRH; consider exit from icing if applicable.</li>
  <li><strong>FDR / CVR fault:</strong> usually a maintenance write-up. The ramifications are post-event reconstruction, not in-flight safety.</li>
  <li><strong>Clock #1 failure:</strong> FDR auto-switches to No.2. CVR loses real-time sync to clock #1 — the recording continues but the stamping is degraded.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — DUs, Switchlights, Recorders',
 '<ul>
  <li><strong>5 Display Units (PFD1, MFD1, ED, MFD2, PFD2):</strong> all identical, all interchangeable.</li>
  <li><strong>EFCP1/EFCP2 PFD/MFD brightness knobs:</strong> rotary, detent at OFF.</li>
  <li><strong>ESCP MFD1/MFD2 selector:</strong> PFD / NAV / SYS / ENG (rotary, 4-position).</li>
  <li><strong>ESCP system pushbuttons:</strong> ELEC SYS, ENG SYS, FUEL SYS, DOORS SYS, ALL.</li>
  <li><strong>EFIS ATT/HDG SOURCE selector:</strong> NORM / 1 / 2 (cross-side).</li>
  <li><strong>ADC SOURCE selector:</strong> NORM / 1 / 2 (cross-side).</li>
  <li><strong>ED BRT knob:</strong> ED brightness.</li>
  <li><strong>STALL WARNING TEST switch:</strong> TEST 1 / OFF / TEST 2 (3-position spring-loaded).</li>
  <li><strong>T/O WARNING HORN TEST switch:</strong> 2-position spring-loaded.</li>
  <li><strong>STICK PUSHER SHUT-OFF switchlight (amber, alternate):</strong> push to disable pusher; "OFF" illuminates.</li>
  <li><strong>PULL UP GPWS TEST switch (red, momentary):</strong> exercises GPWS warning chain.</li>
  <li><strong>BELOW G/S switch (amber, momentary):</strong> cancels "BELOW GLIDESLOPE" aural.</li>
  <li><strong>TERRAIN INHIBIT switchlight (white, alternate):</strong> inhibits TAD + TCF.</li>
  <li><strong>GPWS FLAP OVERRIDE switch (amber, alternate):</strong> inhibits GPWS mode 4B (permits 0° flap landing).</li>
  <li><strong>GPWS LANDING FLAP SELECT switch (rotary):</strong> 10 / 15 / 35 (green segments).</li>
  <li><strong>MASTER WARNING switchlight (flashing RED, momentary):</strong> resets flash; warning lights persist if fault persists.</li>
  <li><strong>MASTER CAUTION switchlight (flashing AMBER, momentary):</strong> same logic.</li>
  <li><strong>FMA on PFD:</strong> Flight Mode Annunciator — armed and active autopilot/autothrottle modes.</li>
  <li><strong>Two digital clocks:</strong> on instrument panels.</li>
  <li><strong>FDR / CVR:</strong> typically in tail / aft fuselage. No flight-deck control.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Total Display Units:</strong> 5 (PFD1, MFD1, ED, MFD2, PFD2). All identical.</li>
  <li><strong>EFIS DUs:</strong> 4 (PFDs + MFDs). ESID DUs: 3 (MFDs + ED).</li>
  <li><strong>FDPS modules:</strong> 5 across 2 IFCs (one per side).</li>
  <li><strong>FADEC channels:</strong> 2 per FADEC, one FADEC per engine.</li>
  <li><strong>Flashing rate:</strong> 1 Hz, 50% duty cycle.</li>
  <li><strong>Reverse video time limit:</strong> ~5 seconds.</li>
  <li><strong>Stall warning test position hold:</strong> &gt; 10 seconds at TEST 1, then &gt; 10 seconds at TEST 2.</li>
  <li><strong>RA reading during stall warn TEST 1:</strong> increases above 500 ft (may disappear above 550). Decreases to 50 ft on test exit.</li>
  <li><strong>Number of clocks:</strong> 2 (No.1 pilot side, No.2 copilot side).</li>
  <li><strong>FDR clock source:</strong> normally No.1. Auto-switches to No.2 on No.1 fail.</li>
  <li><strong>CVR clock source:</strong> No.1 directly only.</li>
  <li><strong>T/O warning horn trigger conditions:</strong> 5 (spoilers / trim / brake / condition lever / flaps).</li>
  <li><strong>Flap range for T/O horn trigger:</strong> &gt; 20° OR &lt; 3.5°.</li>
  <li><strong>GPWS LANDING FLAP SELECT positions:</strong> 10° / 15° / 35°.</li>
  <li><strong>GPWS "TOO LOW FLAPS" aural threshold:</strong> AGL altitude &lt; 200 ft with flaps less than selected.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>5-DU-IDENTICAL</strong> — 5 DUs total, all identical and interchangeable.</li>
  <li><strong>EFIS-4-ESID-3</strong> — EFIS uses 4 DUs (PFDs+MFDs); ESID uses 3 DUs (MFDs+ED).</li>
  <li><strong>MFD2-DIES-WITH-ESCP</strong> — after ESCP power loss, MFD2 selector dies; MFD1 selector keeps working.</li>
  <li><strong>ALL-CYCLE-EFDD-E</strong> — ALL pushbutton cycles ENG → FUEL → DOORS → ELEC → ENG.</li>
  <li><strong>RED-YELLOW-WHITE-GREEN-CYAN-MAGENTA</strong> — color rules: RED warning, YELLOW caution, WHITE status, GREEN active, CYAN pilot-set, MAGENTA TCAS/FMS.</li>
  <li><strong>FLASH-1HZ-5SEC</strong> — flashing 1 Hz, 50% duty, time-limited 5 sec usual.</li>
  <li><strong>BRACKETS-MEAN-ACTION</strong> — [BRACKETED MESSAGES] = required crew action.</li>
  <li><strong>TWO-CLOCKS-FDR-CVR</strong> — 2 digital clocks; #1 to CVR direct + FDR via FDPS; #2 to FDR via FDPS only. FDR auto-switches.</li>
  <li><strong>TEST1-LSHAKE-TEST2-RSHAKE</strong> — TEST 1 = SPS channel 1 / left shaker; TEST 2 = channel 2 / right shaker.</li>
  <li><strong>5-COND-TO-HORN</strong> — 5 conditions trigger T/O warning horn: spoilers, trim, brake, condition lever, flaps.</li>
  <li><strong>TERRAIN-INHIBIT-WHITE</strong> — TERRAIN INHIBIT switchlight is WHITE; inhibits TAD/TCF only.</li>
  <li><strong>FLAP-OVERRIDE-AMBER-MODE-4B</strong> — GPWS FLAP OVERRIDE is amber crosshatch; inhibits mode 4B only (permits 0° flap landings).</li>
  <li><strong>MASTER-RED-AMBER</strong> — Master Warning RED; Master Caution AMBER. Switchlight push resets flash; underlying lights persist if fault persists.</li>
 </ol>
<p>Color chant: <em>"RED-warn · YELLOW-caution · WHITE-state · GREEN-active · CYAN-mine · MAGENTA-FMS."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
