-- =============================================================================
-- AviatorTutor — Phase 12: ATA 33 Lighting — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'lighting' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'lighting-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Three Categories — Inside, Outside, Emergency',
 'Q400 lighting splits cleanly into three groups. Interior covers the cockpit (panel/instrument 5 VDC variable, dome/storm, utility, map, circuit-breaker) plus cabin (21 overhead + 21 sidewall fluorescent, reading, signs, lavatory). Exterior covers landing (outboard approach + inboard flare), taxi (on the steerable nose gear, gear-down inhibit), position (green/red/white with primary+secondary auto-failover), anti-collision, recognition, inspection, logo. Emergency is a 3-position lever-locked switch (ON/ARM/OFF) feeding the cabin egress chain from battery packs. Two key auto-logic items to remember: NO SMOKING signs auto-illuminate when the gear selector goes to DN, and the taxi light will not come on unless the gear is locked down.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'Q400 lighting overview',
 'Interior · Exterior · Emergency. Two auto-logic items: NO SMOKE on gear DN; TAXI inhibited unless locked.',
 'Lighting questions on a recurrent are usually the small auto-logic items — drill them.',
 NULL),

(@lesson_id, 20, 'concept',
 'Panel Lighting — 5 VDC + Per-Pilot Knobs',
 'Cockpit panel and instrument lighting is variable-intensity 5 VDC. Disc-shaped lamp assemblies sit behind Plexiglas in each panel. The PANEL LIGHTING control panel has four rotary knobs — Overhead Console, Glareshield, Forward Centre Console, Aft Centre Console — each with an OFF detent at the counter-clockwise end and BRT at the maximum. The Glareshield knob doubles as the clock-light control. Two additional per-pilot knobs handle the side panels: <strong>PILOTS FLT PNL knob</strong> drives pilot side console + ICP1 + Standby Instruments. <strong>COPILOTS FLT PNL knob</strong> drives copilot side console + ICP2 + Landing Gear selector panel + GPWS/Hydraulic Control panel. The standby compass light is on a different control — the CAUT/ADVSY LIGHTS DIM/BRT toggle switch.',
 'diagram', '/assets/aircraft/q400/lighting-flow.svg',
 'Panel lighting knob layout + per-pilot zones',
 '5 VDC variable · 4 PANEL knobs + 2 per-pilot · standby compass on CAUT/ADVSY toggle.',
 'On a check question, knowing that COPILOTS FLT PNL drives the GPWS/Hyd panel is a small but real fact.',
 NULL),

(@lesson_id, 30, 'concept',
 'Dome vs Storm — Different Buses, Different Roles',
 'Two distinct flight-deck overhead lighting controls. The <strong>DOME light switch</strong> is two-position; powered from the <strong>BATTERY PWR bus</strong>; operates regardless of BATTERY MASTER position — dome will work at all times when there is battery on board. Use it for general cockpit illumination. The <strong>STORM/DOME switch</strong> is three-position: STORM (storm lights only), STORM/DOME (both), OFF. Storm lights are powered from the <strong>L SECONDARY bus</strong>. Storm lights are bright cockpit lights designed to compensate for night-vision loss after a lightning flash — they over-saturate the eye to prevent the temporary blindness from the flash. Use them only when actually flying through lightning.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'Dome (BATTERY PWR) vs storm (L SECONDARY)',
 'DOME-BATTERY-NO-MASTER · STORM-FLASH-RECOVER.',
 'Storm lights are not for normal flying — they''re bright and uncomfortable. Use only for lightning conditions.',
 JSON_OBJECT(
   'prompt', 'Which bus powers the cockpit DOME light, and what is the special property?',
   'options', JSON_ARRAY(
     'L Secondary bus; only with BATTERY MASTER ON',
     'BATTERY PWR bus; operates without BATTERY MASTER ON',
     'Right Main DC bus; only with engines running',
     'Standby ESS bus; emergency only'
   ),
   'correct_index', 1,
   'explanation', 'DOME light on BATTERY PWR bus. Operates regardless of BATTERY MASTER. Storm lights on L SECONDARY. Mnemonic: DOME-BATTERY-NO-MASTER.'
 )),

