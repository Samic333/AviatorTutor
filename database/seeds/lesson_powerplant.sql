-- =============================================================================
-- AviatorTutor — Phase 17 (ATA 71 Powerplant) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'powerplant' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'powerplant-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Powerplant — PW150A, FADEC, Ratings, PMA',
     'powerplant-overview',
     'overview',
     'Two Pratt & Whitney PW150A turboprops. NL axial first-stage compressor + NH centrifugal second-stage compressor with their respective single-stage turbines. Two-stage power turbine drives reduction gearbox to propeller. Accessory gearbox driven by NH compressor: oil pumps, HP fuel pump, PMA, DC starter-generator. 4,580 SHP normal takeoff; auto UPTRIM on MTOP rating gives max 5,071 SHP brief period on engine failure during takeoff. Two control levers per engine: Power Lever (PLA) drives FADEC in forward, controls prop blade angle in beta/reverse. Condition Lever (CL) drives PEC for RPM, selects ratings, manual feather, fuel on/off. Engine ratings: NTOP/MTOP/MCP/MCL/MCR + RDC TOP. RDC NP LDG reduces NP for landing. PMA primary electrical source for FADEC (active above NH 20%); a/c ESS buses backup. HBOVs (one LP + one HP) bleed for surge margin. Bypass doors at intake prevent solids ingestion (icing/precip/bird/contaminated runway).',
     '<p>Two Pratt & Whitney PW150A turboprops drive Dowty R408 six-blade constant-speed variable-pitch fully-feathering propellers through engine reduction gearboxes. The PW150A architecture is two-spool: an axial NL low-pressure compressor + centrifugal NH high-pressure compressor, each with their own single-stage turbines, with a separate two-stage power turbine driving the reduction gearbox to the propeller. Two control levers per engine: the Power Lever (PLA) commands the FADEC in the forward range and the propeller blade angle in beta/reverse; the Condition Lever (CL) commands the PEC to set propeller RPM in the forward thrust range, selects engine ratings, performs manual feathering, and provides fuel on/off for start and shutdown. Engine ratings are layered: NTOP, MTOP, MCP, MCL, MCR plus reduced takeoff (RDC TOP). The 5,071 SHP UPTRIM on MTOP is the safety margin for engine failure during takeoff. The PMA on the accessory gearbox is the primary electrical source for FADEC (above NH 20%); essential power is the backup. Two HBOVs (LP + HP) bleed compressor air for surge margin; bypass doors at the intakes protect the engine from solids in icing, precipitation, bird activity, or on contaminated runways.</p>',
     JSON_ARRAY(
       'Two PW150A turboprop engines. Drive Dowty R408 6-blade composite propellers through reduction gearbox',
       'Normal takeoff power: 4,580 SHP. MTOP UPTRIM: 5,071 SHP for brief period (engine failure during takeoff)',
       'Architecture: NL axial 1st-stage compressor + single-stage NL turbine; NH centrifugal 2nd-stage compressor + single-stage NH turbine; 2-stage power turbine on independent 3rd shaft drives reduction gearbox to propeller',
       'Accessory gearbox driven by NH compressor: Oil Pressure + Scavenge Pumps, HP Fuel Pump, Permanent Magnet Alternator (PMA), DC Starter/Generator',
       'Control: Power Lever (PLA) + Condition Lever (CL) per engine. Power Lever drives FADEC in forward range, drives prop blade angle in beta/reverse. Condition Lever drives PEC for RPM, ratings, manual feather, fuel on/off',
       'Engine ratings (CL position + bleed setting + pushbuttons determine rating displayed on ED): NTOP / MTOP / MCP / MCL / MCR + RDC TOP',
       'NTOP: CL MAX/1020, BLEED MIN/OFF or ON/MIN. MTOP: CL 1020 + MTOP pushbutton on (or auto via UPTRIM), bleed similar. MCP: CL 1020, BLEED ON/NORM or MAX',
       'MCL: CL MIN/850, MCL pushbutton selected. Displays MCL with 900 RPM',
       'MCR: CL 900, MCR pushbutton selected. Displays MCR with 850 RPM',
       'RDC TOP TRQ DEC pushbutton reduces NTOP requested power in 2% steps to a limit of 10%. Cannot be activated while in MTOP or MCP rating. RDC TOP TRQ RESET restores normal',
       'RDC NP LDG (Reduced Np for Landing) pushbutton: enables reduced prop speed for landing. Configuration: power levers between Flight Idle and ~50% rating, CL at MIN/850, push RDC NP LDG, then advance CL to MAX/1020 within 15 seconds. NP remains at 850 RPM. Cancel at PLA ≥ 65° or by pushing again',
       'EVENT MARKER pushbutton: places bookmark in Engine Monitoring System (EMS). Stores data snapshot + trace 2 minutes before event + 1 minute after',
       'PMA (Permanent Magnet Alternator): primary electrical source for engine control system. Independent coils provide power to individual FADEC channels. Active when NH > 20% minimum. A/C essential power buses provide alternate for engine starting + PMA failure',
       'HBOVs (Handling Bleed-Off Valves): bleed engine air from main gas path for increased surge margin during starting, steady state, and transient operation. Two valves: one LP + one HP',
       'Bypass doors at each engine nacelle intake: prevent solids and precipitation from entering intake. Controlled by switchlights on ICE PROTECTION panel. Open in: icing, heavy precipitation, bird activity, contaminated runways',
       'Engine Display (ED) parameters from FADEC: TRQ %, PROP RPM, NH %, ITT °C, NL %, Fuel Flow (hundreds of kg/hr), Oil temp °C + pressure PSI (dual analog/digital), Engine Rating Mode annunciation (green)',
       'Normal engine shutdown: CL to FUEL OFF. Engine System tests NH overspeed protection circuitry by using it to shut down the engine',
       'Fire handle shutdown: dedicated fuel shutoff switch in FMU activated via PULL FUEL/HYD OFF handle on Fire Protection Panel. Energized closed when handle pulled',
       'Power Lever positions: MAX REV / DISC / FLT IDLE / Forward / Rating Detent. Beta range: PLA below Flight Idle (blade angle controlled by PLA position)',
       'PROPELLER GROUND RANGE lights illuminate at blade angles 10° and below — confirms ground beta operation (different from 16° flight fine stop in Phase 16)',
       'Power request vs PLA (forward range): IDLE ~35°, NTOP 80°, MCR 77.5°, MCL 82.5°, MTOP 95°, O/T 100°. Emergency rating = 1.25 × MTOP',
       'Bleed display on ED below engine rating mode: BLEED in white when MIN bleed; BLEED in YELLOW when bleed selection forces NTOP → MCP rating change'
     ),
     JSON_ARRAY(
       'PMA active only above NH 20%. Below 20%, FADEC runs on a/c essential bus. So engine start uses ESS bus; once NH > 20%, PMA takes over.',
       'MTOP UPTRIM is takeoff-only protection — automatic 5,071 SHP for brief period if engine fails. Condition: MTOP rating selected before takeoff.',
       'RDC NP LDG procedure: PLA between Flight Idle and 50% rating + CL at MIN/850 first, then push RDC NP LDG, then CL to 1020 within 15 sec. Order matters; window matters.',
       'BLEED selection changes rating: NTOP rating becomes MCP if bleed is selected NORM or MAX. Yellow BLEED on ED warns of this.',
       'Bypass door is a manual selection — open in icing/precip/bird/contaminated. Doesn''t auto-open.',
       'Fire handle pulled = FMU dedicated fuel shutoff energized closed = engine fuel cut. Different path from CL FUEL OFF.',
       'Power Lever in beta/reverse drives blade angle, NOT FADEC. The PEC handles the prop in beta; FADEC handles fuel.',
       'EVENT MARKER stores 2 min before + 1 min after — useful for incident investigation. Push it on any unusual event.'
     ),
     JSON_ARRAY(
       'Normal takeoff: 4,580 SHP. MTOP UPTRIM: 5,071 SHP. Different numbers.',
       'NL = axial 1st stage. NH = centrifugal 2nd stage. Different compressor types.',
       'PMA active above NH 20% only. Below = ESS bus.',
       'Engine ratings displayed depend on CL position + BLEED setting + pushbuttons. Five core ratings: NTOP / MTOP / MCP / MCL / MCR.',
       'RDC TOP TRQ DEC: 2% steps to limit of 10%. Cannot activate in MTOP or MCP.',
       'RDC NP LDG: 15-second window from CL movement to MAX/1020 — easy trap.',
       'PROPELLER GROUND RANGE lights at 10° (NOT 16° — that''s flight fine stop). Different thresholds.',
       'Two HBOVs: one LP, one HP. Not just one.',
       'Bypass door: prevents solids in icing/precip/bird/contam runways. Manual selection.',
       'EVENT MARKER: 2 min before + 1 min after. Both numbers.',
       'CL FUEL OFF tests NH overspeed protection during normal shutdown.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'powerplant-overview';
