-- =============================================================================
-- AviatorTutor — Phase 8: ATA 28 Fuel
-- Two quizzes: Practice (25 Q, no time limit, pass 70) + Type Rating Mock (10 Q, 12 min, pass 80)
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fuel' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM quizzes WHERE system_id = @system_id;

-- ----------------------------------------------------------------------------
-- Quiz 1 — Practice
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Fuel — Practice',
 'Twenty-five-question practice quiz on fuel architecture, transfer logic, refuel procedure, FUEL LOW conditions, BALANCE handling, and leak recognition. No time limit.',
 'practice', NULL, 70, 1);

SET @quiz_practice := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_practice, 'How many fuel tanks does the Q400 have, and what does each feed?',
 'mcq',
 JSON_ARRAY(
   '1 tank, both engines crossfeed from it',
   '2 tanks: No.1 left feeds left engine + APU, No.2 right feeds right engine, NO crossfeed',
   '2 tanks with full engine crossfeed capability',
   '3 tanks: 2 wing + 1 centre fuselage'
 ),
 JSON_OBJECT('correct_index', 1),
 'Two integral wing tanks. No.1 left → left engine + APU. No.2 right → right engine. NO engine crossfeed.',
 'easy', 10),

(@quiz_practice, 'How many bays does each Q400 wing tank have, and what are they?',
 'mcq',
 JSON_ARRAY(
   '2: main and surge',
   '3: surge, main, collector',
   '3: main, transfer, refuel',
   '4: main, surge, collector, vent'
 ),
 JSON_OBJECT('correct_index', 1),
 'Three bays: SURGE (vent + recovery), MAIN (storage), COLLECTOR (engine feed). Mnemonic: 3-BAY-TANK.',
 'medium', 20),

(@quiz_practice, 'What is the total usable fuel quantity of the Q400?',
 'mcq',
 JSON_ARRAY('4,500 kg','5,000 kg','5,318 kg','6,000 kg'),
 JSON_OBJECT('correct_index', 2),
 '5,318 kg total usable. Mnemonic: 5318-USABLE.',
 'medium', 30),

(@quiz_practice, 'What is the maximum lateral fuel imbalance before the BALANCE message triggers?',
 'mcq',
 JSON_ARRAY('100 kg','200 kg','272 kg','500 kg'),
 JSON_OBJECT('correct_index', 2),
 '272 kg — yellow [BALANCE] flashes above FUEL legend on ED; analog dials turn solid yellow. Mnemonic: 272-IMBALANCE.',
 'medium', 40),

(@quiz_practice, 'What three conditions must ALL be met for the TANK FUEL LOW caution to illuminate?',
 'mcq',
 JSON_ARRAY(
   'Engine running + collector bay <150 kg only',
   'Park brake OFF + collector bay <150 kg + engine running',
   'Park brake ON + collector bay <100 kg',
   'Engine running + total fuel <500 kg'
 ),
 JSON_OBJECT('correct_index', 1),
 'All three: park brake OFF, collector bay below ~150 kg, related engine running. Mnemonic: 150-COLLECTOR-LOW.',
 'hard', 50),

(@quiz_practice, 'What type of pump is the auxiliary fuel pump in each collector bay?',
 'mcq',
 JSON_ARRAY('DC centrifugal','AC variable-frequency','Hydraulic-driven','Engine-driven mechanical'),
 JSON_OBJECT('correct_index', 1),
 'AC variable-frequency. One per collector bay. Backs up the primary ejector pump.',
 'medium', 60),

(@quiz_practice, 'When are the auxiliary fuel pumps required to be ON?',
 'mcq',
 JSON_ARRAY(
   'During cruise only',
   'Takeoff and landing',
   'Always — the primary ejector cannot feed the engine alone',
   'Only during fuel transfer'
 ),
 JSON_OBJECT('correct_index', 1),
 'AUX pumps ON for takeoff and landing — backup boost during high-thrust regimes. Primary ejector handles cruise alone.',
 'medium', 70),

(@quiz_practice, 'During fuel transfer, what happens to the donor tank''s aux pump?',
 'mcq',
 JSON_ARRAY(
   'Crew must manually push it ON',
   'It auto-activates; ON segment turns green automatically',
   'It is locked OFF during transfer',
   'It cycles on/off every 30 seconds'
 ),
 JSON_OBJECT('correct_index', 1),
 'Donor tank''s aux pump auto-activates during transfer. ON segment goes green without crew push. Mnemonic: AUTO-AUX-DURING-TRANSFER.',
 'hard', 80),

