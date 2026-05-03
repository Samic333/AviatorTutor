-- =============================================================================
-- AviatorTutor — Phase 18: ATA 22B FMS — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fms' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fms-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Universal UNS-1 — The Q400 FMS',
 'The Q400 FMS is a Universal Avionics UNS-1 series Flight Management System. The standard Q400 build is typically a single FMS, with some operators having dual installations. The system architecture has four components: the NCU (Navigation Computer Unit) does the calculation, the FPCDU (Flat-Panel Control Display Unit) is the crew interface, the DTU (Data Transfer Unit) loads the navigation database from a memory cartridge, and the Configuration Module stores aircraft-specific configuration. Position determination is layered: GPS is primary, scanning DME is the backup, VOR/DME provides single-station fixes, and AHRS+ADC dead reckoning is the last resort. The Sensor Watchdog continuously picks the best source. This lesson walks the architecture, modes, position determination, and the Position Uncertain logic.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'Q400 FMS overview',
 'UNS-1-NCU-FPCDU-DTU-CONFIG · GPS-DME-VOR-AHRS sensor hierarchy.',
 'On a real GPS-loss event in cruise, the FMS handles the transition silently. Trust the Sensor Watchdog.',
 NULL),

(@lesson_id, 20, 'concept',
 'Four Components — NCU, FPCDU, DTU, Config',
 'Memorise the four FMS components and their roles. <strong>NCU (Navigation Computer Unit):</strong> the brain. Performs position calculation, lateral guidance (LNAV), vertical guidance (VNAV), fuel management, frequency management. Outputs steering commands to the AFCS. <strong>FPCDU (Flat-Panel Control Display Unit):</strong> 4" or 5" display + alphanumeric keyboard. The crew interface — flight plan entry, mode selection, data review. <strong>DTU (Data Transfer Unit, model DTU 100):</strong> loads the navigation database from a sealed memory cartridge into the NCU. Used during AIRAC database updates. <strong>Configuration Module:</strong> stores aircraft-specific configuration — sensor types, output formats, options. Read by the NCU on power-up. Plus auxiliary modules: UniLink for datalink, AFIS for weather/NOTAM.',
 'diagram', '/assets/aircraft/q400/fms-flow.svg',
 'Four FMS components',
 'UNS-1-NCU-FPCDU-DTU-CONFIG.',
 'When you load a new database via DTU, the NCU receives the data; the FPCDU is your interface to verify the cycle and date.',
 NULL),

(@lesson_id, 30, 'concept',
 'Sensor Hierarchy — GPS Primary',
 'FMS position determination uses a layered sensor architecture, with the Sensor Watchdog continuously selecting the best source. <strong>GPS</strong> (primary): WGS-84 position with SBAS augmentation supporting LPV approaches. <strong>Scanning DME</strong> (backup): FMS scans multiple DME stations and triangulates a position from the distances — requires at least 2 stations in range. <strong>VOR/DME</strong> (single-station fix): radial + distance from one station. Less accurate than scanning DME. <strong>AHRS + ADC</strong> (dead reckoning): when no other sensor is adequate. Accuracy degrades over time. The Sensor Watchdog ranks these and selects the best for the FMS position. If all are degraded, Position Uncertain message displays.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'Sensor hierarchy + Watchdog logic',
 'GPS-DME-VOR-AHRS · SENSOR-WATCHDOG selects best.',
 'In remote areas with no DME coverage, GPS loss takes you straight to AHRS dead reckoning — Position Uncertain comes fast.',
 JSON_OBJECT(
   'prompt', 'What is the primary position sensor for the Q400 FMS, and what is the backup if GPS is lost?',
   'options', JSON_ARRAY(
     'VOR/DME primary; GPS backup',
     'GPS primary; scanning DME backup; VOR/DME and AHRS lower priority',
     'AHRS primary; GPS backup',
     'No primary; all equally weighted'
   ),
   'correct_index', 1,
   'explanation', 'GPS primary, scanning DME backup, then VOR/DME, then AHRS+ADC dead reckoning. Sensor Watchdog selects best. Mnemonic: GPS-DME-VOR-AHRS.'
 )),

