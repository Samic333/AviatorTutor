-- =============================================================================
-- AviatorTutor — Phase 2: ATA 21 Air Conditioning & Pressurization
-- 14-slide interactive lesson for the ECS + Pressurization overview.
--
-- Idempotent. Run AFTER lesson_air_cond_press.sql.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'air-cond-press' LIMIT 1);
SET @lesson_id := (
    SELECT id FROM lessons
    WHERE system_id = @system_id
      AND slug = 'air-cond-press-overview'
    LIMIT 1
);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides
    (lesson_id, sort_order, slide_type, title, body,
     media_type, media_url, media_alt, key_point, ops_relevance, question)
VALUES
-- 1. Intro
(@lesson_id, 10, 'intro',
 'The Cabin You Cannot Open at FL250',
 'At FL250 the outside air is minus 35 degrees, the partial pressure of oxygen will not keep a pilot conscious for more than a few minutes, and the door stays closed because the cabin is at roughly two PSI above ambient. Everything that keeps that situation tolerable is what this lesson covers. ECS supplies the air, Pressurization keeps it at a survivable pressure, and the same controller schedules both. By the end of the deck you should be able to draw the architecture from memory and brief any abnormal in this system to your FO without opening the QRH.',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'ECS + pressurization architecture overview diagram',
 'Two ACMs supply, aft outflow regulates, two safety valves catch the misses.',
 'In an FAA-style oral, ECS / pressurization is the single most asked aircraft-systems topic. Confidence here drives the tone of the rest of the check.',
 NULL),

-- 2. Concept — two halves
(@lesson_id, 20, 'concept',
 'Two Halves, One Source — How ECS and Pressurization Connect',
 'On the Q400, ECS and Pressurization are not separate systems with separate plumbing. The same bleed-air feed that conditions the cabin also pressurises it. ECS supplies (packs in, conditioned air out). Pressurization regulates (outflow valve out, set differential maintained). The Cabin Pressure Controller (CPC) is the brain that ties them together — it watches aircraft altitude, target altitude, take-off vs flight vs descent state, and modulates the aft outflow valve to schedule cabin altitude.',
 'diagram', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Block diagram: bleed source → ACMs → cabin → outflow valve',
 'Source feeds both. Outflow regulates. CPC schedules.',
 'When you brief a "loss of one pack" abnormal, the impact on pressurisation is automatic — you fall to single-pack 70% flow, the CPC adapts, you keep flying. They are linked but the system handles the link for you.',
 NULL),

-- 3. Concept — ACM architecture
(@lesson_id, 30, 'concept',
 'Two ACMs + One Dual Heat Exchanger',
 'There are TWO air-cycle machines on the Q400. They share a SINGLE primary heat exchanger and a SINGLE secondary heat exchanger. That is the design quirk. Two ACMs gives you redundancy — lose one, the other keeps you in the air. Sharing one large heat exchanger gives you weight savings AND access to a much larger heat-rejection surface than two separate small ones would. Both ACMs live in the aft equipment bay (the unpressurised aft fuselage section we covered in Aeroplane General).',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Two ACMs sharing dual heat exchanger schematic',
 'ACM-2-DUAL-HX. Two machines, one big shared exchanger.',
 'A line crew can almost never tell from the cockpit which ACM is doing the work — only the flow rate. The redundancy logic is automatic. Trust the indications.',
 JSON_OBJECT(
   'prompt', 'How many air-cycle machines (ACMs) and how many heat exchangers does the Q400 ECS use?',
   'options', JSON_ARRAY(
     'One ACM, one primary + one secondary heat exchanger',
     'Two ACMs, two complete sets of heat exchangers',
     'Two ACMs, one shared primary + one shared secondary heat exchanger',
     'Two ACMs and a single combined heat exchanger'
   ),
   'correct_index', 2,
   'explanation', 'TWO ACMs share ONE primary + ONE secondary heat exchanger. The shared dual-heat-exchanger design is the Q400 weight-saving signature.'
 )),

