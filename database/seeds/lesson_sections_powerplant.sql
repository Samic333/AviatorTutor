-- =============================================================================
-- AviatorTutor — Phase 17: ATA 71 Powerplant — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'powerplant' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'powerplant-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — PW150A Architecture',
 '<p>Two Pratt & Whitney PW150A turboprops drive Dowty R408 6-blade composite propellers through reduction gearboxes. Architecture: NL axial 1st-stage compressor + single-stage NL turbine; NH centrifugal 2nd-stage compressor + single-stage NH turbine; 2-stage power turbine on independent third shaft drives the reduction gearbox. The HP compressor (NH) drives the accessory gearbox, which mounts: oil pumps, HP fuel pump, PMA, and DC starter-generator. Normal takeoff power 4,580 SHP. MTOP UPTRIM gives 5,071 SHP for a brief period if an engine fails on takeoff. Crew interface is two control levers per engine — Power Lever (PLA) drives FADEC in forward range and prop blade angle in beta/reverse; Condition Lever (CL) drives PEC for RPM, ratings, manual feather, fuel on/off.</p>',
 'overview', 10),

(@lesson_id, 'Components — Engine + Controls + FADEC',
 '<ul>
  <li><strong>PW150A engines:</strong> 2 turboprops. Drive Dowty R408 propellers through reduction gearboxes.</li>
  <li><strong>NL axial compressor + NL turbine:</strong> low-pressure stage. Single-stage turbine on shared shaft.</li>
  <li><strong>NH centrifugal compressor + NH turbine:</strong> high-pressure stage. Single-stage turbine on shared shaft. Drives accessory gearbox.</li>
  <li><strong>Power turbine:</strong> two-stage. Independent third shaft. Drives reduction gearbox to propeller.</li>
  <li><strong>Reduction gearbox:</strong> at front of engine. Steps down power turbine speed for propeller.</li>
  <li><strong>Accessory gearbox:</strong> mounted on top of engine. Driven by NH compressor. Operates oil pressure pump, oil scavenge pump, HP fuel pump, PMA, DC starter-generator.</li>
  <li><strong>FADEC:</strong> Full Authority Digital Engine Control. Receives PLA, ambient conditions, engine sensors, NPT (power turbine speed), remote engine failure (for UPTRIM). Outputs to engine systems.</li>
  <li><strong>PEC:</strong> Propeller Electronic Control. Drives prop in forward, governs RPM. Uses CLA position.</li>
  <li><strong>ECIU:</strong> Engine Control Interface Unit. Pilot inputs to FADEC: rating discretes (MTOP/MCL/MCR), RDC TOP TRQ selection, ECS bleed selection.</li>
  <li><strong>PMA:</strong> Permanent Magnet Alternator. Primary electrical source for FADEC. Independent coils per channel. Active above NH 20%.</li>
  <li><strong>Power Lever (PLA):</strong> per engine. Positions: MAX REV / DISC / FLT IDLE / forward range / rating detent.</li>
  <li><strong>Condition Lever (CL):</strong> per engine. Positions: FUEL OFF / START & FEATHER / MIN 850 / 900 / MAX 1020.</li>
  <li><strong>Engine Control Panel pushbuttons:</strong> MTOP, EVENT MARKER, RDC NP LDG, MCL, MCR, RDC TOP TRQ DEC, RDC TOP TRQ RESET.</li>
  <li><strong>HBOVs:</strong> Handling Bleed-Off Valves. 2 per engine (one LP + one HP). Increase surge margin during start, steady state, transient.</li>
  <li><strong>Bypass door:</strong> at each nacelle intake. Prevents solids/precipitation. Selected via ICE PROTECTION panel.</li>
  <li><strong>FMU:</strong> Fuel Metering Unit. Includes dedicated fuel shutoff switch activated by PULL FUEL/HYD OFF handle.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — Ratings, Power Curve, RDC Modes',
 '<h4>Engine ratings displayed on ED</h4>
<ul>
  <li><strong>NTOP:</strong> CL MAX/1020, BLEED MIN/OFF or ON/MIN. Normal takeoff.</li>
  <li><strong>MTOP:</strong> CL 1020 + MTOP pushbutton (or auto via UPTRIM). Max takeoff. UPTRIM 5,071 SHP for brief period on engine failure.</li>
  <li><strong>MCP:</strong> CL 1020, BLEED ON/NORM or MAX. Max continuous power.</li>
  <li><strong>MCL:</strong> CL MIN/850 + MCL pushbutton. Displays MCL with 900 RPM.</li>
  <li><strong>MCR:</strong> CL 900 + MCR pushbutton. Displays MCR with 850 RPM.</li>
  <li><strong>RDC TOP:</strong> NTOP/MTOP with reduced power requested via RDC TOP TRQ DEC pushbutton (2% steps to 10% limit; not in MTOP/MCP).</li>
</ul>
<h4>Power request vs PLA (forward range)</h4>
<ul>
  <li>IDLE: ~35° PLA.</li>
  <li>1000 SHP: ~5° PLA (very low).</li>
  <li>MCR: 77.5° PLA.</li>
  <li>NTOP: 80° PLA.</li>
  <li>MCL: 82.5° PLA.</li>
  <li>MTOP: 95° PLA.</li>
  <li>O/T (overtravel): 100° PLA.</li>
  <li>Emergency rating: 1.25 × MTOP.</li>
</ul>
<h4>Reduced NP for landing (RDC NP LDG)</h4>
<ol>
  <li>Power Levers between Flight Idle and ~50% rating.</li>
  <li>Condition Lever at MIN/850.</li>
  <li>Push RDC NP LDG pushbutton.</li>
  <li>Within 15 SECONDS, advance Condition Lever to MAX/1020.</li>
  <li>NP remains at 850 RPM (despite CL at 1020).</li>
  <li>ED indicates REDUCED NP LANDING.</li>
  <li>Cancellation: PLA ≥ 65°, OR push RDC NP LDG button again.</li>
</ol>
<h4>Event marker</h4>
<ul>
  <li>Push EVENT MARKER pushbutton on any unusual event.</li>
  <li>EMS stores data snapshot + trace 2 MINUTES before event + 1 MINUTE after.</li>
</ul>
<h4>Bypass door</h4>
<ul>
  <li>Selected open via ICE PROTECTION panel switchlight.</li>
  <li>Open in: icing condition, heavy precipitation, bird activity, contaminated runways.</li>
  <li>Manual selection — does not auto-open.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Start, Cruise, Shutdown',
 '<h4>Pre-flight</h4>
<ul>
  <li>External: bypass doors per conditions; cowls secure.</li>
  <li>Cockpit: PLA at idle, CL at FUEL OFF, MTOP/MCL/MCR pushbuttons in default state.</li>
  <li>Run propeller heater test (Phase 9 + 16).</li>
  <li>Run OSG test on ground (Phase 16) via PROP O''SPEED GOVERNOR test switch.</li>
</ul>
<h4>Engine start</h4>
<ul>
  <li>External or APU power applied. APU GEN may be ON (per Phase 15).</li>
  <li>CL at START & FEATHER position. Push starter (separate switch). FADEC sequences start using essential bus power (PMA inactive below NH 20%).</li>
  <li>NH builds; at NH 20% PMA takes over electrical for FADEC.</li>
  <li>CL to MIN/850 at idle; CL to MAX/1020 for normal operation.</li>
</ul>
<h4>Takeoff</h4>
<ul>
  <li>CL MAX/1020. MTOP pushbutton ON for takeoff. ED shows MTOP rating.</li>
  <li>Autofeather ON (Phase 16). Bypass doors per conditions.</li>
  <li>PLA to RATING DETENT for MTOP power. UPTRIM auto-engages on engine failure (5,071 SHP).</li>
</ul>
<h4>Cruise</h4>
<ul>
  <li>CL to 900 + MCR pushbutton (displays MCR 850 RPM) — fuel-efficient cruise.</li>
  <li>Or CL to MIN/850 + MCL pushbutton (displays MCL 900 RPM) — climb rating.</li>
  <li>Bleed selection ON/NORM. ED shows MCP if NTOP would otherwise be set.</li>
</ul>
<h4>Approach + landing</h4>
<ul>
  <li>RDC NP LDG sequence per company SOP. NP at 850 RPM.</li>
  <li>Touchdown: PLA into beta (below Flight Idle). PROPELLER GROUND RANGE lights at 10° and below.</li>
  <li>Reverse: PLA into reverse arc. 660-950 RPM, 1500 SHP max (Phase 16).</li>
</ul>
<h4>Engine shutdown</h4>
<ul>
  <li>Normal: CL to FUEL OFF. Engine System tests NH overspeed protection by using it to shut down.</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — Failures, Fire, Auto-Protection',
 '<ul>
  <li><strong>Engine failure on takeoff:</strong> autofeather (Phase 16) feathers prop after 3-sec confirm. UPTRIM commanded to operating engine FADEC — power rises automatically to 5,071 SHP MTOP. Continue per ENGINE FAILURE on TAKEOFF QRH.</li>
  <li><strong>Engine fire (in flight):</strong> per Phase 6 — flash, press, pull T-handle, EXTG. Pulling T-handle activates dedicated fuel shutoff in FMU + closes hyd valves.</li>
  <li><strong>FADEC fault on one channel:</strong> dual-channel architecture maintains control. Investigate; defer per MEL.</li>
  <li><strong>PMA failure:</strong> A/C essential power buses provide alternate electrical to FADEC. No interruption to engine control. Defer per MEL.</li>
  <li><strong>NH overspeed:</strong> FADEC NH O/S circuitry shuts the engine down. Same circuitry tested on every normal CL FUEL OFF shutdown.</li>
  <li><strong>HBOV stuck open:</strong> reduced engine performance. Investigate via FADEC fault codes; defer per MEL.</li>
  <li><strong>Bypass door fault:</strong> if won''t open in icing — risk of solids ingestion. Plan around (avoid icing, divert).</li>
  <li><strong>RDC NP LDG cancellation unexpectedly:</strong> CL not advanced within 15 sec, OR PLA ≥65°. NP returns to demanded RPM.</li>
  <li><strong>Engine over-torque:</strong> recorded by EMS. Push EVENT MARKER. Investigate after landing.</li>
  <li><strong>Fuel pressure low to engine:</strong> AUX PUMP ON (Phase 8). #X ENG FUEL PRESS caution clears with AUX boost.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — ED, Switches, Annunciations',
 '<ul>
  <li><strong>ED parameters:</strong> TRQ %, PROP RPM, NH %, ITT °C, NL %, Fuel Flow (hundreds of kg/hr), Oil temp + pressure (dual analog/digital).</li>
  <li><strong>Engine Rating Mode annunciation (green):</strong> NTOP / MTOP / MCP / MCL / MCR / RDC TOP.</li>
  <li><strong>BLEED status:</strong> below engine rating mode. White or yellow per selection.</li>
  <li><strong>Torque bug digital value:</strong> cyan when valid, white dashes when invalid. 0-199% in 1% increments.</li>
  <li><strong>UPTRIM TRQ:</strong> on ED during autofeather event. Operating engine extra power.</li>
  <li><strong>OSG TEST IN PROG:</strong> on ED during ground OSG test.</li>
  <li><strong>A/F TEST IN PROG:</strong> on ED during autofeather test.</li>
  <li><strong>PROPELLER GROUND RANGE lights:</strong> illuminate at 10° blade angle and below.</li>
  <li><strong>MTOP pushbutton:</strong> alternate action. Enables MTOP rating with CL at 1020.</li>
  <li><strong>EVENT MARKER pushbutton:</strong> momentary. Bookmark in EMS.</li>
  <li><strong>RDC NP LDG pushbutton:</strong> momentary. Reduces NP for landing per sequence.</li>
  <li><strong>MCL pushbutton:</strong> momentary. Changes 850 CLA to MCL 900 rating.</li>
  <li><strong>MCR pushbutton:</strong> momentary. Changes 900 CLA to MCR 850 rating.</li>
  <li><strong>RDC TOP TRQ DEC pushbutton:</strong> momentary. Reduces NTOP power 2% per push, max 10% reduction.</li>
  <li><strong>RDC TOP TRQ RESET pushbutton:</strong> momentary. Restores normal takeoff power.</li>
  <li><strong>Power Lever positions:</strong> MAX REV / DISC / FLT IDLE / forward range / rating detent.</li>
  <li><strong>Condition Lever positions:</strong> FUEL OFF / START & FEATHER / MIN 850 / 900 / MAX 1020.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Number of engines:</strong> 2 PW150A turboprops.</li>
  <li><strong>Normal takeoff power:</strong> 4,580 SHP per engine.</li>
  <li><strong>MTOP UPTRIM:</strong> 5,071 SHP per engine for brief period (engine failure during takeoff).</li>
  <li><strong>Emergency rating:</strong> 1.25 × MTOP.</li>
  <li><strong>NL compressor type:</strong> axial (1st stage).</li>
  <li><strong>NH compressor type:</strong> centrifugal (2nd stage).</li>
  <li><strong>Power turbine:</strong> 2-stage on independent shaft.</li>
  <li><strong>PMA active threshold:</strong> NH > 20%.</li>
  <li><strong>HBOVs per engine:</strong> 2 (one LP + one HP).</li>
  <li><strong>RDC TOP TRQ DEC step:</strong> 2% per push.</li>
  <li><strong>RDC TOP TRQ DEC limit:</strong> 10% reduction max.</li>
  <li><strong>RDC NP LDG window:</strong> 15 seconds from CL movement to MAX/1020.</li>
  <li><strong>RDC NP LDG cancellation:</strong> PLA ≥ 65°.</li>
  <li><strong>EVENT MARKER record window:</strong> 2 minutes BEFORE event + 1 minute AFTER event.</li>
  <li><strong>PROPELLER GROUND RANGE light threshold:</strong> 10° blade angle and below.</li>
  <li><strong>Number of engines per Q400:</strong> 2.</li>
  <li><strong>Max torque digital range:</strong> 0-199% in 1% increments.</li>
  <li><strong>NTOP rating PLA:</strong> 80°. MTOP: 95°. MCR: 77.5°. MCL: 82.5°. O/T: 100°.</li>
  <li><strong>Engine ratings count:</strong> 5 core (NTOP / MTOP / MCP / MCL / MCR) + RDC TOP variant.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>PW150A-2-ENG</strong> — 2 Pratt & Whitney PW150A turboprops. Dowty R408 6-blade props.</li>
  <li><strong>4580-NTOP-5071-MTOP</strong> — 4,580 SHP normal takeoff; 5,071 SHP MTOP UPTRIM (engine failure).</li>
  <li><strong>NL-AXIAL-NH-CENTRIFUGAL</strong> — NL 1st stage axial, NH 2nd stage centrifugal. NH drives accessory gearbox.</li>
  <li><strong>5-RATINGS-CORE</strong> — NTOP / MTOP / MCP / MCL / MCR + RDC TOP variant.</li>
  <li><strong>PLA-FADEC-FORWARD-PEC-BETA</strong> — PLA drives FADEC in forward; drives prop blade angle in beta/reverse.</li>
  <li><strong>CL-PEC-RPM-FUEL</strong> — CL drives PEC for RPM, ratings, manual feather, fuel on/off.</li>
  <li><strong>PMA-NH-20</strong> — PMA active above NH 20%; below = ESS bus alternate.</li>
  <li><strong>2-HBOV-LP-HP</strong> — 2 HBOVs per engine: one LP + one HP for surge margin.</li>
  <li><strong>BYPASS-ICING-PRECIP-BIRD-CONTAM</strong> — bypass doors open in icing / heavy precip / bird activity / contaminated runway.</li>
  <li><strong>RDC-TOP-2-10</strong> — RDC TOP TRQ DEC: 2% steps to 10% limit. Not in MTOP/MCP.</li>
  <li><strong>RDC-NP-LDG-15-65</strong> — RDC NP LDG: 15-sec window from CL move; cancel at PLA ≥65°.</li>
  <li><strong>EVENT-2-1</strong> — EVENT MARKER: 2 min before + 1 min after.</li>
  <li><strong>10-DEG-GROUND-RANGE</strong> — PROPELLER GROUND RANGE lights at 10° blade angle and below (different from 16° flight fine stop).</li>
  <li><strong>FUEL-OFF-TESTS-OS</strong> — normal CL FUEL OFF shutdown tests NH overspeed protection.</li>
 </ol>
<p>Engine start chant: <em>"CL START & FEATHER · starter · NH builds · 20% PMA active · CL idle · CL 1020."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
