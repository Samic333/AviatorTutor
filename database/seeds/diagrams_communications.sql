-- =============================================================================
-- AviatorTutor — Phase 4: ATA 23 Communications — interactive diagram
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'communications' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 Communications and Audio Architecture',
 'Block diagram of the Q400 communication systems. Two ARCDUs are the cockpit''s primary interface to ARMS. The VHF1 Standby Control and Display Unit is a dedicated backup for VHF1 only. Three VHF radios — VHF1 and VHF2 for voice, VHF3 dedicated to ACARS data with no voice. The ELT (Kannad 406 with COSPAS/SARSAT) auto-triggers on 5 to 7 G longitudinal acceleration. The solid-state CVR records the last 2 hours. Audio panels at each crew station feed PTT/INPH with a 6 dB flight-deck speaker mute on transmission.',
 '/assets/aircraft/q400/communications-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — ARCDU 1 (Captain Side)',
 'Audio and Radio Control Display Unit 1. Active matrix LCD. Active frequency green (valid) or white (invalid). Preset cyan. Highlighted-cyan = tune window with 5-second timeout. Side keys access VHF/NAV/audio panels.',
 31.66, 31.66, 'component', '#22d3ee', 'arcdu'),
(@diagram_id, '2 — ARCDU 2 (FO Side)',
 'Mirror unit on the FO side. Each ARCDU can independently tune all radios. A single-ARCDU failure is operationally manageable; both failing falls back to the VHF1 Standby tuner.',
 68.33, 31.66, 'component', '#22d3ee', 'arcdu'),
(@diagram_id, '3 — VHF1 Standby Control + Display Unit',
 'Dedicated backup for VHF1 only. Three-position rotary OFF / ON / TEST. Independent of both ARCDUs. Tx annunciator lights when mic is keyed and RF output detected. Lifeline if both ARCDUs fail.',
 11.66, 32.50, 'component', '#fbbf24', 'backup'),
(@diagram_id, '4 — HF (optional)',
 'Long-range HF comms when fitted. Useful for oceanic / remote-area operations. Tuned via the ARCDU.',
 88.33, 32.50, 'component', '#7dd3fc', 'hf'),
(@diagram_id, '5 — VHF 1',
 'Primary voice radio. Standard tuning via ARCDU; backup tuning via VHF1 Standby unit.',
 28.33, 51.66, 'component', '#7dd3fc', 'vhf'),
(@diagram_id, '6 — VHF 2',
 'Secondary voice radio. Used for monitoring or as backup to VHF1 in busy airspace.',
 40.83, 51.66, 'component', '#7dd3fc', 'vhf'),
(@diagram_id, '7 — VHF 3 (ACARS data)',
 'Dedicated 3rd VHF Comm (Thompson EVR 76). DATA only — no voice capability. Tuned by ACARS itself; crew has no manual tune control.',
 55.83, 51.66, 'component', '#a78bfa', 'acars'),
(@diagram_id, '8 — ACARS',
 'Aircraft Communications, Addressing and Reporting System. Allied Signal MK II + Globalstar. Down-link aircraft data (OOOI position), up-link clearances and weather. Half-size data printer below copilot side console.',
 79.58, 64.16, 'component', '#a78bfa', 'acars'),
(@diagram_id, '9 — ELT (Kannad 406 with COSPAS/SARSAT)',
 'Three frequencies: 121.5, 243, 406 MHz. Auto-triggers on 5–7 G longitudinal acceleration. Remote switch positions: ON (manual override), ARMED (auto), RESET & TEST (momentary). Monitor light: 1 long flash every 3 sec = normal armed.',
 11.66, 53.33, 'component', '#dc2626', 'elt'),
(@diagram_id, '10 — Solid-State CVR (SSCVR)',
 'Records the last 2 hours of: flight crew comms, flight deck area mic, PA announcements, clock data. Pull the CVR CB before taxi-in to preserve recording after an incident.',
 11.66, 70.83, 'component', '#22c55e', 'recorders'),
(@diagram_id, '11 — Flight Data Recorder (FDR)',
 'Records flight parameters. Tested via cockpit GND TEST switch when on the ground (with A/COL exterior light switch in OFF). FLT DATA RECORDER caution illuminates during test then goes out — pass.',
 31.66, 70.83, 'component', '#22c55e', 'recorders'),
(@diagram_id, '12 — Audio Panels (PTT/INPH)',
 'Control wheels (PTT/INPH 3-position spring-loaded), copilot side panel (XMIT/INPH), steering panel (PTT only), Observer Audio Control Panel (13 audio knobs, mechanically interlocked transmitter keys, INT/RAD switch).',
 55.00, 70.83, 'component', '#7a8aa6', 'audio'),
(@diagram_id, '13 — PTT Speaker Mute',
 'Pressing PTT mutes the flight-deck speakers by 6 dB. Anti-feedback measure. Audible only — speakers do not go fully silent.',
 77.50, 70.83, 'note', '#fbbf24', 'audio'),
(@diagram_id, '14 — ELT Real Distress (ON)',
 'For real distress: set ELT remote to ON. This overrides the inertia switch and forces transmission. Monitor light flashes every 4 sec confirming transmission. Leave ON to assist SAR.',
 95.00, 85.00, 'note', '#dc2626', 'elt');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'Both ARCDUs operational, all VHFs tuned, ELT armed, CVR running. Default colour scheme on all hotspots.',
 JSON_OBJECT()),
(@diagram_id, 'arcdu_dual_fail', 'Dual ARCDU Failure',
 'Both ARCDUs unusable. The VHF1 Standby Control and Display Unit (h3) becomes the active interface. Switch ON, tune VHF1, declare with ATC. NAV audio integration is impaired; brief approach as hand-flown.',
 JSON_OBJECT(
   'h1', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h2', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h3', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true)
 )),
(@diagram_id, 'elt_active', 'ELT Active (Inadvertent or Distress)',
 'ELT is transmitting on all three frequencies (121.5/243/406 MHz). Monitor light flashing every 4 seconds. If inadvertent (post-hard-landing): set RESET & TEST momentarily on stand to clear. If real distress: leave ON to assist SAR.',
 JSON_OBJECT(
   'h9', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#dc2626', 'pulse', false)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