(@lesson_id, 40, 'system',
 'Cabin — 21 Overhead + 21 Sidewall',
 'The Q400 cabin has <strong>21 fluorescent overhead lights</strong> running the length of the ceiling and <strong>21 fluorescent sidewall lights</strong> under the valance on both sides — symmetric numbers. Both banks are controlled from the forward cabin attendant panel via membrane switches: CABIN OVERHD and CABIN SIDEWALL. Two dimming membrane switches (DIM OVERHD, DIM SIDEWALL) provide independent dimming. There is also a single dimmer control on the C/A panel that dims all main cabin lights together — useful for the top-of-descent transition to night arrival. Reading lights are 2 per Passenger Service Unit (PSU) with a pushbutton each; gated by the PSU ON/OFF membrane switch. Plus information signs on each PSU and at the front: NO SMOKING, FASTEN BELTS, plus RETURN TO SEAT in the lavatory.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'Cabin lighting: 21 overhead + 21 sidewall + reading + signs',
 '21-OVER-21-SIDE. F/A panel membrane controls + single cabin dimmer.',
 'For a night arrival, the single dimmer is your friend — dim main cabin lights at 10000 ft for passenger night-vision adaptation.',
 NULL),

(@lesson_id, 50, 'system',
 'NO SMOKING + FASTEN BELTS — Auto-Logic',
 'Information signs use a clean two-switch model with one auto-logic surprise. <strong>FASTEN BELTS switch</strong> (cockpit panel): FASTEN BELTS position illuminates the FASTEN SEAT BELTS signs at the front and on each PSU; sounds a low chime through the PA; and illuminates the lavatory <strong>RETURN TO SEAT</strong> sign. <strong>NO SMOKING switch</strong> (cockpit panel): NO SMOKING position illuminates the no-smoking signs and sounds a low chime through the PA. <strong>The auto-logic surprise:</strong> NO SMOKING signs ALSO illuminate AUTOMATICALLY when the landing gear selector is moved to the DN position — even if the cockpit switch is OFF. This serves as a pre-landing reminder for the cabin. The NO SMOKING signs go off again only if the cockpit switch is OFF AND the gear selector is not at DN.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'NO SMOKING auto-logic with gear DN',
 'NO-SMOKE-GEAR-DN. FASTEN BELTS = chime + lavatory RETURN TO SEAT.',
 'When the cabin attendants see NO SMOKING illuminate without an announcement, they know the gear is coming down — a useful unspoken cue.',
 JSON_OBJECT(
   'prompt', 'Which Q400 information sign comes on automatically when the landing gear selector is moved to DN?',
   'options', JSON_ARRAY(
     'FASTEN SEAT BELTS',
     'NO SMOKING',
     'RETURN TO SEAT',
     'OCCUPIED'
   ),
   'correct_index', 1,
   'explanation', 'NO SMOKING signs auto-on with gear selector at DN. FASTEN BELTS is cockpit-switch only. Mnemonic: NO-SMOKE-GEAR-DN.'
 )),

(@lesson_id, 60, 'system',
 'Landing Lights — Outboard Approach, Inboard Flare',
 'Each wing has TWO landing lights on the leading edge, just outboard of the engine nacelle — total of 4 across both wings. The <strong>OUTBOARD lights are APPROACH lights</strong>, designed for general lighting during the approach. The <strong>INBOARD lights are FLARE lights</strong>, angled DOWNWARD specifically to light the runway during the flare manoeuvre. So during a night approach you turn approach + flare on at the same time, but they serve different visual roles: approach lights illuminate ahead, flare lights illuminate the touchdown zone for the flare cue. Some operators leave only approach on for the descent and add flare on short final.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'Outboard approach + inboard flare',
 'OUT-APPROACH-IN-FLARE. 4 lights total. Different angles, different roles.',
 'On a black-hole approach the flare lights make all the difference — they show you exactly where the wheels are going.',
 NULL),

