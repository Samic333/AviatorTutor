-- =============================================================================
-- AviatorTutor — Phase 3 sample module
-- 12-slide interactive lesson for Q400 Hydraulic Power (ATA 29)
--
-- Idempotent: re-running will wipe and re-insert slides for this one lesson.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE ata_code = 'ATA29' LIMIT 1);
SET @lesson_id := (
    SELECT id FROM lessons
    WHERE system_id = @system_id
    ORDER BY sort_order, id
    LIMIT 1
);

-- Bail loudly if the prerequisite rows are missing.
-- (MySQL won't `RAISE`, but a NULL @lesson_id would make the inserts no-op-fail.)
SELECT
    @system_id AS resolved_system_id,
    @lesson_id AS resolved_lesson_id;

-- Wipe any prior slides for this lesson so the seed can be re-run safely.
DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides
    (lesson_id, sort_order, slide_type, title, body,
     media_type, media_url, media_alt, key_point, ops_relevance, question)
VALUES
-- 1. Intro
(@lesson_id, 10, 'intro',
 'Why Hydraulics Matter on the Q400',
 'Hydraulic power is what moves the big things on this airplane: flight controls, landing gear, nose-wheel steering, and brakes. Without hydraulics, you cannot fly the airplane the way it was designed to be flown. Over the next slides we will walk through the four hydraulic systems on the Q400, what each one drives, what happens when one fails, and what you do as a crew.',
 'diagram', '/assets/uploads/hydraulics/q400_hydraulics_overview.png',
 'Q400 hydraulics overview diagram',
 'Hydraulics = Muscles of the airplane. No hydraulics, no movement.',
 'Every Q400 captain must understand the hydraulic redundancy logic before takeoff — it directly drives go/no-go decisions on the ground.',
 NULL),

-- 2. Concept
(@lesson_id, 20, 'concept',
 'Three Mains + One Emergency = Four Systems',
 'The Q400 has four hydraulic systems total:\n  • No.1 Main — primary, left side\n  • No.2 Main — primary, right side\n  • No.3 Main — standby, feeds elevators only when 1 or 2 fail\n  • Emergency hand pump — manual backup for gear extension and brakes\n\nAll three main systems run at 3000 PSI. The hand pump is mechanical — no engine or electric drive needed.',
 'image', '/assets/uploads/hydraulics/three_main_systems.png',
 'Diagram: three main hydraulic systems with auxiliary',
 '3 + E = 3000 — Three mains plus Emergency, all at 3000 PSI.',
 'When ATC asks if you are ready to depart, your hydraulic-status quick-check is: are all three mains pressurized at 3000?',
 NULL),

-- 3. System (No.1 & No.2 mains)
(@lesson_id, 30, 'system',
 'No.1 and No.2 Main Systems',
 'No.1 and No.2 are the workhorses. Each one is independent — its own reservoir, pump, lines, and accumulator. Each main system powers:\n  • Flight controls (its assigned surfaces)\n  • Landing gear (No.1 extends, No.2 retracts)\n  • Nose-wheel steering (No.1)\n  • Brakes (No.2)\n\nIf one of these systems fails in flight, the airplane is still controllable, but you lose redundancy and certain functions degrade.',
 'diagram', '/assets/uploads/hydraulics/no1_no2_systems.png',
 'No.1 and No.2 hydraulic system schematic',
 'F-L-B — Flight controls, Landing gear, Brakes. The three big jobs.',
 'Loss of either No.1 or No.2 in flight is a non-emergency abnormal — slow down, run the QRH, plan for a longer landing roll.',
 NULL),

-- 4. System (No.3 standby) — gate
(@lesson_id, 40, 'system',
 'No.3 Standby System — The Insurance Policy',
 'No.3 is the standby. In normal cruise it sits passive — pressurized but doing nothing. It feeds the left and right elevators only, and only takes over when No.1 OR No.2 fails. That means even with two mains lost, you still have pitch authority via No.3.\n\nNo.3 has its own reservoir and electric pump. It is not connected to landing gear or brakes — only the elevators.',
 'animation', '/assets/uploads/hydraulics/no3_failover_animation.json',
 'Animation: No.3 takes over elevator power on No.1/No.2 failure',
 'No.3 = elevator-only insurance. It is your last layer for pitch control.',
 'If you see a No.3 caution after a No.1 or No.2 failure, you have lost the redundancy chain — declare and divert.',
 JSON_OBJECT(
   'prompt', 'In cruise, the No.1 hydraulic system fails completely. What automatically takes over to power the elevators?',
   'options', JSON_ARRAY(
     'No.2 main system, via the crossfeed valve',
     'No.3 standby system',
     'The emergency hand pump',
     'Nothing — elevators are on cables only'
   ),
   'correct_index', 1,
   'explanation', 'No.3 standby system feeds the left and right elevators automatically when either No.1 or No.2 fails. The emergency hand pump is for landing gear extension and braking only, not flight controls. Crossfeed between No.1 and No.2 does not exist on the Q400.'
 )),

-- 5. Normal op (pressure)
(@lesson_id, 50, 'normal_op',
 '3000 PSI Everywhere — Normal Indications',
 'In normal cruise you should see:\n  • No.1 — 3000 PSI ± 200, green\n  • No.2 — 3000 PSI ± 200, green\n  • No.3 — pressurized but low flow (standby)\n\nEach system has an engine-driven pump (EDP) and an electric AUX pump. The EDPs do all the work in normal flight; AUX pumps cover ground ops, single-engine ops, and EDP failures.',
 'image', '/assets/uploads/hydraulics/cockpit_hyd_panel.png',
 'Cockpit hydraulic pressure indications',
 'Green = good. 3000 ± 200 = within tolerance.',
 'Crosscheck both pressure gauges before takeoff and on every cruise scan. A drop into the yellow band is your first warning of a pump or leak issue.',
 NULL),

-- 6. Normal op (fluid)
(@lesson_id, 60, 'normal_op',
 'The Fluid: MIL-H-46000 Phosphate Ester',
 'The Q400 uses phosphate-ester hydraulic fluid (Skydrol-type, MIL-H-46000). It is fire-resistant — meaning it will not sustain a flame from a typical ignition source. It is NOT non-flammable, and it IS aggressive: it will eat paint, harm skin and eyes, and degrade many plastics and rubbers.\n\nNever mix it with mineral-based hydraulic oil. Never assume a leak is harmless to surrounding wiring or composites.',
 'image', '/assets/uploads/hydraulics/fluid_warning.png',
 'Phosphate ester fluid warning placard',
 'F-I-R-E — Fluid Is Resistant to Engulfment.  But still aggressive — wear gloves and goggles.',
 'A reported "small hyd leak" on a turnaround is never small — it can mean wire-bundle damage. Get an engineer on the airplane.',
 NULL),

-- 7. Abnormal — gate
(@lesson_id, 70, 'abnormal',
 'Single-System Failure: What Stays, What Goes',
 'Lose No.1 in flight, you keep:\n  • All flight controls (No.2 + No.3)\n  • Nose-wheel steering at low speed (degraded)\n  • Alternate brakes (No.2)\n  • Manual gear extension (hand pump)\n\nLose No.2, you keep:\n  • All flight controls (No.1 + No.3)\n  • Normal brakes (No.1) — but only after manual selection\n  • Normal gear extension via No.1\n\nIn either case the airplane lands safely with the QRH followed in order.',
 'diagram', '/assets/uploads/hydraulics/single_failure_matrix.png',
 'Single-failure consequences matrix',
 'Single failure = degraded but flyable. Two failures = serious. Three = emergency.',
 'On a single hydraulic failure, your priorities are: confirm the failure, run the QRH, brief the approach for longer rollout, and declare PAN-PAN if at a busy field.',
 JSON_OBJECT(
   'prompt', 'After a No.2 hydraulic system failure, which of the following is true?',
   'options', JSON_ARRAY(
     'Both engines must be shut down because brakes are lost',
     'The crew must use the hand pump to extend the gear',
     'Flight controls remain available via No.1 and No.3',
     'The aircraft becomes unflyable and must ditch'
   ),
   'correct_index', 2,
   'explanation', 'No.1 still drives its share of flight controls and No.3 backs up the elevators. Gear can be extended normally on No.1. The airplane is degraded, not unflyable. Always run the QRH and brief a longer landing roll.'
 )),

-- 8. Abnormal (dual loss)
(@lesson_id, 80, 'abnormal',
 'Dual Failure and Total Hydraulic Loss',
 'If you lose No.1 AND No.2:\n  • No.3 keeps powering the elevators — pitch control survives\n  • Roll and yaw degrade — manual reversion on some surfaces\n  • Landing gear must be extended with the EMERGENCY HAND PUMP\n  • Braking is by accumulator pressure only (limited applications)\n\nThis is a PAN-PAN minimum, MAYDAY if performance, weather, or fuel is marginal. Land at the nearest suitable airport, longer runway, full crew brief, full ATC notification.',
 'video', '/assets/uploads/hydraulics/dual_failure_brief.mp4',
 'Dual-failure crew briefing video',
 'Dual loss = hand pump + accumulator brakes + nearest suitable.',
 'A dual hydraulic failure escalation is one of the few times the Q400 QRH explicitly authorises the hand pump in flight. Use it.',
 NULL),

-- 9. Operational (real story)
(@lesson_id, 90, 'operational',
 'Real Scenario: Low-Pressure Light at FL250',
 'You are northbound at FL250 over Lake Turkana. PNF says: "HYD 1 PRESS LO" caution. Pressure gauge reads 1800 PSI and dropping. Quantity is dropping too.\n\nYour next 60 seconds:\n  1. PF maintains aircraft control, autopilot stays engaged.\n  2. PNF identifies the failure on the EICAS.\n  3. Both crew confirm — "No.1 hydraulic system failure".\n  4. PNF starts the QRH "HYD 1 PRESS LO" checklist.\n  5. PF briefs the diversion field, fuel state, weather.\n  6. Once stabilised, declare PAN-PAN, request descent, plan landing on No.2 + No.3.',
 'image', '/assets/uploads/hydraulics/scenario_map.png',
 'Lake Turkana diversion scenario map',
 'Aviate → Navigate → Communicate. Always in that order, even on a hydraulic.',
 'A leak that drops quantity is not coming back. Do not wait for the pressure to fully bottom out before committing to a divert.',
 NULL),

-- 10. QRH connection
(@lesson_id, 100, 'qrh',
 'QRH Connection: HYD 1 PRESS LO',
 'The QRH "HYD 1 PRESS LO" non-normal calls for:\n  • HYD 1 PUMP — OFF\n  • Affected services — checked / available\n  • Land at nearest suitable\n\nThere is no memory item for a single-system loss — you have time. The memory items live in the dual-failure procedures. Memorise the dual-loss steps, not the single-loss steps.\n\nWhen you brief, brief the FULL chain: which surfaces are still on which system, which brakes you are using, and your gear-down plan.',
 'image', '/assets/uploads/hydraulics/qrh_hyd1_press_lo.png',
 'QRH HYD 1 PRESS LO page',
 'Single-loss = no memory items, run the book. Dual-loss = memory items first.',
 'Before every flight, fingertip-locate the HYD pages in your QRH. You should be able to find them eyes-closed in turbulence.',
 NULL),

-- 11. Scenario (final gate)
(@lesson_id, 110, 'scenario',
 'Captain Decision: Both Mains Lost, Approaching the FAF',
 'Setup: 4nm from the FAF, both No.1 and No.2 have failed in the last 6 minutes. Gear is up. You are configured clean. The hand pump is available, No.3 is supporting elevators. Weather is VFR, runway is 2500m, dry, no traffic.\n\nThe FO asks: "Captain, what do we do about the gear?"',
 'animation', '/assets/uploads/hydraulics/dual_failure_approach.json',
 'Approach animation: dual-fail gear extension scenario',
 'Hand pump = early. Gear-down with hand pump takes time. Start now, not later.',
 'When in doubt, lower the gear early and slow the approach. You can always go around. You cannot land without gear.',
 JSON_OBJECT(
   'prompt', 'You have lost both No.1 and No.2 hydraulic systems on a Q400. Gear is up, approaching the FAF. What is the correct sequence?',
   'options', JSON_ARRAY(
     'Continue the approach as planned — gear will extend on No.3',
     'Go around immediately, declare MAYDAY, request a hold to run the emergency hand-pump gear extension',
     'Land gear-up — the hand pump is unsafe in flight',
     'Use crossfeed from No.3 to extend the gear'
   ),
   'correct_index', 1,
   'explanation', 'No.3 only feeds the elevators — never the gear. The hand pump is the only way to extend the gear with both mains failed, and pumping takes real time and effort. The right move is to abandon the approach, declare, get vectors for time and space, run the gear extension, then re-approach. Never accept a hurried gear-down on a hand pump.'
 )),

-- 12. Revision
(@lesson_id, 120, 'revision',
 'Lesson Recap: Hydraulics in 60 Seconds',
 'Recap:\n  • Three main systems (No.1, No.2, No.3) at 3000 PSI + one emergency hand pump.\n  • No.1 + No.2 do flight controls, gear, steering, brakes. No.3 does elevators only.\n  • Fluid is MIL-H-46000 phosphate ester — fire-resistant but aggressive.\n  • Single failure = QRH, no memory items, plan for degraded landing.\n  • Dual failure = memory items, hand-pump gear, accumulator brakes, nearest suitable.\n  • In a dual failure, lower gear early — the hand pump takes time and effort.\n\nClick Next to mark this lesson complete and update your progress.',
 'none', NULL, NULL,
 '3 + E = 3000.   F-L-B = jobs.   F-I-R-E = fluid.   No.3 = elevators only.',
 'These four mnemonics will get you through any hydraulic question on a recurrent or line check. Drill them.',
 NULL);

-- Re-apply difficulty visibility after reseed (Phase 2 columns).
-- Keeps Beginner mode focused on foundations; Intermediate adds abnormals
-- and QRH; Advanced sees everything.
-- Only runs if the columns exist (i.e. 2026_04_29_slide_difficulty.sql
-- migration has been applied). The IGNORE keyword on UPDATE silences
-- "unknown column" if the columns aren't there yet.
UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');

UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND title = 'The Fluid: MIL-H-46000 Phosphate Ester';

UPDATE IGNORE lesson_slides
   SET show_beginner     = 0,
       show_intermediate = 0
 WHERE lesson_id = @lesson_id
   AND title = 'Captain Decision: Both Mains Lost, Approaching the FAF';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
