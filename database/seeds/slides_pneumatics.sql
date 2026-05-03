-- =============================================================================
-- AviatorTutor — Phase 15: ATA 36 Pneumatics — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'pneumatics' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'pneumatics-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'APU + Bleed — A Ground-Only System',
 'Q400 pneumatics is essentially the APU and the bleed-air distribution it supports. The APU is a tailcone-mounted gas turbine driving a 28 VDC starter-generator — a tightly-integrated package that replaces the standard composite tailcone with a titanium tailcone and firewall. The defining limitation: the APU CANNOT BE OPERATED IN FLIGHT. The shutoff valve closes automatically as soon as the aircraft is airborne. So the APU is your ground asset for electrical and bleed support — engine starts, ECS pre-conditioning, ground power. In flight, you''re on engine bleed and engine generators. Memorise the 7-second auto-extg, the half-speed starter cutoff, and the four conditions that close the APU fuel valve.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'Q400 APU + bleed overview',
 'APU-GROUND-ONLY · titanium tail · 7-SEC-AUTO-EXTG · 4-COND-FUEL-CLOSE.',
 'On a real ground emergency the APU is your battery saver. Knowing the start-arming preconditions saves time.',
 NULL),

(@lesson_id, 20, 'concept',
 'APU Architecture — Tailcone, Inlet, Exhaust',
 'The APU lives in a titanium tailcone with a firewall — replacing the standard composite tailcone for fire-rating reasons. Access is via two clamshell doors on the BOTTOM of the tailcone — designed for ground servicing without ladders. The intake air is drawn through a screened inlet duct on the RIGHT REAR of the fuselage. Exhaust gases flow through an ejector and discharge through an upwards-pointing outlet at the AFT END of the titanium tailcone — directs hot exhaust away from ground crew. An optional louvered cover for the air inlet protects against snow/sleet ingress during long turnarounds or overnight stops.',
 'diagram', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU architecture: titanium tail, clamshell doors, inlet, exhaust',
 'TITANIUM-TAIL-2-CLAMSHELL · RIGHT-REAR-INLET · upward exhaust.',
 'Walking the airframe: the louvered inlet on the right rear is the APU entry. Train your eye to find it on every walk-around.',
 NULL),

(@lesson_id, 30, 'concept',
 'CANNOT Operate in Flight',
 'The single most important APU limitation: the APU CANNOT be operated in flight. The APU shutoff valve closes automatically when the aircraft becomes airborne (weight-on-wheels signal removed). So the APU is purely a ground asset. This means: you cannot use the APU for in-flight backup electrical or bleed in an emergency. You cannot start the APU at altitude after a dual-generator failure. The Q400 is designed for the in-flight scenario to be handled entirely by the engine generators, TRUs, and batteries (per Phase 5 Electrical). The APU buys you ground time — for engine start, ECS pre-cooling, electrical support during turnaround.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'No-flight-operation rule — auto shutoff airborne',
 'APU-GROUND-ONLY. Shutoff valve auto-closes airborne. No in-flight backup.',
 'A common cockpit instinct under stress is "start the APU." On the Q400, no — engine generators + TRUs + batteries are the in-flight redundancy.',
 JSON_OBJECT(
   'prompt', 'Can the Q400 APU be operated in flight as a backup electrical or bleed source?',
   'options', JSON_ARRAY(
     'Yes, with PWR switchlight pushed',
     'NO — APU cannot be operated in flight. Shutoff valve closes automatically when airborne',
     'Only above FL250',
     'Only if external power was applied before takeoff'
   ),
   'correct_index', 1,
   'explanation', 'APU cannot operate in flight. Shutoff valve auto-closes when aircraft becomes airborne. Ground asset only. Mnemonic: APU-GROUND-ONLY.'
 )),

