-- =============================================================================
-- AviatorTutor — Phase 17: ATA 71 Powerplant — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'powerplant' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'powerplant-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Two PW150A — 4,580 SHP, 5,071 with UPTRIM',
 'Two Pratt & Whitney PW150A turboprops drive Dowty R408 6-blade composite propellers through reduction gearboxes. Normal takeoff power is 4,580 SHP per engine. With MTOP rating selected, an automatic UPTRIM provides 5,071 SHP for a brief period if an engine fails during takeoff — your safety margin. Two control levers per engine: Power Lever drives the FADEC in forward range and the propeller blade angle in beta/reverse; Condition Lever drives the PEC for RPM, ratings, manual feather, and fuel on/off. The architecture is two-spool: NL axial 1st-stage compressor + NH centrifugal 2nd-stage, each with its own single-stage turbine, plus a separate two-stage power turbine driving the reduction gearbox.',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'PW150A architecture',
 'PW150A-2-ENG · 4580-NTOP-5071-MTOP · two control levers per engine.',
 'On engine failure at V1, the MTOP UPTRIM gives you 5,071 SHP automatically. That extra power is what keeps the climb-out manageable.',
 NULL),

(@lesson_id, 20, 'concept',
 'NL Axial + NH Centrifugal',
 'PW150A is a two-spool design with a clean compressor split. <strong>NL — Low-Pressure compressor — AXIAL — first stage.</strong> Single-stage NL turbine drives it. <strong>NH — High-Pressure compressor — CENTRIFUGAL — second stage.</strong> Single-stage NH turbine drives it. After the NH stage the gas drives a separate <strong>two-stage power turbine</strong> on a third shaft, which connects through the reduction gearbox to the propeller. The NH compressor also drives the accessory gearbox mounted on top of the engine — that gearbox runs the oil pumps, HP fuel pump, PMA, and DC starter-generator. Memorise the compressor types: NL axial, NH centrifugal.',
 'diagram', '/assets/aircraft/q400/powerplant-flow.svg',
 'PW150A two-spool architecture',
 'NL-AXIAL-NH-CENTRIFUGAL. NH drives accessory gearbox.',
 'A check captain quoting "NH compressor type" — answer is centrifugal. NL is axial. Don''t swap.',
 NULL),

(@lesson_id, 30, 'concept',
 'PLA + CL — Two Levers, Different Jobs',
 'The Power Lever (PLA) and Condition Lever (CL) divide engine control cleanly. <strong>Power Lever:</strong> in the forward range (PLA above Flight Idle), drives the FADEC to set engine power. In beta/reverse (PLA below Flight Idle), drives the propeller blade angle directly via PEC closed-loop blade angle control (Phase 16). <strong>Condition Lever:</strong> via the PEC, sets propeller RPM in the forward thrust range (850 / 900 / 1020 per Phase 16 constant-speed entries). Selects engine power ratings (MCL / MCR via pushbuttons). Provides manual propeller feathering. Provides fuel on/off control for engine start (START & FEATHER position) and shutdown (FUEL OFF). The CL is the "rating selector" + fuel master. The PLA is the "power demand."',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'Power Lever vs Condition Lever roles',
 'PLA-FADEC-FORWARD-PEC-BETA · CL-PEC-RPM-FUEL.',
 'When you move the PLA forward, you''re asking FADEC for more power. When you move the CL forward, you''re asking PEC for higher RPM.',
 JSON_OBJECT(
   'prompt', 'In the forward range, the Power Lever drives which controller and for what purpose?',
   'options', JSON_ARRAY(
     'Power Lever drives PEC for prop RPM',
     'Power Lever drives FADEC for engine power; in beta/reverse it drives prop blade angle',
     'Power Lever drives only the prop, no engine effect',
     'Power Lever drives the autofeather only'
   ),
   'correct_index', 1,
   'explanation', 'Forward: PLA → FADEC for power. Beta/reverse: PLA → prop blade angle (via PEC). Mnemonic: PLA-FADEC-FORWARD-PEC-BETA.'
 )),

