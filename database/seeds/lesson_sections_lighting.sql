-- =============================================================================
-- AviatorTutor — Phase 12: ATA 33 Lighting — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'lighting' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'lighting-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Three Categories of Lighting',
 '<p>Q400 lighting is split into three distinct categories with three different control philosophies. <strong>Interior lighting</strong> covers the cockpit (panel/instrument 5 VDC variable, dome/storm, utility, map, circuit breaker) plus cabin (overhead/sidewall fluorescent, reading, signs, lavatory, galley, wardrobe, baggage). <strong>Exterior lighting</strong> covers landing/approach/flare on the wing leading edges, taxi on the steerable nose gear, position lights (green right / red left / white aft) with primary+secondary failover, anti-collision (upper bullet + lower fuselage), recognition (red, fuselage centreline), and inspection + logo lights. <strong>Emergency lighting</strong> is on a 3-position lever-locked switch (ON/ARM/OFF) with battery packs powering the cabin egress chain (ceiling, floor markings, locator signs, exit signs, egress lights). Two key auto-logic items: NO SMOKING signs come on automatically when the gear selector is moved to DN, and the taxi light will not illuminate unless the gear is locked down.</p>',
 'overview', 10),

(@lesson_id, 'Components — Switches, Knobs, Lamps',
 '<ul>
  <li><strong>PANEL LIGHTING control panel:</strong> rotary knobs for Overhead Console, Glareshield (also drives clocks), Fwd Centre, Aft Centre. OFF detent counter-clockwise.</li>
  <li><strong>STORM/DOME switch:</strong> 3-position. STORM (bright flash recovery), STORM/DOME (both), OFF. Storm lights on L SECONDARY bus.</li>
  <li><strong>DOME light switch:</strong> 2-position. Powered from BATTERY PWR bus — operative without BATTERY MASTER.</li>
  <li><strong>PILOTS FLT PNL knob:</strong> drives pilot side console, ICP1, Standby Instruments lighting.</li>
  <li><strong>COPILOTS FLT PNL knob:</strong> drives copilot side console, ICP2, Landing Gear selector panel, GPWS/Hydraulic Control panel lighting.</li>
  <li><strong>2 utility lights:</strong> swivel ball, ceiling, one above each pilot. Plus 1 observer''s utility light.</li>
  <li><strong>Map lights:</strong> below each side window with adjacent dimming knob.</li>
  <li><strong>Circuit breaker panel lights:</strong> 2 white floodlights each side. Separate per-pilot toggle switch.</li>
  <li><strong>W/S WIPER ICE DETECT pushbuttons:</strong> illuminate the wiper spigot for ice check.</li>
  <li><strong>Forward cabin attendant panel:</strong> membrane switches: CABIN OVERHD, CABIN SIDEWALL, DIM OVERHD, DIM SIDEWALL, BOARDING, LAVATORY, AIRSTAIR DOOR, PSU ON/OFF, PSU TEST.</li>
  <li><strong>Cabin overhead:</strong> 21 fluorescent panels along ceiling.</li>
  <li><strong>Cabin sidewall:</strong> 21 fluorescent under valance both sides.</li>
  <li><strong>Single dimmer:</strong> on C/A panel — dims all main cabin lights together.</li>
  <li><strong>Reading lights:</strong> 2 per PSU, pushbutton each, gated by PSU ON/OFF.</li>
  <li><strong>Information signs:</strong> NO SMOKING + FASTEN SEAT BELTS at front + each PSU. Lavatory RETURN TO SEAT.</li>
  <li><strong>Lavatory:</strong> 2 lamps + 2 fluorescent. LAVATORY LTS arms; OCCUPIED latch activates fluorescent + indicator at F/A seat.</li>
  <li><strong>Wardrobe:</strong> incandescent, door-switch activated.</li>
  <li><strong>Galley:</strong> work-surface + overhead lights; switches on galley control panel.</li>
  <li><strong>Forward passenger door:</strong> 4 step lights on risers (left main bus). 2 boarding lights — lower threshold + forward boarding (Battery bus).</li>
  <li><strong>Baggage:</strong> forward 1 dome / aft 2 dome — auto-on when door unlocked.</li>
  <li><strong>Landing lights (×4):</strong> 2 per wing leading edge, outboard of nacelles. Outboard = approach; inboard = flare (angled down).</li>
  <li><strong>Taxi light:</strong> on steerable section of nose gear. Inhibited unless gear locked down.</li>
  <li><strong>Position lights:</strong> green right wingtip / red left wingtip / white aft of vertical stab bullet. Each = primary + secondary with failover.</li>
  <li><strong>Anti-collision lights:</strong> upper on bullet fairing; lower on fuselage.</li>
  <li><strong>Recognition light:</strong> red, top fuselage centreline, just forward of wings.</li>
  <li><strong>Inspection lights:</strong> engine, wing.</li>
  <li><strong>Logo lights:</strong> on horizontal stabiliser, illuminating the vertical stab fin.</li>
  <li><strong>EMER LIGHTS switch:</strong> 3-position lever-locked. ON / ARM / OFF.</li>
  <li><strong>Emergency battery packs:</strong> power the egress chain when normal AC lost or ON selected.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — How Lights Work',
 '<h4>Flight deck panel + utility</h4>
<ul>
  <li>Disc-shaped lamp assemblies in Plexiglas behind each panel. 5 VDC variable.</li>
  <li>Each rotary knob: counter-clockwise OFF detent → clockwise rotation increases brightness to BRT max.</li>
  <li>Dome light independent on BATTERY PWR — works at all times.</li>
  <li>Storm lights bright on demand for lightning night-vision recovery.</li>
</ul>
<h4>Cabin lighting</h4>
<ul>
  <li>Forward C/A panel membrane switches arm the fluorescent banks.</li>
  <li>DIM OVERHD and DIM SIDEWALL provide independent dimming.</li>
  <li>Single dimmer control dims all main cabin together — useful for top-of-descent night transition.</li>
  <li>Reading lights gated by PSU ON/OFF + individual pushbutton.</li>
</ul>
<h4>Information signs</h4>
<ul>
  <li>FASTEN BELTS switch on cockpit panel: signs come on with low chime through PA. Lavatory RETURN TO SEAT also illuminates.</li>
  <li>NO SMOKING switch on cockpit panel: signs come on with low chime. <strong>Auto-on when landing gear selector to DN.</strong></li>
  <li>Lavatory LAVATORY LTS arms fluorescent. OCCUPIED latch closes circuit; OCCUPIED indicator at F/A seat illuminates.</li>
</ul>
<h4>Exterior lighting</h4>
<ul>
  <li>Landing/approach/flare: switches on right EXTERIOR LIGHTS panel.</li>
  <li>Taxi light: inhibited until gear locked down. Once locked, illuminates in direction nose gear points.</li>
  <li>Position lights: POSN switch ON → all primary + secondary on. After ~1 second, secondaries go off but ARMED. Primary failure → secondary auto-on.</li>
  <li>Anti-collision: upper + lower flash. Recognition: red steady, top fuselage.</li>
</ul>
<h4>Emergency lighting</h4>
<ul>
  <li>EMER LIGHTS switch: lever-locked 3-position. Lever-lock prevents accidental selection.</li>
  <li>ON = lights on now, powered from emergency battery packs.</li>
  <li>ARM = normal flight setting. Auto-illuminates on AC power loss.</li>
  <li>OFF = inhibits.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Cruise, Approach',
 '<h4>Pre-flight</h4>
<ul>
  <li>EMER LIGHTS to ARM (lever-locked). Verify in QRH/checklist.</li>
  <li>Test cockpit panel lights via knobs OFF→BRT.</li>
  <li>Cabin: brief F/A on lighting plan. Cabin overhead + sidewall on for boarding.</li>
  <li>Position lights ON for ground operations + taxi.</li>
  <li>Anti-collision lights ON for engine start + taxi.</li>
</ul>
<h4>Taxi + take-off</h4>
<ul>
  <li>Taxi light ON — confirms direction of nose-gear pointing.</li>
  <li>FASTEN BELTS ON for taxi. NO SMOKING ON.</li>
  <li>Approach + flare: outboard approach + inboard flare lights ON for night.</li>
  <li>Logo lights for ramp ops + cruise (depending on company SOP).</li>
</ul>
<h4>Cruise</h4>
<ul>
  <li>Position + anti-collision continuous.</li>
  <li>Cabin lighting per flight phase. Single dimmer for night settling.</li>
  <li>FASTEN BELTS off if smooth (with chime). NO SMOKING per route.</li>
</ul>
<h4>Approach + landing</h4>
<ul>
  <li>Approach lights ON. Flare lights ON.</li>
  <li>NO SMOKING auto-on when gear DN selected.</li>
  <li>Cabin lighting to subdued for night arrival per F/A.</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — Failures and Reversion',
 '<ul>
  <li><strong>Single panel light failure:</strong> rotate knob through OFF and back to confirm power-cycling. If single bulb, defer per MEL.</li>
  <li><strong>Position light primary failure:</strong> related secondary auto-illuminates within ~1 sec. No crew action; document.</li>
  <li><strong>Both primary AND secondary fail:</strong> position-light loss on that side. Required equipment for night ops; consider divert.</li>
  <li><strong>Loss of L SECONDARY bus:</strong> storm lights inoperative. Dome remains (BATTERY PWR).</li>
  <li><strong>AC power loss in flight:</strong> EMER LIGHTS at ARM auto-illuminate the cabin egress chain. Cabin attendants alerted by sudden cabin emergency lighting.</li>
  <li><strong>Taxi light fails to illuminate:</strong> verify gear is locked down. If locked but no taxi light, defer per MEL or use landing lights for ground taxi (per SOP).</li>
  <li><strong>Cabin sidewall/overhead fluorescent fault:</strong> single fixture out — defer per MEL. Multiple out — investigate ballast or DC power.</li>
  <li><strong>Lavatory occupancy not detected:</strong> check LAVATORY LTS membrane on; check latch movement.</li>
  <li><strong>EMER LIGHTS check fails (ON):</strong> battery pack discharge or fault. Defer per MEL; emergency egress reverts to AC + reflective markings only — significant degradation for night ops.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Lights, Switches, Signs',
 '<ul>
  <li><strong>PANEL LIGHTING knobs:</strong> Overhead Console, Glareshield (drives clocks), Fwd Centre, Aft Centre.</li>
  <li><strong>STORM/DOME switch:</strong> STORM / STORM/DOME / OFF.</li>
  <li><strong>DOME light switch:</strong> DOME / OFF (BATTERY PWR bus).</li>
  <li><strong>PILOTS FLT PNL knob:</strong> pilot side + ICP1 + Standby.</li>
  <li><strong>COPILOTS FLT PNL knob:</strong> copilot side + ICP2 + Landing Gear selector + GPWS/Hyd panel.</li>
  <li><strong>CIR BKR LIGHT switches:</strong> per pilot, 2-position. White floodlights above CB panels.</li>
  <li><strong>W/S WIPER ICE DETECT pushbuttons:</strong> illuminate wiper spigots.</li>
  <li><strong>FASTEN BELTS switch:</strong> 2-position. Sign + chime + lavatory RETURN TO SEAT.</li>
  <li><strong>NO SMOKING switch:</strong> 2-position. Sign + chime. Auto-on with gear selector DN.</li>
  <li><strong>EMER LIGHTS switch:</strong> 3-position lever-locked. ON / ARM / OFF.</li>
  <li><strong>F/A panel membrane switches:</strong> CABIN OVERHD / SIDEWALL, DIM OVERHD / SIDEWALL, BOARDING, LAVATORY, AIRSTAIR DOOR, PSU ON/OFF, PSU TEST, single cabin dimmer.</li>
  <li><strong>EXTERIOR LIGHTS panel:</strong> landing/approach/flare, POSN, anti-collision, recognition, taxi, logo, inspection.</li>
  <li><strong>OCCUPIED indicator:</strong> at F/A seat — illuminates when lavatory latch in OCCUPIED position with LAVATORY LTS armed.</li>
  <li><strong>RETURN TO SEAT sign:</strong> in lavatory, with FASTEN BELTS.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>Cabin overhead fluorescent fixtures:</strong> 21.</li>
  <li><strong>Cabin sidewall fluorescent fixtures:</strong> 21.</li>
  <li><strong>Reading lights per PSU:</strong> 2.</li>
  <li><strong>Forward passenger door step lights:</strong> 4 (on risers).</li>
  <li><strong>Boarding lights:</strong> 2 (lower threshold + forward boarding).</li>
  <li><strong>Forward baggage compartment dome lights:</strong> 1.</li>
  <li><strong>Aft baggage compartment dome lights:</strong> 2.</li>
  <li><strong>Landing lights per wing:</strong> 2 (outboard approach + inboard flare).</li>
  <li><strong>Total landing lights:</strong> 4.</li>
  <li><strong>Position lights per location:</strong> 2 (primary + secondary).</li>
  <li><strong>Position-light auto-failover delay:</strong> ~1 second after POSN switch ON, secondaries go off but ARMED.</li>
  <li><strong>Position colours:</strong> green right wingtip / red left wingtip / white aft of bullet fairing.</li>
  <li><strong>Lavatory lighting:</strong> 2 lamps + 2 fluorescent.</li>
  <li><strong>Utility lights on flight deck ceiling:</strong> 2 (one each pilot) + 1 observer.</li>
  <li><strong>Storm-light bus:</strong> L SECONDARY.</li>
  <li><strong>Dome-light bus:</strong> BATTERY PWR (operates without BATTERY MASTER).</li>
  <li><strong>Forward door step-light bus:</strong> Left Main.</li>
  <li><strong>Boarding-light bus:</strong> Battery.</li>
  <li><strong>Panel/instrument lighting voltage:</strong> 5 VDC variable.</li>
  <li><strong>EMER LIGHTS switch positions:</strong> 3 (ON / ARM / OFF), lever-locked.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>OUT-APPROACH-IN-FLARE</strong> — outboard wing landing lights = approach; inboard = flare.</li>
  <li><strong>GREEN-RIGHT-RED-LEFT-WHITE-AFT</strong> — position-light colours.</li>
  <li><strong>PRI-SEC-1-SEC-ARM</strong> — position lights primary + secondary with ~1-sec arm delay; secondary auto-on if primary fails.</li>
  <li><strong>21-OVER-21-SIDE</strong> — cabin: 21 overhead + 21 sidewall fluorescent.</li>
  <li><strong>NO-SMOKE-GEAR-DN</strong> — NO SMOKING auto-on when gear selector DN.</li>
  <li><strong>TAXI-LOCK-DOWN</strong> — taxi light inhibited unless gear locked down.</li>
  <li><strong>EMER-3-POS-ARM</strong> — EMER LIGHTS 3 positions ON / ARM / OFF; ARM is normal flight setting.</li>
  <li><strong>STORM-FLASH-RECOVER</strong> — storm lights for night-vision recovery after lightning flash. L SECONDARY bus.</li>
  <li><strong>DOME-BATTERY-NO-MASTER</strong> — dome light on BATTERY PWR; works without BATTERY MASTER set.</li>
  <li><strong>LAV-OCCUPIED-LATCH</strong> — lavatory fluorescent activates only with LAVATORY LTS armed AND latch at OCCUPIED.</li>
  <li><strong>1-FWD-2-AFT-BAG</strong> — 1 dome forward baggage; 2 dome aft baggage. Auto-on with door unlocked.</li>
 </ol>
<p>Pre-flight chant: <em>"EMER LIGHTS ARM · panel lights up · cabin briefed · external POSN ON · taxi light when locked."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