(@lesson_id, 40, 'system',
 'PWR Switchlight — Three Arming Conditions',
 'The APU PWR switchlight (alternate action) only arms if THREE conditions are all true: <strong>(1) aircraft is ON GROUND</strong>, <strong>(2) NO FIRE detected</strong>, <strong>(3) EXTG switch NOT selected</strong>. If any condition fails, PWR cannot be pushed to arm. When pushed: APU start circuits arm + APU fuel shutoff valve opens (FUEL VALVE OPEN green on FPP). RUN segment goes green at operating speed (after START is pushed and sequence completes). Push again to shut down: closes fuel valve, FUEL VALVE CLOSED white, APU stops. FAIL segment (amber) on detection of a fault — APU auto-shuts down; PWR must be reselected after auto shutdown.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'PWR switchlight 3 arming conditions + RUN / FAIL behaviour',
 '3-COND-PWR-ARM. Ground · no fire · EXTG not selected.',
 'On a check question "why won''t my APU arm?" check: ground, no fire, EXTG not pushed. One of those three.',
 NULL),

(@lesson_id, 50, 'system',
 'Start Sequence — Half-Speed Starter Cutoff',
 'After PWR arms and fuel valve is open, push START switchlight. STARTER amber illuminates while the FADEC sequences the start. The starter stays engaged until the APU reaches HALF its operating speed — then the starter disengages automatically. The FADEC continues monitoring fuel scheduling, EGT, oil pressure, and other parameters through to operating speed. At operating speed: PWR shows RUN green, STARTER segment goes out, GEN can be selected. <strong>Battery start specifics:</strong> with NO external DC, the 2×40 Ahr NiCad batteries supply the starter. With batteries at 100% charge, bus voltage drops to about <strong>20 VDC</strong> during the start. With batteries at 50% charge, bus voltage drops to about <strong>18 VDC</strong> — risk of brown-out and start failure. Pre-flight: check battery charge before APU start.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'Start sequence + battery voltage drop',
 'HALF-SPEED-STARTER · 100-20-50-18.',
 'Don''t try to start the APU on weak batteries — system damage risk and start fail. Apply external DC instead.',
 JSON_OBJECT(
   'prompt', 'When does the APU starter disengage during a normal start?',
   'options', JSON_ARRAY(
     'Immediately after light-off',
     'When the APU reaches HALF its operating speed',
     'At full operating speed',
     'When the GEN switchlight is pushed'
   ),
   'correct_index', 1,
   'explanation', 'Starter engaged until APU reaches HALF operating speed; then disengages automatically. Mnemonic: HALF-SPEED-STARTER.'
 )),

(@lesson_id, 60, 'system',
 'Bleed Air — Auto De-Energization with Engine Bleed',
 'When the APU is operating, push BL AIR switchlight to open the APU bleed valve. OPEN green illuminates. APU bleed supplies the ECS AND holds the CPCS aft safety valve open. <strong>Critical auto-logic:</strong> if either main engine BLEED toggle switch is set to 1 or 2, the APU BL AIR switchlight is automatically de-energized — the system prevents simultaneous APU + engine bleed supply. So pre-flight: APU bleed for ECS during boarding works fine; once engines are started and engine BLEED is selected, APU BL AIR de-energizes on its own. <strong>Also:</strong> if APU EGT reaches an established temperature limit, the bleed air supply is reduced — APU GEN load gets priority over bleed air. Don''t fight the system.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU bleed auto-de-energization with engine BLEED',
 'BL-AIR-AUTO-OFF-WITH-ENG · EGT-LIMIT-BLEED-DOWN.',
 'During engine start, watch the BL AIR switchlight de-energize automatically as soon as engine BLEED is selected — that''s the system handing off, not a fault.',
 NULL),