(@lesson_id, 40, 'system',
 'Engine Ratings — NTOP / MTOP / MCP / MCL / MCR',
 'Five core engine ratings, displayed on the Engine Display (ED), are determined by Condition Lever position + bleed selection + pushbuttons. <strong>NTOP (Normal Takeoff):</strong> CL MAX/1020, BLEED MIN/OFF or ON/MIN. <strong>MTOP (Max Takeoff):</strong> CL 1020 + MTOP pushbutton (or auto via UPTRIM on engine failure). 4,580 SHP normal; 5,071 SHP UPTRIM. <strong>MCP (Max Continuous Power):</strong> CL 1020, BLEED ON/NORM or MAX. <strong>MCL (Max Climb):</strong> CL MIN/850 + MCL pushbutton — display shows MCL with 900 RPM. <strong>MCR (Max Cruise):</strong> CL 900 + MCR pushbutton — display shows MCR with 850 RPM. Plus <strong>RDC TOP</strong> variant when reduced takeoff power is selected via RDC TOP TRQ DEC pushbutton (2% steps, max 10%).',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'Five engine ratings + bleed/CL/pushbutton interaction',
 '5-RATINGS-CORE. NTOP/MTOP/MCP/MCL/MCR + RDC TOP.',
 'When BLEED is ON/NORM with CL at 1020, NTOP becomes MCP — the system protects the engine from the bleed load.',
 NULL),

(@lesson_id, 50, 'system',
 'PLA Power Curve — 35° Idle to 95° MTOP',
 'In the forward range, the PLA position commands FADEC power per a programmed curve. <strong>IDLE: ~35°</strong>. <strong>1000 SHP: ~5°</strong> (very low flight idle range). <strong>MCR: 77.5°.</strong> <strong>NTOP: 80°.</strong> <strong>MCL: 82.5°.</strong> <strong>MTOP: 95°.</strong> <strong>O/T (overtravel): 100°.</strong> Emergency rating beyond MTOP: 1.25 × MTOP. The non-linear curve is intentional — small movements at low PLA produce small power changes; large movements at high PLA produce proportionally larger power changes. Memorise the rating PLA values for sim work.',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'PLA-vs-power curve forward range',
 'IDLE 35 · MCR 77.5 · NTOP 80 · MCL 82.5 · MTOP 95.',
 'When the PLA is between idle and 50% rating, the prop may briefly enter flight beta (Phase 16) — PEC re-enters constant speed automatically as airspeed builds.',
 NULL),

(@lesson_id, 60, 'system',
 'RDC NP LDG — Reduced Prop Speed for Landing',
 'Reduced Np for Landing is a noise + comfort feature for landing. The procedure has a sequence and a 15-second window. <strong>(1)</strong> Power Levers between Flight Idle and ~50% rating. <strong>(2)</strong> Condition Lever at MIN/850. <strong>(3)</strong> Push the RDC NP LDG pushbutton on the engine control panel. <strong>(4)</strong> Within 15 SECONDS, advance the Condition Lever to MAX/1020. <strong>(5)</strong> NP remains at 850 RPM despite CL at 1020 — REDUCED NP LANDING displayed on the ED. Cancellation: PLA ≥ 65°, OR push RDC NP LDG button again. If you don''t advance the CL within 15 seconds, the RDC NP mode cancels.',
 'video', '/assets/aircraft/q400/powerplant-flow.svg',
 'RDC NP LDG sequence + 15-sec window',
 'RDC-NP-LDG-15-65. PLA between idle and 50% · CL 850 · push · CL 1020 within 15 sec.',
 'On a noise-sensitive arrival, RDC NP LDG saves cabin and ground noise. Brief the F/A about the unusual prop sound.',
 JSON_OBJECT(
   'prompt', 'In the RDC NP LDG procedure, what is the time window from pushing the RDC NP LDG button to advancing the CL to MAX/1020?',
   'options', JSON_ARRAY(
     '5 seconds',
     '10 seconds',
     '15 SECONDS — RDC NP mode cancels if exceeded',
     '30 seconds'
   ),
   'correct_index', 2,
   'explanation', '15-second window from RDC NP LDG push to CL MAX/1020 advance. Mnemonic: RDC-NP-LDG-15-65.'
 )),

