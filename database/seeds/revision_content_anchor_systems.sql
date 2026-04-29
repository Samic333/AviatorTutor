-- =============================================================================
-- AviatorTutor — Phase 2.3 follow-up
-- Quick Revision content seed for the three anchor systems:
--   - Hydraulic Power (ATA29) — lessons.id=4 (existing)
--   - Electrical Power Overview (ATA24) — lessons.id=1 (existing)
--   - Powerplant Overview (ATA71) — created if missing
--
-- Populates lessons.{key_facts, must_know, exam_traps} with 8-12 entries each
-- so /study/{system}/revision feels "premium" in the QA walk-through.
--
-- Idempotent: pure UPDATEs + a guarded INSERT for the powerplant lesson.
-- =============================================================================

-- ── Hydraulic Power Overview (lesson 4) ─────────────────────────────────────
UPDATE lessons SET
  key_facts = JSON_ARRAY(
    'Three main hydraulic systems on the Q400: No.1, No.2, and No.3 (standby)',
    'Normal operating pressure is 3000 PSI on every system, every gauge',
    'Each engine drives one Engine-Driven Pump (EDP) — No.1 from L engine, No.2 from R engine',
    'No.3 standby system runs on AC electrical power only — engines can be shut down',
    'PTU (Power Transfer Unit) lets No.2 motor a hydraulic-only pump on No.1 if its EDP fails',
    'Hydraulic fluid is MIL-H-46000 phosphate ester (Skydrol) — incompatible with mineral oil',
    'Total system capacity is approximately 3.6 US gallons across the three reservoirs',
    'Accumulators store pressure for emergency brake applications — minimum 6 brake cycles',
    'Hand pump in flight deck pressurises No.3 for landing-gear free-fall extension assist',
    'No.3 alone powers: rudder, intermittent flaps drive, alternate gear extension, parking brake'
  ),
  must_know = JSON_ARRAY(
    'If both EDPs fail and AC power is lost, you have NO hydraulic power — accumulator brakes only',
    'Loss of No.1 = autopilot disconnect, loss of inboard spoilers, loss of nose-wheel steering',
    'Loss of No.2 = loss of outboard spoilers, loss of one elevator PCU, alternate brakes only',
    'Loss of No.3 = loss of standby flight controls, no alternate gear extension assistance',
    'HYD 1 PRESS LO at 1800 PSI triggers caution; below 1500 PSI is failure threshold',
    'PTU operates automatically when No.1 pressure < 1500 PSI and No.2 pressure is normal',
    'Engine fire handle pulled = closes hydraulic shutoff valve to that engine''s EDP',
    'Quantity below 0.5 gal on any reservoir → leak suspected — isolate before further use',
    'Brake accumulator pre-charge nitrogen pressure: 1200 PSI — checked on walk-around',
    'Reservoir pressurisation comes from engine bleed air — loss of bleed = pump cavitation risk'
  ),
  exam_traps = JSON_ARRAY(
    'TRAP: PTU is hydraulic-to-hydraulic only — it does NOT generate pressure from electrics',
    'TRAP: No.3 standby is AC-powered — DC ESS bus alone will not run it',
    'TRAP: Free-fall gear extension uses gravity + airloads, but the hand pump assists DOOR sequencing',
    'TRAP: Nose-wheel steering is on No.1 — if No.1 fails, expect differential braking taxi only',
    'TRAP: A green HYD pressure gauge does NOT confirm fluid quantity — check the QTY indicator',
    'TRAP: Skydrol attacks paint and skin — fluid leaks are a maintenance + crew safety issue',
    'TRAP: Both elevators have dual PCUs — losing No.2 does not lose elevator authority entirely',
    'TRAP: Parking brake holds via accumulator pressure — bleeds down if left set unattended for hours'
  )
WHERE id = 4;

