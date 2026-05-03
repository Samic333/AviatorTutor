-- =============================================================================
-- AviatorTutor — Phase 15: ATA 36 Pneumatics — sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'pneumatics' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'pneumatics-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — APU + Bleed Architecture',
 '<p>The Q400 pneumatic system is built around the APU and the bleed-air distribution. The APU is a tailcone-mounted gas turbine driving a 28 VDC starter-generator, replacing the standard composite tailcone with a titanium tailcone and firewall. The APU supplies two products: 28 VDC for the electrical system and bleed air for the ECS. The defining limitation is that the APU CANNOT be operated in flight — its shutoff valve closes automatically when airborne. The bleed-air integration logic is clean: APU BL AIR auto-de-energizes when either main engine BLEED switch is at 1 or 2, preventing simultaneous APU + engine bleed supply. Auto fire detection + extinguishing protects the APU compartment continuously when right essential 28 VDC bus is energised.</p>',
 'overview', 10),

(@lesson_id, 'Components — APU + Bleed System',
 '<ul>
  <li><strong>APU:</strong> gas turbine engine in titanium tailcone with firewall. Two clamshell access doors on bottom of tailcone.</li>
  <li><strong>APU FADEC:</strong> controls start, normal operation, malfunction monitoring.</li>
  <li><strong>Starter-generator:</strong> 28 VDC, gearbox-mounted. Starts from a/c batteries OR external power.</li>
  <li><strong>APU intake:</strong> screened inlet duct on RIGHT REAR of fuselage. Optional louvered cover for snow/sleet protection.</li>
  <li><strong>APU exhaust:</strong> ejector + upwards-pointing outlet at AFT END of titanium tailcone.</li>
  <li><strong>APU shutoff valve:</strong> at left wing collector bay end of fuel line. Opens on PWR push, closes on shutdown / fire / EXTG / aircraft in flight.</li>
  <li><strong>APU control panel:</strong> overhead console. Switchlights: PWR, START, GEN, BL AIR. Advisory: GEN OHT.</li>
  <li><strong>APU bleed valve:</strong> opens on BL AIR switchlight push when APU operating. Supplies bleed to ECS + holds CPCS aft safety valve open.</li>
  <li><strong>APU check valve:</strong> prevents APU bleed entering engine bleed supply.</li>
  <li><strong>Wing duct check valves:</strong> additional protection against cross-feed of APU bleed into airframe de-icing system.</li>
  <li><strong>APU Fire Protection Panel (FPP):</strong> overhead console. EXTG switchlight (guarded), FIRE TEST pushbutton, FIRE/BTL ARM/BOTTLE LOW/FAULT/FUEL VALVE OPEN/CLOSED indicators.</li>
  <li><strong>APU fire bottle:</strong> stainless steel, with distribution tubing.</li>
  <li><strong>APU loop sensor:</strong> along tailcone above APU. Continuous fire/overheat detection.</li>
  <li><strong>APU starter batteries:</strong> 2×40 Ahr NiCad (mains).</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — Start, Normal Run, Bleed, Shutdown',
 '<h4>APU start</h4>
<ul>
  <li>Aircraft on ground, no fire detected, EXTG not selected — only then PWR can arm.</li>
  <li>Push PWR: opens APU shutoff valve, arms start circuits, FUEL VALVE OPEN green on FPP.</li>
  <li>Push START: STARTER amber on START switchlight; FADEC sequences start.</li>
  <li>Starter engaged until APU reaches HALF operating speed; then disengages.</li>
  <li>At operating speed: PWR RUN green; STARTER segment goes out.</li>
  <li>Battery start (no external DC): bus voltage drops — 100% charge → ~20 V; 50% charge → ~18 V.</li>
</ul>
<h4>Normal run</h4>
<ul>
  <li>Push GEN switchlight: ON green, APU starter-generator supplies 28 VDC.</li>
  <li>Push BL AIR switchlight: OPEN green, bleed valve open, bleed flows to ECS + holds CPCS aft safety valve open.</li>
  <li>If either engine BLEED switch is at 1 or 2: APU BL AIR switchlight auto-de-energizes.</li>
  <li>If APU EGT high: bleed reduces (priority to GEN load).</li>
  <li>If external AC or DC power applied: APU GEN output auto-prevented.</li>
  <li>If starter-generator overheats: APU auto-shuts down + GEN OHT amber advisory.</li>
</ul>
<h4>Aircraft becomes airborne</h4>
<ul>
  <li>APU shutoff valve closes automatically — APU stops if running.</li>
</ul>
<h4>APU normal shutdown</h4>
<ul>
  <li>Close BL AIR (push to OPEN out).</li>
  <li>Select GEN off (push to ON out).</li>
  <li>Push PWR: shutoff valve closes, APU stops, FUEL VALVE CLOSED white.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Pre-Flight, Ground Ops',
 '<h4>Pre-flight (APU start on ground)</h4>
<ul>
  <li>Verify battery charge — full preferred (100% gives ~20 V bus during start; 50% gives ~18 V — risk of brown-out).</li>
  <li>Or: external DC power applied — start from cart, no battery drain.</li>
  <li>Start sequence per AOM: PWR → START → wait for RUN → GEN → BL AIR as required.</li>
  <li>Monitor APU fire panel during start — no FIRE/BTL ARM/FAULT.</li>
  <li>If using APU bleed for ECS: confirm BL AIR OPEN green; engine BLEED switches OFF (cross-de-energization).</li>
</ul>
<h4>Engine start with APU running</h4>
<ul>
  <li>APU GEN supplies 28 VDC to buses in parallel with batteries — eases engine start load.</li>
  <li>APU bleed (if open) reduces during high-EGT events to preserve GEN load.</li>
</ul>
<h4>APU shutdown after engine start</h4>
<ul>
  <li>Engine GENs on line → APU GEN continues in parallel until manually selected off.</li>
  <li>Normal shutdown: close BL AIR, GEN off, push PWR.</li>
  <li>For takeoff: APU must be shut down (it cannot operate in flight; shutoff valve closes anyway).</li>
</ul>',
 'normal', 40),