(@lesson_id, 70, 'system',
 'PMA — Active Above NH 20%',
 'The Permanent Magnet Alternator (PMA) is the primary electrical source for the engine control system. It''s mounted on the accessory gearbox, driven by the NH compressor. PMA has independent coils that supply power to the individual channels of the FADEC. <strong>Critical threshold: PMA is active when NH is above 20% MINIMUM.</strong> Below NH 20%, the aeroplane essential power buses provide alternate electrical power to the FADEC. So the engine start sequence uses ESS bus power for FADEC; once NH passes 20%, the PMA takes over and the FADEC runs on its own dedicated electrical source. PMA failure in flight: ESS bus continues to power FADEC — no interruption to engine control.',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'PMA active threshold + ESS bus alternate',
 'PMA-NH-20. ESS bus alternate for start + PMA fail.',
 'A real PMA failure in flight is a maintenance write-up, not an emergency — ESS bus has it covered.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'HBOV + Bypass Door',
 'Two helpful subsystems for engine reliability. <strong>HBOVs (Handling Bleed-Off Valves):</strong> 2 per engine, one LP + one HP. They bleed compressor air from the main gas path to provide increased surge margin during starting, steady state operation, and transient PLA movements. The FADEC controls them automatically — no crew action needed. <strong>Bypass door:</strong> at each engine nacelle intake. Prevents solids and precipitation from entering the engine. Manually selected via switchlights on the ICE PROTECTION panel. Open in: ICING conditions, heavy PRECIPITATION, BIRD activity (e.g. flocks near runway), or CONTAMINATED RUNWAYS (slush, snow).',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'HBOV (auto) + bypass door (manual)',
 '2-HBOV-LP-HP · BYPASS-ICING-PRECIP-BIRD-CONTAM.',
 'On a contaminated runway take-off, brief bypass doors open before lining up. The conditions list is short and operationally relevant.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Engine Start + Shutdown — CL is the Master',
 'Engine start sequence (with external or APU power applied). <strong>(1)</strong> CL at START & FEATHER position. <strong>(2)</strong> Push the dedicated starter switch. <strong>(3)</strong> FADEC runs on essential bus power (PMA inactive below NH 20%). FADEC sequences fuel, ignition, light-off. <strong>(4)</strong> NH builds; passes 20% — PMA takes over electrical for FADEC. <strong>(5)</strong> Starter cuts at half NH operating speed (per Phase 16 starter-generator). <strong>(6)</strong> CL to MIN/850 at idle; CL to MAX/1020 for normal operation. <strong>Engine shutdown:</strong> CL to FUEL OFF. The Engine System tests the NH overspeed protection circuitry by USING IT to shut down the engine — every shutdown is also a test of the OS protection. Fire handle shutdown: separate path via the FMU dedicated fuel shutoff energized closed by the PULL FUEL/HYD OFF handle.',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'Start + shutdown sequence',
 'CL START & FEATHER · NH 20% PMA · CL FUEL OFF tests OS · fire handle alt path.',
 'Brief the FO on the start sequence. The PMA handover at NH 20% is silent — no crew action.',
 JSON_OBJECT(
   'prompt', 'How is the NH overspeed protection circuitry tested during normal operations?',
   'options', JSON_ARRAY(
     'Pre-flight test pushbutton',
     'Every normal CL FUEL OFF shutdown — Engine System uses NH O/S protection to shut down the engine',
     'Manual annual test only',
     'Auto-test in flight'
   ),
   'correct_index', 1,
   'explanation', 'Every normal shutdown via CL FUEL OFF tests NH overspeed protection. The system shuts down the engine using the OS circuitry. Mnemonic: FUEL-OFF-TESTS-OS.'
 )),