(@lesson_id, 40, 'system',
 'Dual-Cycle Database — 28-Day AIRAC',
 'The FMS navigation database follows the ICAO 28-day AIRAC cycle. The Q400 FMS supports a <strong>dual-cycle</strong> architecture: the database loaded into the NCU contains BOTH the current AIRAC cycle AND the next AIRAC cycle simultaneously. On the day the new cycle becomes effective, the crew can swap from current to next via the FPCDU — no need for ground service to load a new database. This is operationally valuable: a crew flying through a database transition (e.g. midnight UTC) can swap cycles in the cockpit. The dual-cycle architecture also means database updates can be done several days early without affecting current operations. The DTU loads the dual-cycle database from a sealed memory cartridge — typically performed by maintenance.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'Dual-cycle database with 28-day AIRAC',
 'DUAL-CYCLE-28-DAYS. Current + next AIRAC simultaneously.',
 'On a long-haul international leg crossing the AIRAC change date, plan for the cycle swap mid-flight if needed.',
 NULL),

(@lesson_id, 50, 'system',
 'Pilot Data Storage + Company Routes',
 'Beyond the navigation database (which is updated AIRAC-cycle), the FMS supports <strong>pilot data storage</strong>: custom airports, routes, waypoints stored in non-volatile memory. Pilot data is preserved across database updates — your custom waypoints and routes don''t get wiped when a new AIRAC cycle is loaded. <strong>Company routes</strong> are pre-programmed operator-specific routes — frequently-flown city pairs with standard routings. Loaded by the operator''s dispatch or maintenance system. Selecting a company route in the FPCDU loads the entire routing in one selection rather than building it waypoint by waypoint. <strong>Offline flight planning</strong>: external tools (e.g. Jeppesen Mission Planner, operator-specific systems) can generate flight plans loaded into the FMS via the DTU. Useful for complex international routings.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'Pilot data + company routes + offline flight planning',
 'Pilot data preserved across DB updates · company routes one-touch · offline planning via DTU.',
 'Company routes save 5+ minutes on standard sectors. Use them.',
 NULL),

(@lesson_id, 60, 'system',
 'LNAV + VNAV + Frequency Management',
 'Three core FMS guidance functions. <strong>LNAV (Lateral Navigation):</strong> FMS computes the desired track from the current position to the next waypoint per the active flight plan. Output to the AFCS (Phase 3) as steering commands. The Bearing pointer set to FMS (Phase 13) points to the next waypoint. <strong>VNAV (Vertical Navigation):</strong> FMS computes a vertical profile per altitude constraints in the flight plan — top-of-descent (TOD), descent angles, altitude/speed targets. Output to the AFCS for vertical mode. <strong>Frequency Management:</strong> FMS automatically tunes the active nav radios (VOR1, VOR2, ADF1, ADF2) for the next waypoint or approach via the ARCDU. Manual override via the ARCDU. So the FMS is your guidance brain, your radio tuner, and your fuel calculator all in one.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'LNAV + VNAV + Frequency Management',
 'LNAV steering · VNAV profile · FREQ-AUTO-FMS-MANUAL-ARCDU.',
 'On a day with multiple frequency changes, FMS auto-tune is your friend — frees you to focus on flying.',
 NULL),

