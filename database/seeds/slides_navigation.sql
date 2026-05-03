-- =============================================================================
-- AviatorTutor — Phase 13: ATA 34 Navigation — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'navigation' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'navigation-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Dual Receivers, Cross-Side Failover, FMS Layer',
 'Q400 navigation is dual-redundant by design: 2 VHF NAV receivers, 2 ADF, 2 DME, 2 ATC transponders, 2 AHRS, 2 ADCs. Cross-side source selection via the EFIS ATT/HDG SOURCE and ADC SOURCE selectors handles single-side failures with a yellow PFD indication. The FMS layer provides automatic radio tuning and waypoint navigation; the ARCDU is your manual fallback. The EFCP drives bearing pointers, format, range, TCAS, WX/TERR, and DATA — and there''s subtle reversion logic worth knowing (e.g. EFCP malfunction auto-sets TCAS to AUTO mode). This lesson walks the components, the bearing-pointer behaviour, the EFCP controls, and the EGPWS layer.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'Q400 navigation overview',
 'Dual everything · cross-side failover · FMS auto / ARCDU manual.',
 'On a real degraded-nav scenario, knowing which switch reroutes which data is the difference between a controlled flight and a confused one.',
 NULL),

(@lesson_id, 20, 'concept',
 'VOR/ILS — Frequency Spacing Tells You the Mode',
 'The two VHF NAV receivers operate in either VOR or ILS mode based purely on tuned frequency. <strong>VOR mode:</strong> 108.00–117.95 MHz with 50 kHz <strong>EVEN</strong> spacing. <strong>Localizer mode:</strong> 108.10–111.95 MHz with 50 kHz <strong>ODD</strong> spacing. The Glideslope is automatically paired with the LOC frequency. The EIS shows VOR bearing course, lateral deviation, glideslope vertical deviation, and marker passage. Course selection knobs on the FGCP set the desired course on the EFIS. Tuning is via ARCDU manual or FMS auto. Loss of one VHF NAV reroutes both EFIS sides via cross-side selection.',
 'diagram', '/assets/aircraft/q400/navigation-flow.svg',
 'VOR vs LOC frequency ranges + spacing parity',
 'VOR-EVEN-LOC-ODD. 108.00-117.95 even / 108.10-111.95 odd.',
 'When you tune 110.30 you''re on a LOC. When you tune 110.40 you''re on a VOR. Spacing parity is a fast mental check.',
 NULL),

(@lesson_id, 30, 'concept',
 'Marker Beacons — Three Colours by Location',
 'Marker beacons are integral to the VOR/LOC navigation receivers. There are three colour-coded indicators on the EFIS for each marker passage: <strong>OUT (blue)</strong> at the outer marker, <strong>MID (amber)</strong> at the middle marker, and <strong>INN (white)</strong> at the inner or airway marker. Sensitivity is selectable HI or LO from the ARCDU on the centre console — HI is the standard for normal approach use. The marker indication is your visual + aural confirmation of position on the approach. Memorise the colour mapping: OUT is blue (outer), MID is amber (middle), INN is white (inner).',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'Marker beacon colour-coded indicators',
 'OUT-BLUE-MID-AMBER-INN-WHITE.',
 'On a black-hole approach into a field with marker beacons, the OUT-MID-INN sequence is your timing sanity check.',
 JSON_OBJECT(
   'prompt', 'On the Q400 marker beacon indicators, what colour shows for the MIDDLE marker?',
   'options', JSON_ARRAY(
     'Blue',
     'Amber',
     'White',
     'Green'
   ),
   'correct_index', 1,
   'explanation', 'OUT (blue), MID (amber), INN (white). Mnemonic: OUT-BLUE-MID-AMBER-INN-WHITE.'
 )),

