-- =============================================================================
-- AviatorTutor — Phase 16 (ATA 61 Propeller) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'propeller' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'propeller-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Propeller — Pitch Control, Modes, Overspeed, Autofeather',
     'propeller-overview',
     'overview',
     'Six-blade composite propeller per engine. PEC (dual-channel) commands the PCU which meters HP engine oil through a two-stage servo valve to fine/coarse pitch chambers. Counterweighted blades with natural twisting moment toward HIGH pitch — HP loss → autocoarsen to safe windmilling pitch. Modes: Constant Speed (in flight via PEC+PCU+OSG), Beta (PLA below Flight Idle, ground, blade-angle control), Reverse (660–950 RPM, max 1500 SHP). Overspeed governor: hydraulic OSG at ~1071 RPM (105%); electronic FADEC NP at ~1122 RPM. Hydraulic OSG LOCKED OUT in reverse — FADEC is primary reverse protection. Flight fine stop: 16° hard (hydraulic) / 16.5° soft (PEC) when PLA at/above Flight Idle. Ground Beta Enable valve (GBE) locks out OSG on ground beta. Autofeather: ARM with both torque >50% + PLA >60°; trigger ONE torque <25% OR Np <816 (80%) for ≥3 sec — auto-feathers + UPTRIM operating engine. Synchrophasing reduces cabin noise; not active at takeoff.',
     '<p>The Q400 has a six-blade composite propeller per engine, controlled by an electronic control system (PEC) that commands a hydromechanical pitch control unit (PCU) feeding HP engine oil to fine or coarse pitch chambers. The defining design choice is that the blades are counterweighted to seek HIGH PITCH naturally — so a loss of hydraulic pressure auto-coarsens the blades to a safe windmilling condition rather than an over-speed/over-torque event. Layered protection: a hydraulic Overspeed Governor at 1071 RPM (105%) and an electronic FADEC overspeed at 1122 RPM, with the hydraulic section locked out in reverse so the FADEC takes priority. The flight-fine-stop logic prevents in-flight blade angles below 16° (hydraulic hard stop) or 16.5° (PEC soft stop) — keeping the counterweight effort positive toward coarse pitch and ensuring OSG effectiveness. Autofeather watches both engines and auto-feathers a failed engine after a 3-second confirmation, while uptrimming the operating FADEC. Memorise the 16/16.5 stops, the 1071/1122 OSG numbers, the 50/25 autofeather torques, and the 80% Np autofeather trigger.</p>',
     JSON_ARRAY(
       'Six-blade composite propeller per engine. Counterweighted blades — natural twisting moment toward HIGH PITCH in flight (counterweight effort dominates centrifugal twisting moment)',
       'PEC (Propeller Electronic Control): dual-channel microprocessor, mounted in each engine nacelle. Inputs from aeroplane + propeller sensors + engine control system',
       'PCU (Pitch Control Unit): hydromechanical, electrically commanded by PEC. Meters HP engine oil to two-stage servo valve. Feeds fine or coarse pitch chambers',
       'High-Pressure PCU Pump + Propeller Overspeed Governor: HP pump driven from reduction gearbox. OSG is independent flyweight design, driven directly from pump driver gear',
       'Propeller Feathering Pump: 28 VDC electrical motor driving external gear pump. Independent feathering source. Also used for feather/unfeather during maintenance',
       'Magnetic Pickup Unit (MPU): provides propeller speed signal to PEC for governing, synchrophasing, and ANVS propeller balance monitoring',
       'HP loss in flight: blades autocoarsen to safe high-pitch windmilling (low drag) — counterweight effort dominates',
       'HP loss in reverse: blades go toward MAX REVERSE blade angle',
       'Constant Speed Mode entered when propeller speed reaches 850, 900, or 1020 RPM per Condition Lever selection',
       'Hydraulic Overspeed Governor (OSG): drops HP supply at ~105% (1071 RPM). Reduces RPM via natural counterweight coarse-seeking. Reconnects below the threshold',
       'Electronic Overspeed (FADEC): NP overspeed circuit signals FMU to reduce fuel at ~1122 RPM. Reduces engine power, drops prop RPM',
       'Hydraulic OSG LOCKED OUT in reverse — FADEC electronic section is primary overspeed protection in reverse',
       'OSG tested on ground via PROP O''SPEED GOVERNOR test switch on Pilots Side Panel',
       'Flight Fine Stop: 16° hard hydraulic cut-off (cannot go below 16° in flight constant-speed mode)',
       'Flight Fine Stop: 16.5° SOFT stop programmed in PEC, operative while PLA at or above Flight Idle',
       'To enable blade angles below 16°: PLA below Flight Idle AND weight-on-wheels',
       'PROPELLER GROUND RANGE lights illuminate when blade angles below 16° (PLA below Flight Idle)',
       'Detent on PLA quadrant prevents unintentional movement of PLA below Flight Idle in flight',
       'Beta warning horn sounds if PLA is brought below Flight Idle gate in flight',
       'Ground Beta Enable valve (GBE): locks out OSG during ground beta to prevent transient overspeed at flat pitch interfering with pitch control. GBE failure caught by scheduled OSG test',
       'Beta Range: PLA below Flight Idle (ground only). NP underspeed governed at 660 RPM by FADEC + engine fuel system',
       'Reverse Speed Control: closed-loop propeller RPM control between 660 and 950 RPM. Max 1500 SHP',
       'Reverse: at low airspeeds prop may reach max reverse stop; engine overspeed governor controls speed up to 1020 RPM',
       'Synchrophasing: PEC enters synchrophase mode when both propeller speeds within predetermined difference. Reduces cabin noise by phase-controlling slave-vs-master propeller. Phase demand from CLA position',
       'Synchrophasing does NOT operate at takeoff',
       'Autofeather: selected ON for takeoff only via AUTOFEATHER switchlight on engine instrument panel. SELECT light + A/F SELECT on ED',
       'Autofeather ARM conditions: both engine torques >50% AND both PLAs advanced beyond 60°',
       'Autofeather TRIGGER: ONE engine torque drops below 25% OR Np below 816 (80%) for at least 3 SECONDS',
       'Autofeather actions: A/F ARM light goes out, AUX FEATHER PUMP energized, prop feathers automatically, FADEC of operating engine receives UPTRIM command'
     ),
     JSON_ARRAY(
       'Counterweighted blades + HP loss = autocoarsen to safe windmill. The system fails safe in the high-pitch direction in flight.',
       'In reverse, the OSG hydraulic section is LOCKED OUT — the FADEC electronic overspeed is your only protection. Don''t test OSG in reverse.',
       '16° / 16.5° flight fine stops: hard hyd stop at 16° + soft PEC stop at 16.5° while PLA at or above Flight Idle. Both prevent in-flight feather creep and ensure OSG effectiveness.',
       'Beta warning horn fires if you raise the PLA gate in flight. Don''t do it. The whole flight-fine-stop architecture exists because below-Flight-Idle in flight is dangerous.',
       'Synchrophasing turns off at takeoff intentionally — high-power transients would confuse the slave/master timing.',
       'Autofeather is takeoff-only. Selected on before takeoff, off after climb-out. The 50/25 torque thresholds + 3-second confirm window prevent nuisance feathers.',
       'Autofeather UPTRIMs the operating engine via FADEC — extra power on the surviving engine is automatic.'
     ),
     JSON_ARRAY(
       'Flight fine stop: 16° HARD (hydraulic) and 16.5° SOFT (PEC). Two different numbers; both relevant.',
       'OSG: 1071 RPM hydraulic / 1122 RPM electronic FADEC. Two different limits.',
       'Constant speed entry RPMs: 850 / 900 / 1020 per CL position. Three values.',
       'Beta governing: 660 RPM by FADEC (engine fuel system) — different from constant speed.',
       'Reverse range: 660–950 RPM normal; up to 1020 RPM if reaching max reverse stop. 1500 SHP max.',
       'Autofeather ARM: torque BOTH engines >50% + PLA BOTH >60°. Both conditions.',
       'Autofeather TRIGGER: ONE engine torque <25% OR Np <816 (80%) for ≥3 SECONDS. Three thresholds (25% torque, 816 Np, 3 seconds).',
       'Hydraulic OSG locked out in REVERSE only — not in beta or constant speed.',
       'Synchrophasing: NOT active at takeoff. Important for noise certification and cabin comfort.',
       'GBE valve locks out OSG ON GROUND BETA only. Failure caught by OSG test.',
       'Counterweights cause HIGH-pitch seeking in flight, but at flat pitch / negative blade angle they seek NEGATIVE pitch (toward max reverse).'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'propeller-overview';
