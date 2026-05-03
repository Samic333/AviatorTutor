-- =============================================================================
-- AviatorTutor — Phase 6 (ATA 26 Fire Protection) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fire-protection' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'fire-protection-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Fire Protection — Detection, Indication, and Extinguishing',
     'fire-protection-overview',
     'overview',
     'Six pneumatic APDs in the engine nacelles + one in the APU detect overheat and fire. Four smoke detectors guard the baggage compartments and lavatory. Two dual-port FIRE bottles in the left wing root deliver two shots into either engine. Two HRD bottles + one shared LRD bottle protect the forward and aft baggage compartments with a 7-minute LRD delay. The lavatory waste bin gets a thermally-activated Potty Bottle with no electrical interface. Four Halon 1211 portables live in the cabin and cockpit. Lavatory smoke is cabin-only — NOT indicated on the flight deck.',
     '<p>The Q400 fire protection system is a tightly integrated detection-and-suppression network covering every fire-credible volume on the aeroplane: engine nacelles, APU compartment, both baggage holds, and the lavatory. Detection is mostly pneumatic — Advanced Pneumatic Detectors (APDs) use a helium-filled sensor element whose pressure rises with temperature; an alarm switch closes when fire pressure is reached, an integrity switch opens when the loop ruptures. Extinguishing is electric for engines and baggage (squib-fired bottles via T-handles and EXTG switches), thermal-fuse only for the lavatory waste bin (no wiring). The crew interface is the Fire Protection Panel on the centre overhead — and the captain who has memorised the panel is the captain who runs an engine fire correctly when it matters.</p>',
     JSON_ARRAY(
       'APDs (Advanced Pneumatic Detectors): 6 in the engine nacelles (3 per engine — Primary Engine Zone PEZ, Leading Edge Zone LEZ, Main Wheel Well MWW) + 1 in the APU = 7 total',
       'Smoke detectors: 2 in aft baggage compartment + 1 in forward baggage + 1 in lavatory = 4 total',
       'Two dual-port FIRE bottles installed FWD and AFT in the LEFT wing root for engine extinguishing — each bottle gives one shot, two shots per engine',
       'Baggage compartment fire: 2 High-Rate Discharge (HRD) bottles — one each for fwd and aft baggage — PLUS 1 shared Low-Rate Discharge (LRD) bottle in the AFT equipment bay',
       'LRD bottle 7-minute delay: After AFT baggage HRD discharge, the LRD bottle automatically discharges 7 minutes later; FWD baggage discharges HRD and LRD simultaneously',
       'Lavatory protection: thermally-activated Potty Bottle with NO electrical interface — fusible end-cap seals melt and release extinguishant into the waste bin',
       'Lavatory smoke: indicated in CABIN only — NO indication in the flight deck. Audible chime through P/A; cabin repeater lights illuminate; smoke detector LED on',
       'PULL FUEL/HYD OFF T-handle: closes fuel and hydraulic shut-off valves, ARMS the extinguisher squibs, illuminates yellow bottle ARM lights — EXTG switch then discharges chosen bottle',
       'BTL LOW (amber) advisory light: bottle empty or pressure low. Control Amplifier monitors bottle pressure constantly',
       'FAULT A / FAULT B (amber) lights: malfunction in loop detector circuit (integrity switch); APD sensor element rupture',
       'Four Halon 1211 portable extinguishers: 1 in flight compartment + 3 in passenger compartment. Effective on electrical / oil / fuel fires; non-corrosive, non-toxic, will not freeze',
       'Portable extinguisher gauge: GREEN serviceable, YELLOW overcharge, RED recharge',
       'Cockpit extinguisher use REQUIRES all crew on oxygen masks with EMERGENCY position selected (100% O2 positive pressure)'
     ),
     JSON_ARRAY(
       'Engine fire procedure sequence: ENGINE FIRE light flashes → PULL the affected T-handle → arms the bottles and closes fuel/hyd valves → select EXTG switch FWD or AFT → wait 30 sec → if fire persists select the OTHER bottle. Two shots per engine.',
       'Aft baggage smoke: HRD into the aft baggage immediately on switchlight push. After 7 minutes the shared LRD bottle automatically dumps. INLET and OTLT vent valves close (relay drops power) to starve the fire.',
       'Forward baggage smoke: HRD AND LRD discharge SIMULTANEOUSLY on switchlight push (no 7-minute delay).',
       'Lavatory smoke produces NO flight-deck indication. The cabin crew is your sensor here — brief them. Audible chime + cabin repeater lights + LED on the detector.',
       'BTL LOW shared between fwd and aft baggage: the LRD low-pressure light illuminates whichever side is active. Read the panel logic carefully.',
       'During cockpit portable extinguisher use the crew MUST select EMERGENCY on the oxygen mask. Halon 1211 can displace cockpit oxygen — protect breathing first.'
     ),
     JSON_ARRAY(
       'APD count: SEVEN total (6 engine + 1 APU). Easy trap: students quote 6, forgetting the APU.',
       'Smoke detector count: FOUR total (2 aft baggage, 1 fwd, 1 lavatory). Easy trap: quote 3, forgetting the second aft detector.',
       'LRD timing: aft baggage gets a 7-minute delay; FWD baggage discharges HRD and LRD simultaneously. Easy trap: assume both compartments behave the same.',
       'Lavatory smoke: NOT shown in flight deck. Trap question — students assume it appears like baggage smoke.',
       'Lavatory bottle: thermally-activated, NO electrical interface. Cannot be discharged from the cockpit. Different from every other bottle on the aeroplane.',
       'Fire bottles location: LEFT wing root, FWD and AFT positions — NOT one per engine. Two bottles serve both engines via cross-feed plumbing.',
       'Halon 1211 gauge colour: GREEN serviceable, YELLOW overcharge, RED recharge — NOT a normal-amber-red logic.',
       'PULL T-handle action: closes BOTH fuel AND hydraulic shut-off valves on that engine. Easy trap to think it is fuel-only.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'fire-protection-overview';