(@lesson_id, 40, 'system',
 'Bearing Pointers — 5 Positions, Behaviour by Source',
 'Each EFCP has two bearing-pointer selectors — Bearing 1 and Bearing 2 — each 5-position rotary. Positions: <strong>OFF / VOR / ADF / FMS / AUX</strong>. (Bearing 1 selects from VOR1/ADF1/FMS1/AUX1; Bearing 2 from VOR2/ADF2/FMS2/AUX2.) Pointer behaviour by source: <strong>VOR:</strong> if frequency is invalid OR an ILS frequency is selected, the pointer is removed. <strong>ADF:</strong> if signal/frequency is invalid, pointer parks at 90° relative bearing. <strong>FMS:</strong> if FMS is operating, pointer points to the next waypoint. <strong>AUX:</strong> functional only with optional MLS equipment.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'Bearing pointer 5 positions + behaviour',
 'BRG-5-POS. VOR removed if invalid. ADF parks 90°. FMS to next WPT.',
 'When you see a bearing pointer parked at 90°, you''re on ADF with no signal — switch to a different source or retune.',
 JSON_OBJECT(
   'prompt', 'You select Bearing 2 to ADF2. The pointer parks at 90° relative bearing. What does this indicate?',
   'options', JSON_ARRAY(
     'ADF is functioning normally; 90° is the default rest position',
     'ADF signal or frequency is invalid; pointer parks until valid signal restored',
     'EFCP malfunction; switch EFCPs',
     'Cross-side source selection active'
   ),
   'correct_index', 1,
   'explanation', 'ADF pointer parks at 90° on invalid signal/frequency. Also parks at 90° in ANT mode or TEST mode. Mnemonic: ADF-PARKS-90.'
 )),

(@lesson_id, 50, 'system',
 'EFCP — FORMAT, RANGE, WX/TERR, TCAS, DATA',
 'Five EFCP controls drive the EFIS NAV display. <strong>FORMAT pushbutton:</strong> push 1 = ARC mode with VOR/ILS source. Push 2 or 3 = ARC with FMS source (default). Push and HOLD 1 sec = FULL mode (360° north-up, A/C centred). <strong>TCAS pushbutton:</strong> push 1 = continuous traffic at 40 nm or less. Push 2 = automatic. Auto-mode also activates if EFCP malfunctions. <strong>WX/TERR pushbutton:</strong> cycles WX / EGPWS terrain / off. Default WX. <strong>RANGE selector:</strong> 6-position 10 / 20 / 40 / 80 / 160 / 240 nm. Default 40 nm at NAV initialisation. <strong>DATA pushbutton:</strong> 10 nearest nav aids / 10 nearest airports / both / off (default).',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'EFCP control suite for NAV display',
 'FORMAT-1-SEC-FULL · TCAS-40-NM · RANGE 10/20/40/80/160/240 default 40.',
 'When the FO calls "EFCP malfunction" the TCAS quietly goes to AUTO without your input — note that and brief the FO.',
 NULL),

(@lesson_id, 60, 'system',
 'DME Cross-Use — Pilot DME1, Copilot DME2',
 'DME architecture is dual-channel, dual-unit, with cross-side failover. <strong>Pilot EFIS uses DME1.</strong> Channel 1 of DME1 supplies VOR1 data; Channel 2 of DME1 supplies VOR2 data. <strong>Copilot EFIS uses DME2.</strong> Channel 1 of DME2 supplies VOR1 data; Channel 2 of DME2 supplies VOR2 data. So both EFIS sides simultaneously have access to both VOR1 and VOR2 distance via their own DME unit. <strong>Loss of one DME:</strong> both EFIS sides automatically use the remaining DME — silent failover, no crew action. The DME HOLD function keeps the current DME station tuned even when the VOR frequency is changed.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'DME dual-channel cross-side architecture',
 'DME-CROSS-USE. Pilot uses DME1 · copilot uses DME2 · loss auto-reroutes both.',
 'On a single DME loss the panel shows nothing; both sides quietly use the remaining DME. Maintenance writeup only.',
 NULL),

