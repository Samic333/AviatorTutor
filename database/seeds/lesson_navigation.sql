-- =============================================================================
-- AviatorTutor — Phase 13 (ATA 34 Navigation) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'navigation' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'navigation-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Navigation — VOR/ILS, DME, ADF, FMS, TCAS, EGPWS, AHRS',
     'navigation-overview',
     'overview',
     'Dual VHF NAV receivers handle VOR (108.00–117.95 MHz, 50 kHz even spacing), Localizer (108.10–111.95 MHz, 50 kHz odd spacing), Glideslope, DME, and integral Marker Beacons (OUT blue / MID amber / INN white; HI/LO sensitivity). Two ADF systems (ADF/ANT/BFO/TEST modes). Two ATC transponders. Weather Radar. FMS auto-tunes nav radios; ARCDU manually tunes. EFCP Bearing 1 + Bearing 2 selectors are 5-position (OFF/VOR/ADF/FMS/AUX). Format: ARC default ±45° around heading; push-and-hold 1 sec for FULL 360° north-up. TCAS continuous display at 40 nm or less; auto mode if EFCP fails. AHRS: 2 AHCPs / 2 AHRUs / 2 flux valves / 2 memory modules. ADF parks at 90° on invalid signal, ANT mode, or TEST mode. DME cross-use: pilot EFIS uses DME1, copilot uses DME2; loss → both sides auto-use remaining DME.',
     '<p>Q400 navigation is a dual-redundant suite covering every IFR + VFR navigation requirement: VHF NAV (VOR/ILS/Localizer/Glideslope/DME/Marker), ADF, transponder, weather radar, EGPWS, TCAS, and the FMS. Two of (almost) everything: 2 VHF NAV receivers, 2 ADF receivers, 2 DME units, 2 ATC transponders, 2 AHRS, 2 ADCs, plus a single weather radar and a single FMS in the standard build (some operators have dual FMS). Key control panels: ARCDU (Audio + Radio Control Display Unit) for manual tuning, FMS for automatic tuning, EFCP for bearing-pointer selection + format + range + TCAS + WX/TERR + DATA. The EFIS uses cross-side data with auto-failover (DME loss → both sides use remaining DME). Memorise the bearing-pointer behaviour by source, the marker-beacon colours, and the TCAS continuous-display range.</p>',
     JSON_ARRAY(
       'Dual VHF NAV receivers handle VOR + Localizer + Glideslope + DME + Marker Beacons',
       'VOR frequency range: 108.00–117.95 MHz with 50 kHz EVEN spacing',
       'Localizer frequency range: 108.10–111.95 MHz with 50 kHz ODD spacing (paired with Glideslope)',
       'Marker Beacon colours: OUT (blue) at outer marker, MID (amber) at middle marker, INN (white) at inner/airway marker',
       'Marker Beacon sensitivity: HI or LO selectable from ARCDU on centre console',
       'ARCDUs (ARCDU1, ARCDU2) manually tune nav radios. FMS provides automatic tuning',
       'Two DME units (DME1, DME2). Each is dual-channel. DME HOLD keeps current DME station active when frequency changes',
       'DME cross-use: Pilot EFIS uses DME1; Copilot EFIS uses DME2. Loss of one DME → both sides auto-use remaining DME receiver',
       'EFIS DME parameters: Slant Range, Ground Speed, DME HOLD',
       'Two ADF receivers. Modes: ADF (bearing to station), ANT (audio only, pointer parks 90°), BFO (1000 Hz tone for valid station), TEST (pointer parks 90°)',
       'EFCP Bearing 1 selector: 5-position OFF / VOR1 / ADF1 / FMS1 / AUX1 (white single-bar pointer with white circle)',
       'EFCP Bearing 2 selector: 5-position OFF / VOR2 / ADF2 / FMS2 / AUX2',
       'Bearing pointer behaviour: VOR removed if frequency invalid OR ILS frequency selected. ADF parks at 90° if signal/frequency invalid. FMS points to next waypoint. AUX requires optional MLS',
       'EFCP FORMAT pushbutton: PUSH 1 = ARC mode with VOR/ILS source. PUSH 2/3 = ARC with FMS source (default). PUSH and HOLD 1 sec = FULL mode, 360° north-up, A/C centred',
       'EFCP TCAS pushbutton: PUSH 1 = continuous TCAS traffic at 40 nm or less. PUSH 2 = automatic mode. Auto-mode also activates if EFCP malfunctions',
       'EFCP WX/TERR pushbutton: cycles weather radar / EGPWS terrain / OFF. Default = weather radar',
       'EFCP RANGE selector: 6-position 10 / 20 / 40 / 80 / 160 / 240 nm. Default 40 nm at NAV initialisation',
       'EFCP DATA pushbutton: cycles 10 nearest NAV AIDS / 10 nearest AIRPORTS / both / off (default)',
       'EGPWS provides Terrain Awareness Display (TAD) and Terrain Clearance Floor (TCF). TERRAIN INHIBIT switchlight (white, alternate) inhibits TAD + TCF only',
       'GPWS LANDING FLAP SELECT switch (10 / 15 / 35) — TOO LOW FLAPS aural at <200 ft AGL with flaps less than selected',
       'GPWS FLAP OVERRIDE switchlight (amber crosshatch) inhibits GPWS mode 4B only — permits 0° flap landings',
       'AHRS: 2 AHRS control panels (AHCP1, AHCP2), 2 AHRUs, 2 remote flux valves (FDU1, FDU2), 2 remote memory modules (RMM1, RMM2)',
       'AHRU outputs: EIS (EADI, EHSI, ALT, IVSI), AFCS, SPS, FDR via IFC, FDPS, WXR, GPWS, TCAS, CDS',
       'Two Air Data Computers (ADC1, ADC2). EFIS ATT/HDG SOURCE selector and ADC SOURCE selector cross-select. Cross-side selection illuminates yellow on PFD'
     ),
     JSON_ARRAY(
       'EFCP malfunction → TCAS automatically goes to AUTO mode without crew action.',
       'Loss of one DME automatically reroutes both EFIS sides to the remaining DME — no manual selection needed.',
       'Bearing 1 pointer is single-bar white with white circle; Bearing 2 is double-bar (per company SOP) — different visual cues.',
       'FORMAT push-and-hold for 1 SECOND switches ARC to FULL mode; quick taps cycle ARC sources only.',
       'EFCP DATA: pressing on a non-FMS configuration shows white NO DATA flashing for 5 seconds then reverts.',
       'AHRS uses gyros + accelerometers + remote flux valves; the RMM stores calibration after AHRU power loss for fast realignment.',
       'In flight, default EFCP configuration: ARC mode + WXR display ON + no optional Map data + 40 nm range. Re-asserted on each NAV mode initialisation.'
     ),
     JSON_ARRAY(
       'VOR range 108.00–117.95 MHz / 50 kHz EVEN; LOC 108.10–111.95 MHz / 50 kHz ODD. Different ranges, different spacing parity.',
       'Marker beacon colours: OUT BLUE, MID AMBER, INN WHITE. Don''t swap.',
       'Bearing selector 5 positions (NOT 4): OFF / VOR / ADF / FMS / AUX.',
       'TCAS continuous at 40 nm or less. Above 40 nm → not continuous. Mnemonic: TCAS-40-NM.',
       'FORMAT push-and-hold = 1 SECOND for FULL mode. Not instant; not a quick tap.',
       'RANGE 6 positions: 10 / 20 / 40 / 80 / 160 / 240. Default 40.',
       'TERRAIN INHIBIT inhibits TAD and TCF ONLY. Other GPWS modes still active.',
       'GPWS FLAP OVERRIDE inhibits mode 4B ONLY (0° flap landings).',
       'ADF parks at 90° in: invalid signal, ANT mode, TEST mode. Three different triggers.',
       'DME cross-use: pilot uses DME1; copilot uses DME2. Loss → both auto-use remaining.',
       'AHRS uses gyros + accelerometers (NOT laser ring gyros — that would be IRS). Q400 standard is AHRS.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'navigation-overview';