(@lesson_id, 70, 'system',
 'APU Fuel Valve — 4 Close Conditions',
 'The APU fuel shutoff valve at the left wing collector bay is the gatekeeper for APU operation. Open: APU fuel flows. Closed: APU fuel stops. The valve closes if any of FOUR conditions occur: <strong>(1) PWR switchlight pushed off</strong> (normal shutdown), <strong>(2) FIRE detected in tailcone</strong> (auto-protect), <strong>(3) EXTG switchlight pushed</strong> (fire-extinguishing protocol), <strong>(4) AIRCRAFT in flight</strong> (no in-flight ops). Position is shown on the APU FPP: FUEL VALVE OPEN (green) when fuel flowing; FUEL VALVE CLOSED (white) when shut. The valve is mechanically rigid, routed outside the pressurised fuselage for safety.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU fuel shutoff valve — 4 close conditions',
 '4-COND-FUEL-CLOSE · LEFT-COLLECTOR-APU-FUEL.',
 'On taxi-in shutdown: pushing PWR closes the fuel valve and stops the APU. Don''t skip the BL AIR off + GEN off steps in the proper sequence.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight + APU Start Sequence',
 'Pre-flight APU start: (1) Verify battery charge or apply external DC. (2) Confirm 3 PWR arming conditions: aircraft on ground, no fire detected on FPP, EXTG not selected. (3) Push PWR — fuel valve opens, FUEL VALVE OPEN green on FPP. (4) Push START — STARTER amber on switchlight. (5) Monitor start: light-off, EGT rise, half-speed starter cutoff, RUN green at operating speed. (6) If using APU GEN: push GEN switchlight — ON green, APU supplies 28 VDC. Note: external AC/DC inhibits APU GEN output. (7) If using APU bleed for ECS: confirm engine BLEED switches are OFF (otherwise BL AIR auto-de-energizes). Push BL AIR — OPEN green. (8) Monitor APU FIRE panel — no FIRE/BTL ARM/FAULT.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU start pre-flight sequence',
 'BATTERY · ARM 3-COND · PWR · START · HALF SPEED · GEN · BL AIR.',
 'Brief the FO on the start sequence. The BL AIR auto-de-energization is normal, not a fault.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'APU Normal Shutdown',
 'Normal APU shutdown sequence (in this order): <strong>(1) Close BL AIR</strong> — push BL AIR switchlight, OPEN segment goes out. Bleed valve closes; ECS reverts to engine bleed if engine BLEED is on, otherwise loses bleed. <strong>(2) GEN off</strong> — push GEN switchlight, ON segment goes out. APU starter-generator goes off-line; loads transfer to engine GENs (or external power if applied). <strong>(3) Push PWR</strong> — RUN segment goes out, fuel valve closes (FUEL VALVE CLOSED white on FPP), APU stops. The order matters: close BL AIR first to allow EGT to stabilise (no high bleed load when GEN drops), then GEN to manage electrical load transition cleanly, then PWR to actually stop the engine.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU normal shutdown sequence',
 'BL AIR off · GEN off · PWR off. Order matters.',
 'Skipping the order — pushing PWR with bleed and GEN still on — works but is harder on the APU. Use the proper sequence.',
 JSON_OBJECT(
   'prompt', 'What is the correct APU normal shutdown sequence?',
   'options', JSON_ARRAY(
     'Push PWR first; bleed and GEN auto-close',
     'Close BL AIR → GEN off → push PWR',
     'Push START → wait for cooldown → push PWR',
     'Push EXTG to safely stop the APU'
   ),
   'correct_index', 1,
   'explanation', 'Close BL AIR first (let EGT stabilise), then GEN off (load transfer), then PWR (stops APU). Order matters for clean shutdown.'
 )),

