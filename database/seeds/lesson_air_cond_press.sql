-- =============================================================================
-- AviatorTutor — Phase 2 (ATA 21 Air Conditioning & Pressurization) lesson row.
-- Creates the overview lesson for system_id = air-cond-press so the
-- subsequent slide / section / flashcard / quiz / qrh / diagram seeds can
-- attach to it.
--
-- Idempotent: re-running wipes the lesson and all CASCADE-deleted children
-- (lesson_sections, lesson_slides, lesson_qrh_links, user_slide_progress).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'air-cond-press' LIMIT 1);

SELECT @system_id AS resolved_system_id;

DELETE FROM lessons
 WHERE system_id = @system_id
   AND slug = 'air-cond-press-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Air Conditioning & Pressurization — Q400 ECS, ACMs, and the Pressure Schedule',
     'air-cond-press-overview',
     'overview',
     'Two ACMs sharing one dual heat exchanger, three air sources (left bleed, right bleed, APU, plus ground), aft outflow valve as the boss with a forward and aft safety valve as the backup. Maximum 5.5 PSI differential, CABIN PRESS warning at 9,800 ft cabin altitude, automatic schedule from take-off through landing. The system you fly behind every day; the one that demands a memory item if it ever lets you down at altitude.',
     '<p>Air Conditioning and Pressurization are bound together on the Q400. The same bleed-air supply that conditions the cabin also pressurises it; the same ECU that opens the pack inlet valve also schedules the outflow. This lesson teaches them as one unit because that is how the airframe operates them.</p><p>The deck below walks the architecture in flow order: source (engine or APU bleed), conditioning (two air-cycle machines + shared heat exchangers), distribution (recirc fan + cabin/flight-deck split), and pressure control (aft outflow valve regulating the schedule, two safety valves catching the abnormals). Then we move into the limits, the warnings, the emergency procedures, and the captain decisions you need to make automatic.</p>',
     JSON_ARRAY(
       'Two air-cycle machines (ACMs) integrated with one primary + one secondary heat exchanger, in the aft equipment bay',
       'Three normal air sources: No.1 bleed, No.2 bleed, APU. Plus a ground A/C connection at fuselage station X 860.00 right side',
       'Maximum cabin-to-ambient differential: 5.5 PSI',
       'CABIN PRESS warning light: cabin altitude above 9,800 ft',
       'Single-pack mode: 70% flow, recirc fan low speed. Dual-pack mode: full performance, recirc fan high speed',
       'BLEED selector MIN required for take-off (only legal selection); NORM and MAX permitted in flight',
       'On the ground (power levers <60°): aft outflow fully OPEN. Above 60°: outflow modulates; pre-pressurise to 400 ft below TO alt at -300 fpm',
       'CPC stays in TO mode for 10 minutes after lift-off to support emergency return without re-selecting LDG ALT',
       'Anti-suckback: ground 0.5 psi differential ceiling so external pressure cannot exceed internal'
     ),
     JSON_ARRAY(
       'Both packs and the avionics cooling fans live in the unpressurised aft fuselage — same place a smoke event in this lesson originates from.',
       'Pack inlet FCSOV defaults to OPEN pneumatically on a single ECU channel failure (continued ops). Dual digital channel loss closes it — ECS stops; emergency ram air ventilation needed.',
       'Forward safety valve has only NORMAL or OPEN. It cannot be modulated by the selector — only fully opened in emergency.',
       'Manual pressurisation mode: AUTO-MAN-DUMP toggle to MAN. DECR opens outflow → cabin altitude increases. INCR closes outflow → cabin altitude decreases.',
       'Avionics cooling has three fans (Pilot, Copilot, Standby) and is fully automatic — never a pilot-action item.',
       'BLEED MIN for takeoff is the ONLY legal selection; on a NORM or MAX takeoff the ED indicates BLEED amber as a caution.'
     ),
     JSON_ARRAY(
       'Max differential: students often quote 8.0 or 8.6 psi (737 numbers). The Q400 number is 5.5 PSI.',
       'CABIN PRESS warning altitude: students often quote 10,000 ft. The Q400 trip is 9,800 ft.',
       'Number of ACMs: it is TWO ACMs sharing ONE dual heat exchanger — not two complete pack modules.',
       'Recirc filter location: behind the AFT class C baggage compartment, NOT in the forward equipment bay.',
       'BLEED setting for takeoff: MIN is the only legal choice. NORM/MAX show BLEED amber on the ED.'
     ),
     10,
     1);

SELECT
    id AS resolved_lesson_id,
    title
FROM lessons
WHERE system_id = @system_id AND slug = 'air-cond-press-overview';
