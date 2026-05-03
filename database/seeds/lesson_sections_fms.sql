-- =============================================================================
-- AviatorTutor — Phase 18: ATA 22B FMS — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fms' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fms-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Universal UNS-1 Architecture',
 '<p>The Q400 FMS is a Universal Avionics UNS-1 series Flight Management System. The standard build is typically a single FMS, with some operators having dual installations. Architecture has four main components: the NCU (Navigation Computer Unit) does the calculation, the FPCDU (Flat-Panel Control Display Unit) is the crew interface, the DTU (Data Transfer Unit) loads the navigation database, and the Configuration Module stores aircraft-specific configuration. Position determination is layered with GPS as primary, scanning DME as backup, VOR/DME for single-station fixes, and AHRS+ADC dead reckoning as last resort. The Sensor Watchdog continuously selects the best position source. The dual-cycle navigation database lets crew swap from current to next AIRAC on its effective date without ground service.</p>',
 'overview', 10),
(@lesson_id, 'Components — NCU, FPCDU, DTU, Config Module',
 '<ul>
  <li><strong>Navigation Computer Unit (NCU):</strong> the FMS brain. Performs position calculation, navigation, vertical/lateral guidance, fuel management, frequency management.</li>
  <li><strong>Flat-Panel Control Display Unit (FPCDU):</strong> 4" or 5" display + alphanumeric keyboard. Crew interface for flight plan entry, modes, data review.</li>
  <li><strong>Data Transfer Unit (DTU 100):</strong> loads navigation database into the NCU via memory module/cartridge. Used for AIRAC database updates.</li>
  <li><strong>Configuration Module:</strong> stores aircraft-specific configuration (sensor types, output formats, options).</li>
  <li><strong>Position sensors:</strong> GPS (with SBAS for LPV approaches), scanning DME, VOR/DME, AHRS, ADC.</li>
  <li><strong>UniLink datalink module:</strong> ACARS-equivalent communications. AFIS interface.</li>
  <li><strong>Pilot data storage:</strong> custom airports, routes, waypoints in non-volatile memory.</li>
  <li><strong>Navigation database:</strong> ICAO standard, dual-cycle (current + next AIRAC, 28-day cycle).</li>
  <li><strong>Company routes:</strong> pre-programmed operator-specific routes.</li>
  <li><strong>Sensor Watchdog:</strong> continuous monitoring of all sensors, selects best position source.</li>
 </ul>',
 'components', 20),
(@lesson_id, 'Operation — Position, LNAV, VNAV, Frequency Management',
 '<h4>Position determination</h4>
<ul>
  <li>GPS (primary): WGS-84 position. SBAS augmentation supports LPV approaches.</li>
  <li>Scanning DME: FMS scans multiple DME stations, triangulates position from distances. Requires ≥2 stations in range.</li>
  <li>VOR/DME: single-station fix from radial + distance. Less accurate than scanning DME.</li>
  <li>AHRS + ADC: dead reckoning when no other sensor available. Accuracy degrades over time.</li>
  <li>Sensor Watchdog selects best source; Position Uncertain if all degraded.</li>
</ul>
<h4>Lateral Navigation (LNAV)</h4>
<ul>
  <li>FMS computes track from current position to next waypoint per active flight plan.</li>
  <li>Output to AFCS (Phase 7) as steering commands.</li>
  <li>Bearing pointer (Phase 13) set to FMS shows next waypoint.</li>
  <li>Course over ground (CRS) on PFD shows current desired track.</li>
</ul>
<h4>Vertical Navigation (VNAV)</h4>
<ul>
  <li>FMS computes vertical profile per flight plan altitude constraints.</li>
  <li>Calculates top-of-descent (TOD), descent angles, altitude/speed targets.</li>
  <li>Output to AFCS as vertical mode commands.</li>
</ul>
<h4>Frequency management</h4>
<ul>
  <li>FMS auto-tunes nav radios (VOR1, VOR2, ADF1, ADF2) for next waypoint or approach.</li>
  <li>ARCDU manual tune overrides FMS auto-tune.</li>
  <li>FMS frequency management ensures appropriate radio is tuned for VHF/ILS approach without crew action.</li>
</ul>
<h4>Fuel management</h4>
<ul>
  <li>FMS uses fuel sensor inputs (Phase 8) + fuel-flow data to predict fuel-on-board at each waypoint.</li>
  <li>Computes fuel reserves at destination + alternates.</li>
  <li>Alerts if fuel below threshold.</li>
</ul>
<h4>Database management</h4>
<ul>
  <li>DTU loads navigation database from cartridge into NCU.</li>
  <li>Dual-cycle architecture: current + next AIRAC simultaneously available.</li>
  <li>Crew swaps cycles on effective date via FPCDU.</li>
  <li>Pilot data storage separate from nav database (preserved across updates).</li>
</ul>',
 'operation', 30),
