-- =============================================================================
-- AviatorTutor — Phase 2: ATA 21 Air Conditioning & Pressurization
-- Lesson sections (8 section types) — render as the structured "Notes" tab
-- on /systems/air-cond-press and feed the revision-mode 3/5/10-minute summaries.
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

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections
    (lesson_id, title, body, section_type, sort_order)
VALUES
-- 1. Overview
(@lesson_id, 'Overview — ECS + Pressurization, One Source, One Schedule',
 '<p>The Q400 Environmental Control System (ECS) and Cabin Pressurization Control are designed as a single integrated package. The same engine-bleed or APU-bleed air feed conditions the cabin AND pressurises it. The same Electronic Control Unit (ECU) modulates the pack inlet valve AND coordinates with the Cabin Pressure Controller (CPC) to schedule cabin altitude. Treat them as one system.</p>
<p>Two air-cycle machines (ACMs) sit in the aft equipment bay, sharing one primary heat exchanger and one secondary heat exchanger. The aft outflow valve at the aft pressure dome is the primary regulator; aft and forward safety valves catch the abnormals. Maximum cabin-to-ambient differential is <strong>5.5 PSI</strong>; the CABIN PRESS warning fires at <strong>9,800 ft</strong> cabin altitude.</p>',
 'overview',
 10),

-- 2. Components
(@lesson_id, 'Components — What Each Box Does',
 '<h4>Air conditioning side</h4>
<ul>
  <li><strong>Two ACMs (air-cycle machines):</strong> integrated with a SHARED primary + secondary heat exchanger. Located in the aft equipment bay (unpressurised).</li>
  <li><strong>Pack Flow Control + Shut-Off Valve (FCSOV):</strong> one per pack inlet. Pneumatic default OPEN on single-channel ECU fail; CLOSED on dual-channel fail.</li>
  <li><strong>Pack Bypass Shut-Off Valves (SOVs):</strong> open with pack startup; modulated for temperature control.</li>
  <li><strong>Turbine SOVs:</strong> one per ACM. Selecting one pack to MAN/AUTO opens one Turbine SOV; both packs opens both.</li>
  <li><strong>Nacelle Shut-Off Valves:</strong> the engine-side bleed valves. Modulated by the ECU digital channel in control to set flow per BLEED selection.</li>
  <li><strong>Recirc fan + filter:</strong> filter behind AFT class-C baggage compartment. Fan starts at low speed (current inrush limit), then auto-switches to high speed.</li>
  <li><strong>Avionics cooling:</strong> three fans (Pilot, Copilot, Standby). Extraction loop pulling heat off avionics rack, five LCD displays, wardrobe rack. Fully automatic.</li>
  <li><strong>ECS Ground Air connection:</strong> right aft fuselage at station X 860.00. 8" industry-standard fitting. Latched door. Flapper check valve prevents reverse flow.</li>
</ul>
<h4>Pressurization side</h4>
<ul>
  <li><strong>Aft outflow valve:</strong> primary regulator. Located on the aft pressure dome. Modulated by CPC under AUTO; toggle-controlled under MAN.</li>
  <li><strong>Aft safety valve:</strong> on the aft pressure dome. Opens on the ground when at least one engine is at idle or APU is running. Backup to the outflow valve.</li>
  <li><strong>Forward safety valve:</strong> on the forward pressure bulkhead. NORMAL or OPEN only — cannot be modulated by the selector. Used for emergency rapid depressurisation.</li>
  <li><strong>Forward Outflow knob:</strong> on the CPC panel. Bleeds pressure progressively through the forward safety valve.</li>
  <li><strong>Cabin Pressure Controller (CPC):</strong> programmed pressure-schedule computer. Power-up self test; FAULT alert light on the panel.</li>
  <li><strong>Pressurization Indicator Panel:</strong> shows cabin altitude, MAX PRESS at 5.5 PSI placard, DIFF PSI gauge, cabin rate of change in fpm × 1000.</li>
</ul>',
 'components',
 20),

-- 3. Operation
(@lesson_id, 'Operation — How the Architecture Behaves',
 '<p><strong>Single-pack operation</strong> (one pack to MAN or AUTO): the system runs at 70% of selected flow. The recirculation fan runs at low speed.</p>
<p><strong>Dual-pack operation</strong> (both packs to MAN or AUTO): the system runs at full performance based on flow selection and environmental conditions. The recirculation fan runs at high speed.</p>
<p><strong>Cabin/flight-deck temperature split:</strong> the ECU uses the left digital channel for approximately half of the flow from the LEFT pack to control flight-compartment temperature. The right digital channel uses the other half from the left pack PLUS all of the right pack''s flow to control cabin temperature. Net result: cabin gets ~75% of total airflow, flight deck gets ~25%.</p>
<p><strong>Bleed flow control:</strong> with both engines and at least one pack operating, the ECU modulates Nacelle SOVs to balance bleed from the two engines. APU bleed flow is NOT controlled by the BLEED selector knob — APU follows an internal ECU schedule.</p>
<p><strong>Pressurization scheduling:</strong> CPC programmed schedule of cabin altitude vs aircraft altitude. AUTO mode runs the schedule from take-off through landing with minimal crew input. Crew sets LDG ALT and (optionally) MAN DIFF on the CPC panel pre-flight.</p>',
 'operation',
 30),

