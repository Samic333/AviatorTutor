-- =============================================================================
-- AviatorTutor — Phase 5: ATA 24 Electrical Power — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'electrical' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'electrical-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'The System That Keeps Everything Else Awake',
 'Every other system on the Q400 — flight controls, hydraulics, pressurisation, comms, lighting, anti-ice — relies on electrical power being there. Lose it and the aeroplane is dark and dumb. The EPGDS is engineered to stay alive through extraordinary failures: lose both engines and you still have batteries; lose all generators and the TRUs keep DC alive while the AC gens are turning. This lesson is about understanding the redundancy, recognising the cautions in the right order, and knowing what the EPCU is doing for you behind the scenes.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'EPGDS architecture overview',
 'EPGDS-DC-AC. Four DC sources in flight. EPCU re-configures.',
 'A hand-flown sector with a single bus fault is manageable. A check ride sets you up for the four-second window between the DC BUS caution and the generator trip — read the cautions in order.',
 NULL),

(@lesson_id, 20, 'concept',
 'EPGDS — Two Halves, One Brain',
 'The Electrical Power Generation and Distribution System (EPGDS) has two halves. The AC generation system uses two engine-driven AC generators producing 115 VAC variable frequency. The DC generation system uses two engine-driven starter/generators (28 VDC), two Transformer Rectifier Units (TRUs that convert AC to 28 VDC), three NiCad batteries (two 40 Ahr main + aux, one 17 Ahr standby), and an optional APU starter/generator. The brain is the Electrical Power Control Unit (EPCU) — it monitors every source and bus, opens and closes bus tie contactors, and re-configures power flow automatically.',
 'diagram', '/assets/aircraft/q400/electrical-flow.svg',
 'EPGDS block diagram with EPCU centre',
 'EPGDS = AC + DC. EPCU is the bus-tie automation brain.',
 'When you read the MFD electrical page on every cruise scan, you are reading the EPCU''s output — it tells you the current configuration.',
 NULL),

(@lesson_id, 30, 'concept',
 'Four DC Sources In Flight — How the Redundancy Works',
 'In normal flight there are FOUR independent sources of DC power. Two come directly from the engine-driven starter/generators (DC GEN 1 and DC GEN 2). Two come from the TRUs that convert AC to DC (TRU 1 fed by AC GEN 1; TRU 2 fed by AC GEN 2). Each source normally powers its own dedicated bus. The EPCU watches all four; if one fails, the bus tie contactors close to feed the orphaned bus from a healthy source. You can lose a generator without losing power to its bus — the redundancy is automatic and silent.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'Four DC sources + bus tie reconfiguration diagram',
 '2-GEN + 2-TRU = 4-DC-SOURCES. Auto-reconfig through bus ties.',
 'On a single-engine ride the bus you lost from the dead engine reconfigures within milliseconds. The cabin notices nothing.',
 JSON_OBJECT(
   'prompt', 'How many independent sources of DC power does the Q400 have in flight?',
   'options', JSON_ARRAY(
     'Two — the two engine-driven generators only',
     'Three — two generators plus one APU',
     'Four — two engine-driven generators plus two TRUs',
     'Five — two generators, two TRUs, and the standby battery'
   ),
   'correct_index', 2,
   'explanation', 'Four DC sources in flight: 2 engine-driven starter/generators + 2 TRUs. Each normally powers its own dedicated bus. EPCU re-configures via bus ties on any failure.'
 )),

(@lesson_id, 40, 'system',
 'AC Generators — 115 VAC Variable Frequency',
 'Two engine-driven AC generators supply 115 VAC at variable frequency (the frequency varies with engine speed; this drives some heater loads directly without conversion). Each AC generator powers its own variable-frequency bus. The variable-frequency buses also feed the two TRUs that produce DC. The MFD AC GENERATOR LOAD display shows live load: 1.00 means 100% of nominal load. ".60" is 60% loaded. A "+" prefix means OVERLOAD — "+1.30" indicates 130% of rated capacity. The leading zero is suppressed; nothing displayed at all means load is in the expected range.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'AC generator MFD load display examples',
 '115-VAC variable freq · "+" prefix = overload · 1.00 = 100%.',
 'Watch the AC GEN load on every cruise sweep. A trend toward 1.0 means a load somewhere is rising — investigate before it overloads.',
 NULL),