(@lesson_id, 70, 'system',
 'Position Uncertain — Three Conditions',
 'The Position Uncertain message displays when the FMS cannot establish reliable position. Three conditions must coincide: <strong>(1) GPS LOST</strong> — primary sensor unavailable. <strong>(2) DME INPUTS INSUFFICIENT</strong> — fewer than 2 stations in range, OR scanning DME ranges showing inconsistent triangulation. <strong>(3) NO OTHER VALID POSITION SENSOR</strong> — VOR/DME unavailable, AHRS dead reckoning has degraded beyond accuracy threshold. When all three conditions are met, the FMS displays Position Uncertain on the FPCDU and possibly on the PFD. Crew action: revert to RAW NAV — VOR/ADF via Bearing pointers (Phase 13). Cross-check FMS position against raw nav. Run the QRH non-normal. Consider divert to a station with adequate ground-based nav coverage.',
 'video', '/assets/aircraft/q400/fms-flow.svg',
 'Position Uncertain three-condition logic',
 'POSITION-UNCERTAIN-3. GPS + DME + no-other = display message.',
 'Position Uncertain in remote ops (oceanic, polar) is a real risk. Brief raw-nav fallback before departure.',
 JSON_OBJECT(
   'prompt', 'What three conditions must all be true for the FMS to display the Position Uncertain message?',
   'options', JSON_ARRAY(
     'GPS lost only',
     'GPS lost AND DME inputs insufficient AND no other valid position sensor',
     'AHRS failed only',
     'Database expired'
   ),
   'correct_index', 1,
   'explanation', 'Three conditions: GPS lost + DME insufficient + no other valid sensor. Mnemonic: POSITION-UNCERTAIN-3.'
 )),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight FMS Initialisation',
 'FMS pre-flight initialisation sequence. (1) Power on. NCU initialises; FPCDU shows initialisation page. (2) Verify navigation database AIRAC cycle current — cycle number + effective dates displayed. (3) Verify position: GPS-derived position should match the parked aircraft latitude/longitude (known reference). Manually adjust if needed. (4) Enter date/time if not auto-set. (5) Load company route via FPCDU, OR build flight plan waypoint-by-waypoint, OR load pre-planned route via DTU. (6) Verify cruise altitude, alternate, fuel reserves, performance data. (7) Brief approach: which approach loaded, runway, transitions. (8) Verify frequency management active. The FMS is now ready for taxi.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'FMS pre-flight initialisation steps',
 'Power · DB cycle · POS · date · route · cruise data · approach · freq mgmt.',
 'Skipping the database cycle check is the most common pre-flight error. Verify on every leg.',
 JSON_OBJECT(
   'prompt', 'How long is the standard ICAO AIRAC cycle?',
   'options', JSON_ARRAY(
     '7 days',
     '14 days',
     '28 days',
     '56 days'
   ),
   'correct_index', 2,
   'explanation', 'AIRAC cycle is 28 days. The Q400 FMS dual-cycle architecture has current + next AIRAC simultaneously. Mnemonic: DUAL-CYCLE-28-DAYS.'
 )),

(@lesson_id, 90, 'normal_op',
 'Cruise — Cross-Check, Frequency Tune, Fuel',
 'Cruise FMS discipline. <strong>(1) Cross-check FMS position against raw VOR/DME at least every 30 minutes</strong> (per Phase 13). Bearing 1 set to VOR1 with appropriate radial + DME for distance. Confirm FMS position agrees with raw nav. Discrepancy <1° normal (magnetic variation difference). <strong>(2) Frequency management:</strong> verify FMS auto-tunes the next waypoint''s VOR/DME. Manual override via ARCDU only if needed. <strong>(3) Fuel management:</strong> monitor fuel-at-destination prediction. Alert if below company minimum. Plan divert if trend unfavourable. <strong>(4) Sensor Watchdog:</strong> usually transparent — but watch for unexpected position jumps that indicate sensor source change. <strong>(5) Database cycle:</strong> if mid-flight crosses AIRAC change date, swap cycles via FPCDU when new cycle becomes effective.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'Cruise FMS discipline',
 'Cross-check 30 min · auto-tune · fuel · sensor watch · cycle swap.',
 'A captain who never cross-checks FMS vs raw nav is one captain. A captain who does every 30 min is the captain who catches the FMS error before it bites.',
 NULL),