(@lesson_id, 70, 'system',
 'ADF — Four Modes, Park Behaviour',
 'Each ADF receiver has four operating modes. <strong>ADF mode:</strong> bearing pointer to selected ground station — normal operation. <strong>ANT (Antenna) mode:</strong> audio-only receiver for station identification. Loop antenna disabled (higher audio sensitivity for ID). Bearing pointer parks at 90° relative bearing. <strong>BFO (Beat Frequency Oscillator) mode:</strong> intermittent 1000 Hz audio tone when receiver gets a valid station transmission — used for non-modulated CW signals. ADF system continues to show bearing in BFO mode if ADF mode is also selected. <strong>TEST mode:</strong> confidence test. Bearing pointer parks at 90° relative bearing. Note: three of the four modes can park the pointer at 90° — only ADF mode with a valid signal gives you a real bearing.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'ADF four modes + 90° park behaviour',
 '4-MODE-ADF · ADF-PARKS-90 · BFO-1000-HZ.',
 'A common pre-flight gotcha: leaving ADF in ANT mode and seeing the pointer at 90° — gives the wrong impression of "no signal" when actually the receiver is in audio-only mode.',
 JSON_OBJECT(
   'prompt', 'In ADF BFO mode, what audio cue indicates a valid station transmission?',
   'options', JSON_ARRAY(
     'Continuous 400 Hz tone',
     'Intermittent 1000 Hz tone',
     'Voice ID only',
     'No audio cue; visual only'
   ),
   'correct_index', 1,
   'explanation', 'BFO produces an intermittent 1000 Hz audio tone for valid station transmissions. Used for CW (Morse) signals. Mnemonic: BFO-1000-HZ.'
 )),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight + Cruise Navigation Setup',
 'Pre-flight: FMS init (position, route, performance). Tune nav radios via FMS auto or ARCDU manual; verify frequencies and station IDs. EFCP setup: Bearing 1 = VOR1 or FMS1 (per pilot preference + SOP); Bearing 2 = VOR2 or FMS2; FORMAT = ARC; RANGE = 40 nm default; WX/TERR = WX; TCAS = continuous. AHRS aligned per company SOP. Transponder selected and verified. Cruise: cross-check FMS position against raw VOR/DME at least every 30 minutes. Verify next waypoint and ETA. WX in WX mode for storm avoidance; switch to TERRAIN in mountainous areas if your route allows visual scan. TCAS continuous at 40 nm or less.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'Pre-flight + cruise NAV setup',
 'FMS · ARCDU · EFCP setup · cross-check FMS vs raw nav.',
 'A captain who never cross-checks FMS vs raw VOR/DME is one captain. A captain who does is the captain who catches the FMS data error before it bites.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Approach Sequence — ILS Tune to Touchdown',
 'Approach sequence in order. (1) Tune the ILS frequency via ARCDU or FMS — LOC + GS automatically pair. (2) FORMAT to ARC mode for the approach. (3) Bearing 1 to VOR1 with the appropriate radial; Bearing 2 to FMS or VOR2 for waypoint awareness. (4) Confirm course on FGCP. (5) On glideslope intercept, GS deviation needle centres. (6) Marker passage: OUT (blue) at outer, MID (amber) at middle, INN (white) at inner if equipped. (7) DME for distance to threshold. (8) Continue stabilised approach criteria checks. (9) GPWS modes active throughout — keep TERRAIN INHIBIT off unless explicitly required by SOP for a specific approach.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'ILS approach sequence with marker beacons',
 'TUNE · ARC · BRG · MARKER OUT-MID-INN · DME.',
 'Brief the marker sequence on every ILS — they''re your timing milestones, especially when the glideslope is doing something unusual.',
 JSON_OBJECT(
   'prompt', 'On final approach to an ILS-equipped runway, you pass the outer marker. What colour and label appears on the EFIS marker indicator?',
   'options', JSON_ARRAY(
     'Amber MID',
     'Blue OUT',
     'White INN',
     'Green LOC'
   ),
   'correct_index', 1,
   'explanation', 'Outer marker = OUT in blue. Middle = MID amber. Inner/airway = INN white. Mnemonic: OUT-BLUE-MID-AMBER-INN-WHITE.'
 )),