-- ── Electrical Power Overview (lesson 1) ────────────────────────────────────
UPDATE lessons SET
  key_facts = JSON_ARRAY(
    'Two engine-driven Variable-Frequency Generators (VFGs) — one per engine, 115V AC 3-phase',
    'One Auxiliary Power Unit (APU) generator — 115V AC, available on ground and in-flight to FL250',
    'Battery: 24V DC 38Ah Ni-Cad (or sealed lead-acid retrofit) — one main + standby option',
    'Two Transformer-Rectifier Units (TRUs) convert AC → 28V DC for DC bus distribution',
    'Essential bus structure: AC ESS, DC ESS, BAT BUS — fed automatically from any healthy source',
    'External (GPU) AC power available on ground via single 6-pin plug at fwd belly',
    'Battery alone provides standby flight instruments + ESS DC for ~30 minutes (typical)',
    'Generator priority on dual-engine: each VFG feeds its own side bus; APU GEN is backup',
    'Inverters are NOT used — Q400 has no static inverter; AC ESS comes from generators directly',
    'Bus tie operation: open in normal flight, closes automatically on single-source loss'
  ),
  must_know = JSON_ARRAY(
    'Loss of both VFGs in-flight: APU start, then APU GEN online — practise the IMMEDIATE actions',
    'Battery-only flight time is finite — plan diversion within standby duration',
    'AC ESS bus loss = lose one PFD, weather radar, autopilot — degraded but flyable',
    'DC ESS bus loss = lose engine fire detection on one side, anti-skid degradation',
    'External power must be selected OFF before pushing back — pin damage and arc-flash risk',
    'On ground, never select GEN OFF with engine running unless preparing for shutdown',
    'GCU (Generator Control Unit) faults trip the generator off and latch — RESET only once airborne if SOP allows',
    'TRU failure halves DC bus capacity — load shed non-essential DC items immediately',
    'Smoke / electrical odour: SMOKE checklist takes precedence over GEN OFF reset attempts'
  ),
  exam_traps = JSON_ARRAY(
    'TRAP: APU GEN is rated to FL250 — not full cruise altitude — plan a step-down if dual VFG out',
    'TRAP: Battery start of APU on ground depletes battery faster than you think — check voltage drop',
    'TRAP: Bus tie auto-closes on loss but stays open if a fault is sensed downstream — read EICAS',
    'TRAP: A "GEN OFF" message can mean tripped OR pilot-selected — check switch position before troubleshooting',
    'TRAP: Some retrofit batteries have different time-limited capability — know your ship''s fit',
    'TRAP: GPU connection plug is keyed but reverse-polarity events have damaged TRUs historically',
    'TRAP: Anti-ice loads are huge — losing one generator with both anti-ice on may auto-shed items',
    'TRAP: AC ESS feed-fault cascades — a single CB pop can simulate a generator fault on the panel'
  )
WHERE id = 1;

-- ── Powerplant Overview (ATA71) — create if not present ─────────────────────
SET @ata71_system_id := (SELECT id FROM systems WHERE ata_code = 'ATA71' LIMIT 1);

-- Insert overview lesson if missing.
INSERT INTO lessons (system_id, slug, title, content_type, summary, body, key_facts, must_know, exam_traps, sort_order, is_published)
SELECT
  @ata71_system_id,
  'powerplant-overview',
  'Powerplant System - Overview',
  'overview',
  'PW150A turboprop fundamentals: free turbine layout, FADEC, propeller drive, and the operational consequences of how this engine is built.',
  '<p>The Q400 is powered by two Pratt & Whitney Canada PW150A turboprop engines, each rated at 5071 SHP (de-rated to 4580 SHP for normal operations). The engine uses a 3-spool free-turbine architecture with a Reduction Gearbox (RGB) driving the Dowty R408 6-blade composite propeller.</p><p>FADEC (Full Authority Digital Engine Control) manages all engine operations including starting, fuel scheduling, propeller pitch coordination, and limit protection. The crew interface is intentionally simple — Power Lever, Condition Lever — but the consequences of mishandling either are operationally severe.</p>',
  JSON_ARRAY(
    'Two Pratt & Whitney PW150A turboprops, 5071 SHP rated (de-rated to 4580 SHP normal ops)',
    'Three-spool free-turbine layout: LP compressor, HP compressor, HP turbine, LP turbine, free-power turbine',
    'Reduction Gearbox (RGB) drives the Dowty R408 6-blade composite prop at ~1020 RPM cruise',
    'FADEC controls fuel schedule, propeller pitch, start sequence, and protects all engine limits',
    'Power Lever sets requested torque; Condition Lever selects START / MIN / MAX / FUEL OFF',
    'Three torque ratings: NTOP (90% Np), MTOP (Max takeoff), MCP (Max continuous), all FADEC-protected',
    'Propeller is full-feathering, reversing — Beta range below flight idle for taxi/landing rollout',
    'Bleed air taps from HP compressor for anti-ice, air conditioning, and engine starting',
    'Auto-feather system arms above 60 KIAS at takeoff and feathers a failed engine''s prop automatically',
    'PEC (Propeller Electronic Control) is part of FADEC — loss can give ALT GOV (alternate governing)'
  ),
  JSON_ARRAY(
    'Engine fire handle: closes fuel SOV, closes hydraulic SOV, arms both fire bottles, trips generator',
    'Auto-feather is your friend at V1 — confirm "AUTO FEATHER ARMED" before takeoff thrust',
    'NTOP (Normal Takeoff Power) is preferred over MTOP for engine wear — only use MTOP per SOP',
    'ITT (Inter-Turbine Temperature) limits are absolute — exceeding even briefly logs an event',
    'Beta range on the ground only — selecting reverse in flight can damage the prop hub',
    'Condition Lever in MIN feather pre-armed on the ground — prevents thrust on uncommanded power lever',
    'Propeller overspeed: PEC reduces torque automatically, then mechanical governor cuts in at 110%',
    'Hot start protection: FADEC aborts auto if ITT exceeds limit — manual abort if FADEC degraded',
    'Engine restart in flight has a defined envelope — outside it, expect windmill restart only'
  ),
  JSON_ARRAY(
    'TRAP: Reversing on a contaminated runway is permitted but raises FOD risk — SOP-bound',
    'TRAP: ITT redline differs by phase — start vs takeoff vs cruise — know which limit is active',
    'TRAP: Auto-feather arms only above 60 KIAS AND with both PLs in NTOP/MTOP detent',
    'TRAP: Engine "fire" detection is loop-based — a single loop fault gives FAULT, not FIRE',
    'TRAP: Propeller "low-pitch lockout" prevents below-flight-idle in air — but failures are documented',
    'TRAP: A frozen Power Lever quadrant in cold ops can mask uncommanded power changes',
    'TRAP: PEC has two channels — single-channel fault is degraded mode, not loss of governing',
    'TRAP: Condition Lever selection out of detent at high power can trigger alternate governing'
  ),
  10,
  1