(@lesson_id, 100, 'abnormal',
 'Engine Failure on Takeoff with UPTRIM',
 'Engine failure on takeoff with autofeather armed (Phase 16). The propeller side: torque drops below 25% OR Np below 816 (80%) for ≥3 seconds → autofeather triggers, prop feathers, A/F ARM out, AUX FEATHER PUMP energized. The engine side: <strong>FADEC of the OPERATING engine receives an UPTRIM command via PEC.</strong> UPTRIM raises maximum takeoff power from 4,580 SHP NTOP to 5,071 SHP MTOP — automatically. This is the safety margin built into the design. The crew sees UPTRIM TRQ on the ED + MTOP rating displayed. Combined with autofeather, the system handles both the failed prop AND the operating engine''s power increase simultaneously. Crew action: maintain control, identify, run ENGINE FAILURE on TAKEOFF QRH, declare, return.',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'UPTRIM 4580 → 5071 SHP automatic',
 'AF feathers · UPTRIM 5071 SHP · MTOP rating · MIRROR Phase 16.',
 'The 491 SHP UPTRIM increase is what keeps the climb-out from being a struggle. It''s automatic; trust it.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'Engine Fire — FMU Fuel Shutoff Path',
 'Engine fire procedure (Phase 6). The PULL FUEL/HYD OFF handle on the Fire Protection Panel triggers a dedicated path: a fuel shutoff switch in the Fuel Metering Unit (FMU) is energized closed when the handle is pulled. This shuts off engine fuel at the source — different from the CL FUEL OFF path which runs through the engine system test. Both paths achieve fuel cutoff but via different components. The fire handle path also closes the hydraulic shutoff valve (Phase 11 + 6) and arms the engine fire bottles. Engine fire memory items: FLASH · PRESS · PULL · EXTG · 30-sec · OTHER (Phase 6).',
 'video', '/assets/aircraft/q400/powerplant-flow.svg',
 'Fire handle FMU fuel shutoff path',
 'FMU fuel shutoff via PULL FUEL/HYD OFF handle. Different path from CL FUEL OFF.',
 'The two paths to fuel shutoff (CL vs fire handle) are designed for different scenarios — both work, both should be drilled.',
 NULL),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Powerplant Non-Normals',
 'Q400 QRH non-normals for the powerplant cluster into seven groups. (1) ENGINE FAILURE on TAKEOFF — autofeather + UPTRIM (combined Phase 16/17). (2) ENGINE FIRE in flight — Phase 6 chant (FLASH/PRESS/PULL/EXTG/30-sec/OTHER). (3) PMA failure — ESS bus alternate; defer per MEL. (4) FADEC channel fault — dual-channel maintains control. (5) Bypass door fault — plan around (avoid icing, divert if persistent). (6) Engine over-torque — push EVENT MARKER, investigate post-flight via EMS. (7) RDC NP LDG cancellation — verify CL position and PLA. Most are not memory items but the engine-failure-with-autofeather sequence must be reflexive.',
 'image', '/assets/aircraft/q400/powerplant-flow.svg',
 'QRH powerplant cluster',
 'ENGINE FAIL · FIRE · PMA · FADEC · BYPASS · OVER-TORQUE · RDC NP LDG.',
 'The intersection of Phase 6 (fire), Phase 16 (autofeather), and Phase 17 (UPTRIM) is the engine-failure-on-takeoff scenario. Drill it.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Engine Over-Torque on Takeoff',
 'Setup: takeoff roll. Both PLAs to RATING DETENT (95° MTOP). Autofeather armed. Halfway through the takeoff roll the FO calls "torque check — engine 1 indicating 105%, engine 2 normal."\n\nFirst 5 seconds: PF maintains aircraft control. Decision: continue or reject? At 105% torque the engine is in over-torque territory but not yet at a critical limit; the engine is still producing useful thrust. Below V1: rejected takeoff is the safer call if discomfort with the situation. Above V1: continue, the over-torque is recorded by EMS for post-flight investigation.\n\nNext action: PF (or PNF if hands free) pushes the EVENT MARKER pushbutton. EMS captures 2 minutes BEFORE the event + 1 minute AFTER — covers the takeoff roll + initial climb. Post-flight: maintenance reviews the EMS data and decides on engine inspection/removal. The crew''s job is to fly the aeroplane and document the event; the engine experts make the long-term call.',
 'animation', '/assets/aircraft/q400/powerplant-flow.svg',
 'Engine over-torque scenario',
 'EVENT MARKER · 2 min before + 1 min after · maintenance reviews EMS.',
 'EVENT MARKER is your friend on any unusual engine event. Push it; let the engine team review.',
 JSON_OBJECT(
   'prompt', 'You experience an engine over-torque event during takeoff. What does the EVENT MARKER pushbutton do, and why use it?',
   'options', JSON_ARRAY(
     'Resets the FADEC',
     'Bookmarks the event in EMS — captures 2 minutes before + 1 minute after for post-flight investigation',
     'Triggers autofeather',
     'Reduces takeoff power'
   ),
   'correct_index', 1,
   'explanation', 'EVENT MARKER bookmarks the EMS data: 2 min before + 1 min after the push. Mnemonic: EVENT-2-1.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Powerplant in 60 Seconds',
 'Recap:\n  • 2 PW150A turboprops · Dowty R408 6-blade composite props · reduction gearbox.\n  • 4,580 SHP NTOP · 5,071 SHP MTOP UPTRIM (engine failure brief period). Emergency = 1.25 × MTOP.\n  • Architecture: NL axial 1st + NL turbine; NH centrifugal 2nd + NH turbine; 2-stage power turbine on independent shaft.\n  • Accessory gearbox driven by NH: oil pumps, HP fuel pump, PMA, DC starter-gen.\n  • Power Lever: FADEC in forward, prop blade angle in beta/reverse.\n  • Condition Lever: PEC for RPM, ratings, manual feather, fuel on/off.\n  • 5 ratings: NTOP / MTOP / MCP / MCL / MCR + RDC TOP. CL + bleed + pushbuttons determine displayed rating.\n  • RDC NP LDG: PLA <50% rating + CL 850 + push + CL 1020 within 15 sec; cancel at PLA ≥65°.\n  • RDC TOP TRQ DEC: 2% steps to 10% limit; not in MTOP/MCP.\n  • EVENT MARKER: 2 min before + 1 min after.\n  • PMA active above NH 20%. ESS bus alternate.\n  • 2 HBOVs (LP + HP) for surge margin. Bypass doors open in icing/precip/bird/contam.\n  • Normal CL FUEL OFF tests NH overspeed protection.\n  • Fire handle: FMU dedicated fuel shutoff (different path from CL).\n  • PROPELLER GROUND RANGE lights at 10° (Phase 16 was 16° flight fine stop).\n  • PLA forward range: IDLE 35° · MCR 77.5° · NTOP 80° · MCL 82.5° · MTOP 95° · O/T 100°.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'PW150A-2-ENG · 4580-NTOP-5071-MTOP · NL-AXIAL-NH-CENTRIFUGAL · 5-RATINGS-CORE · PLA-FADEC-FORWARD-PEC-BETA · CL-PEC-RPM-FUEL · PMA-NH-20 · 2-HBOV-LP-HP · BYPASS-ICING-PRECIP-BIRD-CONTAM · RDC-TOP-2-10 · RDC-NP-LDG-15-65 · EVENT-2-1 · 10-DEG-GROUND-RANGE · FUEL-OFF-TESTS-OS',
 'Fourteen mnemonics carry every powerplant question. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