(@lesson_id, 70, 'system',
 'Position Lights — Primary + Secondary Failover',
 'Q400 position lights use a clean dual-redundant architecture. Three locations: <strong>GREEN</strong> on the right wing tip (transparent), <strong>RED</strong> on the left wing tip (transparent), <strong>WHITE</strong> at the aft end of the vertical stabiliser bullet fairing. Each location has TWO lights: a primary and a secondary. When the POSN switch is set to POSN, all primary AND secondary lights illuminate together. After approximately ONE SECOND, the secondary lights go off but stay <strong>ARMED</strong> via an electronic switch unit. If a primary fails at any time, the related armed secondary automatically illuminates. Result: a single bulb failure gives no visible loss to ATC or other traffic — the secondary picks up. The taxi light, by the way, is on the steerable section of the nose gear and will NOT illuminate if the gear is not locked down — so you can''t accidentally taxi-light in flight.',
 'video', '/assets/aircraft/q400/lighting-flow.svg',
 'Position lights primary+secondary auto-failover',
 'GREEN-RIGHT-RED-LEFT-WHITE-AFT · PRI-SEC-1-SEC-ARM · TAXI-LOCK-DOWN.',
 'Position-light failover happens silently — you never see a single-bulb failure on the panel. Maintenance finds it on inspection.',
 JSON_OBJECT(
   'prompt', 'When the POSN switch is set to POSN, what happens to the secondary position lights?',
   'options', JSON_ARRAY(
     'Secondary lights stay off; they only illuminate via primary failure',
     'All primary AND secondary illuminate together; after ~1 second, secondary go off but stay ARMED. Auto-illuminate if primary fails',
     'Secondary lights illuminate only on the ground',
     'Secondary lights flash; primary stay solid'
   ),
   'correct_index', 1,
   'explanation', 'Both illuminate; after ~1 sec secondary go off but armed. Primary failure → secondary auto-on. Mnemonic: PRI-SEC-1-SEC-ARM.'
 )),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight — EMER LIGHTS ARM, Panel Test',
 'Pre-flight is brief but disciplined. (1) <strong>EMER LIGHTS to ARM</strong>: lever-locked 3-position switch. ARM is the normal flight setting — emergency lights auto-illuminate on AC power loss without crew action. (2) Test cockpit panel knobs: rotate Overhead, Glareshield, Fwd Centre, Aft Centre, PILOTS FLT PNL, COPILOTS FLT PNL through OFF→BRT to verify each zone responds. (3) Test exterior lighting per company SOP: position lights, anti-collision, recognition. (4) Brief F/A on cabin lighting plan. (5) Confirm forward boarding lights work when door is open + power available. (6) Verify dome light + storm/dome group OFF for cruise.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'Pre-flight lighting checklist',
 'EMER LIGHTS ARM · panel knobs cycle · ext lights ON · F/A briefed.',
 'Skipping the EMER LIGHTS ARM step is a common pre-flight skip with significant consequences in a real evacuation — make it a touch-and-confirm.',
 NULL),

(@lesson_id, 90, 'normal_op',
 'Cabin Sequence — Boarding to Cruise to Approach',
 'Cabin lighting sequence by phase: <strong>Boarding:</strong> CABIN OVERHD + CABIN SIDEWALL on; BOARDING lights on; reading lights enabled via PSU ON. <strong>Pre-departure:</strong> FASTEN BELTS on (cockpit) + chime; cabin announcement; F/A signal. <strong>Climb to cruise:</strong> after 10000 ft, F/A may dim cabin via single dimmer for night sectors. <strong>Cruise:</strong> reading lights at passenger discretion; FASTEN BELTS off if smooth. <strong>Top-of-descent:</strong> FASTEN BELTS on (cockpit, with chime). Cabin lighting up to full or per F/A. <strong>Approach:</strong> NO SMOKING auto-on as gear selector to DN. Cabin briefing for landing per F/A. <strong>Touchdown + taxi:</strong> taxi light on automatically once gear locked. Cabin lighting up to full per F/A. <strong>Door open:</strong> boarding lights resume.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'Cabin lighting sequence by flight phase',
 'BOARD · CRUISE · DESCENT · APPROACH · TAXI. Dimmer for night transitions.',
 'Coordinate cabin lighting with the F/A on every leg — it''s their show, but coordination matters for night arrivals.',
 NULL),