-- 4. System — air sources
(@lesson_id, 40, 'system',
 'Air Sources — Engine Bleed, APU, and the Ground Connection',
 'Three normal sources feed the packs. No.1 engine bleed, No.2 engine bleed, and the APU bleed. A fourth source for ground operations: an ECS Ground Air connection on the right side of the aft fuselage at station X 860.00, a latched-door, 8-inch industry-standard fitting. A flapper-style check valve at the ground-air junction prevents reverse flow if the cabin is pressurised. BLEED switch positions for engines: 1 / 2 / OFF — selecting starts or stops bleed flow from the chosen engine to the packs. APU bleed flow is not controlled by the BLEED selector knob — it follows an internal ECU schedule.',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Air sources block diagram with engine bleed and APU',
 '3 sources + 1 ground. APU has its own internal flow schedule.',
 'On a long ground-hold in a hot apron with both engines off, the APU is what keeps the cabin tolerable. Brief APU operation explicitly during pre-flight planning in summer.',
 NULL),

-- 5. System — BLEED selector
(@lesson_id, 50, 'system',
 'BLEED Selector — MIN, NORM, MAX, and Why MIN Is Sacred for Takeoff',
 'The BLEED rotary selector is a three-position knob: MIN, NORM, MAX. MIN gives the engines maximum thrust for takeoff at the cost of reduced cabin flow — and on the Q400, MIN is the ONLY legal selection for takeoff. With BLEED switches ON and a take-off rating set, the ED shows BLEED in white when MIN is selected. Pick NORM or MAX for takeoff and the ED indicates BLEED in amber as a caution; on MTOP the rating display itself changes to MCP. The system enforces "MIN for takeoff" through its instrumentation.',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'BLEED selector diagram with ED indications',
 'MIN-FOR-TO. NORM/MAX in TO = BLEED amber on ED.',
 'Confirm BLEED MIN before line-up every time. It is in the takeoff briefing for a reason — performance margins are calculated on MIN bleed.',
 JSON_OBJECT(
   'prompt', 'Which BLEED selector position is permitted for take-off on the Q400?',
   'options', JSON_ARRAY(
     'NORM only',
     'MAX for shorter runways, NORM for longer',
     'MIN only — NORM and MAX trigger amber BLEED indication',
     'Any position; the BLEED selector is independent of takeoff'
   ),
   'correct_index', 2,
   'explanation', 'MIN is the only legal take-off setting. The ED shows BLEED amber if NORM or MAX is selected with a take-off rating set.'
 )),

-- 6. System — recirc + avionics cooling
(@lesson_id, 60, 'system',
 'Recirc and Avionics Cooling — Two Loops You Should Know About',
 'The recirc fan pulls cabin air through a recirculation filter mounted behind the AFT class-C baggage compartment, then mixes it with pack-conditioned air on the way back to the cabin. Selecting RECIRC starts the fan at low speed (to limit current inrush) then auto-switches to high speed. The ECU adapts fan speed based on conditions. Separately, the avionics cooling system runs three fans (Pilot side, Copilot side, Standby) on an extraction-type loop — pulling heat off the avionics rack, the five LCD instrument displays, and the wardrobe rack. Avionics cooling is fully automatic: no pilot action in any phase.',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Recirc + avionics cooling distribution diagram',
 'Recirc filter = AFT class-C baggage. Avionics cooling = 3 fans, fully auto.',
 'A musty cabin smell on a hot turn after parking is often a recirc-filter saturation event. Note in the tech log; engineering can pull and inspect.',
 NULL),

-- 7. System — pressurization architecture
(@lesson_id, 70, 'system',
 'Pressurization — Aft Outflow + Two Safety Valves',
 'Three valves regulate cabin pressure. The aft outflow valve is the boss — it modulates open and closed under CPC command to schedule cabin altitude. The aft safety valve, also on the aft pressure dome, opens on the ground when at least one engine is at idle or the APU is running. The forward safety valve, on the forward pressure bulkhead, is for emergency operations only — controlled by the FORWARD SAFETY VALVE selector on the copilot''s side console (NORMAL / OPEN). Note: the forward safety valve cannot be modulated by the selector — only fully opened. For finer control there is also the FWD OUTFLOW knob on the CPC panel that bleeds pressure progressively.',
 'animation', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Pressurization valve diagram showing all three valves',
 'Aft outflow = boss. Two safety valves = backup. FWD safety = on/off only.',
 'Briefing pressure abnormals: if the aft outflow stops responding, the FWD OUTFLOW knob is your fine control; the FORWARD SAFETY VALVE selector is your "dump it now" lever.',
 NULL),