(@lesson_id, 100, 'abnormal',
 'GPS Loss + Sensor Watchdog Reversion',
 'GPS lost in cruise. The Sensor Watchdog automatically reverts the FMS to its next-best position source: scanning DME (if 2+ stations in range), then VOR/DME (single station), then AHRS+ADC dead reckoning. <strong>The transition is silent</strong> — no Position Uncertain message unless all sources degrade. Position accuracy may drop slightly: scanning DME is good, VOR/DME OK, AHRS dead reckoning degrades over time. Monitor for unexpected position jumps. Cross-check raw nav more frequently — every 10 min instead of 30. If you''re in a remote area without DME coverage, prepare for Position Uncertain — brief the divert plan now. Run the QRH GPS LOSS non-normal. Document for maintenance on landing.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'GPS loss with Sensor Watchdog reversion',
 'GPS lost · DME backup · VOR · AHRS · monitor for Position Uncertain.',
 'A clean GPS loss with good DME coverage is non-eventful. A GPS loss in mid-Atlantic is a real divert plan.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'High-Latitude TRUE Heading Switch',
 'Near the magnetic pole — both magnetic and geographic — magnetic variation becomes extreme and unreliable. The FMS automatically switches its heading reference from MAGNETIC to TRUE at high latitudes. So the FMS course displayed becomes a TRUE course, not a magnetic course. The standby compass is still magnetic, so it disagrees with the FMS — this is normal and expected. Captains operating in polar or near-polar regions must understand the switch and brief the FO. ATC clearances may be issued in TRUE in polar regions; otherwise convert. Note: the auto-switch threshold varies by FMS model — typically near 70°N/S or 80°N/S latitude. Once back in middle latitudes, the FMS auto-switches back to magnetic.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'High-latitude TRUE heading auto-switch',
 'HIGH-LAT-TRUE. Standby compass still magnetic — disagreement normal.',
 'Polar ops on the Q400 are unusual but not zero. Brief the TRUE switch on any planned high-latitude leg.',
 JSON_OBJECT(
   'prompt', 'In polar operations, what does the FMS automatically do with its heading reference?',
   'options', JSON_ARRAY(
     'Stays on magnetic',
     'Switches from MAGNETIC to TRUE at high latitudes',
     'Switches from true to magnetic',
     'Disables heading'
   ),
   'correct_index', 1,
   'explanation', 'High-latitude FMS auto-switches heading reference from magnetic to TRUE. Standby compass still magnetic — disagreement normal. Mnemonic: HIGH-LAT-TRUE.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: FMS Non-Normals',
 'Q400 QRH non-normals for FMS cluster into seven groups. (1) GPS LOSS: Sensor Watchdog reverts to backup; cross-check raw nav more often; brief for Position Uncertain risk. (2) POSITION UNCERTAIN: revert to raw nav (VOR/ADF/DME); verify position; consider divert. (3) NCU FAULT: on dual-FMS swap to other FMS; on single, raw nav only and defer per MEL. (4) FPCDU FAULT: NCU continues current flight plan; no flight plan changes; defer. (5) DATABASE EXPIRED: cannot dispatch IFR; update via DTU before flight. (6) MAGNETIC VARIATION DISCREPANCY: <1° between FMS and VOR is normal; >1° investigate but don''t panic. (7) HIGH-LATITUDE TRUE SWITCH: brief F/O; cross-check raw nav; ATC clearances in TRUE in polar.',
 'image', '/assets/aircraft/q400/fms-flow.svg',
 'QRH FMS cluster',
 'GPS · POS UNCERTAIN · NCU · FPCDU · DATABASE · VARIATION · HIGH LAT.',
 'Most FMS non-normals are silent reversions handled by the Sensor Watchdog. The captain''s job is the cross-check.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Position Uncertain in Cruise',
 'Setup: cruise at FL230 over a remote area. 90 minutes from destination. GPS sensor was lost 20 minutes ago — Sensor Watchdog reverted to scanning DME, position acceptable. Now: scanning DME loses last available station as you cross over a low-density nav area. AHRS dead reckoning is the only sensor left; accuracy has been degrading over the GPS-loss period.\n\nFPCDU displays POSITION UNCERTAIN. PFD shows degraded position indication.\n\nFirst 60 seconds: PNF announces "Position Uncertain on FMS." PF maintains aircraft control. Captain calls "raw nav, immediate." PNF sets Bearing 1 to VOR1 with the nearest known VOR. Tunes ARCDU manually. Bearing pointer comes alive on a recognisable VOR. Cross-check: FMS position (degraded) vs raw VOR + DME (where available).\n\nNext minutes: assess whether continued navigation is safe. Options: (a) continue if raw nav coverage is adequate; (b) divert to a field with better ground-based nav; (c) climb or descend to find better DME coverage. Run the QRH POSITION UNCERTAIN. Declare PAN-PAN if appropriate. Brief the F/O that workload is high.\n\nLanding side: full diagnosis on landing. Likely a maintenance write-up on GPS receiver or scanning DME logic. Plan next sector around any restrictions.',
 'animation', '/assets/aircraft/q400/fms-flow.svg',
 'Position Uncertain scenario in remote cruise',
 'Raw nav · cross-check · QRH · divert if coverage poor.',
 'Position Uncertain in a covered area is annoying. In a remote area it''s an emergency. Plan accordingly.',
 JSON_OBJECT(
   'prompt', 'Position Uncertain in cruise. Captain''s first action?',
   'options', JSON_ARRAY(
     'Restart FMS',
     'Revert to raw nav (VOR/ADF/DME via Bearing pointers); cross-check FMS position; run QRH',
     'Declare emergency immediately',
     'Continue and ignore message'
   ),
   'correct_index', 1,
   'explanation', 'Raw nav first; cross-check; run QRH; consider divert if coverage poor. Position Uncertain is degraded position, not always emergency.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: FMS in 60 Seconds',
 'Recap:\n  • Q400 FMS = Universal Avionics UNS-1 series. Standard build single FMS; some operators dual.\n  • 4 components: NCU + FPCDU (4" or 5") + DTU + Configuration Module.\n  • Position sensors (priority): GPS primary, scanning DME, VOR/DME, AHRS+ADC dead reckoning.\n  • Sensor Watchdog continuously selects best source.\n  • SBAS (WAAS) augmentation for LPV approaches.\n  • Dual-cycle navigation database (current + next AIRAC). 28-day cycle.\n  • Pilot data storage preserved across DB updates. Company routes pre-loaded by operator.\n  • LNAV: lateral steering to AFCS. VNAV: vertical profile. Frequency management auto-tunes nav radios.\n  • Position Uncertain: GPS lost AND DME insufficient AND no other valid sensor.\n  • High-latitude FMS auto-switches heading reference from magnetic to TRUE.\n  • Cross-check FMS vs raw nav at least every 30 min.\n  • Magnetic variation discrepancy with VOR <1° typical (database vs station declination).\n  • AFIS datalink for weather/NOTAM. UniLink for Universal-ACARS.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'UNS-1-NCU-FPCDU-DTU-CONFIG · GPS-DME-VOR-AHRS · DUAL-CYCLE-28-DAYS · POSITION-UNCERTAIN-3 · SCAN-DME-2-STATIONS · FMS-NEXT-WPT · HIGH-LAT-TRUE · SBAS-LPV · FREQ-AUTO-FMS-MANUAL-ARCDU · VAR-VS-DECLINATION-1 · SENSOR-WATCHDOG · AFIS-UNILINK',
 'Twelve mnemonics carry every FMS question. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
