-- =============================================================================
-- AviatorTutor — Phase 12 (ATA 33 Lighting) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'lighting' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'lighting-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Lighting — Interior, Exterior, Emergency',
     'lighting-overview',
     'overview',
     'Three categories: interior (flight deck panel/instrument 5 VDC variable, dome/storm, utility, map, circuit-breaker, cabin overhead/sidewall 21+21 fluorescent, reading, signs, lavatory, baggage), exterior (landing — outboard approach + inboard flare, taxi on steerable nose gear with gear-down inhibit, position with primary+secondary failover, anti-collision upper+lower, recognition red, wing/engine inspection, logo), and emergency (3-position lever-locked switch ON/ARM/OFF; battery packs power the cabin egress chain). Storm lights compensate for night-vision loss after lightning. NO SMOKING auto-on with gear DN. Position lights have primary + auto-failover secondary on a 1-second arm delay.',
     '<p>Aircraft lighting is a deceptively wide system: it touches every cabin, every cockpit panel, every part of the airframe visible to ATC and other traffic. The Q400 design splits lighting cleanly into three groups, each with its own control philosophy. Interior lighting is variable-intensity 5 VDC for the cockpit panels (Overhead, Glareshield, Fwd/Aft Centre Console plus per-side flight panel), with separate knobs for utility and map lights, and a dome/storm group on a discrete switch. Cabin lighting is fluorescent-based with 21 overhead + 21 sidewall fixtures controlled from the forward cabin attendant panel via membrane switches. Exterior lighting includes landing, taxi, position, anti-collision, recognition, and inspection lights — with the position lights having an automatic primary→secondary failover. Emergency lighting is on a 3-position lever-locked switch with an ARM mode that auto-illuminates on AC power loss. Memorise the two key auto-logic items: NO SMOKING comes on automatically when the gear selector goes to DN; the taxi light will not illuminate if the gear is not locked down.</p>',
     JSON_ARRAY(
       'Three categories: interior, exterior, emergency lighting',
       'Flight deck panel/instrument lighting: variable-intensity 5 VDC. Disc-shaped lamp assemblies in Plexiglas',
       'PANEL LIGHTING knobs: Overhead Console, Glareshield, Fwd Centre, Aft Centre. Rotary OFF→BRT. Glareshield knob also controls both clock lights',
       'STORM/DOME switch: STORM only (L SECONDARY bus) compensates for lightning night-vision loss. STORM/DOME both. OFF',
       'DOME light switch: separate. Powered from BATTERY PWR bus — works without BATTERY MASTER set',
       'PILOTS FLT PNL knob → pilot side console + ICP1 + Standby Instruments',
       'COPILOTS FLT PNL knob → copilot side console + ICP2 + Landing Gear selector panel + GPWS/Hydraulic Control panel',
       'Standby compass light controlled by CAUT/ADVSY LIGHTS DIM/BRT toggle',
       'Two swivel-ball utility lights on flight deck ceiling (one each pilot), plus observer''s utility light. Each has an adjacent rotary knob',
       'Map lights below each side window. W/S WIPER ICE DETECT pushbutton illuminates the wiper spigot for ice check',
       'Circuit breaker panel lights: 2 white floodlights above each side. Separate toggle switch per pilot',
       'Cabin overhead: 21 fluorescent ceiling lights. CABIN OVERHD membrane on forward cabin attendant panel + DIM OVERHD',
       'Cabin sidewall: 21 fluorescent under valance both sides. CABIN SIDEWALL + DIM SIDEWALL membranes',
       'Single dimmer control on C/A panel dims ALL main cabin lights together',
       'Reading lights: 2 per PSU. Pushbutton adjacent. Active only when PSU ON/OFF membrane is on',
       'NO SMOKING signs come on AUTOMATICALLY when landing gear selector is set to DN. Low chime through PA',
       'FASTEN BELTS sign also illuminates the lavatory RETURN TO SEAT sign + low chime through PA',
       'Lavatory: 2 lamps + 2 fluorescent. LAVATORY LTS membrane arms; lavatory latch OCCUPIED activates fluorescent + OCCUPIED indicator above F/A seat',
       'Forward passenger door: 4 step lights on risers (left main bus). 2 boarding lights — lower threshold + forward boarding (Battery bus)',
       'Baggage: forward 1 dome light auto-on when forward door unlocked. Aft 2 dome lights auto-on when aft door unlocked',
       'Landing lights: 2 per wing on leading edge outboard of nacelles. Outboard = APPROACH; inboard = FLARE. Flare lights angled DOWN for the flare',
       'Taxi light: on steerable section of nose gear (shines in the direction nose gear points). Will NOT illuminate if landing gear is not locked down',
       'Position lights: GREEN right wing tip; RED left wing tip; WHITE at aft of vertical stabiliser bullet fairing. Each has PRIMARY + SECONDARY',
       'Position-light auto-failover: when POSN switch ON, all primary + secondary illuminate. After ~1 second, secondary go off but stay ARMED. If a primary fails, the related secondary illuminates automatically',
       'Anti-collision: upper light on bullet fairing; lower light on fuselage. Recognition light: red, top fuselage centreline forward of wings',
       'Engine and wing inspection lights, logo lights — exterior support',
       'EMER LIGHTS switch: 3-position, lever-locked. ON = lights on if emergency battery packs charged. ARM = auto-on when normal AC power lost. OFF',
       'Emergency lighting includes ceiling lights, reflective floor markings, locator signs, exit signs, an egress light per passenger exit'
     ),
     JSON_ARRAY(
       'Storm lights are for night-vision recovery after a lightning flash — bright flight-deck lights to override flash blindness.',
       'NO SMOKING signs auto-illuminate with gear DN. Useful pre-landing reminder for both pilots and cabin.',
       'Position lights have primary AND secondary, with automatic failover — you don''t lose nav lights on a single bulb failure.',
       'Taxi light is INHIBITED if gear not locked down. So you can''t turn it on in flight — it auto-comes on for taxi.',
       'EMER LIGHTS at ARM is the normal flight setting — auto-illuminates on AC power loss without crew action.',
       'Glareshield knob doubles as clock-light control. Adjusting glareshield brightness affects clocks too.',
       'Single C/A dimmer dims all main cabin lights together — useful at top-of-descent for night arrival.'
     ),
     JSON_ARRAY(
       'Outboard wing landing lights = APPROACH. Inboard = FLARE. Easy to swap.',
       'Position lights: GREEN right, RED left, WHITE aft. Each has primary + secondary.',
       'Cabin: 21 overhead + 21 sidewall fluorescent — both totals are 21.',
       'NO SMOKING is the one that auto-comes on with gear DN. NOT FASTEN BELTS.',
       'Taxi light auto-inhibited unless gear LOCKED DOWN — not just gear DN selected.',
       'EMER LIGHTS three positions (NOT two): ON / ARM / OFF. Lever-locked.',
       'Forward baggage: 1 dome. Aft baggage: 2 dome. Different counts.',
       'Storm lights are on L SECONDARY bus. Dome lights are on BATTERY PWR bus — works without BATTERY MASTER.',
       'Position-light auto-failover: ~1 second arm delay before secondaries go off (but armed). Not instant.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'lighting-overview';
