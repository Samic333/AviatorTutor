-- =============================================================================
-- AviatorTutor — Phase 10: ATA 31 Indicating & Recording — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'indicating-recording' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 EIS — DUs, Control Panels, CWS, Recorders',
 'Schematic of the Electronic Instrument System on the Q400. Five identical interchangeable LCD Display Units (PFD1, MFD1, ED, MFD2, PFD2). EFIS uses 4 DUs (PFDs + MFDs). ESID uses 3 DUs (MFDs + ED). EFCP1 controls PFD1 + MFD1; EFCP2 controls PFD2 + MFD2; ESCP controls MFD1 + MFD2 + ED. After ESCP power loss, MFD1 selector remains operative and MFD2 selector dies. Two digital clocks feed the FDR + CVR with auto-switching from No.1 to No.2 on No.1 failure. Central Warning System: Master Warning (red) + Master Caution (amber) + Warning Tone Generator + GPWS + TCAS. T/O Warning Horn fires on five conditions.',
 '/assets/aircraft/q400/indicating-recording-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — PFD1 (pilot)',
 'Pilot Primary Flight Display. Shows FMA, ASI, EADI, ALT, EHSI, IVSI, TCAS, FMS, GPS. Identical to PFD2; interchangeable. Brightness via EFCP1 rotary.',
 8.33, 16.66, 'component', '#22c55e', 'pfd'),
(@diagram_id, '2 — MFD1 (pilot)',
 'Pilot Multi-Function Display. NAV or SYS page with PFCS Permanent System Data Area. Selector positions: PFD/NAV/SYS/ENG via ESCP. Brightness via EFCP1.',
 25.00, 16.66, 'component', '#22c55e', 'mfd'),
(@diagram_id, '3 — ED (centre)',
 'Engine and System Integrated Display. Engine page in normal use. Composite system page on press-and-hold of ESCP system pushbutton when MFDs failed. Brightness via ESCP ED BRT.',
 41.66, 16.66, 'component', '#fbbf24', 'ed'),
(@diagram_id, '4 — MFD2 (copilot)',
 'Copilot Multi-Function Display. SYS or NAV page with Flap/Hydraulic Permanent System Data Area. Selector dies after ESCP power loss.',
 60.83, 16.66, 'component', '#22c55e', 'mfd'),
(@diagram_id, '5 — PFD2 (copilot)',
 'Copilot Primary Flight Display. Mirror of PFD1. Same content. Brightness via EFCP2 rotary.',
 79.16, 16.66, 'component', '#22c55e', 'pfd'),
(@diagram_id, '6 — EFCP1 (pilot)',
 'Pilot EFIS Control Panel. Controls PFD1 + MFD1. Brightness knobs (rotary, detent at OFF), bearing pointer selectors, range, format.',
 14.16, 40.00, 'component', '#7dd3fc', 'efcp'),
(@diagram_id, '7 — ESCP (shared)',
 'Engine and System Integrated Display Control Panel. Controls MFD1 + MFD2 + ED. MFD1/MFD2 4-position rotary (PFD/NAV/SYS/ENG), system pushbuttons (ELEC/ENG/FUEL/DOORS/ALL), ED brightness, EFIS ATT/HDG SOURCE, ADC SOURCE.',
 50.00, 40.00, 'component', '#22c55e', 'escp'),
(@diagram_id, '8 — EFCP2 (copilot)',
 'Copilot EFIS Control Panel. Controls PFD2 + MFD2. Mirror of EFCP1. Same brightness/bearing/range/format selections.',
 80.00, 40.00, 'component', '#7dd3fc', 'efcp'),
(@diagram_id, '9 — Clock No.1 + Clock No.2',
 'Two digital clocks. Clock No.1 (pilot) directly to CVR + FDR via FDPS. Clock No.2 (copilot) to FDR via FDPS only. FDR auto-switches to No.2 on No.1 failure.',
 14.16, 60.00, 'component', '#a78bfa', 'clocks'),
(@diagram_id, '10 — FDPS / IFC',
 'Flight Data Processing System with 5 modules across 2 IFCs (one per side). Acquires/distributes data; computes warning tones via WTG. Interfaces all DUs.',
 35.83, 60.00, 'component', '#22c55e', 'fdps'),
(@diagram_id, '11 — FDR (Flight Data Recorder)',
 'Records via FDPS. Time-stamped from clock No.1 normally; auto-switches to No.2 on clock No.1 fail. Real time on FDR for sync with CVR.',
 50.00, 60.00, 'component', '#dc2626', 'fdr'),
(@diagram_id, '12 — CVR (Cockpit Voice Recorder)',
 'Records cockpit audio. Real-time stamp from clock No.1 directly. CVR has only the No.1 path — if No.1 fails, real-time sync is lost from that point.',
 64.16, 60.00, 'component', '#dc2626', 'cvr'),
(@diagram_id, '13 — Master Warning + Caution',
 'Glareshield switchlights — dual (each pilot side). Master Warning (RED, flashing). Master Caution (AMBER, flashing). Push resets the FLASH; underlying C/W panel light remains steady if fault persists.',
 80.00, 60.00, 'component', '#dc2626', 'master'),
(@diagram_id, '14 — Stall + GPWS Layer',
 'Stall warning test (TEST 1 / OFF / TEST 2; channels 1 & 2; left and right shakers). Stick pusher with SHUT-OFF switchlight (alternate, amber). GPWS PULL UP TEST, BELOW G/S, TERRAIN INHIBIT (white), FLAP OVERRIDE (amber 4B inhibit), LANDING FLAP SELECT (10/15/35).',
 50.00, 80.00, 'component', '#fbbf24', 'sps');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'All 5 DUs healthy. EFCP1, EFCP2, and ESCP all functional. Clocks running, FDR + CVR recording. No Master Warning or Master Caution. Standard cockpit.',
 JSON_OBJECT()),
(@diagram_id, 'pfd1_failure', 'PFD1 Failure — Reversion to MFD1',
 'PFD1 has gone black; AVAIL (white) appears in its centre. ESCP MFD1 selector to PFD reroutes primary flight content to MFD1. PFD2 unaffected. System pages temporarily move to MFD2 only. Brief FO; consider divert in IMC.',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'escp_power_loss', 'ESCP Power Loss — MFD2 Locked',
 'ESCP power loss. MFD1 selector remains operative. MFD2 selector does NOT operate — locked on default page. System pushbuttons still drive MFD1 (if at SYS) or ED (press-and-hold). Asymmetric cockpit.',
 JSON_OBJECT(
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h4', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