-- 8. Normal op — pressure schedule
(@lesson_id, 80, 'normal_op',
 'The Pressure Schedule — Pre-Pressurise, Climb, Cruise, Descent, Land',
 'On the ground with power levers below 60 degrees, aft outflow is FULLY OPEN — cabin equals ambient. As power levers go above 60 degrees, the CPC pre-pressurises the cabin to 400 ft below take-off altitude at -300 fpm — a deliberate small bump downward so that lift-off does not feel like a jolt. After lift-off the CPC keeps the take-off mode for ten minutes (in case of an emergency return that lets you skip re-selecting LDG ALT). In flight the CPC follows its programmed cabin-altitude-versus-aircraft-altitude curve. On descent, a high rate of aircraft descent triggers a cabin-rate-increase sequence. At landing, the CPC bleeds cabin to ambient with both aft outflow and aft safety valve fully open.',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Cabin altitude vs aircraft altitude schedule diagram',
 '60° = trigger. 400 at -300 = pre-pressurise. 10 min TO mode = return window.',
 'Set LDG ALT before pushback. The CPC needs that number for the descent profile to schedule correctly. Forgetting it is a common day-of-flight error.',
 JSON_OBJECT(
   'prompt', 'During the pre-pressurisation sequence on take-off roll, what is the cabin pressurised TO and AT WHAT RATE?',
   'options', JSON_ARRAY(
     'To take-off altitude at +500 fpm',
     'To 400 ft below take-off altitude at -300 fpm',
     'To 1000 ft above field elevation at -200 fpm',
     'To the LDG ALT setting at automatic rate'
   ),
   'correct_index', 1,
   'explanation', 'Pre-pressurisation puts the cabin at 400 ft BELOW the take-off altitude at -300 fpm. Done deliberately so lift-off is smooth, not abrupt.'
 )),

-- 9. Normal op — limits + indications
(@lesson_id, 90, 'normal_op',
 'Limits and Cockpit Indications',
 'The numbers you must memorise. Maximum cabin-to-ambient differential: 5.5 PSI. CABIN PRESS warning light comes on when cabin altitude exceeds 9,800 ft. On the ground with power levers at flight idle or low power settings, the CPC holds aft outflow and aft safety valve fully open. An anti-suckback feature limits ground negative-differential to 0.5 psi (external cannot exceed internal by more than half a PSI). The Pressurization Indicator Panel shows: cabin altitude, MAX PRESS placard at 5.5 PSI, DIFF PSI gauge, cabin rate-of-change in fpm × 1000. Set LDG ALT and MAN DIFF on the CPC panel pre-flight.',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Pressurization indicator panel layout',
 'MAX 5.5 PSI · WARN at 9,800 ft · GROUND 0.5 psi suckback limit.',
 'Watch DIFF PSI on every cruise scan. Slow drift toward 5.5 = an outflow-valve issue developing; a sudden drop = a leak or a valve commanding wrongly.',
 NULL),