(@lesson_id, 100, 'abnormal',
 'Position Light Failure — Silent Failover',
 'A primary position light bulb burns out in cruise. The related secondary, which has been armed since ~1 second after POSN switch ON, automatically illuminates. <strong>You see nothing on the cockpit panel</strong> — there is no caution. ATC and other traffic see no change. Discovery happens on the next pre-flight inspection or maintenance check. If BOTH primary AND secondary fail on the same side, you''ve lost a position light visible to other traffic — required equipment for night ops. Document for maintenance; consider deferring per MEL on a day flight, divert plan on night flight. The redundancy is exactly the point: a single bulb failure costs you nothing operationally.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'Position light failover — silent recovery',
 'PRI-SEC-1-SEC-ARM. Single failure: silent. Both fail: required equipment.',
 'Position lights are a perfect example of redundancy that works invisibly. The pilot never knows about a single failure.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'AC Power Loss — EMER LIGHTS Auto-On',
 'When EMER LIGHTS is at ARM and normal AC power is lost (e.g. dual generator failure), the emergency lighting system automatically illuminates from the emergency battery packs. The cabin egress chain — ceiling lights, reflective floor markings, locator signs, exit signs, an egress light at each passenger exit — all come on without crew action. Cabin attendants see the cabin transition and follow their procedures. Cockpit-side: dome light remains powered (BATTERY PWR bus, independent of AC). Storm lights are gone (L SECONDARY bus dependent on AC). The EMER LIGHTS feature is your evacuation lighting plan — it works without any crew action provided the switch was at ARM.',
 'video', '/assets/aircraft/q400/lighting-flow.svg',
 'EMER LIGHTS ARM auto-on on AC loss',
 'EMER-3-POS-ARM. Battery packs power egress chain. Dome remains; storm gone.',
 'A real AC loss in flight: cabin already has emergency lighting before you''ve briefed the descent. That''s the point of ARM.',
 JSON_OBJECT(
   'prompt', 'In flight, all AC power is lost. With the EMER LIGHTS switch at ARM, what happens to the cabin emergency lighting?',
   'options', JSON_ARRAY(
     'Cabin emergency lights stay off — crew must select ON manually',
     'Cabin emergency lights AUTO-ILLUMINATE from emergency battery packs without crew action',
     'Only cockpit dome light works — cabin remains dark',
     'EMER LIGHTS at ARM is no different from OFF; ON is needed'
   ),
   'correct_index', 1,
   'explanation', 'ARM = auto-illuminate on AC power loss. Battery packs power the egress chain (ceiling, floor markings, locator/exit signs, egress lights). No crew action needed. Mnemonic: EMER-3-POS-ARM.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: Lighting Non-Normals',
 'Q400 QRH non-normals for lighting cluster into five groups. (1) Single panel light failure: defer per MEL. (2) Position light primary fail: secondary auto-on, no crew action; document. Both fail: required equipment for night ops, plan accordingly. (3) Storm/dome light fail: L SECONDARY bus issue — cross-reference with electrical. (4) Cabin information sign anomaly (NO SMOKING stays off with gear DN): GearDN-NO SMOKING auto-logic broken — defer per MEL. (5) EMER LIGHTS ARM check fail: battery pack discharged or fault; defer per MEL; consider impact on night ops.',
 'image', '/assets/aircraft/q400/lighting-flow.svg',
 'QRH lighting non-normal cluster',
 'Panel · POSN · STORM/DOME · INFO SIGNS · EMER LIGHTS.',
 'Most lighting faults are MEL items. Real-time cockpit handling is rare except the EMER LIGHTS chain.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: Position Lights at Night, ATC Calls',
 'Setup: cruise at FL230 at night, en route to a regional field. ATC calls: "Q400, observed you may have a position light out — left wing red appears intermittent. Please advise." The FO checks the panel — POSN switch is at POSN, no caution illuminated.\n\nDiagnosis: a primary left-wing red position light has failed. The related secondary armed and has illuminated automatically — what ATC saw was the brief transition, or possibly an intermittent primary failing under vibration. Your panel shows nothing because the system is working as designed: silent failover. Reply to ATC: "Position lights all on, possibly a recent primary-to-secondary transition; we''ll check on landing."\n\nNo immediate action required. Continue the flight. The system is designed for this exact scenario; only a dual-redundant failure on the same side would actually leave you without that position light. On landing, write up the position lights for inspection — the next pre-flight will need both primary and secondary to be operative for night dispatch.',
 'animation', '/assets/aircraft/q400/lighting-flow.svg',
 'Position light failover scenario',
 'Silent failover · panel shows nothing · document on landing.',
 'A check captain might run this scenario as "ATC sees you have a light out" — the answer is "system is doing its job."',
 JSON_OBJECT(
   'prompt', 'ATC calls in cruise saying your left-wing red position light may be out, but your panel shows nothing wrong. What is most likely?',
   'options', JSON_ARRAY(
     'A position light primary has failed; the armed secondary has auto-illuminated; system is working as designed',
     'Total position-light system failure; declare emergency',
     'ATC mistake; ignore',
     'L SECONDARY bus failure; cross-reference electrical'
   ),
   'correct_index', 0,
   'explanation', 'Primary failure triggers automatic secondary illumination — the system is silent on this. Document on landing. Mnemonic: PRI-SEC-1-SEC-ARM.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Lighting in 60 Seconds',
 'Recap:\n  • 3 categories: interior, exterior, emergency.\n  • Panel/instrument 5 VDC variable. PANEL LIGHTING knobs + per-pilot FLT PNL knobs.\n  • Dome on BATTERY PWR (works without BATTERY MASTER). Storm on L SECONDARY (lightning night-vision recovery).\n  • Cabin: 21 fluorescent overhead + 21 fluorescent sidewall. F/A membrane controls + single dimmer.\n  • NO SMOKING signs auto-on when gear selector to DN. FASTEN BELTS = chime + lavatory RETURN TO SEAT.\n  • Lavatory: LAVATORY LTS arms; OCCUPIED latch activates fluorescent + F/A indicator.\n  • Forward door: 4 step lights (left main bus). 2 boarding lights (Battery bus).\n  • Baggage: 1 fwd dome / 2 aft dome. Auto-on with door unlocked.\n  • Landing lights: 2 per wing (4 total). Outboard approach; inboard flare (angled down).\n  • Taxi light: on steerable nose gear. Inhibited unless gear locked down.\n  • Position lights: green right / red left / white aft. Primary + secondary; ~1-sec arm delay; auto-failover on primary fail.\n  • EMER LIGHTS: 3-position lever-locked. ARM is normal flight; auto-on on AC loss. Battery packs power egress chain.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 'OUT-APPROACH-IN-FLARE · GREEN-RIGHT-RED-LEFT-WHITE-AFT · PRI-SEC-1-SEC-ARM · 21-OVER-21-SIDE · NO-SMOKE-GEAR-DN · TAXI-LOCK-DOWN · EMER-3-POS-ARM · STORM-FLASH-RECOVER · DOME-BATTERY-NO-MASTER · LAV-OCCUPIED-LATCH · 1-FWD-2-AFT-BAG',
 'Eleven mnemonics carry every lighting question on a recurrent. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