(@lesson_id, 'Normal — Pre-Flight, Cruise, Approach',
 '<h4>Pre-flight</h4>
<ul>
  <li>FMS init: position (entered or GPS-derived), date/time, route from filed plan.</li>
  <li>Verify navigation database AIRAC cycle current.</li>
  <li>Load company route or build flight plan via FPCDU.</li>
  <li>Verify cruise altitude, alternate, fuel reserves.</li>
  <li>Brief approach: which approach loaded, runway, transitions.</li>
</ul>
<h4>Cruise</h4>
<ul>
  <li>LNAV active. Cross-check FMS position against raw VOR/DME at least every 30 minutes.</li>
  <li>VNAV active per company SOP.</li>
  <li>Frequency management auto-tunes nav radios for upcoming waypoint.</li>
  <li>Fuel management updates fuel-at-destination predictions.</li>
  <li>Sensor Watchdog continuously selects best position source.</li>
</ul>
<h4>Approach</h4>
<ul>
  <li>Verify approach loaded in flight plan; approach mode armed (per AFCS Phase 3).</li>
  <li>Bearing 1/2 set to FMS or VOR per pilot preference.</li>
  <li>FMS automatically tunes ILS frequency at approach activation.</li>
  <li>VNAV provides vertical profile for non-precision/RNAV approaches.</li>
  <li>For LPV: GPS+SBAS provides vertical guidance.</li>
</ul>',
 'normal', 40),
(@lesson_id, 'Abnormal — Position Uncertain, GPS Loss, Database',
 '<ul>
  <li><strong>Position Uncertain message:</strong> GPS lost AND DME inputs insufficient AND no other valid position sensor. Action: revert to raw nav (VOR/ADF/DME via Bearing pointers, Phase 13). Cross-check FMS position against raw nav. Run QRH; consider divert to a station with adequate ground-based nav coverage.</li>
  <li><strong>GPS loss:</strong> FMS reverts to scanning DME. Position accuracy degrades but navigation continues. Sensor Watchdog selects best source. Brief possible Position Uncertain if DME coverage thins out.</li>
  <li><strong>NCU fault:</strong> on dual-FMS aircraft, swap to other FMS. On single-FMS aircraft, raw nav only — defer per MEL on landing. Significant restriction.</li>
  <li><strong>FPCDU fault:</strong> no crew interface for FMS. NCU may continue to provide LNAV via current flight plan, but no flight plan changes possible. Defer.</li>
  <li><strong>Database expired:</strong> AIRAC cycle out of date. Critical for IFR — cannot dispatch on expired database. Update via DTU before flight.</li>
  <li><strong>Magnetic variation discrepancy:</strong> FMS course and raw VOR course disagree by 1-2°. Normal. FMS uses database variation; VOR uses station declination. Don''t fight the discrepancy.</li>
  <li><strong>High-latitude TRUE switch:</strong> near magnetic pole, FMS switches heading reference from magnetic to TRUE. Cross-check standby compass (still magnetic).</li>
  <li><strong>VNAV path discrepancy:</strong> calculated path doesn''t match desired profile. Verify altitude constraints, winds, performance data. Adjust manually if needed.</li>
  <li><strong>Frequency management failure:</strong> FMS not auto-tuning. Manual tune via ARCDU. Brief radio coverage for upcoming waypoints.</li>
 </ul>',
 'abnormal', 50),
