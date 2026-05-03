-- =============================================================================
-- AviatorTutor — Phase 4 (ATA 23 Communications) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'communications' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'communications-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Communications — ARMS, ARCDU, VHF/HF, Audio, ELT, ACARS, CVR',
     'communications-overview',
     'overview',
     'Two ARCDUs (Audio and Radio Control Display Units) are the cockpit gateway to the entire ARMS package — three VHFs, optional HF, navigation audio, the interphone system, PACIS to the cabin, and a Standby Control & Display Unit for VHF1 if the ARCDUs go away. ELT armed at 5–7 G longitudinal; CVR keeps the last two hours; ACARS lives on the dedicated 3rd VHF.',
     '<p>Communications on the Q400 looks complex on the panel and is actually simple in architecture. The Audio and Radio Management System (ARMS) is the umbrella — Radio Communication and Navigation Management, the Passenger Address and Communication Interphone System (PACIS), and the Audio Integration System (AIS). Two ARCDUs (one captain side, one FO side) are the only cockpit interface you normally need. A backup Standby Control and Display Unit lets VHF1 stay alive if the ARCDU side goes black. The ELT, CVR, FDR are separate but related items every crew member must locate, test, and recognise the indications for.</p>',
     JSON_ARRAY(
       'ARMS = Radio Communication and Navigation Management + PACIS + AIS',
       'TWO ARCDUs (ARCDU 1 captain side, ARCDU 2 FO side) — primary cockpit interface',
       'ACTIVE frequency colour: GREEN (valid data), WHITE (invalid or no data)',
       'PRESET frequency colour: CYAN. When highlighted (black-on-cyan), it becomes the tune window / scratch pad. 5-second timeout if no action',
       'VHF1 has a Standby Control and Display Unit as ARCDU backup — 3-position rotary OFF / ON / TEST',
       'ELT auto-trigger: longitudinal inertia between 5 and 7 G',
       'ELT remote switch: ON (manual override) / ARMED (auto via inertia) / RESET & TEST (re-arm)',
       'ELT monitor light: ONE long flash every 3 seconds = normal; series of short flashes = fault',
       'ACARS uses a dedicated 3rd VHF Comm (Thompson EVR 76) — data only, no voice; 3rd VHF tuned by ACARS itself',
       'CVR is solid-state (SSCVR) and records the last 2 hours of flight crew comms, flight deck area mic, PA, and clock data',
       'PTT mutes flight deck speakers by 6 dB to prevent feedback'
     ),
     JSON_ARRAY(
       'When ATC switches frequency in busy airspace, push the VHF side key to highlight the preset, ROTATE the inner knob to dial, push the side key AGAIN to swap active/preset.',
       'For VHF testing: side key for VHF, then EXP key, then side key adjacent to TEST. Listen for noise — that confirms receiver. Test duration 1 second. No automatic pass/fail indication.',
       'Ground Crew Connection annunciators on the steering handwheel: FWD (amber) = DC external connection; AFT (amber) = REFUEL/DEFUEL panel or aft external connection.',
       'On the Observer Audio Control Panel the transmitter keys are MECHANICALLY INTERLOCKED — only one transmits at a time.',
       'Inadvertent ELT activation in the chocks: monitor light on, occasional radio interference. Reset using RESET & TEST on the ELT remote switch.'
     ),
     JSON_ARRAY(
       'Active frequency colour: students often quote "amber" or "blue". The book is GREEN.',
       'Preset frequency timeout: 5 seconds — students sometimes quote 10 or 15 seconds.',
       'ELT trigger threshold: 5–7 G LONGITUDINAL — not vertical. Direction matters.',
       'CVR duration: 2 hours (last). The solid-state recorder eliminated tape; old jets had 30-minute loops.',
       'ACARS voice capability: NONE. ACARS is data only; the dedicated 3rd VHF has no voice.'
     ),
     10,
     1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'communications-overview';