(@quiz_practice, 'What is FOHE and what does it do?',
 'mcq',
 JSON_ARRAY(
   'Fuel Oil Heat Exchanger — filters and heats fuel before the FMU',
   'Fuel Overflow Holding Exchanger — recovers spilled fuel',
   'Fuel Override Hydraulic Engine — backup fuel pump',
   'Fuel On-board Hot Equipment — APU heater'
 ),
 JSON_OBJECT('correct_index', 0),
 'FOHE = Fuel Oil Heat Exchanger. Filters AND heats fuel before the FMU. Heating prevents ice crystal formation; filtering removes particulates. Mnemonic: FOHE-HEATS-FILTERS.',
 'medium', 90),

(@quiz_practice, 'What does FUEL FLTR BYPASS caution indicate?',
 'mcq',
 JSON_ARRAY(
   'Fuel filter is operating normally',
   'Fuel filter is clogging; bypass impending or active',
   'Fuel transfer valve is bypassed',
   'Standby fuel pump active'
 ),
 JSON_OBJECT('correct_index', 1),
 'Filter is clogging up; fuel automatically routes around. Continue but flag for maintenance — sustained bypass means unfiltered fuel.',
 'medium', 100),

(@quiz_practice, 'A #1 ENG FUEL PRESS caution illuminates. What is the FIRST action?',
 'mcq',
 JSON_ARRAY(
   'PULL the No.1 PULL FUEL/HYD OFF T-handle',
   'Push the TANK 1 AUX PUMP switchlight ON',
   'Initiate fuel transfer from tank 2 to tank 1',
   'Reduce thrust on engine 1'
 ),
 JSON_OBJECT('correct_index', 1),
 'First action: AUX PUMP ON for the affected side. AC variable-frequency aux pump restores boost; caution typically clears.',
 'medium', 110),

(@quiz_practice, 'What is the maximum altitude when using JET B / JP-4 fuel with TANK temperature above 35°C?',
 'mcq',
 JSON_ARRAY('FL150','FL200','FL250','No altitude limit'),
 JSON_OBJECT('correct_index', 1),
 'FL200 (20,000 ft) maximum altitude when JET B/JP-4 TANK temp >35°C. Mnemonic: JP4-35-FL200.',
 'hard', 120),

(@quiz_practice, 'What power source is required for pressure refueling?',
 'mcq',
 JSON_ARRAY('AC power only','DC power','Hydraulic pressure','None — pressure refuel is mechanical'),
 JSON_OBJECT('correct_index', 1),
 'DC POWER required. Loss of DC during refuel halts the operation. Mnemonic: DC-FOR-REFUEL.',
 'medium', 130),

(@quiz_practice, 'Where is the single-point pressure refuel access located?',
 'mcq',
 JSON_ARRAY(
   'Forward fuselage, left side',
   'Rear underside of No.2 nacelle',
   'Centre fuselage near the wing root',
   'Tail cone, near the APU'
 ),
 JSON_OBJECT('correct_index', 1),
 'Rear underside of No.2 nacelle (right side). Flush access door.',
 'medium', 140),

(@quiz_practice, 'What does the FUELING ON caution indicate, and what does it inhibit?',
 'mcq',
 JSON_ARRAY(
   'Refuel door open; INHIBITS tank-to-tank transfer',
   'Refuel complete; no inhibitions',
   'Aux pump on; INHIBITS engine start',
   'Fuel quantity high; INHIBITS landing'
 ),
 JSON_OBJECT('correct_index', 0),
 'FUELING ON = refuel/defuel access door OPEN (or sensor fault). INHIBITS tank-to-tank transfer entirely while illuminated. Mnemonic: FUELING-ON-INHIBITS-TRANSFER.',
 'hard', 150),

(@quiz_practice, 'Does the Q400 have engine crossfeed capability?',
 'mcq',
 JSON_ARRAY(
   'Yes — full crossfeed via cockpit switch',
   'Yes — automatic crossfeed on engine fail',
   'NO — only tank-to-tank transfer',
   'Only on the ground via maintenance'
 ),
 JSON_OBJECT('correct_index', 2),
 'NO engine crossfeed. Tank-to-tank transfer only. Mnemonic: NO-CROSSFEED-TANK-TO-TANK.',
 'medium', 160),

(@quiz_practice, 'On the MFD fuel page, what colour is the analog quantity pointer during an imbalance condition?',
 'mcq',
 JSON_ARRAY('White','Green','Yellow','Red'),
 JSON_OBJECT('correct_index', 2),
 'YELLOW pointer on imbalance. White is normal. Mnemonic: YELLOW-IMBALANCE.',
 'medium', 170),

(@quiz_practice, 'On the MFD aux pump pressure-status circle, what does GREEN-fill mean?',
 'mcq',
 JSON_ARRAY(
   'Pump off',
   'Low or no pressure',
   'Normal pressure',
   'Pump fault'
 ),
 JSON_OBJECT('correct_index', 2),
 'GREEN-fill circle = NORMAL pressure. WHITE-fill circle = LOW or no pressure.',
 'medium', 180),

