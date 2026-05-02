-- =============================================================================
-- AviatorTutor — Phase 1: ATA 21 Aeroplane General
-- Lesson sections (8 section types) for the Aeroplane General overview.
-- These render as the structured "Notes" tab on /systems/aeroplane-general
-- and feed the revision-mode 3/5/10-minute summaries.
--
-- Idempotent: re-running wipes prior sections for this lesson and re-inserts.
-- Run AFTER lesson_aeroplane_general.sql.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'aeroplane-general' LIMIT 1);
SET @lesson_id := (
    SELECT id FROM lessons
    WHERE system_id = @system_id
      AND slug = 'aeroplane-general-overview'
    LIMIT 1
);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections
    (lesson_id, title, body, section_type, sort_order)
VALUES
-- 1. Overview
(@lesson_id, 'Overview — What the Q400 Is',
 '<p>The Bombardier Dash 8-Q400 is a high-wing twin-turboprop transport-category aeroplane. Two Pratt &amp; Whitney <strong>PW150A</strong> engines, each rated at <strong>5071 shaft horsepower</strong>, drive six-bladed propellers. The aeroplane is two-pilot certified, approved for instrument flight, and in SAS-variant configuration is approved to a maximum altitude of <strong>FL250 (25,000 ft)</strong>. SAS variants seat 58 to 72 passengers plus two cabin crew members in addition to the pilot, copilot, and flight observer.</p>
<p>Maximum take-off mass varies by configuration:</p>
<ul>
  <li><strong>Basic Gross Mass:</strong> 27,987 kg</li>
  <li><strong>Intermediate Gross Mass:</strong> 28,998 kg</li>
  <li><strong>High Gross Mass:</strong> 29,257 kg</li>
</ul>
<p>The aeroplane has an <strong>Active Noise and Vibration Suppression (ANVS)</strong> system — a Q400 signature feature that reduces cabin vibration by injecting counter-phase signals through cabin speakers and structural tuners. Failure shows on the F/A panel as <em>NVS INOP</em>.</p>',
 'overview',
 10),

-- 2. Components — physical airframe parts
(@lesson_id, 'Components — Airframe Architecture',
 '<h4>Fuselage (three sections)</h4>
<ul>
  <li><strong>Forward:</strong> flight deck, nose wheel well + unpressurised equipment deck, weather radar radome, forward baggage compartment (right forward).</li>
  <li><strong>Center:</strong> passenger cabin. Constant cross-section with slightly flattened bottom.</li>
  <li><strong>Aft:</strong> unpressurised. Houses both air-conditioning packs and the APU. Swept up to support the empennage.</li>
</ul>
<h4>Wing</h4>
<p>Single, high aspect ratio, cantilevered, joined to the upper midsection of the fuselage. Includes integral fuel tanks, engine nacelles, and main gear mounting structures. Outboard portions tapered with <strong>2.5° dihedral</strong>. Pneumatic deicer boots on the leading edges of the centre wing and outboard from the landing lights.</p>
<h4>Wing control surfaces</h4>
<ul>
  <li><strong>Ailerons:</strong> conventional, working with differential lateral-control spoilers on the upper wing skin.</li>
  <li><strong>Flaps:</strong> single-slotted, from fuselage to inboard of ailerons.</li>
  <li><strong>Spoilers:</strong> roll mode in flight; ground mode on landing extends them to dump lift.</li>
</ul>
<h4>Empennage</h4>
<ul>
  <li><strong>Horizontal stabiliser:</strong> separate left and right elevators, composite leading edge with pneumatic deicer boots.</li>
  <li><strong>Vertical stabiliser:</strong> rearmost fuselage and stabiliser are constructed as one piece. Composite leading edge with two-chamber pneumatic deicer boot. Composite bullet fairing on top.</li>
  <li><strong>Rudder:</strong> a fore rudder hinged to the rear vertical-stabiliser spar AND a trailing rudder hinged to the trailing edge of the fore rudder. Trailing rudder is geometrically arranged to deflect at <strong>twice</strong> the angle of the fore rudder. Two hydraulic actuators drive the pair.</li>
  <li><strong>Elevators:</strong> hydraulically operated with artificial feel. Both elevators normally operate together but can split via the pitch-disconnect system. Hydraulic actuators are used for elevator trim.</li>
</ul>',
 'components',
 20),