-- 4. Normal — day-to-day cockpit
(@lesson_id, 'Normal — Day-to-Day Indications and Cockpit Drill',
 '<h4>Pressurization Indicator Panel readouts</h4>
<ul>
  <li><strong>Cabin altitude</strong> (feet × 1000)</li>
  <li><strong>Cabin DIFF PSI</strong> (max 5.5 placarded)</li>
  <li><strong>Cabin rate of change</strong> (fpm × 1000)</li>
  <li><strong>Outflow valve position</strong> (visual indicator)</li>
  <li><strong>Forward safety valve indicator</strong></li>
</ul>
<h4>AIR CONDITIONING control panel</h4>
<ul>
  <li>BLEED switches: 1 / 2 / OFF (per engine)</li>
  <li>BLEED selector: MIN / NORM / MAX</li>
  <li>PACK switches: OFF / MAN / AUTO (one per pack)</li>
  <li>RECIRC fan switch</li>
  <li>FLT COMP TEMP selector + CABIN TEMP selector (rotary; F/A position transfers cabin temp control to the flight-attendant panel)</li>
  <li>CABIN TEMP DISPLAY + scale (C/F)</li>
</ul>
<h4>Cabin Pressure Control (CPC) panel</h4>
<ul>
  <li>AUTO-MAN-DUMP toggle</li>
  <li>LDG ALT thumbwheel (set pre-flight)</li>
  <li>MAN DIFF setting</li>
  <li>FWD OUTFLOW knob (manual fine adjustment via FSV)</li>
  <li>Self-test FAULT alert light</li>
</ul>
<h4>Forward safety valve selector</h4>
<ul>
  <li>Located on the copilot''s side console</li>
  <li>Two positions: NORMAL (closed) / OPEN (fully open)</li>
  <li>Lift safety guard to access</li>
  <li>NOTE: cannot be modulated by this selector</li>
</ul>',
 'normal',
 40),

-- 5. Abnormal
(@lesson_id, 'Abnormal — Failure Modes and Crew Actions',
 '<ul>
  <li><strong>Single ECU digital channel failure:</strong> FCSOV defaults pneumatically OPEN. ECS continues. Other digital channel takes control. Analog backups provide closed/open-only control of the FCSOV.</li>
  <li><strong>Dual ECU digital channel failure:</strong> FCSOV defaults CLOSED. ECS stops. ACMs shut off. Cabin must be ventilated using emergency ram-air. Descend, divert.</li>
  <li><strong>Single pack failure:</strong> select that pack OFF. System reverts to single-pack 70% flow, recirc fan low speed. Continue flight; brief approach for slightly warmer cabin.</li>
  <li><strong>Dual pack failure:</strong> emergency ram-air ventilation. Descend below FL100 immediately. Plan diversion.</li>
  <li><strong>Cabin altitude > 9,800 ft (CABIN PRESS warning):</strong> oxygen masks 100%, EMERGENCY DESCENT, transponder 7700, ATC, run QRH. Memory items first.</li>
  <li><strong>AUTO mode FAULT light persistent:</strong> CPC self-test failed. Switch to MAN mode. Monitor cabin altitude / DIFF PSI / rate continuously.</li>
  <li><strong>Aft outflow valve unresponsive:</strong> use FWD OUTFLOW knob to bleed pressure progressively through forward safety valve. For rapid dump: lift FORWARD SAFETY VALVE selector guard, set OPEN.</li>
  <li><strong>Smoke from packs / aft fuselage:</strong> oxygen masks 100%, recirc fan OFF, packs OFF as per QRH, plan diversion. Smoke source typically traces to the aft equipment bay.</li>
  <li><strong>Slow DIFF PSI trend down at cruise:</strong> early descent, advise ATC, brief cabin and crew, divert nearest suitable. Treat trend as the leading indicator.</li>
</ul>',
 'abnormal',
 50),

