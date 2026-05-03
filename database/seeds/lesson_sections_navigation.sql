-- =============================================================================
-- AviatorTutor — Phase 13: ATA 34 Navigation — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'navigation' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'navigation-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Dual Everything for IFR + VFR',
 '<p>Q400 navigation is built around dual receivers and dual-redundant data sources. Two VHF NAV receivers handle VOR, Localizer, Glideslope, DME, and integral Marker Beacons. Two ADF receivers, two ATC transponders, two AHRS units, two ADCs. The Flight Management System (FMS) provides automatic radio tuning and waypoint navigation. The ARCDU (Audio + Radio Control Display Unit) handles manual tuning. The EFCP (EFIS Control Panel) drives the bearing pointers, format, range, TCAS, WX/TERR, and DATA selections that appear on the EFIS displays. Layered on top: Weather Radar (single unit), EGPWS for terrain awareness, TCAS for traffic. Memorise the bearing-selector five positions (OFF/VOR/ADF/FMS/AUX), the marker beacon colours (OUT-MID-INN = blue/amber/white), and the format and range numbers.</p>',
 'overview', 10),

(@lesson_id, 'Components — Receivers, Computers, Panels',
 '<ul>
  <li><strong>VHF NAV1, VHF NAV2:</strong> dual VHF Navigation receivers. VOR + LOC + GS + DME paired + Marker Beacons.</li>
  <li><strong>ARCDU1, ARCDU2:</strong> Audio + Radio Control Display Units. Manual tuning. Centre console.</li>
  <li><strong>FMS:</strong> Flight Management System. Automatic radio tuning + waypoint navigation. May be dual on some operators.</li>
  <li><strong>DME1, DME2:</strong> dual-channel each. Slant range, ground speed, DME HOLD.</li>
  <li><strong>ADF1, ADF2:</strong> Automatic Direction Finders. Modes ADF / ANT / BFO / TEST.</li>
  <li><strong>ATC1, ATC2:</strong> dual transponders. Modes A/C/S, IDENT.</li>
  <li><strong>WXR:</strong> Weather Radar. Single unit.</li>
  <li><strong>EGPWS:</strong> Enhanced Ground Proximity Warning System. TAD + TCF + 5 GPWS modes.</li>
  <li><strong>TCAS:</strong> Traffic Alert + Collision Avoidance. RA + TA.</li>
  <li><strong>AHRS:</strong> 2 AHCPs (control panels), 2 AHRUs (units with vertical+directional gyros + accelerometers), 2 remote flux valves (FDU1, FDU2), 2 remote memory modules (RMM1, RMM2).</li>
  <li><strong>ADC1, ADC2:</strong> Air Data Computers.</li>
  <li><strong>FGCP:</strong> Flight Guidance Control Panel. Course selectors, speed/heading/altitude bugs.</li>
  <li><strong>EFCP1, EFCP2:</strong> EFIS Control Panels. Bearing 1 + 2 selectors (5-position), FORMAT, TCAS, WX/TERR, RANGE, BRG/MFD/PFD brightness, DATA.</li>
  <li><strong>ESCP:</strong> Engine + System Integrated Display Control Panel (covered in Phase 10). Drives MFD reversion.</li>
  <li><strong>Marker beacon receivers:</strong> integral to VOR/LOC. OUT/MID/INN with HI/LO sensitivity from ARCDU.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — VHF Nav, ADF, FMS, AHRS, EFCP',
 '<h4>VHF NAV (VOR/ILS)</h4>
<ul>
  <li>VOR: 108.00–117.95 MHz, 50 kHz EVEN spacing.</li>
  <li>LOC: 108.10–111.95 MHz, 50 kHz ODD spacing. Auto-paired with Glideslope.</li>
  <li>EFIS shows: VOR bearing course, VOR lateral deviation, LOC lateral deviation, GS vertical deviation, marker passage.</li>
  <li>COURSE selector knobs on FGCP set the desired course on EFIS.</li>
</ul>
<h4>Marker Beacon</h4>
<ul>
  <li>OUTER: blue OUT legend at outer marker.</li>
  <li>MIDDLE: amber MID at middle marker.</li>
  <li>INNER/AIRWAY: white INN.</li>
  <li>Sensitivity HI or LO from ARCDU.</li>
</ul>
<h4>DME</h4>
<ul>
  <li>VHF NAV1/NAV2 tune DME1/DME2 from ARCDU1/ARCDU2 selection.</li>
  <li>DME HOLD keeps current station active when frequency changes.</li>
  <li>Pilot EFIS uses DME1 (Channel 1 for VOR1, Channel 2 for VOR2). Copilot EFIS uses DME2 (Channel 1 for VOR1, Channel 2 for VOR2).</li>
  <li>Loss of a DME → both EFIS sides auto-use remaining DME.</li>
</ul>
<h4>ADF</h4>
<ul>
  <li><strong>ADF mode:</strong> bearing pointer to selected ground station.</li>
  <li><strong>ANT mode:</strong> audio receiver only. Loop disabled (higher audio sensitivity). Pointer parks at 90° relative bearing.</li>
  <li><strong>BFO mode:</strong> intermittent 1000 Hz tone for valid station identification.</li>
  <li><strong>TEST mode:</strong> confidence test. Pointer parks at 90° relative.</li>
</ul>
<h4>EFCP — Bearing pointers</h4>
<ul>
  <li>Bearing 1 selector: 5-position OFF / VOR1 / ADF1 / FMS1 / AUX1. White single-bar pointer with white circle.</li>
  <li>Bearing 2 selector: 5-position OFF / VOR2 / ADF2 / FMS2 / AUX2.</li>
  <li>VOR pointer removed if frequency invalid OR ILS frequency selected.</li>
  <li>ADF pointer parks at 90° if signal or frequency invalid.</li>
  <li>FMS pointer points to next waypoint.</li>
  <li>AUX requires optional MLS.</li>
</ul>
<h4>EFCP — FORMAT / TCAS / WX/TERR / RANGE / DATA</h4>
<ul>
  <li>FORMAT push 1: ARC mode with VOR/ILS source. Push 2 or 3: ARC with FMS (default). Push and HOLD 1 sec: FULL mode, 360° north-up, A/C centred.</li>
  <li>TCAS push 1: continuous traffic at 40 nm or less. Push 2: automatic. Auto also on EFCP malfunction.</li>
  <li>WX/TERR cycles WX / EGPWS terrain / OFF. Default WX.</li>
  <li>RANGE: 10 / 20 / 40 / 80 / 160 / 240 nm. Default 40.</li>
  <li>DATA: 10 nearest nav aids / 10 nearest airports / both / off (default).</li>
</ul>
<h4>AHRS</h4>
<ul>
  <li>AHRU uses gyros + accelerometers. RMM stores calibration for fast realignment after power loss.</li>
  <li>Outputs to EIS (EADI/EHSI/ALT/IVSI), AFCS, SPS, FDR via IFC, FDPS, WXR, GPWS, TCAS, CDS.</li>
</ul>
<h4>EGPWS / GPWS</h4>
<ul>
  <li>5 GPWS modes. TAD + TCF added.</li>
  <li>TERRAIN INHIBIT switchlight (white, alternate): inhibits TAD + TCF only. Other modes active.</li>
  <li>GPWS FLAP OVERRIDE (amber crosshatch): inhibits mode 4B only (permits 0° flap landings).</li>
  <li>GPWS LANDING FLAP SELECT switch: 10 / 15 / 35. TOO LOW FLAPS aural at <200 ft AGL with flaps less than selected.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Cruise, Approach',
 '<h4>Pre-flight</h4>
<ul>
  <li>FMS initialisation: position, route, performance.</li>
  <li>Tune nav radios via FMS auto or ARCDU manual. Verify frequencies and IDs.</li>
  <li>EFCP setup: Bearing 1 typically VOR1 or FMS1; Bearing 2 typically VOR2; FORMAT ARC; RANGE 40 nm; WX/TERR per phase.</li>
  <li>TEST GPWS via PULL UP GPWS TEST (Phase 10) — confirms full warning chain.</li>
  <li>AHRS aligned (ground alignment per company SOP).</li>
  <li>Transponders selected; modes correct.</li>
</ul>
<h4>Cruise</h4>
<ul>
  <li>FMS in LNAV/VNAV per route. Verify next waypoint, ETA.</li>
  <li>Cross-check FMS position vs raw VOR/DME via Bearing 1 set to VOR1 with the appropriate radial.</li>
  <li>WXR in WX mode for weather; switch to terrain in mountainous areas.</li>
  <li>TCAS continuous at 40 nm or less.</li>
</ul>
<h4>Approach</h4>
<ul>
  <li>Tune ILS frequency (LOC + GS automatically paired).</li>
  <li>FORMAT to ARC mode for the approach if not already.</li>
  <li>Bearing 1 to VOR1 with the appropriate radial; Bearing 2 to FMS for waypoint awareness.</li>
  <li>Marker beacons OUT (blue) at outer; MID (amber) at middle; INN (white) for airway/inner.</li>
  <li>DME for distance to threshold.</li>
  <li>EGPWS modes active (TAD/TCF — keep TERRAIN INHIBIT off unless explicitly required).</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — Failures and Reversion',
 '<ul>
  <li><strong>VHF NAV receiver fault (one side):</strong> EFIS source switches to operative receiver via cross-side source selection. Yellow indication on PFD.</li>
  <li><strong>DME loss (one side):</strong> both EFIS sides auto-use remaining DME. Note 4-channel multiplex limitation if both VOR1 and VOR2 are tuned to DME stations.</li>
  <li><strong>ADF signal lost:</strong> bearing pointer parks at 90°. Cross-check via VOR or FMS.</li>
  <li><strong>VOR invalid frequency:</strong> bearing pointer removed. Verify frequency, retune.</li>
  <li><strong>FMS fault:</strong> bearing pointer to FMS removed; reverts to manual VOR/ADF tuning via ARCDU. Use raw nav.</li>
  <li><strong>EFCP malfunction:</strong> TCAS auto-goes to AUTO mode. Other EFCP functions degraded — may require swap to other EFCP.</li>
  <li><strong>AHRS fault (one side):</strong> EFIS ATT/HDG SOURCE to healthy side. Cross-side yellow indication. AFCS may degrade.</li>
  <li><strong>ADC fault (one side):</strong> ADC SOURCE to healthy side. Yellow IAS MISMATCH may appear (Phase 7 cascade) if mismatch exceeds 17 kts.</li>
  <li><strong>WXR fault:</strong> no terrain alternative is just EGPWS terrain — different system. Plan visually around weather where possible.</li>
  <li><strong>EGPWS terrain fault:</strong> TAD/TCF unavailable. WX still works. TERRAIN INHIBIT switchlight cannot toggle if system fault.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Pointers, Selectors, Switches',
 '<ul>
  <li><strong>EFCP Bearing 1 selector:</strong> OFF / VOR1 / ADF1 / FMS1 / AUX1.</li>
  <li><strong>EFCP Bearing 2 selector:</strong> OFF / VOR2 / ADF2 / FMS2 / AUX2.</li>
  <li><strong>FORMAT pushbutton:</strong> ARC default; PUSH+HOLD 1 sec for FULL mode 360° north-up.</li>
  <li><strong>TCAS pushbutton:</strong> continuous (40 nm or less) / automatic.</li>
  <li><strong>WX/TERR pushbutton:</strong> WX / EGPWS terrain / off.</li>
  <li><strong>RANGE selector:</strong> 10 / 20 / 40 / 80 / 160 / 240 nm.</li>
  <li><strong>DATA pushbutton:</strong> 10 nav aids / 10 airports / both / off.</li>
  <li><strong>FGCP COURSE selector knobs:</strong> rotary, set desired course.</li>
  <li><strong>ARCDU:</strong> manual tuning of VOR, LOC/GS, DME, ADF. Marker beacon HI/LO sensitivity.</li>
  <li><strong>Marker beacon legend:</strong> OUT (blue), MID (amber), INN (white).</li>
  <li><strong>EFIS ATT/HDG SOURCE selector:</strong> NORM / 1 / 2 (cross-side AHRS).</li>
  <li><strong>ADC SOURCE selector:</strong> NORM / 1 / 2 (cross-side ADC).</li>
  <li><strong>TERRAIN INHIBIT switchlight:</strong> alternate, white. Inhibits TAD + TCF.</li>
  <li><strong>GPWS FLAP OVERRIDE switch:</strong> alternate, amber. Inhibits mode 4B only.</li>
  <li><strong>GPWS LANDING FLAP SELECT:</strong> 10 / 15 / 35.</li>
  <li><strong>BELOW G/S switch:</strong> momentary, amber. Cancels BELOW GLIDESLOPE aural.</li>
  <li><strong>PULL UP GPWS TEST:</strong> momentary red. Exercises full GPWS chain.</li>
  <li><strong>ATC IDENT button:</strong> on transponder control. Triggers IDENT for ATC.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>VOR frequency range:</strong> 108.00–117.95 MHz, 50 kHz EVEN spacing.</li>
  <li><strong>Localizer frequency range:</strong> 108.10–111.95 MHz, 50 kHz ODD spacing.</li>
  <li><strong>Glideslope:</strong> auto-paired with localiser frequency.</li>
  <li><strong>VHF NAV receivers:</strong> 2.</li>
  <li><strong>DME units:</strong> 2 (each dual-channel).</li>
  <li><strong>ADF receivers:</strong> 2.</li>
  <li><strong>ADF modes:</strong> 4 (ADF / ANT / BFO / TEST).</li>
  <li><strong>ADF parks at 90°:</strong> in ANT mode, TEST mode, or invalid signal/frequency.</li>
  <li><strong>ATC transponders:</strong> 2.</li>
  <li><strong>EFCP Bearing selector positions:</strong> 5 (OFF / VOR / ADF / FMS / AUX).</li>
  <li><strong>EFCP RANGE positions:</strong> 6 (10 / 20 / 40 / 80 / 160 / 240 nm).</li>
  <li><strong>EFCP RANGE default:</strong> 40 nm.</li>
  <li><strong>FORMAT push-and-hold time:</strong> 1 second for FULL mode.</li>
  <li><strong>TCAS continuous range:</strong> 40 nm or less.</li>
  <li><strong>EGPWS modes:</strong> 5 GPWS modes + TAD + TCF.</li>
  <li><strong>GPWS LANDING FLAP SELECT positions:</strong> 10 / 15 / 35.</li>
  <li><strong>GPWS TOO LOW FLAPS threshold:</strong> AGL <200 ft with flaps less than selected.</li>
  <li><strong>AHRS:</strong> 2 AHCPs, 2 AHRUs, 2 flux valves, 2 RMMs.</li>
  <li><strong>ADCs:</strong> 2.</li>
  <li><strong>Marker beacon colours:</strong> OUT (blue), MID (amber), INN (white).</li>
  <li><strong>BFO tone:</strong> 1000 Hz intermittent.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>VHF-NAV-DUAL</strong> — 2 VHF NAV receivers covering VOR + LOC + GS + DME + Marker.</li>
  <li><strong>VOR-EVEN-LOC-ODD</strong> — VOR 108.00–117.95 MHz EVEN spacing; LOC 108.10–111.95 MHz ODD.</li>
  <li><strong>OUT-BLUE-MID-AMBER-INN-WHITE</strong> — marker beacon colours by location.</li>
  <li><strong>BRG-5-POS</strong> — Bearing selector 5 positions: OFF / VOR / ADF / FMS / AUX.</li>
  <li><strong>FORMAT-1-SEC-FULL</strong> — Push-and-hold 1 sec switches ARC to FULL mode.</li>
  <li><strong>TCAS-40-NM</strong> — TCAS continuous traffic at 40 nm or less; auto on EFCP fault.</li>
  <li><strong>RANGE-6-DEFAULT-40</strong> — RANGE 6 positions; default 40 nm.</li>
  <li><strong>4-MODE-ADF</strong> — ADF / ANT / BFO / TEST.</li>
  <li><strong>ADF-PARKS-90</strong> — ADF pointer parks at 90° in ANT, TEST, or invalid signal.</li>
  <li><strong>DME-CROSS-USE</strong> — pilot EFIS uses DME1; copilot uses DME2; loss → auto-use remaining.</li>
  <li><strong>BFO-1000-HZ</strong> — BFO mode produces intermittent 1000 Hz tone.</li>
  <li><strong>2-AHRU-2-ADC-2-FLUX-2-RMM</strong> — AHRS dual everything: 2 AHCPs, 2 AHRUs, 2 flux valves, 2 memory modules. Plus 2 ADCs.</li>
  <li><strong>WPT-FMS-PT</strong> — FMS bearing pointer points to next waypoint.</li>
 </ol>
<p>Approach chant: <em>"ARCDU tune ILS · FORMAT ARC · BRG1 VOR · BRG2 FMS · marker BLUE-AMBER-WHITE."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
