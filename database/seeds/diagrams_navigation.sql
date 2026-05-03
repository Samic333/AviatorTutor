-- =============================================================================
-- AviatorTutor — Phase 13: ATA 34 Navigation — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'navigation' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Navigation — VHF NAV, ADF, FMS, TCAS, EGPWS, AHRS',
 'Schematic of the Q400 navigation suite. Dual VHF NAV receivers cover VOR, Localizer, Glideslope, DME, and integral Marker Beacons. Two ADF receivers (modes ADF/ANT/BFO/TEST). Two ATC transponders. Single Weather Radar. EGPWS layered with TAD/TCF and 5 GPWS modes. TCAS continuous at 40 nm or less. AHRS dual-redundant: 2 AHCPs, 2 AHRUs, 2 flux valves, 2 RMMs. Two ADCs. EFCP drives bearing pointers (5-position OFF/VOR/ADF/FMS/AUX), FORMAT (ARC default; push+hold 1 sec for FULL 360 north-up), RANGE (10/20/40/80/160/240 nm; default 40), WX/TERR cycle, DATA cycle. ARCDU manual tunes; FMS auto tunes. Cross-side AHRS/ADC source selection with yellow PFD indication.',
 '/assets/aircraft/q400/navigation-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — VHF NAV Receivers (×2)',
 'Dual VHF NAV receivers handle VOR (108.00-117.95 MHz, 50 kHz EVEN), Localizer (108.10-111.95, 50 kHz ODD), Glideslope (auto-paired with LOC), DME, and integral Marker Beacons (OUT/MID/INN with HI/LO sensitivity from ARCDU).',
 14.16, 16.66, 'component', '#22c55e', 'vhf-nav'),
(@diagram_id, '2 — DME (×2 dual-channel)',
 'Two DME units, each dual-channel. Pilot EFIS uses DME1; copilot uses DME2. Loss of one DME → both sides auto-use remaining. DME HOLD keeps current station active when frequency changes.',
 35.83, 16.66, 'component', '#22c55e', 'dme'),
(@diagram_id, '3 — ADF (×2)',
 'Two ADF receivers with 4 modes each: ADF (bearing), ANT (audio only, pointer parks 90°), BFO (1000 Hz tone for valid signal), TEST (pointer parks 90°). Pointer also parks at 90° on invalid signal/frequency.',
 50.00, 16.66, 'component', '#a78bfa', 'adf'),
(@diagram_id, '4 — ATC Transponders (×2)',
 'Two ATC transponders. Modes A/C/S, IDENT button. Cross-side TCAS source selection.',
 64.16, 16.66, 'component', '#7dd3fc', 'atc'),
(@diagram_id, '5 — Weather Radar + EGPWS',
 'Single WXR. EGPWS provides TAD + TCF + 5 GPWS modes. TERRAIN INHIBIT (white switchlight) inhibits TAD + TCF only.',
 80.00, 16.66, 'component', '#fbbf24', 'wxr-egpws'),
(@diagram_id, '6 — FMS (Flight Management System)',
 'Auto-tunes nav radios. Provides waypoint navigation. FMS bearing pointer points to next waypoint. Standard build = single FMS; some operators dual.',
 14.16, 40.00, 'component', '#22c55e', 'fms'),
(@diagram_id, '7 — ARCDU (×2)',
 'Audio + Radio Control Display Units. Manual tuning of VOR, LOC, GS, DME, ADF. Marker beacon HI/LO sensitivity selected here.',
 35.83, 40.00, 'component', '#22d3ee', 'arcdu'),
(@diagram_id, '8 — EFCP (×2)',
 'EFIS Control Panels — one per pilot. Bearing 1 + 2 selectors (5-position), FORMAT pushbutton (ARC / FULL via 1-sec hold), TCAS pushbutton (continuous 40 nm or less / auto on EFCP fault), WX/TERR cycle, RANGE selector (10/20/40/80/160/240 nm; default 40), DATA pushbutton (10 nav aids / 10 airports / both / off).',
 50.00, 40.00, 'component', '#7dd3fc', 'efcp'),
(@diagram_id, '9 — TCAS',
 'Traffic Alert + Collision Avoidance System. Continuous traffic display at 40 nm or less. Auto-mode activates if EFCP malfunctions. RA + TA via aural and visual.',
 64.16, 40.00, 'component', '#fbbf24', 'tcas'),
(@diagram_id, '10 — AHRS (2 AHCPs/AHRUs/Flux/RMMs)',
 'Dual-redundant Attitude + Heading Reference System. 2 AHCPs (control panels), 2 AHRUs (vertical+directional gyros + accelerometers), 2 remote flux valves (FDU1/2), 2 remote memory modules (RMM1/2). Cross-side via EFIS ATT/HDG SOURCE selector.',
 80.00, 40.00, 'component', '#22c55e', 'ahrs'),
(@diagram_id, '11 — ADC (×2)',
 'Two Air Data Computers. Cross-side via ADC SOURCE selector NORM/1/2. Yellow PFD indication on cross-side. Possible IAS MISMATCH cascade if airspeed delta >17 kts (Phase 7).',
 14.16, 60.00, 'component', '#22c55e', 'adc'),
(@diagram_id, '12 — Marker Beacons (Integral)',
 'Integral to VOR/LOC receivers. EFIS indicators: OUT (blue) at outer marker, MID (amber) at middle, INN (white) at inner. HI or LO sensitivity from ARCDU.',
 35.83, 60.00, 'component', '#a78bfa', 'marker'),
(@diagram_id, '13 — Bearing Pointers + Sources',
 'Each EFCP has Bearing 1 + Bearing 2, 5-position rotary (OFF/VOR/ADF/FMS/AUX). Pointer behaviour: VOR removed if invalid; ADF parks 90° if invalid; FMS to next waypoint; AUX requires MLS.',
 50.00, 60.00, 'component', '#fbbf24', 'bearing'),
(@diagram_id, '14 — EGPWS Layer (TAD/TCF + 5 modes)',
 'Enhanced Ground Proximity Warning System. TERRAIN INHIBIT (white) inhibits TAD + TCF. GPWS FLAP OVERRIDE (amber) inhibits mode 4B only (0° flap landings). LANDING FLAP SELECT 10/15/35.',
 80.00, 60.00, 'component', '#dc2626', 'egpws');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'All navigation systems healthy. Bearing pointers per pilot preference. TCAS continuous at 40 nm range. WX/TERR per phase. AHRS aligned; cross-side selectors at NORM.',
 JSON_OBJECT()),
(@diagram_id, 'efcp_fault_tcas_auto', 'EFCP Malfunction — TCAS Auto-AUTO',
 'EFCP not responding. TCAS automatically goes to AUTO mode without crew action — preserves traffic awareness. Other EFCP functions degraded. Recovery: use other EFCP, recycle, or use ESCP for major reversion.',
 JSON_OBJECT(
   'h8', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h9', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 )),
(@diagram_id, 'ahrs_cross', 'AHRS Cross-Side Source Selection',
 'AHRS1 has failed. Pilot rotates EFIS ATT/HDG SOURCE selector from NORM to position 2. Cross-side YELLOW indication illuminates on PFD as continuous reminder. AFCS may degrade if its primary source was AHRS1.',
 JSON_OBJECT(
   'h10', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h11', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