(@lesson_id, 50, 'system',
 'TRUs — Where DC Comes From When the Engines Are Running',
 'Two Transformer Rectifier Units (TRUs) take 115 VAC from the variable-frequency buses and convert it to 28 VDC. TRU 1 is fed by AC GEN 1; TRU 2 by AC GEN 2. In flight the TRUs are usually the largest contributors to DC power — the engine-driven DC starter/generators are sized primarily for engine starting and the TRUs handle most of the running DC load. Lose one TRU and the bus tie contactor closes to feed the orphaned bus from the surviving TRU or DC GEN.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'TRU block diagram with input/output',
 'TRU = AC → 28 VDC. TRUs do most of the in-flight DC work.',
 'A single TRU fail shows up on the MFD electrical page first (bus voltage fluctuation) — catch it on the scan.',
 NULL),

(@lesson_id, 60, 'system',
 'Three NiCad Batteries — Where They Live and Why',
 'Three NiCad batteries on the Q400. Two 40 Amp-hour batteries (one MAIN, one AUXILIARY) located together. One 17 Amp-hour STANDBY battery in the forward fuselage adjacent to the two 40 Ahr units. The 40 Ahr batteries have a nominal voltage of 24 VDC (20 cells × 1.2 VDC per cell). No-load voltage approaches 28 VDC. To recharge to full, the charging voltage must be 28–32 VDC (1.4–1.6 VDC/cell). This is why a low-voltage DC GPU below 28 VDC will discharge the batteries — they are paralleled with the cart and supplying current to aircraft loads, NOT being charged.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'Battery installation: 40+40 main/aux + 17 standby in forward fuselage',
 '40-40-17. Three NiCads. Charge needs 28–32 VDC.',
 'Pre-flight always check that the GPU output voltage is above 28 VDC. Otherwise you are flat on departure.',
 JSON_OBJECT(
   'prompt', 'How many NiCad batteries are on the Q400 and what are their capacities?',
   'options', JSON_ARRAY(
     'Two batteries: one 40 Ahr and one 17 Ahr',
     'Three batteries: two 40 Ahr (main + aux) and one 17 Ahr standby',
     'Three batteries: all 40 Ahr',
     'Four batteries: 28 VDC each'
   ),
   'correct_index', 1,
   'explanation', 'Three NiCad batteries. Two 40 Ahr (main + aux) and one 17 Ahr standby in the forward fuselage. Mnemonic: 40-40-17.'
 )),

(@lesson_id, 70, 'system',
 'APU Starter/Generator — The Ground Workhorse',
 'The APU starter/generator lives in the tail cone section of the aeroplane. On the ground it supplies 28 VDC to the essential main and secondary DC buses. A contactor connects the APU starter/generator to the right main feeder bus — this path serves both APU starting (battery powers the starter) and powering the aircraft DC buses (APU starter/generator drives power into the bus once the APU is running). Once the APU is running, the starter/generator can supply power IN PARALLEL with the batteries to start the aircraft engines.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'APU starter/generator electrical path diagram',
 'APU 28 VDC → right main feeder bus. Parallel start with batteries.',
 'In a cold-start scenario with weak batteries, run the APU first. Once the APU starter/gen is online, engine start has more current available.',
 NULL),