(@quiz_practice, 'How does fuel transfer stop automatically?',
 'mcq',
 JSON_ARRAY(
   'After exactly 5 minutes',
   'Receiver tank''s high-level sensor detects overfill condition',
   'When the donor tank empties',
   'Only when the crew deselects the switch'
 ),
 JSON_OBJECT('correct_index', 1),
 'High-level sensor in the receiver tank halts transfer automatically on overfill. Crew can also manually deselect.',
 'medium', 190),

(@quiz_practice, 'TRUE or FALSE — On a fuel leak, the captain should immediately initiate transfer toward the lighter tank.',
 'true_false',
 JSON_ARRAY('True','False'),
 JSON_OBJECT('correct_index', 1),
 'FALSE. NEVER transfer into a leaking tank. Identify burn rate vs quantity math; run QRH FUEL LEAK; secure affected engine; divert.',
 'hard', 200),

(@quiz_practice, 'What is the function of the surge bay?',
 'mcq',
 JSON_ARRAY(
   'Engine fuel feed',
   'Fuel storage only',
   'Tank venting and fuel recovery',
   'Refuel/defuel adapter mount'
 ),
 JSON_OBJECT('correct_index', 2),
 'Surge bay vents the tank (via float vent valves and NACA vents) and recovers any spilled fuel back to the main tank.',
 'hard', 210),

(@quiz_practice, 'How do scavenge ejector pumps work?',
 'mcq',
 JSON_ARRAY(
   'Centrifugal pumps run from DC power',
   'Driven by high-pressure motive flow; draw fuel from low points to the collector bay',
   'Manual hand pumps for refuel',
   'Hydraulic pumps powered by No.1 hyd'
 ),
 JSON_OBJECT('correct_index', 1),
 'Scavenge ejectors use motive flow (high-pressure fuel) to draw from tank low points back to the collector bay. Continuous operation.',
 'hard', 220),

(@quiz_practice, 'What is the digital display range for total fuel quantity?',
 'mcq',
 JSON_ARRAY('0–10,000 KG in 10 KG steps','0–15,000 KG in 5 KG steps','0–20,000 LB in 100 LB steps','0–8,000 KG in 1 KG steps'),
 JSON_OBJECT('correct_index', 1),
 '0 to 15,000 KG, 5-KG increments. White dashes if data invalid.',
 'medium', 230),

(@quiz_practice, 'What is the role of the magnetic dipsticks?',
 'mcq',
 JSON_ARRAY(
   'In-flight backup quantity indication',
   'Ground quantity check via magnet-attaches-to-float on the underside of each wing',
   'Fuel temperature measurement',
   'Tank water drain valves'
 ),
 JSON_OBJECT('correct_index', 1),
 'Magnetic dipsticks on the underside of wings. Tank float magnet attracts the dipstick magnet at fuel level for ground quantity check.',
 'hard', 240),

(@quiz_practice, 'What two pressure-refuel modes are available?',
 'mcq',
 JSON_ARRAY(
   'PRESELECT REFUEL (auto stop at preset KG) and REFUEL (manual via PRECHECK/OPEN/CLOSE)',
   'AUTO and MANUAL — same thing',
   'PRESET and DEFUEL only',
   'GRAVITY and PRESSURE'
 ),
 JSON_OBJECT('correct_index', 0),
 'PRESELECT REFUEL = auto stop at preset quantity via INCR/DECR. REFUEL = manual control through the PRECHECK/OPEN/CLOSE switches per tank.',
 'medium', 250);

-- ----------------------------------------------------------------------------
-- Quiz 2 — Type Rating Mock
-- ----------------------------------------------------------------------------
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published)
VALUES (@system_id,
 'Fuel — Type Rating Mock',
 'Ten-question mock at type-rating oral standard. Twelve-minute timer, pass score 80%. Designed to expose holes BEFORE the check ride.',
 'exam', 12, 80, 1);

SET @quiz_exam := LAST_INSERT_ID();

INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order) VALUES
(@quiz_exam, 'Quote total usable fuel and the imbalance limit.',
 'mcq',
 JSON_ARRAY(
   '5,000 kg / 100 kg',
   '5,318 kg / 272 kg',
   '6,000 kg / 500 kg',
   '5,318 kg / 100 kg'
 ),
 JSON_OBJECT('correct_index', 1),
 'Total usable 5,318 kg. Imbalance limit 272 kg. Mnemonics: 5318-USABLE · 272-IMBALANCE.',
 'medium', 10),