(@lesson_id, 100, 'abnormal',
 'APU Fire — 7-Second Auto-Extg',
 'APU FIRE event in tailcone. Detection chain: loop sensor along tailcone above APU senses fire/overheat → control circuit signals FPP. FPP indications: FIRE light (red) illuminates, MASTER WARNING flashes, CHECK FIRE DET flashes, BTL ARM amber illuminates, fuel valve auto-closes (FUEL VALVE CLOSED white, FUEL VALVE OPEN out), EXTG segment (white) illuminates. APU AUTO-SHUTS DOWN. <strong>After 7 SECONDS of FIRE detection, the extinguishing agent is automatically released</strong> — BTL ARM goes out. If auto-extg fails (BTL ARM stays on), the guarded EXTG switchlight can be pushed to manually discharge. <strong>Once the bottle is discharged, APU restart is PREVENTED until the bottle is replaced</strong>. Plan around no APU for the rest of the flight.',
 'video', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU fire 7-second auto-extg sequence',
 '7-SEC-AUTO-EXTG · NO-RESTART-AFTER-DISCHARGE.',
 'APU fires are usually caught early by the loop sensor. The 7-sec auto-release is fast enough that the crew rarely needs to manually fire EXTG.',
 JSON_OBJECT(
   'prompt', 'How long after FIRE detection on the APU does the extinguishing agent automatically release?',
   'options', JSON_ARRAY(
     'Immediate',
     '3 seconds',
     '7 seconds',
     '15 seconds'
   ),
   'correct_index', 2,
   'explanation', '7 seconds of FIRE detection → auto-release. BTL ARM goes out after auto-extg. Mnemonic: 7-SEC-AUTO-EXTG.'
 )),

(@lesson_id, 110, 'abnormal',
 'APU Faults + GEN OHT',
 'The APU FADEC monitors a long list of fault conditions: overspeed, underspeed, start failure, accelerate failure, EGT overtemperature, low oil pressure, high oil temperature, failed sensors, failed valves/relays/circuits, internal failure. On any detected fault, the APU automatically shuts down: FAIL segment of PWR switchlight illuminates amber, FUEL VALVE CLOSED white, FUEL VALVE OPEN out, APU caution illuminates amber. PWR must be reselected after auto shutdown — the FAIL segment doesn''t auto-clear. <strong>GEN OHT</strong> (separate amber advisory): the starter-generator has overheated. APU auto-shuts down. <strong>If external AC or DC power is applied:</strong> APU GEN output is automatically prevented — system protects against parallel feeds. <strong>BOTTLE LOW</strong> (amber) on FPP: fire bottle low/empty. <strong>FAULT</strong> (amber) on FPP: fire system or FPP fault.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU fault list + auto-shutdown indications',
 'FAIL amber · GEN OHT · external power inhibits APU GEN.',
 'A clean APU fault is usually a non-event for the flight — APU is ground-only anyway. Focus on whether you need it for engine start or ECS pre-cool at the next stop.',
 NULL),

