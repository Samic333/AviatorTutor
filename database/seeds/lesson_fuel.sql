-- =============================================================================
-- AviatorTutor — Phase 8 (ATA 28 Fuel) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fuel' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'fuel-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Fuel — Tanks, Pumps, Transfer, Refuel, Indications',
     'fuel-overview',
     'overview',
     'Two integral wing tanks (No.1 left feeds left engine + APU; No.2 right feeds right engine). Each tank has three bays: surge + main + collector. Total usable fuel 5,318 kg. Maximum lateral imbalance 272 kg before BALANCE message flashes. Tank-to-tank transfer ONLY — no engine crossfeed capability on the Q400. AC variable-frequency auxiliary pumps in each collector bay back up the primary ejector pumps. Single-point pressure refuel + gravity overwing options. FOHE filters and heats fuel before the FMU. FUEL LOW caution at ~150 kg in collector bay (with park brake off and engine running). Refueling needs DC power; FUELING ON caution when door is open.',
     '<p>The Q400 fuel system is deliberately simple: two integral wing tanks, one per side, each engine fed only from its own tank. The notable design choice is the absence of engine crossfeed — the only way to balance the aeroplane laterally is tank-to-tank transfer through the central transfer plumbing. Inside each tank, three bays handle different jobs: the surge bay vents and recovers spilled fuel, the main tank stores the bulk volume, and the collector bay delivers fuel to the engine regardless of attitude. Boost pressure comes from primary ejector pumps driven by motive flow; AC variable-frequency auxiliary pumps in the collector bays serve as backup. The MFD fuel page is your single source of truth for quantity, temperature, valve state, transfer direction, and aux pump status. Memorise the 272-kg imbalance limit, the 150-kg FUEL LOW threshold, and the 5,318-kg usable total — these are the numbers a check captain will quote at you in the briefing room.</p>',
     JSON_ARRAY(
       'Two integral wing tanks: No.1 (LEFT) feeds left engine + APU. No.2 (RIGHT) feeds right engine. NO engine crossfeed — tank-to-tank transfer ONLY',
       'Three bays per tank: SURGE bay (vent + fuel recovery), MAIN tank (storage), COLLECTOR bay (engine feed at any attitude)',
       'Total usable fuel: 5,318 kg',
       'Maximum lateral imbalance: 272 kg. Above this the FQC flashes a yellow [BALANCE] message above the FUEL legend on the ED; analog dials turn solid yellow',
       'Each collector bay has: scavenge ejector pumps (draw fuel from tank low points), primary ejector pump (low-pressure feed to engine), and an AC variable-frequency auxiliary pump (backup boost)',
       'Auxiliary pumps automatically activate during fuel transfer (from donor tank); ON segment turns green without the switchlight being pushed',
       'Engine feed path: collector bay → primary ejector / AC aux pump → engine driven pump → FOHE (Fuel Oil Heat Exchanger) → FMU',
       'PULL FUEL/HYD OFF T-handle on Fire Protection Panel closes the engine feed shutoff valve for that side',
       '#1 or #2 TANK FUEL LOW caution: park brake OFF + collector bay drops below approximately 150 kg + engine running',
       '#1 or #2 ENG FUEL PRESS caution: engine driven pump inlet pressure below preset limit',
       '#1 or #2 FUEL FLTR BYPASS caution: fuel filter bypass impending (filter clogging up; fuel automatically routes around)',
       'FUELING ON caution: refuel/defuel access door is open. While illuminated, fuel transfer is INHIBITED',
       'Tank-to-tank transfer: TRANSFER switch TO TANK 1 / CENTER / TO TANK 2. Donor tank''s aux pump auto-activates. Halts automatically on high-level overfill in receiver',
       'Refueling: single-point pressure (under No.2 nacelle) or gravity overwing. DC power REQUIRED for pressure refuel. PRESELECT REFUEL (auto stop at preset quantity) or REFUEL (manual via PRECHECK/OPEN/CLOSE switches)',
       'Surge bay vents through 2 outboard float vent valves + 1 inboard vent line per side, to 2 NACA vents on the bottom of each wing. Spilled fuel returned to main tank by the partial vacuum as fuel is consumed',
       'JET B or JP-4 limitation: if TANK temperature exceeds 35°C, maximum altitude is FL200',
       'Magnetic dipsticks on underside of wings for ground quantity check (float magnet attracts dipstick magnet at fuel level)',
       'Total fuel digital display range: 0 to 15,000 KG in 5-KG increments. Tank temperature display: -99 to +99°C in 1° increments'
     ),
     JSON_ARRAY(
       'There is NO engine crossfeed on the Q400. To balance the aeroplane laterally you must transfer fuel between tanks via the central transfer plumbing.',
       'Lateral imbalance limit is 272 kg. The BALANCE message and yellow analog dials are your action cue.',
       'FUEL LOW caution requires THREE conditions to be true: park brake OFF, collector bay below ~150 kg, and the related engine running. On the ground with engines off and park brake on, no caution.',
       'Auxiliary pumps must be ON for takeoff and landing — they back up the primary ejector pump for engine boost during high-thrust regimes.',
       'During fuel transfer, the donor tank''s aux pump auto-activates without crew action. The ON segment turns green even though no one pushed the switchlight.',
       'FUELING ON caution INHIBITS fuel transfer entirely. Close the refuel door before any in-flight transfer attempt — relevant for technical-stop turnarounds.',
       'JP-4 / JET B at TANK temp >35°C limits altitude to FL200. Mostly a hot-and-high concern — relevant in summer ops in arid regions.'
     ),
     JSON_ARRAY(
       'Q400 has NO engine crossfeed — many students assume there is one. Only tank-to-tank transfer.',
       'Imbalance limit is 272 kg (NOT 100, 200, or 300).',
       'Total usable fuel: 5,318 kg (specific number — check captains may ask for it exactly).',
       'FUEL LOW threshold: ~150 kg in the COLLECTOR BAY (not main tank). Three trigger conditions, not one.',
       'Aux pump pressure status indicator: WHITE-fill circle = low/no pressure; GREEN-fill circle = normal pressure.',
       'BALANCE pointer turns YELLOW (not red) on imbalance.',
       'Refuel needs DC POWER (not AC) — easy trap.',
       'JET B / JP-4 altitude limit at >35°C: FL200 (NOT FL250 — a common confusion with the SAS-variant max ceiling).',
       'The auxiliary pump can illuminate ON without the switch being pushed — when fuel transfer is active. Read the panel carefully.',
       'Refuel/defuel access panel location: rear underside of NO.2 nacelle (right side). Easy to confuse with No.1.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'fuel-overview';