(@lesson_id, 80, 'system',
 'External Power — DC Left, AC Right',
 'Ground Power Units (GPUs) connect at two distinct receptacles. The DC GPU receptacle is on the LEFT side of the forward fuselage. The AC GPU receptacle is on the RIGHT side of the forward fuselage near the nose cone. AC external power goes through the External Power Protection Unit (AC PPU) which monitors voltage / frequency / phase quality before letting the power onto the variable-frequency buses. While DC external power is connected, generator connections to the main buses are INHIBITED by the EPCU — the cart powers the bus, the generators wait. Main and auxiliary batteries stay connected; standby battery is diode-isolated from the left main during engine start.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'External power receptacle locations',
 'DC-LEFT-AC-RIGHT. AC PPU monitors quality before connection.',
 'Walking around on a hot apron at 0500, the location of these receptacles matters. Brief the ground crew explicitly which one to plug in.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Cruise Scan — What You Watch on the MFD Electrical Page',
 'In cruise the electrical scan is brief but disciplined. Open the MFD electrical page and check: (1) AC GEN 1 and AC GEN 2 loads — both within expected band, no "+" prefix. (2) DC GEN 1 and DC GEN 2 loads. (3) TRU 1 and TRU 2 voltages — both stable around 28 VDC. (4) Battery indications — main and aux at expected charge. (5) Bus tie status — no anomalous closures. (6) No DC BUS caution illuminated. Run this every 10 minutes minimum and any time something visual or auditory changes. The scan catches a developing source failure before the warning fires.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'MFD electrical page scan checklist',
 '6-item electrical scan: AC loads · DC loads · TRU volts · batt · bus ties · DC BUS caution.',
 'On a long sector the disciplined scan is the difference between catching a slow leak and reacting to a hard fault. Brief the FO to call any anomaly.',
 NULL),

(@lesson_id, 100, 'abnormal',
 'Main Bus Fault — The Five-Second Window',
 'A main bus fault has a deliberate timing logic. (1) Fault detected → EPCU prevents the upper horizontal and two vertical bus ties from closing → the affected bus is isolated. The DC BUS caution light comes on warning of the fault impending condition. (2) If the fault PERSISTS for approximately 5 seconds → EPCU sends a TRIP signal to the GCU → affected generator is isolated. The EPCU also opens and locks-out the contactors connecting the batteries to the affected main bus. The MAIN BATTERY or AUX and STBY BATTERY caution light(s) and the related DC GEN caution light come on. (3) All main DC services on the faulted bus side will not function. Crew action is per QRH non-normal.',
 'video', '/assets/aircraft/q400/electrical-flow.svg',
 'Main bus fault timing diagram',
 '5-SEC-TRIP. DC BUS caution first; 5 sec of fault persistence triggers gen trip and battery lockout.',
 'When you see DC BUS caution, START the timing. If it goes away, the EPCU cleared a transient. If it persists, the next 5 seconds will bring a cascade of cautions.',
 JSON_OBJECT(
   'prompt', 'A DC BUS caution light illuminates in flight. After approximately how many seconds will the EPCU trip the affected generator and lock-out the battery contactors?',
   'options', JSON_ARRAY(
     '1 second',
     '5 seconds',
     '15 seconds',
     '30 seconds'
   ),
   'correct_index', 1,
   'explanation', '5 seconds of fault persistence. Up to 5 seconds the bus is isolated; after 5 seconds the EPCU sends a TRIP signal to the GCU, isolates the generator, and opens battery contactors. Mnemonic: 5-SEC-TRIP.'
 )),

