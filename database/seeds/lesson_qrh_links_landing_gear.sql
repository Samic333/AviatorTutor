-- =============================================================================
-- AviatorTutor — Phase 11: ATA 32 Landing Gear — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'landing-gear' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'landing-gear-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'LDG GEAR INOP — Alternate Extension',
 'Caution: hydraulic sequencing valve failure or PSEU loss of control. Sequence (8 steps): (1) LANDING GEAR INHIBIT switch (overhead, guarded) to INHIBIT — isolates all hyd from gear. (2) Open MAIN LANDING GEAR ALTERNATE RELEASE door (overhead) — opens bypass valve mechanically. (3) Pull MAIN L/G RELEASE handle fully — releases main doors and uplocks; mains free-fall. (4) Open LANDING GEAR ALTERNATE EXTENSION door (floor) — opens MLG alt selector valve. (5) Pull NOSE L/G RELEASE handle fully — nose gear free-falls. (6) If MLG not down/locked, hand-pump (handle behind copilot seat into floor socket) until handle is stiff. (7) ALTERNATE DOWNLOCK VERIFICATION switch to AFT — three green floor lights confirm. (8) BOTH doors LEFT FULLY OPEN after extension.',
 0,
 'Reflexive alternate-extension is the difference between a controlled gear-down and a gear-up landing. Drill in the sim. The temptation is to skip INHIBIT — don''t.',
 'LDG GEAR INOP caution illuminated; gear handle DN does not produce three green.',
 'INHIBIT · MAIN RELEASE · NOSE RELEASE · PUMP · 3 GREEN FLOOR · DOORS OPEN.',
 10),

(@lesson_id, NULL,
 'GEAR INDICATION FAILURE',
 'Gear handle DN; gear-down indications not all three green and red unsafe (or vice versa) — possible indication failure or actual gear position issue. Action: climb to a workable altitude (5000+ AGL); set ALTERNATE DOWNLOCK VERIFICATION switch to AFT — three green floor downlock verification lights confirm gear position. If three floor lights illuminate, treat as indication-only failure; continue approach with caution and brief contingency. If floor lights do NOT confirm, run alternate extension. Brief approach for nose-gear or main-gear contingency; declare PAN-PAN; full ARFF; longest available runway.',
 0,
 'Floor lights are your independent confirmation. Don''t descend on a non-three-green panel — climb first, verify via floor lights, then commit.',
 'Mismatch between gear handle position and panel advisory lights; possible amber HANDLE persistent.',
 'CLIMB · FLOOR LIGHTS · INDICATION OR ACTUAL · ALT EXTEND IF NEEDED.',
 20),

(@lesson_id, NULL,
 'NOSE STEERING Caution — Identify Trigger',
 'Two distinct triggers, distinct meanings. (1) SCU fault with STEERING switch ON — system reverts to passive caster (±120°). Use differential braking + power for ground directional control. (2) Hydraulic pressure detected with STEERING switch OFF — unexpected hyd in the steering circuit; possible stuck valve or leak. Investigate; consider towing rather than taxiing. NOTE: if electrical power is removed from the SCU, NO caution illuminates but no steering either. Action: identify which trigger from switch position; run QRH NOSE STEERING; brief approach for differential-brake taxi if SCU fault.',
 0,
 'On the ground at the destination, NOSE STEERING with switch OFF means something is wrong — get a tow rather than taxi.',
 'NOSE STEERING caution illuminated.',
 'SWITCH POSITION · SCU FAULT vs HYD PRESSURE · CASTER OR INVESTIGATE.',
 30),

(@lesson_id, NULL,
 'INBD / OUTBD ANTISKID Caution',
 'ASCU has detected fault on inboard or outboard wheel. Brake pressure is no longer modulated on the affected side. Action: confirm via TEST cycle (caution illuminates 6 sec on ground / 3 sec in air); brief approach for asymmetric or longer landing roll; consider longer runway and earlier braking. With sustained INBD or OUTBD ANTISKID, manual braking discipline matters — don''t over-brake the affected side. Use EMERG BRAKE if anti-skid is needed manually but expect no anti-skid protection in EMERG mode.',
 0,
 'A clean anti-skid fault is a non-emergency. Brief the FO for asymmetric braking. Plan a longer runway if available.',
 'INBD or OUTBD ANTISKID caution illuminated.',
 'CONFIRM · BRIEF · ASYMMETRIC BRAKE · LONGER RUNWAY.',
 40),

(@lesson_id, NULL,
 'PARK BRAKE LOW PRESSURE',
 'Park brake accumulator pressure on PK BRK indicator (PSA on MFD) below the 500 PSI minimum required before engine start. Recharge methods: (1) Hand pump in the right main wheel well — pump until target pressure is reached. (2) Run the SPU + PTU on AC power to repressurise the No.2 hyd system, which recharges the accumulator. After engine start, normal No.2 hyd will keep the accumulator topped up. Note: a fully charged accumulator gives approximately 6 brake applications. After 6 applications without recharging, you''re relying on hand pump or hyd pressure for further holds.',
 0,
 'A flat accumulator on a sloped ramp is a recipe for an unscheduled push. Always check the 500 PSI minimum before engine start.',
 'PK BRK indicator below 500 PSI on the PSA of the MFD.',
 'HAND PUMP · OR SPU+PTU · OR ENGINE START FOR HYD.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