(@quiz_exam, 'Three conditions for TANK FUEL LOW caution.',
 'mcq',
 JSON_ARRAY(
   'Park brake OFF + collector bay <150 kg + engine running',
   'Engine running + total fuel <500 kg',
   'Aux pump OFF + park brake ON + idle thrust',
   'Park brake OFF + main tank <150 kg'
 ),
 JSON_OBJECT('correct_index', 0),
 'All three: park brake OFF, collector bay below ~150 kg, related engine running. Mnemonic: 150-COLLECTOR-LOW.',
 'hard', 20),

(@quiz_exam, 'Engine fuel feed path from collector bay to FMU.',
 'mcq',
 JSON_ARRAY(
   'Collector → primary ejector → engine driven pump → FOHE → FMU',
   'Collector → engine driven pump → FOHE → primary ejector → FMU',
   'Collector → FMU directly',
   'Collector → AC aux pump → FMU directly'
 ),
 JSON_OBJECT('correct_index', 0),
 'Collector → primary ejector (or AC aux as backup) → engine driven pump → FOHE → FMU.',
 'medium', 30),

(@quiz_exam, 'Does the Q400 have engine crossfeed?',
 'mcq',
 JSON_ARRAY('Yes — automatic on failure','Yes — manual via cockpit switch','NO — only tank-to-tank transfer','Only on the ground'),
 JSON_OBJECT('correct_index', 2),
 'NO engine crossfeed. Tank-to-tank transfer only. Defining quirk.',
 'easy', 40),

(@quiz_exam, 'A #2 ENG FUEL PRESS caution illuminates in cruise. First action?',
 'mcq',
 JSON_ARRAY(
   'PULL the No.2 PULL FUEL/HYD OFF T-handle',
   'Push the TANK 2 AUX PUMP switchlight ON',
   'Initiate fuel transfer to tank 2',
   'Reduce thrust on engine 2'
 ),
 JSON_OBJECT('correct_index', 1),
 'AUX PUMP ON. Confirm aux pressure circle GREEN. If sustained, run QRH and consider divert.',
 'medium', 50),

(@quiz_exam, 'JET B / JP-4 altitude limitation when TANK temperature is above 35°C.',
 'mcq',
 JSON_ARRAY('FL150','FL200','FL250','No altitude limit'),
 JSON_OBJECT('correct_index', 1),
 'FL200 max when JET B/JP-4 TANK temp >35°C. Mnemonic: JP4-35-FL200.',
 'hard', 60),

(@quiz_exam, 'BALANCE message in cruise. Fuel page shows tank 1 = 1,400 kg, tank 2 = 1,100 kg. Burn rate symmetric. Best action?',
 'mcq',
 JSON_ARRAY(
   'Suspect a leak; declare PAN-PAN',
   'FUEL TRANSFER switch TO TANK 2; donor aux pump auto-on; monitor and stop at zero',
   'Push both AUX PUMP switchlights ON',
   'Open the engine crossfeed valve'
 ),
 JSON_OBJECT('correct_index', 1),
 'Symmetric burn = imbalance correction, not leak. Transfer toward lighter tank. Donor aux auto-on. Stop at zero. (No engine crossfeed exists.)',
 'medium', 70),

(@quiz_exam, 'Suspected fuel leak — rapid imbalance growth + total fuel dropping faster than fuel flow. WORST action?',
 'mcq',
 JSON_ARRAY(
   'Run QRH FUEL LEAK',
   'Initiate fuel transfer toward the lighter tank',
   'Calculate landing fuel from actual remaining',
   'Notify ATC and request divert'
 ),
 JSON_OBJECT('correct_index', 1),
 'NEVER transfer into a leaking tank — feeds the leak. Burn-rate math first. Run QRH FUEL LEAK; secure affected engine; divert.',
 'hard', 80),

(@quiz_exam, 'Power requirement for pressure refueling.',
 'mcq',
 JSON_ARRAY('AC power only','DC power','Hydraulic pressure','None'),
 JSON_OBJECT('correct_index', 1),
 'DC power required. Loss of DC halts pressure refuel. Gravity refuel (no DC) is the backup.',
 'easy', 90),

(@quiz_exam, 'During tank-to-tank transfer, what does the donor tank''s aux pump do?',
 'mcq',
 JSON_ARRAY(
   'Stays off — primary ejector handles transfer',
   'Auto-activates; ON segment turns green automatically without crew push',
   'Crew must manually push the switchlight to enable',
   'Cycles on/off every 30 seconds'
 ),
 JSON_OBJECT('correct_index', 1),
 'Auto-activates on the donor side during transfer. ON-green segment without push = transfer in progress. Mnemonic: AUTO-AUX-DURING-TRANSFER.',
 'medium', 100);

SELECT
    (SELECT COUNT(*) FROM quizzes WHERE system_id = @system_id) AS quizzes_inserted,
    (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id IN (SELECT id FROM quizzes WHERE system_id = @system_id)) AS questions_inserted;