(@lesson_id, 'Indications — FPCDU, Bearing Pointer, Messages',
 '<ul>
  <li><strong>FPCDU display pages:</strong> POS (position), FPL (flight plan), ARR (arrival), DEP (departure), PERF (performance), FUEL, DATA (NAV aids/airports), TUNE (frequencies).</li>
  <li><strong>Bearing pointer to FMS (Phase 13):</strong> on PFD when EFCP Bearing 1 or Bearing 2 selected to FMS. Points to NEXT WAYPOINT.</li>
  <li><strong>FMS course:</strong> displayed on PFD as the desired track from current waypoint to next.</li>
  <li><strong>Position Uncertain message:</strong> on FPCDU + possibly PFD when FMS position degraded.</li>
  <li><strong>NAV/APT data:</strong> EFCP DATA pushbutton (Phase 13) cycles 10 nearest NAV AIDS / 10 nearest AIRPORTS / both / off.</li>
  <li><strong>NO DATA flash:</strong> EFCP DATA push with FMS off → white NO DATA for 5 sec.</li>
  <li><strong>VNAV indications:</strong> on PFD vertical deviation indicator.</li>
  <li><strong>Sensor source indications:</strong> typically not visible to crew, but FMS uses Sensor Watchdog choice automatically.</li>
  <li><strong>AIRAC cycle indication:</strong> on FPCDU initialisation page. Cycle number + effective dates.</li>
 </ul>',
 'indications', 60),
(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>FMS standard build:</strong> 1 FMS. Some operators dual.</li>
  <li><strong>FMS components:</strong> NCU + FPCDU + DTU + Configuration Module.</li>
  <li><strong>Position sensors used:</strong> GPS (primary), scanning DME, VOR/DME, AHRS, ADC.</li>
  <li><strong>Navigation database type:</strong> dual-cycle (current + next AIRAC).</li>
  <li><strong>AIRAC cycle length:</strong> 28 days.</li>
  <li><strong>Position Uncertain trigger:</strong> GPS lost + DME insufficient + no other valid sensor.</li>
  <li><strong>Scanning DME minimum stations:</strong> 2 (for triangulation).</li>
  <li><strong>Magnetic variation discrepancy with VOR:</strong> typically <1° (informational; not a fault).</li>
  <li><strong>FMS heading reference at high latitudes:</strong> TRUE (switches from magnetic).</li>
  <li><strong>FPCDU sizes:</strong> 4" or 5" Flat-Panel Control Display Unit.</li>
  <li><strong>SBAS support:</strong> for LPV approaches (GPS+WAAS or equivalent).</li>
  <li><strong>Database cartridge:</strong> sealed memory module loaded via DTU.</li>
  <li><strong>Frequency management:</strong> auto-tunes nav radios via ARCDU; manual override via ARCDU.</li>
  <li><strong>Cross-check vs raw nav recommended:</strong> at least every 30 min in cruise (per Phase 13 nav).</li>
 </ul>',
 'limitations', 70),
(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>UNS-1-NCU-FPCDU-DTU-CONFIG</strong> — 4 components: Navigation Computer Unit + Flat-Panel CDU + Data Transfer Unit + Configuration Module.</li>
  <li><strong>GPS-DME-VOR-AHRS</strong> — position sensor priority: GPS primary, scanning DME, VOR/DME, AHRS+ADC.</li>
  <li><strong>DUAL-CYCLE-28-DAYS</strong> — dual-cycle database (current + next AIRAC); 28-day cycle.</li>
  <li><strong>POSITION-UNCERTAIN-3</strong> — Position Uncertain on 3 conditions: GPS lost + DME insufficient + no other valid sensor.</li>
  <li><strong>SCAN-DME-2-STATIONS</strong> — scanning DME requires at least 2 stations for triangulation.</li>
  <li><strong>FMS-NEXT-WPT</strong> — FMS bearing pointer points to NEXT WAYPOINT (not destination).</li>
  <li><strong>HIGH-LAT-TRUE</strong> — high-latitude operations: FMS switches heading reference to TRUE.</li>
  <li><strong>SBAS-LPV</strong> — SBAS (WAAS) augmentation supports LPV approaches.</li>
  <li><strong>FREQ-AUTO-FMS-MANUAL-ARCDU</strong> — frequency auto-tune via FMS; manual override via ARCDU.</li>
  <li><strong>VAR-VS-DECLINATION-1</strong> — FMS uses database variation; VOR uses station declination. Discrepancy typically <1°.</li>
  <li><strong>SENSOR-WATCHDOG</strong> — continuously selects best position source from available sensors.</li>
  <li><strong>AFIS-UNILINK</strong> — datalink interfaces: AFIS for weather/NOTAM/dispatch; UniLink for Universal-Avionics ACARS.</li>
 </ol>
<p>Position uncertainty chant: <em>"GPS lost · check DME · check VOR · raw nav · QRH if persistent."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