(@lesson_id, 100, 'abnormal',
 'EFCP Malfunction — TCAS Auto Mode',
 'EFCP malfunction in cruise. The most important automatic behaviour: <strong>TCAS automatically goes to AUTO mode</strong> without crew action. This ensures TCAS continues to function even with the bearing/format/range controls degraded. Other EFCP functions may be lost — bearing pointer selection, FORMAT, WX/TERR, RANGE, DATA. Recovery options: (1) Use the OTHER EFCP if available (each pilot has an EFCP). (2) Reset/recycle EFCP per QRH. (3) If both EFCPs malfunction, use ESCP MFD selectors for major reversion (per Phase 10). Brief the FO; consider divert if workload is asymmetric in IMC.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'EFCP malfunction recovery',
 'TCAS auto-AUTO. Use other EFCP. ESCP for MFD reversion.',
 'A real EFCP fault is rare. The auto-set to TCAS AUTO is exactly what you want — it preserves traffic awareness without any crew step.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'AHRS Loss — Cross-Side Source Selection',
 'AHRS fault on one side: the EFIS shows flagged or invalid attitude/heading parameters on that PFD. Action: rotate the EFIS ATT/HDG SOURCE selector from NORM to the operative side (1 or 2). Cross-side source selection illuminates a YELLOW indication on the PFD as a continuous reminder. The AFCS may degrade if its primary AHRS source is the failed unit — verify autopilot status; consider hand-flying. Run QRH AHRS non-normal. Continue per QRH; consider divert based on weather, route remaining, and remaining redundancy. A single AHRS loss with healthy cross-side is not necessarily an emergency, but the redundancy buffer is gone — a second AHRS loss would be a serious event.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'AHRS fault recovery via cross-side source',
 'EFIS ATT/HDG SOURCE 1 or 2 · yellow PFD · brief FO.',
 'On a single AHRS loss, the yellow source flag is your continuous reminder you''re on the other side. Don''t miss it on the scan.',
 JSON_OBJECT(
   'prompt', 'AHRS1 has failed. Pilot PFD shows flagged attitude. How is the EFIS rerouted?',
   'options', JSON_ARRAY(
     'Rotate EFIS ATT/HDG SOURCE selector from NORM to position 2; yellow indication on PFD confirms cross-side',
     'Switch off the failed AHRS at the AHCP1; rerouting is automatic',
     'PFD content cannot be rerouted; standby instruments only',
     'Press the Master Caution to clear the flag'
   ),
   'correct_index', 0,
   'explanation', 'Cross-side via EFIS ATT/HDG SOURCE selector to position 2. Yellow indication on PFD reminds the crew of cross-side selection.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Navigation Non-Normals',
 'Q400 QRH non-normals for navigation cluster into seven groups. (1) VHF NAV fault: cross-side via EFIS source selector. (2) DME loss: silent auto-failover to remaining DME. (3) ADF fault: pointer parks 90°; cross-check via FMS or VOR. (4) FMS fault: revert to manual ARCDU tuning + raw nav. (5) EFCP fault: TCAS auto-AUTO; use other EFCP. (6) AHRS fault: EFIS ATT/HDG SOURCE to operative side. (7) ADC fault: ADC SOURCE to operative side; possible IAS MISMATCH cascade per Phase 7. Most are not memory items but the cross-side source selectors must be reflexive — yellow PFD indicates cross-side active.',
 'image', '/assets/aircraft/q400/navigation-flow.svg',
 'QRH navigation non-normal cluster',
 'VHF NAV · DME · ADF · FMS · EFCP · AHRS · ADC.',
 'Drill cross-side source selection in the sim. The recovery path is reflexive: yellow flag → switch source → continue.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: VOR Bearing Pointer Removed at 50 NM',
 'Setup: cruise 50 nm from a VOR station that is your primary nav fix. You set Bearing 1 to VOR1 with the station''s frequency tuned. The bearing pointer is REMOVED (not parked at 90°, not flagged — gone). FMS shows the route active and the station as a waypoint.\n\nDiagnosis: the VOR pointer is removed when the frequency is invalid OR when an ILS frequency is selected. Check the tuned frequency — does it match the published VOR? If yes but pointer still removed, the receiver may be flagged. Possible: (a) wrong frequency (transposed digits); (b) ILS frequency tuned (50 kHz odd spacing 108-111.95); (c) VHF NAV1 receiver fault.\n\nFirst 60 seconds: Verify ARCDU frequency vs charts. If frequency wrong, retune. If correct, switch Bearing 1 to FMS1 (points to next waypoint — useful) and try Bearing 2 to VOR2 with same station — if pointer appears, VHF NAV1 is the fault. Use cross-side ADC/AHRS source if needed. Run QRH VHF NAV; continue with FMS as primary nav.',
 'animation', '/assets/aircraft/q400/navigation-flow.svg',
 'VOR bearing pointer removed scenario',
 'VOR removed = invalid freq or ILS freq. Verify · cross-check via VOR2 · QRH.',
 'A removed pointer is different from a parked-at-90° pointer. Different cause, different fix.',
 JSON_OBJECT(
   'prompt', 'You have Bearing 1 set to VOR1 with a valid station frequency tuned. The bearing pointer is REMOVED. What is most likely?',
   'options', JSON_ARRAY(
     'ADF signal lost — pointer auto-parks at 90°',
     'VOR frequency is invalid OR an ILS frequency is selected on that channel',
     'EFCP malfunction',
     'AHRS source fault'
   ),
   'correct_index', 1,
   'explanation', 'VOR pointer removed = invalid frequency OR ILS frequency selected. Verify ARCDU tuning vs charts.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Navigation in 60 Seconds',
 'Recap:\n  • Dual: 2 VHF NAV + 2 ADF + 2 DME + 2 ATC + 2 AHRS + 2 ADC. Single FMS + WXR + EGPWS + TCAS.\n  • VOR 108.00–117.95 EVEN spacing. LOC 108.10–111.95 ODD. GS auto-paired.\n  • Marker beacons: OUT blue / MID amber / INN white. HI/LO sensitivity ARCDU.\n  • Bearing selector 5 positions: OFF / VOR / ADF / FMS / AUX.\n  • Bearing pointer behaviour: VOR removed if invalid; ADF parks 90° if invalid; FMS points to next waypoint.\n  • FORMAT: ARC default ±45°; push+hold 1 sec for FULL 360° north-up.\n  • RANGE: 10/20/40/80/160/240 nm; default 40.\n  • TCAS continuous at 40 nm or less; auto on EFCP malfunction.\n  • DATA: 10 nav aids / 10 airports / both / off.\n  • DME cross-use: pilot DME1 / copilot DME2; loss → auto reroute.\n  • ADF 4 modes: ADF / ANT / BFO / TEST. BFO 1000 Hz. Pointer parks 90° in ANT/TEST/invalid.\n  • EGPWS TAD/TCF + 5 GPWS modes. TERRAIN INHIBIT (white) inhibits TAD/TCF only. FLAP OVERRIDE (amber) inhibits mode 4B only.\n  • Cross-side: EFIS ATT/HDG SOURCE + ADC SOURCE; yellow PFD on cross-side.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'VHF-NAV-DUAL · VOR-EVEN-LOC-ODD · OUT-BLUE-MID-AMBER-INN-WHITE · BRG-5-POS · FORMAT-1-SEC-FULL · TCAS-40-NM · RANGE-6-DEFAULT-40 · 4-MODE-ADF · ADF-PARKS-90 · DME-CROSS-USE · BFO-1000-HZ · WPT-FMS-PT',
 'Twelve mnemonics carry every navigation question. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