-- 3. Operation — how it goes together in normal use
(@lesson_id, 'Operation — How the Architecture Works in Practice',
 '<p>In normal operation the three-section fuselage means three different pressure regimes you need to keep track of:</p>
<ul>
  <li><strong>Forward + Center sections:</strong> sealed by the forward and aft pressure bulkheads, pressurised in flight.</li>
  <li><strong>Aft section:</strong> behind the aft pressure bulkhead, deliberately unpressurised. APU runs and packs run there with no pressure load on their casings.</li>
  <li><strong>Nose:</strong> in front of the forward pressure bulkhead, also unpressurised. Houses nose-wheel well + unpressurised equipment deck.</li>
</ul>
<p>The wing layout drives day-to-day decisions: the high mounting clears propellers from ground debris but raises both the cabin floor and the fueling access. The 2.5° dihedral plus the spoiler/aileron interconnect gives the Q400 its characteristic crisp roll response.</p>
<p>The split-rudder design means the cockpit feels a single rudder pedal but the airframe is moving two surfaces in geared series. This matters during single-engine training: the rudder authority you have at low speed comes partly from the geometric advantage of the trailing rudder, not from the size of the fin.</p>
<p>The pitch-disconnect system on the elevators is a Q400 quirk worth understanding: a jam in one elevator can be isolated by splitting the system, leaving the other elevator and No.3 hydraulic to keep pitch authority on one side. Sim recurrent training drills this scenario.</p>',
 'operation',
 30),

-- 4. Normal — normal-flight references
(@lesson_id, 'Normal — Day-to-Day Indications and Locations',
 '<h4>Cockpit equipment locations</h4>
<ul>
  <li><strong>Glareshield:</strong> Caution &amp; Warning panel (centre), engine fire press-to-reset switchlights, A/P disengage, GPWS, terrain inhibit, RUD/SPLR push-off switchlights, anti-skid switch.</li>
  <li><strong>Centre console:</strong> Emergency Brake Lever, Control Lock Lever, Flap Selector Lever, Elevator Trim Indicator, two Power Levers, two Condition Levers.</li>
  <li><strong>Aft centre console:</strong> ARCDU (Audio &amp; Radio Control Display Unit), Trim Control Panel, ESCP (Engine and System Integrated Displays Control Panel), Weather Radar Control Panel.</li>
  <li><strong>Pilot side panel:</strong> Wx wiper, side-panel dimmer, prop overspeed governor test, T/O warning test, ADC test, stall warning test, NWS toggle.</li>
  <li><strong>Copilot side panel:</strong> Wx wiper, side-panel dimmer, CB lighting, mic INPH/XMIT toggle.</li>
  <li><strong>Overhead console:</strong> Ice protection, DC Control, audible evacuation, FDR, fire protection, panel lighting, AC Control, A/C, emergency lights, exterior lights, cabin altitude, engine start, APU, PFD altimeter units, altitude/differential placard, cabin altitude indicator.</li>
  <li><strong>F/A panel:</strong> cabin temperature display + scale, NVS system controls, lighting controls (sidewall, overhead, lavatory, airstair, boarding), PSU test, F/A control enabled advisory.</li>
  <li><strong>Towing switch:</strong> two-position guarded switch (NORMAL / TOWING), 7.5 A circuit breaker, power direct from main battery.</li>
</ul>',
 'normal',
 40),

-- 5. Abnormal — failure modes touching this system
(@lesson_id, 'Abnormal — Failures and First Crew Actions',
 '<p>Aeroplane General is an overview lesson, but several airframe-touching abnormals fall here:</p>
<ul>
  <li><strong>Door / baggage door caution:</strong> below 100 kt on takeoff roll, REJECT. In flight at low altitude, descend, slow, run the QRH, return to land if confirmation cannot be obtained.</li>
  <li><strong>Smoke / fumes traced to aft fuselage:</strong> oxygen 100%, run SMOKE / FUMES / FIRE QRH, isolate the suspect bus, consider APU shutdown, plan diversion. Cockpit cannot access the aft fuselage in flight.</li>
  <li><strong>Tail strike on landing:</strong> after stopping, do not pressurise. Engineering inspection of aft fuselage and pressure bulkhead before next flight.</li>
  <li><strong>Pitch jam (one elevator):</strong> activate the pitch-disconnect system per QRH; cross-check both control columns for force feedback; brief approach for split-elevator characteristics.</li>
  <li><strong>Hydraulic gear-extension failure:</strong> use the emergency hand pump on the aft flight deck. Pump <em>early</em>; gear-down with the hand pump takes time.</li>
  <li><strong>Loss of ANVS:</strong> non-emergency. NVS INOP indication on F/A panel; cabin will be louder. No effect on flight safety.</li>
</ul>',
 'abnormal',
 50),