WHERE @ata71_system_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM lessons WHERE slug = 'powerplant-overview');

-- If the lesson already existed (e.g. seed previously partially run), refresh
-- only the JSON revision columns so we don't trample handcrafted body edits.
UPDATE lessons SET
  key_facts = JSON_ARRAY(
    'Two Pratt & Whitney PW150A turboprops, 5071 SHP rated (de-rated to 4580 SHP normal ops)',
    'Three-spool free-turbine layout: LP compressor, HP compressor, HP turbine, LP turbine, free-power turbine',
    'Reduction Gearbox (RGB) drives the Dowty R408 6-blade composite prop at ~1020 RPM cruise',
    'FADEC controls fuel schedule, propeller pitch, start sequence, and protects all engine limits',
    'Power Lever sets requested torque; Condition Lever selects START / MIN / MAX / FUEL OFF',
    'Three torque ratings: NTOP (90% Np), MTOP (Max takeoff), MCP (Max continuous), all FADEC-protected',
    'Propeller is full-feathering, reversing — Beta range below flight idle for taxi/landing rollout',
    'Bleed air taps from HP compressor for anti-ice, air conditioning, and engine starting',
    'Auto-feather system arms above 60 KIAS at takeoff and feathers a failed engine''s prop automatically',
    'PEC (Propeller Electronic Control) is part of FADEC — loss can give ALT GOV (alternate governing)'
  ),
  must_know = JSON_ARRAY(
    'Engine fire handle: closes fuel SOV, closes hydraulic SOV, arms both fire bottles, trips generator',
    'Auto-feather is your friend at V1 — confirm "AUTO FEATHER ARMED" before takeoff thrust',
    'NTOP (Normal Takeoff Power) is preferred over MTOP for engine wear — only use MTOP per SOP',
    'ITT (Inter-Turbine Temperature) limits are absolute — exceeding even briefly logs an event',
    'Beta range on the ground only — selecting reverse in flight can damage the prop hub',
    'Condition Lever in MIN feather pre-armed on the ground — prevents thrust on uncommanded power lever',
    'Propeller overspeed: PEC reduces torque automatically, then mechanical governor cuts in at 110%',
    'Hot start protection: FADEC aborts auto if ITT exceeds limit — manual abort if FADEC degraded',
    'Engine restart in flight has a defined envelope — outside it, expect windmill restart only'
  ),
  exam_traps = JSON_ARRAY(
    'TRAP: Reversing on a contaminated runway is permitted but raises FOD risk — SOP-bound',
    'TRAP: ITT redline differs by phase — start vs takeoff vs cruise — know which limit is active',
    'TRAP: Auto-feather arms only above 60 KIAS AND with both PLs in NTOP/MTOP detent',
    'TRAP: Engine "fire" detection is loop-based — a single loop fault gives FAULT, not FIRE',
    'TRAP: Propeller "low-pitch lockout" prevents below-flight-idle in air — but failures are documented',
    'TRAP: A frozen Power Lever quadrant in cold ops can mask uncommanded power changes',
    'TRAP: PEC has two channels — single-channel fault is degraded mode, not loss of governing',
    'TRAP: Condition Lever selection out of detent at high power can trigger alternate governing'
  )
WHERE slug = 'powerplant-overview';

-- Quick sanity output.
SELECT
  id, system_id, title,
  JSON_LENGTH(key_facts)  AS key_facts_count,
  JSON_LENGTH(must_know)  AS must_know_count,
  JSON_LENGTH(exam_traps) AS exam_traps_count
FROM lessons
WHERE id IN (1, 4) OR slug = 'powerplant-overview';