-- 6. Indications
(@lesson_id, 'Indications — Lights, Gauges, and What They Tell You',
 '<ul>
  <li><strong>CABIN PRESS warning light (red):</strong> cabin altitude exceeded 9,800 ft. Memory-item event.</li>
  <li><strong>FAULT light on CPC panel:</strong> momentary at power-up self-test (normal). Persistent = controller failure → switch to MAN.</li>
  <li><strong>BLEED indication on Engine Display (ED):</strong>
    <ul>
      <li>BLEED <em>white</em> = MIN bleed selected with switches ON, take-off rating set (NORMAL).</li>
      <li>BLEED <em>amber</em> = NORM or MAX selected with NTOP set (CAUTION — wrong setting for take-off).</li>
      <li>On MTOP with NORM/MAX: rating display itself changes to MCP and BLEED is not shown.</li>
    </ul>
  </li>
  <li><strong>Pack indications:</strong> each pack switch position (OFF/MAN/AUTO) annunciated. Pack inlet temperature, mass flow.</li>
  <li><strong>Cabin altitude indicator:</strong> live cabin altitude.</li>
  <li><strong>DIFF PSI gauge:</strong> live cabin-to-ambient differential. Watch for trend down on every cruise scan.</li>
  <li><strong>Rate-of-change indicator:</strong> cabin altitude fpm × 1000. High positive = climbing fast = potential leak.</li>
  <li><strong>NVS INOP (F/A panel):</strong> separate item — Active Noise & Vibration Suppression failure (covered in Aeroplane General, not pressurisation).</li>
  <li><strong>Forward safety valve indicator:</strong> shows FSV closed vs open.</li>
</ul>',
 'indications',
 60),

-- 7. Limitations
(@lesson_id, 'Limitations — The Numbers You Cannot Exceed',
 '<ul>
  <li><strong>Maximum cabin-to-ambient differential pressure:</strong> 5.5 PSI.</li>
  <li><strong>CABIN PRESS warning trip:</strong> cabin altitude > 9,800 ft.</li>
  <li><strong>Anti-suckback (ground):</strong> external pressure cannot exceed internal cabin pressure by more than 0.5 PSI.</li>
  <li><strong>BLEED selection for take-off:</strong> MIN ONLY — NORM/MAX trigger amber BLEED on the ED.</li>
  <li><strong>Pre-pressurisation rate (take-off):</strong> -300 fpm to 400 ft below take-off altitude.</li>
  <li><strong>CPC take-off mode duration after lift-off:</strong> 10 minutes (supports emergency return to departure without re-selecting LDG ALT, valid only for take-off altitudes over 8,000 ft).</li>
  <li><strong>Single-pack flow level:</strong> 70% of selected flow.</li>
  <li><strong>Power-lever angle threshold for ground/take-off mode:</strong> 60° (above 60° → CPC starts pre-pressurisation; below 60° → aft outflow fully open).</li>
  <li><strong>Aft safety valve ground behaviour:</strong> opens on the ground when at least one engine at idle OR APU running.</li>
  <li><strong>Forward safety valve modulation:</strong> NOT possible via the selector — only NORMAL or OPEN. For modulated bleed, use the FWD OUTFLOW knob.</li>
</ul>',
 'limitations',
 70),

-- 8. Memory items
(@lesson_id, 'Memory — Mnemonics for the Sim and the Oral',
 '<p>Drill these. They carry every ECS / pressurisation question on a recurrent.</p>
<ol>
  <li><strong>ACM-2-DUAL-HX</strong> — TWO ACMs share ONE primary + ONE secondary heat exchanger.</li>
  <li><strong>5-5-9-8</strong> — 5.5 PSI max diff. 9,800 ft cabin altitude warning trip.</li>
  <li><strong>MIN-FOR-TO</strong> — only MIN bleed is legal for take-off. NORM / MAX trigger amber BLEED.</li>
  <li><strong>60° POWER-LEVER</strong> — the threshold that flips ground (outflow open) to flight (outflow modulating).</li>
  <li><strong>400-AT-300</strong> — pre-pressurise to 400 ft below take-off altitude at -300 fpm.</li>
  <li><strong>10-MIN-RETURN</strong> — CPC stays in take-off mode 10 minutes after lift-off for emergency return.</li>
  <li><strong>0-PT-5-SUCKBACK</strong> — ground anti-suckback differential limit is 0.5 PSI.</li>
  <li><strong>DECR = UP</strong> — in MAN mode, DECR opens outflow → cabin pressure decreases → cabin altitude INCREASES.</li>
  <li><strong>FSV = ON-OFF</strong> — forward safety valve selector can ONLY be NORMAL or OPEN; cannot modulate.</li>
</ol>
<p>For the cabin altitude warning, run the memory chant: <em>"Mask · 100% · Descend · 7700 · ATC."</em> Five words; rehearse them every recurrent.</p>',
 'memory',
 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
