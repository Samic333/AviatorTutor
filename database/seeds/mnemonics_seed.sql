-- =============================================================================
-- Seed mnemonics. Migrates the hardcoded set from views/study/detail.php
-- (ATA24/29/28/72) into the mnemonics table and adds a few high-value
-- additions per system. Skip silently if mnemonics table is missing.
--
-- Re-runnable: deletes any existing rows for the same (system_id, phrase)
-- before inserting.
-- =============================================================================

-- Helper: resolve system id by slug.
SET @s_electrical      := (SELECT id FROM systems WHERE slug = 'electrical' LIMIT 1);
SET @s_hydraulic       := (SELECT id FROM systems WHERE slug = 'hydraulic' LIMIT 1);
SET @s_fuel            := (SELECT id FROM systems WHERE slug = 'fuel' LIMIT 1);
SET @s_powerplant      := (SELECT id FROM systems WHERE slug = 'powerplant' LIMIT 1);
SET @s_propeller       := (SELECT id FROM systems WHERE slug = 'propeller' LIMIT 1);
SET @s_flightcontrols  := (SELECT id FROM systems WHERE slug = 'flight-controls' LIMIT 1);
SET @s_landinggear     := (SELECT id FROM systems WHERE slug = 'landing-gear' LIMIT 1);
SET @s_fireprot        := (SELECT id FROM systems WHERE slug = 'fire-protection' LIMIT 1);
SET @s_ice             := (SELECT id FROM systems WHERE slug = 'ice-rain' LIMIT 1);

DELETE FROM mnemonics WHERE phrase IN (
  'GRAB','PIG','FLAPS','FOUR','TIPS','HOT','SAFE','GEAR','FIRE','ICE'
);

INSERT INTO mnemonics (system_id, phrase, breakdown_json, why_it_works, worked_example, sort_order, is_published) VALUES
(@s_electrical, 'GRAB',
 JSON_ARRAY(
   JSON_OBJECT('letter','G','meaning','Generators (AC GEN 1, AC GEN 2 + DC starter/gens)'),
   JSON_OBJECT('letter','R','meaning','Rectifiers (TRU 1, TRU 2 — convert AC to DC)'),
   JSON_OBJECT('letter','A','meaning','APU starter/gen + standby battery'),
   JSON_OBJECT('letter','B','meaning','Batteries (main, aux, standby — three NiCads)')
 ),
 'GRAB groups the four DC sources of power available in flight by the order you would lose them: generators first, rectifiers next, APU and batteries last.',
 'In a dual generator failure, walk the EPGDS in GRAB order: confirm both gens off, check that TRUs took the load, that APU gen is available, and that all three batteries are healthy before any battery shed.',
 10, 1),

(@s_hydraulic, 'PIG',
 JSON_ARRAY(
   JSON_OBJECT('letter','P','meaning','Pumps (engine-driven, electric, PTU)'),
   JSON_OBJECT('letter','I','meaning','Indications (HYD pressure + quantity on EICAS)'),
   JSON_OBJECT('letter','G','meaning','Gauges (system 1, 2, standby — confirm 3000 PSI nominal)')
 ),
 'PIG is the read-flow for any HYD caution: confirm what pumps you have, check the indications, then read the gauges in the established order.',
 'On HYD 1 PRESS LO caution: P — engine pump 1 lost, electric pump available; I — quantity normal so it is a pump failure not a leak; G — gauge confirms < 1500 PSI. Cross-feed via PTU from system 2.',
 20, 1),

(@s_fuel, 'FLAPS',
 JSON_ARRAY(
   JSON_OBJECT('letter','F','meaning','Feed (collector tank → engine)'),
   JSON_OBJECT('letter','L','meaning','Levels (wing tanks — never below 400 lb each)'),
   JSON_OBJECT('letter','A','meaning','AC pump (electric boost pump for crossfeed)'),
   JSON_OBJECT('letter','P','meaning','Pressure (FUEL PRESS LO threshold)'),
   JSON_OBJECT('letter','S','meaning','Selector (crossfeed valve position)')
 ),
 'FLAPS walks the fuel system from tank to engine — same direction the fuel actually flows, so the order is intuitive.',
 'After a single engine flame-out: F — collector tank still feeding the live engine; L — confirm imbalance; A — boost pump on for the dead-side tank; P — pressure recovering; S — open crossfeed.',
 30, 1),