-- 6. Indications — what to look for
(@lesson_id, 'Indications — Caution Lights, Cockpit Cues',
 '<p>Indications you will see on the day-to-day Q400 that touch Aeroplane General:</p>
<ul>
  <li><strong>DOOR caution (CAS / EICAS):</strong> any door not latched. Treat as a hard caution — do not move below 100 kt; investigate before continuing.</li>
  <li><strong>BAGGAGE DOOR caution:</strong> as above. Cannot have physically opened in flight or on the roll (outside-only doors), but the latch sensor may be telling you the truth.</li>
  <li><strong>NVS INOP advisory (F/A panel):</strong> Active Noise &amp; Vibration Suppression has failed. Advisory only — fly the aircraft normally; cabin will be louder.</li>
  <li><strong>Glareshield engine-fire switchlights (left + right):</strong> mirrored at both seats. Press to reset is a memory step in any engine-fire procedure.</li>
  <li><strong>RUD / SPLR PUSH OFF switchlights (centre glareshield):</strong> isolate the powered surface if a runaway or jam is suspected. Used during specific QRH non-normals.</li>
  <li><strong>Cabin altitude indicator (overhead):</strong> shows cabin altitude vs. aircraft altitude. Watch for divergence from the schedule.</li>
  <li><strong>Standby compass:</strong> reverts on loss of AHRS power. Cross-check with primary heading every cruise scan.</li>
</ul>',
 'indications',
 60),

-- 7. Limitations
(@lesson_id, 'Limitations — Numbers You Cannot Exceed',
 '<ul>
  <li><strong>Maximum operating altitude (SAS variant):</strong> FL250 (25,000 ft).</li>
  <li><strong>Maximum take-off mass (configuration-dependent):</strong> 27,987 kg / 28,998 kg / 29,257 kg.</li>
  <li><strong>Crew minimum:</strong> two pilots (transport-category certification).</li>
  <li><strong>Cabin crew (SAS variant):</strong> two cabin crew members + flight observer position available.</li>
  <li><strong>Passenger seating (SAS variant):</strong> 58 to 72 passengers depending on configuration.</li>
  <li><strong>Wing dihedral:</strong> 2.5° outboard of nacelles (design figure, not a limitation per se but relevant to performance assumptions).</li>
  <li><strong>Minimum pavement width for 180° turn at 70° NWS:</strong> 25.7 m (84 ft 5 in) — without backing up.</li>
  <li><strong>Tail-strike attitude:</strong> covered in performance manuals; relevant on heavy / high-flap takeoffs and on flared landings.</li>
</ul>
<p>Operational limits (V<sub>1</sub>, V<sub>R</sub>, V<sub>2</sub>, V<sub>MO</sub>, M<sub>MO</sub>, flap speeds) live in the AFM and the QRH and are NOT covered in Aeroplane General. Know where to find them.</p>',
 'limitations',
 70),

-- 8. Memory items / mnemonics
(@lesson_id, 'Memory — Mnemonics for the Oral and the Sim',
 '<p>These six mnemonics carry the foundation. Drill them.</p>
<ol>
  <li><strong>F-C-A</strong> — Forward, Center, Aft. The fuselage in three sections; APU + packs in the unpressurised aft.</li>
  <li><strong>28-by-33</strong> — wing span 28.42 m, length 32.83 m. The Q400 footprint.</li>
  <li><strong>5-0-7-1</strong> — 5071 SHP per PW150A engine. Six-bladed propellers.</li>
  <li><strong>BAGS-OUT-ONLY</strong> — baggage doors open from outside only; pax door + Type II/III exit open from either side.</li>
  <li><strong>FORE+TRAIL = TWO-FOR-ONE</strong> — trailing rudder deflects 2x the fore rudder; one set of pedals, two surfaces.</li>
  <li><strong>APU=AFT-UNPRESS</strong> — APU and packs live in the aft unpressurised section. Smoke from there has nowhere to vent except overboard.</li>
</ol>
<p>For the cockpit walkdown, run the location chant: <em>"Pump, Axe, PBE, Extinguisher, Goggles, Vest"</em> — every item locatable without looking, eyes-closed if needed.</p>',
 'memory',
 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