-- 10. Abnormal — pack failure
(@lesson_id, 100, 'abnormal',
 'Pack Failures — FCSOV Defaults, Single ECU, and Emergency Ram Air',
 'The Flow Control Shut-Off Valve (FCSOV) defaults pneumatically to OPEN if a single ECU channel fails — that is intentional, so flow continues automatically. If BOTH digital ECU channels lose electrical power or fail, the FCSOV defaults to CLOSED and ECS operation stops. The ACMs shut off, conditioned-air flow ends, and the cabin must be ventilated using emergency ram air (covered separately under unpressurised flight). When in single-pack ops, the system runs at 70% flow and the recirc fan runs at low speed. With both packs running, the system runs at full performance and the recirc fan is high speed.',
 'video', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Pack failure flow chart',
 'Single channel fail = FCSOV opens (good). Dual channel fail = FCSOV closes (ram air time).',
 'Dual ECU channel failure is a "land at the nearest suitable airport" event. Until you do, accept ram-air ventilation, descend to a breathable altitude, and brief crew + cabin.',
 JSON_OBJECT(
   'prompt', 'Both digital channels of the ECU fail in flight. What happens to the pack inlet FCSOV, and what is the consequence?',
   'options', JSON_ARRAY(
     'Defaults to OPEN; cabin temperature uncontrolled but flow continues',
     'Defaults to CLOSED; ECS stops; emergency ram-air ventilation needed',
     'Stays at last commanded position; minor flow change',
     'Cycles between open and closed every 30 seconds until reset'
   ),
   'correct_index', 1,
   'explanation', 'Single channel failure → FCSOV defaults OPEN (continued ops). DUAL channel failure → FCSOV defaults CLOSED, ECS stops, must use emergency ram-air ventilation while descending and diverting.'
 )),

-- 11. Abnormal — pressurization failure
(@lesson_id, 110, 'abnormal',
 'Pressurization Failure — CABIN PRESS Light, Manual Mode, Forward Safety Valve',
 'CABIN PRESS warning at >9,800 ft cabin altitude is a hard caution. Crew memory items: oxygen masks 100%, descend immediately, advise ATC, run the QRH. If the AUTO mode itself has failed, switch the AUTO-MAN-DUMP toggle to MAN — DECR opens the outflow (cabin altitude rises), INCR closes it (cabin altitude falls). The cabin altitude, DIFF PSI, and rate gauges must be watched continuously in MAN. If the aft outflow is stuck or unserviceable, the FWD OUTFLOW knob bleeds pressure progressively through the forward safety valve. For a fast dump, the FORWARD SAFETY VALVE selector on the copilot''s side console — lift the guard, set OPEN — opens the FSV fully (cannot modulate; only fully open).',
 'video', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Pressurization failure decision tree',
 'CABIN PRESS @ 9,800 = mask + descend. MAN mode if AUTO dies. FSV selector for fast dump.',
 'Cabin pressure abnormals are TIME critical above FL200. Memory items first, descent second, QRH third. Do not invert this order.',
 JSON_OBJECT(
   'prompt', 'In manual mode, holding the AUTO-MAN-DUMP toggle to DECR will:',
   'options', JSON_ARRAY(
     'Close the outflow valve, cabin pressure increases, cabin altitude decreases',
     'Open the outflow valve, cabin pressure decreases, cabin altitude increases',
     'Open the safety valves only',
     'Reset the controller to AUTO mode'
   ),
   'correct_index', 1,
   'explanation', 'DECR opens the aft outflow valve — cabin pressure drops, cabin altitude rises. INCR is the opposite (close outflow, cabin pressure rises, cabin altitude falls). Memorise: DECR = decrease cabin pressure = increase cabin altitude.'
 )),

-- 12. QRH connection — cabin altitude warning
(@lesson_id, 120, 'qrh',
 'QRH Connection: CABIN ALTITUDE WARNING (>9,800 ft)',
 'The CABIN ALTITUDE WARNING QRH non-normal is a memory-item event. Sequence: oxygen masks ON, 100%, regulator EMERGENCY if needed; crew comms confirmed; aircraft to MAX RATE descent; transponder 7700; advise ATC; passenger oxygen drop confirmed; run the QRH for the secondary actions (pack/CPC/safety-valve checks); land at the nearest suitable airport. The QRH gives explicit guidance on whether to continue flight at FL100 or below if cabin altitude can be re-established at safe levels — but the default decision is to land.',
 'image', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'QRH page: CABIN ALT WARN procedure',
 'Mask · 100% · Emergency descent · 7700 · Nearest suitable.',
 'The first thirty seconds of a cabin-altitude warning are everything. Memorise the memory items and rehearse them in the sim. You will not have time to read the QRH at FL250 with mask fogging.',
 NULL),

