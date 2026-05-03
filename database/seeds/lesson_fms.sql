-- =============================================================================
-- AviatorTutor — Phase 18 (ATA 22B FMS) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fms' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'fms-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'FMS — Universal UNS-1, LNAV/VNAV, Database, Position',
     'fms-overview',
     'overview',
     'Universal Avionics UNS-1 series Flight Management System on the Q400. Components: NCU (Navigation Computer Unit) + FPCDU (4" or 5" Flat-Panel Control Display Unit) + DTU (Data Transfer Unit for database loading) + Configuration Module. Functions: lateral guidance (LNAV), vertical guidance (VNAV), fuel management, frequency management (auto-tunes nav radios), navigation database. Position determination: GPS primary + scanning DME + VOR/DME + AHRS/ADC for backup. Dual-cycle navigation database (current + future AIRAC). Pilot data storage for airports/routes/waypoints. Position Uncertain message when GPS lost or insufficient sensor inputs. High-latitude operations: FMS switches to TRUE heading near pole. Magnetic variation handling (FMS database variation vs VOR station declination). Sensor watchdog monitors and selects best position source. AFIS / UniLink datalink interfaces.',
     '<p>The Q400 FMS is a Universal Avionics UNS-1 series Flight Management System. The Q400 standard build typically has a single FMS, though some operators have dual installations. The system architecture revolves around four key components: the NCU (Navigation Computer Unit) does the calculation; the FPCDU (Flat-Panel Control Display Unit) is the crew interface; the DTU (Data Transfer Unit) is how the navigation database is loaded; and the Configuration Module stores aircraft-specific configuration. Position determination is layered: GPS is primary; scanning DME provides position fixes via multiple ground station distances; VOR/DME provides single-station fixes; AHRS + ADC provide dead reckoning when other sensors are degraded. The navigation database is dual-cycle (current AIRAC + next AIRAC), allowing crew to swap to the new cycle on the day it becomes effective. Pilot data storage allows custom waypoints and routes. Critical conditions: Position Uncertain when GPS is lost and DME inputs insufficient; high-latitude operations switch heading reference to TRUE; magnetic variation in the database may differ slightly from VOR station declination. The Sensor Watchdog continuously monitors all position sensors and selects the best source.</p>',
     JSON_ARRAY(
       'Universal Avionics UNS-1 series FMS. Q400 standard build typically single FMS; some operators dual',
       'Components: Navigation Computer Unit (NCU), Flat-Panel Control Display Unit (FPCDU 4" or 5"), Data Transfer Unit (DTU 100) for database loading, Configuration Module',
       'Functions: lateral guidance (LNAV), vertical guidance (VNAV), fuel management, frequency management (auto-tunes nav radios via ARCDU), navigation database lookup',
       'Position sensors: GPS (primary), scanning DME (position fix via multiple stations), VOR/DME (single-station fix), AHRS + ADC (dead reckoning backup)',
       'GPS as primary means of navigation. Spaced-Based Augmentation System (SBAS, e.g. WAAS) supports LPV approaches',
       'Scanning DME: FMS scans multiple DME stations and triangulates a position fix — supports navigation when GPS unavailable',
       'Dual-cycle navigation database: current AIRAC cycle + next AIRAC cycle. Crew can swap to next cycle on its effective date. 28-day AIRAC cycle',
       'Pilot data storage: custom airports, routes, waypoints. Stored separately from the navigation database',
       'Company routes: pre-programmed routes specific to the operator',
       'Offline flight planning: external tools generate flight plans loaded via DTU',
       'Frequency management: FMS automatically tunes the active nav radio for the next waypoint or approach',
       'Lateral guidance (LNAV): steers aircraft along the flight plan track. Output to AFCS as steering commands',
       'Vertical guidance (VNAV): provides vertical profile for descent/climb. Output to AFCS for vertical mode',
       'Position Uncertain message: GPS lost AND DME inputs insufficient AND no other valid position sensor. Crew must use raw nav (VOR/ADF) to verify position',
       'Sensor Watchdog: continuously monitors all position sensors (GPS, DME, VOR/DME, IRS/AHRS) and selects the best source for FMS position. Excludes faulted sensors',
       'High-latitude operations: near magnetic pole, FMS switches heading reference from MAGNETIC to TRUE. Required because magnetic variation becomes excessive and unreliable',
       'Magnetic variation handling: FMS uses variation from its database; VOR stations use their published declination. Small discrepancies possible — typically <1°',
       'AFIS (Aircraft Flight Information Service): datalink interface for weather, NOTAMs, dispatch communications',
       'UniLink: Universal Avionics datalink module for ACARS-equivalent communications',
       'Data Transfer Unit (DTU 100): loads navigation database into NCU. Database is loaded via memory module (typically a sealed cartridge)',
       'Bearing pointer to FMS (Phase 13): when FMS is operating, points to next waypoint',
       'Frequency tuning: FMS feeds nav radio frequencies to ARCDUs via FMS auto-tune. Manual override via ARCDU',
       'Position determination accuracy depends on sensor combination available: GPS gives best accuracy; DME-DME good; VOR/DME OK; AHRS/ADC dead reckoning degrades over time'
     ),
     JSON_ARRAY(
       'GPS is primary position sensor. Loss of GPS forces FMS to scanning DME or other backup. Position Uncertain if no backup adequate.',
       'Dual-cycle database is a workflow tool: load both current + next AIRAC. Crew swaps to next on its effective date — no need to wait for ground service.',
       'Scanning DME triangulation requires 2+ DME stations in range. In remote areas with no DME, FMS may degrade to AHRS dead reckoning.',
       'High-latitude TRUE heading switch: when FMS commands TRUE, the displayed heading is also TRUE — different from the magnetic reading on the standby compass.',
       'Magnetic variation vs VOR declination: small (<1°) typical, but when FMS course and raw VOR course disagree by 1-2°, this is normal. Don''t assume one is wrong.',
       'AFIS datalink is your weather + dispatch link. Confirm AFIS message is current before relying on it.',
       'Frequency management: FMS auto-tunes the active nav radio. Manual override is via the ARCDU, not the FPCDU.'
     ),
     JSON_ARRAY(
       'Universal UNS-1 series FMS, NOT GE Aviation or Honeywell. Q400-specific.',
       'Dual-cycle = current + NEXT AIRAC. Not last + current.',
       'AIRAC cycle is 28 DAYS. Standard ICAO.',
       'Position Uncertain = GPS LOST + DME insufficient + no other valid sensor. Three conditions, not just GPS loss.',
       'Scanning DME requires multiple ground stations (at least 2 for triangulation).',
       'FMS bearing pointer = NEXT WAYPOINT, not destination.',
       'High-latitude: FMS switches to TRUE heading. Magnetic variation becomes unreliable near the poles.',
       'Frequency management is FMS → ARCDU auto-tune. Manual tune is via ARCDU.',
       'Components: NCU + FPCDU + DTU + Config Module. Four parts.',
       'AFIS = Aircraft Flight Information Service. Datalink for weather/NOTAM/dispatch.',
       'UniLink = Universal Avionics datalink (their ACARS equivalent).'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'fms-overview';