(@lesson_id, 'Abnormal — Fire, Faults, Auto Behaviour',
 '<ul>
  <li><strong>APU FIRE detected:</strong> FIRE red on FPP, MASTER WARNING + CHECK FIRE DET flash, BTL ARM amber, fuel valve auto-closes, EXTG segment illuminates. APU auto-shuts down. After 7 SECONDS of fire detection, the extinguishing agent automatically releases — BTL ARM goes out.</li>
  <li><strong>Auto-extg failed (BTL ARM still on after 7 sec):</strong> push the guarded EXTG switchlight to manually discharge. EXTG segment goes out.</li>
  <li><strong>Once bottle discharged:</strong> APU restart is PREVENTED until bottle is replaced. Maintenance write-up; plan around no APU.</li>
  <li><strong>BOTTLE LOW (amber):</strong> fire bottle low or empty. Defer per MEL or replenish.</li>
  <li><strong>FAULT (amber) on FPP:</strong> fire system or FPP fault. Run QRH; defer per MEL.</li>
  <li><strong>GEN OHT (amber):</strong> APU starter-generator overheat → APU auto-shutdown.</li>
  <li><strong>APU PWR FAIL (amber):</strong> APU FAULT detected (overspeed/underspeed/start fail/EGT/oil/sensor/valve/relay/internal). APU auto-shutdown. PWR must be reselected after auto shutdown.</li>
  <li><strong>Insufficient battery for start:</strong> bus voltage drops too low. Apply external DC or recharge before retry. Repeated attempts on weak batteries can cause system damage.</li>
  <li><strong>Aircraft becomes airborne with APU running:</strong> APU shutoff valve auto-closes; APU stops. Crew sees PWR FAIL or normal shutdown depending on timing.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Switchlights, Advisories, Lights',
 '<ul>
  <li><strong>APU PWR switchlight:</strong> RUN (green) at operating speed; FAIL (amber) on fault. Push to start, push to stop.</li>
  <li><strong>APU START switchlight:</strong> STARTER (amber) during sequence; OUT when complete.</li>
  <li><strong>APU GEN switchlight:</strong> ON (green) supplying DC; WARN (amber) generator off-line with APU running.</li>
  <li><strong>APU BL AIR switchlight:</strong> OPEN (green) bleed valve open. OUT when closed or auto-de-energized.</li>
  <li><strong>APU GEN OHT advisory:</strong> amber. Starter-generator overheat — APU auto-shutdown.</li>
  <li><strong>FUEL VALVE OPEN (FPP):</strong> green when valve open.</li>
  <li><strong>FUEL VALVE CLOSED (FPP):</strong> white when valve closed.</li>
  <li><strong>FIRE (FPP):</strong> red. APU fire detected.</li>
  <li><strong>BTL ARM (FPP):</strong> amber while bottle armed; OUT after auto-extg or no power.</li>
  <li><strong>EXTG switchlight (FPP, guarded):</strong> EXTG segment white = bottle armed for manual discharge. Push to manually fire.</li>
  <li><strong>BOTTLE LOW (FPP):</strong> amber. Bottle low/empty.</li>
  <li><strong>FAULT (FPP):</strong> amber. Fire system or FPP fault.</li>
  <li><strong>FIRE TEST pushbutton (FPP):</strong> momentary. Verifies full warning chain.</li>
  <li><strong>MFD ELECTRICAL page APU GEN LOAD:</strong> digital, white. + prefix = overload. Same format as engine GEN load (Phase 5).</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>APU operation in flight:</strong> NOT permitted. Shutoff valve closes automatically.</li>
  <li><strong>Auto-extg time delay after FIRE detected:</strong> 7 SECONDS.</li>
  <li><strong>Starter cutoff:</strong> HALF operating speed.</li>
  <li><strong>Battery start bus voltage at 100% charge:</strong> ~20 VDC.</li>
  <li><strong>Battery start bus voltage at 50% charge:</strong> ~18 VDC.</li>
  <li><strong>Starter batteries:</strong> 2 × 40 Ahr NiCad (the same MAIN + AUX of the EPGDS).</li>
  <li><strong>Ambient temperature limit (composite cooling duct removed):</strong> 30°C OR ISA+25°C (whichever LOWER).</li>
  <li><strong>Ambient temperature limit (Air Inlet Louvre installed):</strong> 21°C.</li>
  <li><strong>APU intake location:</strong> RIGHT REAR of fuselage.</li>
  <li><strong>APU exhaust location:</strong> AFT END of titanium tailcone, upwards-pointing.</li>
  <li><strong>APU access:</strong> 2 clamshell doors on BOTTOM of tailcone.</li>
  <li><strong>APU fire detection power source:</strong> right ESS 28 VDC bus.</li>
  <li><strong>PWR arming preconditions:</strong> 3 (ground + no fire + EXTG not selected).</li>
  <li><strong>APU shutoff valve close conditions:</strong> 4 (PWR off / fire / EXTG / aircraft in flight).</li>
  <li><strong>APU GEN output auto-prevented:</strong> when external AC OR DC power is applied.</li>
  <li><strong>APU BL AIR auto-de-energizes:</strong> when either engine BLEED switch is at 1 or 2.</li>
  <li><strong>APU GEN OHT response:</strong> automatic APU shutdown.</li>
  <li><strong>Restart after fire bottle discharge:</strong> NOT permitted until bottle replaced.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>APU-GROUND-ONLY</strong> — APU cannot be operated in flight; shutoff valve closes airborne.</li>
  <li><strong>TITANIUM-TAIL-2-CLAMSHELL</strong> — APU lives in titanium tailcone with firewall; 2 clamshell access doors on bottom.</li>
  <li><strong>7-SEC-AUTO-EXTG</strong> — APU fire bottle auto-releases 7 seconds after FIRE detected.</li>
  <li><strong>HALF-SPEED-STARTER</strong> — starter stays engaged until APU reaches half operating speed.</li>
  <li><strong>BL-AIR-AUTO-OFF-WITH-ENG</strong> — APU BL AIR auto-de-energizes when either engine BLEED switch is at 1 or 2.</li>
  <li><strong>LEFT-COLLECTOR-APU-FUEL</strong> — APU fuel from LEFT wing collector bay through APU shutoff valve.</li>
  <li><strong>RIGHT-REAR-INLET</strong> — APU intake on RIGHT REAR of fuselage.</li>
  <li><strong>3-COND-PWR-ARM</strong> — PWR arms only if: on ground + no fire + EXTG not selected.</li>
  <li><strong>4-COND-FUEL-CLOSE</strong> — fuel valve closes if: PWR off / fire / EXTG pushed / aircraft in flight.</li>
  <li><strong>100-20-50-18</strong> — battery start bus voltage: 100% charge → ~20 V; 50% → ~18 V.</li>
  <li><strong>LOUVRE-21-DUCT-30</strong> — ambient limits: Louvre 21°C; composite duct removed 30°C/ISA+25 (lower wins).</li>
  <li><strong>NO-RESTART-AFTER-DISCHARGE</strong> — once APU fire bottle discharged, restart prevented until bottle replaced.</li>
  <li><strong>EGT-LIMIT-BLEED-DOWN</strong> — high APU EGT reduces bleed (priority to GEN load).</li>
  <li><strong>EXT-PWR-INHIBITS-APU-GEN</strong> — APU GEN output auto-prevented when external AC or DC power applied.</li>
 </ol>
<p>APU start chant: <em>"PWR · START · half speed · GEN · BL AIR (if engine BLEED off)."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