-- 13. Scenario — captain decision
(@lesson_id, 130, 'scenario',
 'Captain Decision: Slow DIFF PSI Drop, FL220, Two Hours into a Sector',
 'Setup: cruising FL220, two hours since departure, two more to destination. PNF on a routine cruise scan notices DIFF PSI is at 3.8 and trending down — was 4.4 ten minutes ago. Cabin altitude is climbing slowly (currently 5,100 ft, was 4,200 ft). No CABIN PRESS warning yet. AUTO mode still selected. No other cautions.\n\nQuestion: continue, descend, or divert? You have a developing leak or outflow-valve issue. Decision logic: descend NOW to a safer cabin altitude (FL150 or below), advise ATC, prepare crew for possible mask donning, brief cabin, and divert to the nearest suitable. Do not wait for the warning light. A trend that says the system is losing ground is the leading indicator — the warning light is the lagging indicator.',
 'animation', '/assets/aircraft/q400/air-cond-press-flow.svg',
 'Slow leak scenario decision tree',
 'Trend down on DIFF PSI = act on trend, not on warning. Descend early.',
 'Cruise scan discipline catches these. The warning light is for the day you missed the scan. Brief the FO to call any DIFF PSI drift greater than 0.5 PSI from the last scan.',
 JSON_OBJECT(
   'prompt', 'At FL220 you observe DIFF PSI dropping from 4.4 to 3.8 over ten minutes with no warning lights. Best action?',
   'options', JSON_ARRAY(
     'Continue at altitude — no warning, no action required',
     'Descend immediately to FL150 or below, advise ATC, prepare cabin, divert nearest suitable',
     'Switch to manual mode and adjust the aft outflow',
     'Recycle the bleed switches and continue'
   ),
   'correct_index', 1,
   'explanation', 'Trend matters more than thresholds. A losing pressurisation system at altitude is a captain-action event before the warning fires. Descend early to a safer cabin altitude, advise ATC, divert.'
 )),

-- 14. Revision — recap
(@lesson_id, 140, 'revision',
 'Lesson Recap: ECS + Pressurization in 60 Seconds',
 'Recap the foundation:\n  • ECS = two ACMs sharing one primary + one secondary heat exchanger, in the unpressurised aft fuselage.\n  • Air sources: No.1 bleed, No.2 bleed, APU. Ground A/C connection at fuselage station X 860.00 right side.\n  • BLEED selector MIN is the ONLY legal take-off setting. NORM/MAX = amber BLEED on ED.\n  • Single-pack mode: 70% flow, recirc fan low. Dual-pack mode: full flow, recirc fan high.\n  • Single ECU channel fail = FCSOV defaults OPEN (continued ops). Dual fail = FCSOV CLOSED, ECS stops, ram-air ventilation.\n  • Pressurisation: aft outflow = boss; aft safety + forward safety = backup. FSV cannot be modulated, only fully opened.\n  • Limits: max 5.5 PSI differential, CABIN PRESS warn at 9,800 ft, ground anti-suckback at 0.5 psi.\n  • Schedule: pre-pressurise to 400 ft below TO alt at -300 fpm. CPC stays in TO mode 10 minutes after lift-off.\n  • Cabin altitude warning = mask, descend, 7700, divert. Memory items, then QRH.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'ACM-2-DUAL-HX · MIN-FOR-TO · 60° · 400-AT-300 · 10-MIN · 5-5-9-8 · 0-PT-5',
 'These seven mnemonics carry you through any ECS or pressurisation oral. Drill them on every recurrent.',
 NULL);

-- Re-apply difficulty visibility after reseed.
UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');

-- Treat limits-and-indications as intermediate-plus (numbers detail).
UPDATE IGNORE lesson_slides
   SET show_beginner = 0
 WHERE lesson_id = @lesson_id
   AND title = 'Limits and Cockpit Indications';

-- Captain Decision is advanced-only.
UPDATE IGNORE lesson_slides
   SET show_beginner     = 0,
       show_intermediate = 0
 WHERE lesson_id = @lesson_id
   AND title = 'Captain Decision: Slow DIFF PSI Drop, FL220, Two Hours into a Sector';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
