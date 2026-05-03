-- =============================================================================
-- AviatorTutor — Phase 18: ATA 22B FMS — interactive diagram.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fms' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM diagrams WHERE system_id = @system_id;

INSERT INTO diagrams (system_id, title, description, image_path, svg_data, is_interactive, diagram_type)
VALUES (@system_id,
 'Q400 FMS — UNS-1 Architecture, Sensors, Database',
 'Schematic of the Q400 Universal Avionics UNS-1 series Flight Management System. Four components: NCU (Navigation Computer Unit), FPCDU (Flat-Panel Control Display Unit 4 or 5 inch), DTU (Data Transfer Unit, model DTU 100), Configuration Module. Position sensors layered: GPS primary with SBAS for LPV, scanning DME triangulation (2+ stations), VOR/DME single-station fix, AHRS+ADC dead reckoning. Sensor Watchdog selects best source. Dual-cycle navigation database current+next AIRAC, 28-day cycle. Pilot data preserved across updates. LNAV+VNAV+frequency management. Position Uncertain on GPS lost + DME insufficient + no other valid. High-latitude TRUE heading switch. AFIS+UniLink datalink.',
 '/assets/aircraft/q400/fms-flow.svg', NULL, 1, 'flow');

SET @diagram_id := LAST_INSERT_ID();

INSERT INTO diagram_hotspots (diagram_id, label, description, x_pct, y_pct, hotspot_type, color_hex, state_group) VALUES
(@diagram_id, '1 — NCU (Navigation Computer Unit)',
 'The FMS brain. Performs position calculation, lateral guidance (LNAV), vertical guidance (VNAV), fuel management, frequency management. Outputs steering commands to AFCS.',
 14.16, 16.66, 'component', '#22c55e', 'ncu'),
(@diagram_id, '2 — FPCDU (4" or 5")',
 'Flat-Panel Control Display Unit. Crew interface for FMS. Pages: POS, FPL, ARR, DEP, PERF, FUEL, DATA, TUNE. Initialisation page on power-up.',
 35.83, 16.66, 'component', '#7dd3fc', 'fpcdu'),
(@diagram_id, '3 — DTU 100 (Data Transfer Unit)',
 'Loads navigation database from sealed memory cartridge into NCU. Used during AIRAC database updates. Typically operated by maintenance.',
 50.00, 16.66, 'component', '#fbbf24', 'dtu'),
(@diagram_id, '4 — Configuration Module',
 'Stores aircraft-specific configuration: sensor types, output formats, software options. Read by NCU on power-up.',
 64.16, 16.66, 'component', '#a78bfa', 'config'),
(@diagram_id, '5 — UniLink + AFIS Datalink',
 'UniLink: Universal Avionics datalink module (ACARS-equivalent). AFIS: Aircraft Flight Information Service (weather, NOTAMs, dispatch).',
 80.00, 16.66, 'component', '#22d3ee', 'datalink'),
(@diagram_id, '6 — GPS (Primary, with SBAS)',
 'Primary position sensor. WGS-84 position. SBAS (e.g. WAAS) augmentation supports LPV approaches. Loss → Sensor Watchdog reverts to scanning DME.',
 14.16, 40.00, 'component', '#22c55e', 'gps'),
(@diagram_id, '7 — Scanning DME (Backup)',
 'FMS scans multiple DME stations and triangulates a position. Requires AT LEAST 2 stations in range. Backup when GPS unavailable.',
 35.83, 40.00, 'component', '#fbbf24', 'scan-dme'),
(@diagram_id, '8 — Sensor Watchdog',
 'Continuously monitors all position sensors (GPS, DME, VOR/DME, AHRS+ADC). Selects best source for FMS position. Excludes faulted sensors. Position Uncertain if all degraded.',
 50.00, 40.00, 'component', '#22c55e', 'watchdog'),
(@diagram_id, '9 — VOR/DME (Single-Station Fix)',
 'Single-station position fix from radial + distance. Less accurate than scanning DME. Used when scanning DME unavailable.',
 64.16, 40.00, 'component', '#a78bfa', 'vor-dme'),
(@diagram_id, '10 — AHRS + ADC (Dead Reckoning)',
 'Last-resort position source. Dead reckoning from heading + airspeed + time. Accuracy degrades over time. Triggers Position Uncertain when significantly degraded.',
 80.00, 40.00, 'component', '#dc2626', 'ahrs'),
(@diagram_id, '11 — Dual-Cycle Database (28-day AIRAC)',
 'Navigation database in dual-cycle architecture: current AIRAC + next AIRAC simultaneously available. Crew swaps via FPCDU on new cycle effective date. 28-day cycle.',
 14.16, 60.00, 'component', '#22c55e', 'database'),
(@diagram_id, '12 — Pilot Data + Company Routes',
 'Pilot data: custom airports/routes/waypoints in non-volatile memory. Preserved across database updates. Company routes: pre-loaded operator routings.',
 35.83, 60.00, 'component', '#7dd3fc', 'pilot-data'),
(@diagram_id, '13 — LNAV + VNAV + Frequency Management',
 'LNAV: lateral steering to AFCS via flight plan. VNAV: vertical profile (TOD, descent angles, altitude/speed targets). Frequency management: auto-tunes nav radios via ARCDU.',
 64.16, 60.00, 'component', '#fbbf24', 'lnav-vnav'),
(@diagram_id, '14 — Position Uncertain Logic',
 'Three conditions: GPS LOST + DME INPUTS INSUFFICIENT + NO OTHER VALID SENSOR. Crew action: revert to RAW NAV (VOR/ADF via Bearing pointers); cross-check; run QRH; consider divert.',
 80.00, 60.00, 'component', '#dc2626', 'position-uncertain');

INSERT INTO diagram_states (diagram_id, state_name, state_label, description, hotspot_overrides) VALUES
(@diagram_id, 'normal', 'Normal Operations',
 'GPS primary, FMS position green, FPCDU showing flight plan + position. LNAV active to AFCS, VNAV providing profile, frequency management auto-tuning radios. Sensor Watchdog selecting GPS as best source. No Position Uncertain.',
 JSON_OBJECT()),
(@diagram_id, 'gps_loss_dme_backup', 'GPS Loss — Scanning DME Backup',
 'GPS lost. Sensor Watchdog automatically reverts FMS position to scanning DME (2+ stations triangulating). Transition silent. Position accuracy may drop slightly. Cross-check raw nav more frequently. Run QRH GPS LOSS non-normal.',
 JSON_OBJECT(
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#22c55e', 'pulse', true),
   'h8', JSON_OBJECT('color_hex', '#fbbf24', 'pulse', true)
 )),
(@diagram_id, 'position_uncertain', 'Position Uncertain — Raw Nav Reversion',
 'GPS lost AND DME insufficient AND no other valid sensor. POSITION UNCERTAIN displayed on FPCDU + PFD. Crew reverts to raw nav (VOR/ADF/DME via Bearing pointers per Phase 13). Cross-check FMS position; run QRH; consider divert to area with better ground-based nav coverage.',
 JSON_OBJECT(
   'h6', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h7', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h9', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h10', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true),
   'h14', JSON_OBJECT('color_hex', '#dc2626', 'pulse', true)
 ));

SELECT @diagram_id AS diagram_id,
   (SELECT COUNT(*) FROM diagram_hotspots WHERE diagram_id = @diagram_id) AS hotspots_inserted,
   (SELECT COUNT(*) FROM diagram_states WHERE diagram_id = @diagram_id) AS states_inserted;
