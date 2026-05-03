-- =============================================================================
-- AviatorTutor — Phase 5 (ATA 24 Electrical Power) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'electrical' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'electrical-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Electrical Power — EPGDS, Buses, Batteries, and the EPCU',
     'electrical-overview',
     'overview',
     'Two engine-driven starter/generators, two AC generators, two TRUs, three NiCad batteries (two 40 Ahr + one 17 Ahr standby), an optional APU starter/generator, and the EPCU brain that re-configures the bus topology automatically on any failure. 115 VAC from the AC gens; 28 VDC everywhere else. A bus fault is held for 5 seconds before the EPCU trips the generator and locks out the bus.',
     '<p>The Electrical Power Generation and Distribution System (EPGDS) is the most fault-tolerant system on the Q400. Four independent DC sources in flight, four secondary buses, automatic re-configuration through the Electrical Power Control Unit (EPCU), and a battery system that keeps the essential buses alive even with all generators failed. The challenge is reading the indications correctly when something does fail — the cautions for a main bus fault arrive in a specific order with a specific timing, and the crew action depends on what fired first.</p>',
     JSON_ARRAY(
       'EPGDS = Electrical Power Generation and Distribution System. DC + AC subsystems',
       'DC sources in flight: 2 engine-driven starter/generators + 2 TRUs (powered by AC) = 4 independent sources',
       '3 NiCad batteries: two 40 Ahr (main + aux) in one location + one 17 Ahr standby in the forward fuselage',
       '40 Ahr battery nominal voltage: 24 VDC (20 cells × 1.2 VDC/cell). No-load voltage approaches 28 VDC',
       'Charging voltage: 28–32 VDC (1.4–1.6 VDC/cell). DC GPU below 28 VDC → batteries discharge INTO the aircraft loads',
       'AC generators supply 115 VAC variable frequency. TRUs convert AC to 28 VDC for the DC system',
       'APU starter/generator (in the tail cone) supplies 28 VDC to essential main and secondary DC buses on the GROUND',
       'EPCU = Electrical Power Control Unit (the brain). GCU = Generator Control Unit (per generator)',
       'Bus fault protection: 5 seconds of fault tolerance before EPCU trips the generator, locks out the bus, opens battery contactors',
       'DC external GPU receptacle: LEFT forward fuselage. AC external GPU receptacle: RIGHT forward fuselage near the nose cone',
       'MFD AC GEN LOAD display: 1.00 = 100% loaded; "+" prefix means overload (e.g., +1.30 = 130%)'
     ),
     JSON_ARRAY(
       'Each DC source normally powers its own dedicated bus. The EPCU re-configures bus tie contactors to maintain power on bus failures.',
       'On a main bus fault, the DC BUS caution light comes on FIRST. If the fault persists 5 seconds, EPCU trips the generator and the MAIN BATTERY / AUX-STBY BATTERY caution lights also illuminate.',
       'While DC external power is connected, generator connections to the main buses are INHIBITED by the EPCU. Main and auxiliary batteries remain connected.',
       'Standby battery is diode-isolated from the left main during engine start to keep ESS bus voltage acceptable.',
       'The MFD electrical page is the single source of truth for live load, voltage, and bus connectivity. Scan it on every cruise sweep.'
     ),
     JSON_ARRAY(
       'Battery counts: 3 NiCad batteries — students often quote 2 (the 17 Ahr standby is easily forgotten).',
       'Battery voltages: 40 Ahr is 24 VDC nominal (NOT 28 VDC — that is no-load).',
       'External power locations: DC LEFT, AC RIGHT — easy to swap.',
       'Bus fault timing: 5 SECONDS before generator trip — not immediate.',
       'AC GEN load overload indicator: "+" prefix on the digital display means OVERLOAD, not simply "positive value".'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'electrical-overview';