(@lesson_id, 120, 'qrh',
 'QRH Connection: APU Non-Normals',
 'Q400 QRH non-normals for the APU cluster into five groups. (1) APU FIRE: auto-shutdown + 7-sec auto-extg. Manual EXTG via guarded switchlight if BTL ARM still on. No restart after discharge. (2) APU PWR FAIL: auto-shutdown on internal fault. Reselect PWR if you want to retry; investigate cause first. (3) APU GEN OHT: auto-shutdown on starter-generator overheat. Cool down period before retry. (4) APU BOTTLE LOW or FAULT: defer per MEL; APU fire protection compromised. (5) APU bleed not available (BL AIR won''t open or auto-de-energizes unexpectedly): check engine BLEED switch position; check APU EGT.',
 'image', '/assets/aircraft/q400/pneumatics-flow.svg',
 'QRH APU cluster',
 'FIRE · PWR FAIL · GEN OHT · BOTTLE LOW/FAULT · BLEED FAIL.',
 'Most APU non-normals are ground-only events. Plan around them; the flight doesn''t depend on APU.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: APU Fire on the Ramp',
 'Setup: ramp at the destination, taxi-in complete, parking brake set, engines still running. APU is operating, supplying GEN and BL AIR for the cabin. The FPP suddenly illuminates FIRE red, MASTER WARNING flashes, CHECK FIRE DET flashes, BTL ARM amber, FUEL VALVE CLOSED white. APU stops automatically.\n\nFirst 10 seconds: PF maintains aircraft control. PNF identifies "APU FIRE on the ramp." Captain calls "evacuate or normal disembarkation?" — depends on whether smoke/flame is visible from cabin or ramp. PNF starts cabin announcement.\n\nNext 7 seconds: extinguishing agent automatically releases. BTL ARM goes out. Confirm via FPP indications.\n\nNext minute: ground crew alerted via interphone. ARFF on field if appropriate. F/A briefed via interphone. Continue evacuation or normal disembarkation per company SOP. Engine GENs taking over electrical load (loads transferred from APU GEN to engine GENs).\n\nLanding side: bottle discharged → no APU restart until bottle replaced. Maintenance write-up. Plan next sector with no APU support — engine cross-bleed start required, ground power for ECS, careful battery management.',
 'animation', '/assets/aircraft/q400/pneumatics-flow.svg',
 'APU fire on ramp scenario',
 'AUTO SHUTDOWN · 7-SEC AUTO EXTG · NO RESTART · plan no-APU sector.',
 'APU fires on the ramp are rare but visible from the cabin. Cabin perception management is part of the captain''s decision tree.',
 JSON_OBJECT(
   'prompt', 'After an APU FIRE event with the bottle automatically discharged, can the APU be restarted for the next sector?',
   'options', JSON_ARRAY(
     'Yes, after a 30-minute cooldown',
     'NO — APU restart is PREVENTED until the bottle is replaced. Plan the next sector with no APU support',
     'Yes, with manual override',
     'Only with external DC power applied'
   ),
   'correct_index', 1,
   'explanation', 'Once the APU fire bottle is discharged, restart is prevented until bottle is replaced. Mnemonic: NO-RESTART-AFTER-DISCHARGE.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Pneumatics in 60 Seconds',
 'Recap:\n  • APU = gas turbine + 28 VDC starter-generator. Titanium tailcone with firewall. 2 clamshell access doors on bottom.\n  • CANNOT operate in flight. Shutoff valve auto-closes airborne.\n  • Intake on right rear of fuselage. Exhaust upwards from aft tailcone.\n  • Louvered cover for inlet protects against snow/sleet.\n  • PWR arms only on 3 conditions: ground + no fire + EXTG not selected.\n  • Starter engaged until HALF operating speed.\n  • Battery start: 100% → ~20 V; 50% → ~18 V.\n  • External AC/DC applied → APU GEN output auto-prevented.\n  • BL AIR opens → bleed to ECS + holds CPCS aft safety valve open.\n  • If engine BLEED selected → APU BL AIR auto-de-energizes.\n  • APU EGT high → bleed reduces (priority to GEN load).\n  • Fuel valve closes on 4 conditions: PWR off / fire / EXTG / aircraft in flight.\n  • Auto fire detection + extinguishing — bottle auto-releases 7 SEC after FIRE.\n  • Once bottle discharged, NO RESTART until replaced.\n  • Normal shutdown: BL AIR off → GEN off → PWR off.\n  • Limits: composite duct removed 30°C/ISA+25; Louvre installed 21°C.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'APU-GROUND-ONLY · TITANIUM-TAIL-2-CLAMSHELL · 7-SEC-AUTO-EXTG · HALF-SPEED-STARTER · BL-AIR-AUTO-OFF-WITH-ENG · LEFT-COLLECTOR-APU-FUEL · RIGHT-REAR-INLET · 3-COND-PWR-ARM · 4-COND-FUEL-CLOSE · 100-20-50-18 · LOUVRE-21-DUCT-30 · NO-RESTART-AFTER-DISCHARGE',
 'Twelve mnemonics carry every APU/pneumatics question. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