(@s_powerplant, 'FOUR',
 JSON_ARRAY(
   JSON_OBJECT('letter','F','meaning','Fuel flow (within limits for power setting)'),
   JSON_OBJECT('letter','O','meaning','Oil (pressure & temperature green)'),
   JSON_OBJECT('letter','U','meaning','Unfeather solenoid armed (NTS check)'),
   JSON_OBJECT('letter','R','meaning','RPM (NP / NH within band)')
 ),
 'Cross-cockpit flow check after engine start. The four engine indications a captain expects to see green before commit-to-takeoff.',
 'After taxi-out, before takeoff brief: scan F-O-U-R left engine, then F-O-U-R right engine. If any is amber, halt and assess.',
 40, 1),

(@s_propeller, 'TIPS',
 JSON_ARRAY(
   JSON_OBJECT('letter','T','meaning','Torque (within limit for OAT/altitude)'),
   JSON_OBJECT('letter','I','meaning','Ice (auto-feather + de-ice armed)'),
   JSON_OBJECT('letter','P','meaning','Pitch (governor steady, no hunting)'),
   JSON_OBJECT('letter','S','meaning','Sync (props synchronised in cruise)')
 ),
 'Mnemonic for what to look at on the propeller page during cruise scan; covers the four common Q400 prop write-ups in one pass.',
 'Cruise scan over the FIR: T green both sides, I de-ice on for cloud entry, P steady at 850 RPM, S in sync. Move on.',
 50, 1),

(@s_flightcontrols, 'HOT',
 JSON_ARRAY(
   JSON_OBJECT('letter','H','meaning','Hydraulics (1, 2 — both required for spoilers)'),
   JSON_OBJECT('letter','O','meaning','Operability (verify all controls free + correct)'),
   JSON_OBJECT('letter','T','meaning','Trim (pitch, roll, yaw — set for takeoff)')
 ),
 'Pre-takeoff control check: hydraulics powered, controls move correctly, trim set. Reorders the busy F/CTL section into one short trigger.',
 'After taxi: H — both HYD pumps green; O — full and free yoke + rudder check; T — pitch trim within takeoff band. Cleared for line-up.',
 60, 1),

(@s_landinggear, 'SAFE',
 JSON_ARRAY(
   JSON_OBJECT('letter','S','meaning','Speed (V_LO — below 200 KIAS for op)'),
   JSON_OBJECT('letter','A','meaning','Alternate extension (CB pulled if needed)'),
   JSON_OBJECT('letter','F','meaning','Flaps (extension order — gear before flap 35°)'),
   JSON_OBJECT('letter','E','meaning','Emergency horn / 3 greens')
 ),
 'A 4-letter trigger that captures every gear-related limit and procedure on one card.',
 'On approach with abnormal landing gear: S — slow to V_LO; A — alternate extension procedure if no greens; F — sequence flap deployment; E — confirm 3 greens before MDA.',
 70, 1),

(@s_fireprot, 'FIRE',
 JSON_ARRAY(
   JSON_OBJECT('letter','F','meaning','Fuel (cut off the affected engine)'),
   JSON_OBJECT('letter','I','meaning','Isolate (close bleed + fuel valves)'),
   JSON_OBJECT('letter','R','meaning','Restart inhibit (do not restart in flight)'),
   JSON_OBJECT('letter','E','meaning','Extinguish (discharge bottles per QRH)')
 ),
 'Engine fire memory item ordering — matches the QRH sequence so muscle memory and book agree.',
 'Engine 1 fire warning: F — cut fuel; I — close bleed and fuel SOV; R — do not restart; E — discharge bottle 1, monitor; if persists, bottle 2.',
 80, 1),

(@s_ice, 'ICE',
 JSON_ARRAY(
   JSON_OBJECT('letter','I','meaning','Indications (ICE DET light, OAT < +5 in vis. moisture)'),
   JSON_OBJECT('letter','C','meaning','Configuration (boots auto, prop heat, windshield, pitot)'),
   JSON_OBJECT('letter','E','meaning','Egress (climb out of icing as priority)')
 ),
 'Ice protection trigger that emphasises detection then configuration then exit.',
 'Climb through icing layer: I — ICE DET illuminated, OAT 0°C, vis. moisture; C — boots cycle, prop and windshield heat on; E — request higher to FL250 once on top.',
 90, 1);