(@lesson_id, 110, 'abnormal',
 'Generator Failures — Single, Dual, and Battery-Only',
 'Single generator failure: the EPCU re-configures bus ties; the surviving generator + both TRUs feed all four buses. Cabin notices nothing. Dual generator failure with both TRUs operating: 28 VDC continues from the TRUs (still fed by 115 VAC from a windmilling AC gen if available); essential buses remain alive; non-essential loads automatically shed per EPCU logic. All four DC sources lost (extreme): batteries are the only source. The 40 Ahr main and auxiliary keep essential buses for a finite time; the 17 Ahr standby keeps the standby bus (essential flight instruments + key avionics) alive longer because of its lower load. Land at the nearest suitable airport — battery-only flight is time-critical.',
 'video', '/assets/aircraft/q400/electrical-flow.svg',
 'Failure cascade matrix: single gen → dual gen → all sources → battery only',
 'Single gen fail = silent. Dual = TRUs continue. All sources = battery time, divert.',
 'A check captain''s favourite ride is "all four DC sources lost." Memorise the load-shed sequence and the standby-bus content.',
 NULL),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Bus Faults and Battery Operation',
 'Electrical QRH non-normals cluster into four groups. (1) DC BUS caution → if it persists, expect generator trip and battery contactor lockout. Run the QRH; isolate per indication. (2) DC GEN 1 or DC GEN 2 inop → confirm bus tie reconfigured; brief approach for reduced electrical capacity. (3) MAIN BATTERY / AUX BATTERY / STBY BATTERY caution → discrete fault on a battery; check charge state and follow QRH. (4) Battery-only operation (extreme) → load-shed per QRH; standby battery powers ESS standby bus; nearest suitable airport is the only acceptable destination.',
 'image', '/assets/aircraft/q400/electrical-flow.svg',
 'QRH electrical cluster diagram',
 'DC BUS · DC GEN INOP · BATTERY caution · battery-only.',
 'Practice these flows in the sim. Battery-only is one of the few abnormals where you must brief crew rotation on the way to a divert.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: DC BUS Caution at FL230 in Cruise',
 'Setup: cruising FL230, two hours into a four-hour sector, FO calls "DC BUS caution." You start counting. At ~5 seconds the caution is still illuminated; the MFD electrical page shows DC BUS 1 voltage dropping. Almost immediately a DC GEN 1 caution joins it; then MAIN BATTERY caution illuminates as the EPCU locks out the battery contactor.\n\nQuestion: continue, descend, divert? Decision: descend NOW to a more comfortable altitude (FL200 or below) for cabin safety in case of a follow-on smoke event; advise ATC; brief the FO on remaining electrical capacity (DC GEN 2 + both TRUs still feeding the surviving buses); plan a divert to the nearest suitable airport with maintenance capability. Even though essential systems remain alive, the trend on a hard bus fault is unfavourable — get on the ground while you have the option.',
 'animation', '/assets/aircraft/q400/electrical-flow.svg',
 'DC BUS fault cascade scenario',
 'Hard bus fault = descend, advise, divert. Don''t carry it to destination.',
 'Carrying an electrical abnormal across a half-sector is a captain decision. The right call is usually divert.',
 JSON_OBJECT(
   'prompt', 'A DC BUS caution persists past 5 seconds and is followed by DC GEN 1 and MAIN BATTERY cautions in cruise FL230. Best decision?',
   'options', JSON_ARRAY(
     'Continue to destination — surviving sources are sufficient',
     'Descend, advise ATC, divert to nearest suitable airport with maintenance',
     'Cycle the affected generator switch immediately',
     'Reset the EPCU breaker'
   ),
   'correct_index', 1,
   'explanation', 'A confirmed bus-fault cascade is a divert event. Descend for cabin safety, advise ATC, divert to nearest suitable airport with maintenance capability. Continuing to destination puts you outside the safety envelope.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Electrical Power in 60 Seconds',
 'Recap:\n  • EPGDS = AC + DC. Brain: EPCU; per-generator brain: GCU.\n  • In flight: 4 independent DC sources (2 engine starter/gens + 2 TRUs).\n  • AC: two 115 VAC variable-frequency generators. Feed TRUs.\n  • DC: 28 VDC from gens / TRUs / batteries.\n  • Batteries: 3 NiCads — two 40 Ahr (main + aux) + one 17 Ahr standby in forward fuselage.\n  • 40 Ahr nominal 24 VDC (20 cells × 1.2 VDC). No-load ~28 VDC. Charge needs 28–32 VDC.\n  • APU starter/gen in tail cone supplies 28 VDC to ESS main/secondary DC buses on ground.\n  • External: DC GPU LEFT forward fuselage; AC GPU RIGHT forward near nose cone.\n  • Bus fault timing: DC BUS caution first; 5 sec persistence → EPCU trips generator and locks-out battery contactors.\n  • MFD AC GEN load: 1.00 = 100%. "+" prefix = overload.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'EPGDS-DC-AC · 4-DC-SOURCES · 40-40-17 · 28-VDC-CHARGE · DC-LEFT-AC-RIGHT · 5-SEC-TRIP',
 'Six mnemonics carry every electrical question on a recurrent. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
