-- =============================================================================
-- AviatorTutor — Phase 6: ATA 26 Fire Protection — lesson sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'fire-protection' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'fire-protection-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — Detection, Indication, Extinguishing',
 '<p>The Q400 fire protection system has three jobs: <strong>detect</strong> fire and overheat, <strong>indicate</strong> the location and severity to the crew, and <strong>extinguish</strong> the fire when commanded. Each protected zone has its own detector and its own bottle. The architecture is deliberately compartmented so that a failure in one zone does not cascade to another. Detection is mostly pneumatic for engines and APU (helium-filled APD sensor tubes); smoke detection is electronic for the baggage compartments and lavatory. Extinguishing is squib-fired (electrical) for engines and baggage; thermally-activated (no electrical) for the lavatory waste bin. The Control Amplifier ties detection logic to bottle pressure monitoring and drives every advisory light on the Fire Protection Panel.</p>',
 'overview', 10),

(@lesson_id, 'Components — Detectors, Bottles, Panel',
 '<ul>
  <li><strong>Engine APDs (Advanced Pneumatic Detectors):</strong> 6 total — 3 per engine in Primary Engine Zone (PEZ), Leading Edge Zone (LEZ), and Main Wheel Well (MWW). Helium-filled sensor tubes; integrity switch + alarm switch.</li>
  <li><strong>APU APD:</strong> 1 detector. APU fire detection covered separately under OM-B 12.19.4.</li>
  <li><strong>Smoke detectors:</strong> 2 in aft baggage (front and rear of compartment) + 1 in fwd baggage + 1 in lavatory = 4 total.</li>
  <li><strong>Engine fire bottles:</strong> 2 dual-port bottles in LEFT wing root, positioned FWD and AFT. Each bottle is plumbed for two shots into either engine nacelle (PEZ, LEZ, MWW zones).</li>
  <li><strong>Baggage HRD bottles:</strong> 2 High-Rate Discharge bottles — one for each baggage compartment.</li>
  <li><strong>Baggage LRD bottle:</strong> 1 shared Low-Rate Discharge bottle located in the AFT equipment bay. Serves both fwd and aft baggage compartments.</li>
  <li><strong>Lavatory Potty Bottle:</strong> 1 dual-discharge thermally-activated bottle inside the waste-bin cabinet. Fusible seals melt at temperature; no wiring.</li>
  <li><strong>Portable Halon 1211 extinguishers:</strong> 4 total — 1 in flight compartment + 3 in passenger compartment.</li>
  <li><strong>Control Amplifier:</strong> Performs detection / extinguishing monitoring, BIT, drives advisory lights, drives CHECK FIRE DET on Caution and Warning panel, sounds optional fire tone.</li>
  <li><strong>Fire Protection Panel:</strong> Centre overhead. Houses ENGINE 1/2 and APU sections, BAGGAGE FWD and AFT sections, FIRE BOTTLE arming displays, TEST DETECTION switch.</li>
 </ul>',
 'components', 20),

(@lesson_id, 'Operation — How Each Zone Works',
 '<h4>Engine fire</h4>
<ul>
  <li>APD sensor heats up → helium pressure rises → alarm switch closes → Control Amplifier sends signal.</li>
  <li>Both ENGINE FIRE Press-To-Reset lights flash (red); CHECK FIRE DET warning illuminates; PULL FUEL/HYD OFF T-handle on affected engine illuminates red; optional fire tone sounds.</li>
  <li>PNF or PF presses an ENGINE FIRE light to silence the tone and stop flashing — light then stays on steady for the duration.</li>
  <li>PULL the affected T-handle: closes fuel + hydraulic shut-off valves AND arms the bottle squibs (yellow ARM lights illuminate).</li>
  <li>Select EXTG switch to FWD or AFT — squib fires, burst disc ruptures, suppressant discharges into PEZ + LEZ + MWW.</li>
  <li>Wait 30 seconds. If fire light remains, select the OTHER position on the EXTG switch for the second shot.</li>
</ul>
<h4>APU fire</h4>
<ul>
  <li>Single APD in the APU compartment. Detection logic per OM-B 12.19.4. APU shuts down automatically; APU fire bottle discharges automatically OR on crew command depending on configuration.</li>
</ul>
<h4>Aft baggage fire</h4>
<ul>
  <li>Either smoke detector triggers → AFT ARM (amber) and BAGGAGE AFT SMOKE/EXTG switchlight illuminate.</li>
  <li>Control Amplifier drops power to the inlet and outlet vent valves → they close automatically. INLET and OTLT CLOSED lights illuminate. Airflow to the fire is starved.</li>
  <li>PUSH the SMOKE/EXTG switchlight → HRD bottle dumps into aft baggage immediately. AFT ARM goes out, AFT LOW (amber) illuminates.</li>
  <li>After a 7-MINUTE delay → shared LRD bottle automatically discharges into aft baggage. FWD LOW (amber) illuminates when LRD is depleted.</li>
</ul>
<h4>Fwd baggage fire</h4>
<ul>
  <li>FWD smoke detector triggers → FWD ARM (amber) and BAGGAGE FWD SMOKE/EXTG switchlight illuminate.</li>
  <li>PUSH the FWD SMOKE/EXTG switchlight → HRD AND LRD bottles BOTH discharge SIMULTANEOUSLY. No 7-minute delay.</li>
  <li>FWD ARM goes out immediately, FWD LOW (amber) illuminates immediately, AFT LOW illuminates when LRD depletes.</li>
</ul>
<h4>Lavatory fire</h4>
<ul>
  <li>Smoke detector senses smoke → cabin repeater lights illuminate, smoke detector LED illuminates, audible chime through P/A. NO flight-deck indication.</li>
  <li>Waste-bin temperature rises → end-cap fusible seals melt → Potty Bottle dual outlets release extinguishant. No crew action required.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Indications and Crew Discipline',
 '<h4>Pre-flight panel scan</h4>
<ul>
  <li>FAULT A and B lights — both extinguished.</li>
  <li>BTL LOW lights — extinguished (bottles serviceable). Run the TEST DETECTION switch ENGINE 1 then ENGINE 2 — observe full caution-and-warning chain (master warning flash, CHECK FIRE DET red, PULL FUEL/HYD OFF lights, FAULT A/B amber, both ENGINE FIRE lights flash, fire tone). Press an ENGINE FIRE light to acknowledge.</li>
  <li>BAGGAGE smoke detector test pushbutton — momentary press. Master warning flashes, SMOKE light, EXTG segment, ARM segment illuminate as advertised.</li>
  <li>Lavatory smoke detector self-test — performed by cabin crew using switch on the unit. All cabin repeater lights illuminate, single chime through P/A, red LED on detector.</li>
  <li>Confirm 4 portable Halon 1211 extinguishers on board with gauge in GREEN range.</li>
</ul>
<h4>Cruise scan (every 10 min)</h4>
<ol>
  <li>No CHECK FIRE DET or master caution active.</li>
  <li>No FAULT A/B amber on Fire Protection Panel.</li>
  <li>No BTL LOW amber on any bottle.</li>
  <li>No BAGGAGE SMOKE switchlight illuminated.</li>
</ol>',
 'normal', 40),

(@lesson_id, 'Abnormal — What You Do When It Fires',
 '<ul>
  <li><strong>Engine fire (in flight):</strong> ENGINE FIRE flashing red + CHECK FIRE DET + T-handle illuminated. <em>Press to reset</em> stops the tone. Aviate, navigate, communicate. Then: PWR LVR (affected) DISC, PROP LVR FTR, CONDITION LVR FUEL OFF, PULL the T-handle, EXTG switch to applicable bottle. Wait 30 seconds; if fire persists select the second bottle.</li>
  <li><strong>FAULT A or FAULT B caution (no fire):</strong> Loop detector circuit fault — APD sensor or wiring failure. Per the table in 12.7.5, certain fault + fire combinations still produce a fire indication; certain combinations produce only a fault. QRH non-normal — continue, monitor, defer per MEL.</li>
  <li><strong>BTL LOW (amber):</strong> Bottle empty or low pressure. If discovered pre-flight, defer per MEL or maintenance action. In flight, plan around the reduced redundancy.</li>
  <li><strong>Aft baggage smoke:</strong> AFT SMOKE/EXTG illuminates and bottle ARM lights. PUSH the switchlight → HRD discharges, vents close, AFT LOW illuminates. After 7 min the LRD auto-dumps.</li>
  <li><strong>Fwd baggage smoke:</strong> FWD SMOKE/EXTG illuminates. PUSH → HRD AND LRD discharge simultaneously.</li>
  <li><strong>Lavatory smoke:</strong> NOT shown in cockpit. Cabin crew calls. Verify by their report. The Potty Bottle activates on its own thermally — crew action is to land at nearest suitable.</li>
  <li><strong>Cockpit smoke / fire:</strong> All crew oxygen masks ON, EMERGENCY position (100% O2 positive pressure). Use the cockpit Halon 1211 portable. Check fire-extinguisher gauge GREEN before discharge.</li>
  <li><strong>APU fire:</strong> APU shutdown logic + APU bottle discharge per QRH. Continue per non-normal.</li>
 </ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Fire Protection Panel Layout',
 '<ul>
  <li><strong>FAULT A / FAULT B (amber):</strong> loop detector circuit malfunction.</li>
  <li><strong>BOTTLE ARMING (amber):</strong> illuminates when T-handle is pulled and bottle is charged and EXTG armed. Goes out when bottle is discharged or system disarmed.</li>
  <li><strong>EXTG SWITCH:</strong> AFT BTL or FWD BTL — discharges chosen bottle into nacelle.</li>
  <li><strong>PULL FUEL/HYD OFF T-handle (red):</strong> illuminated red on overheat or fire. Pulled-out position = fuel and hydraulic valves CLOSED, bottle squibs ARMED.</li>
  <li><strong>HYD SHUT-OFF VALVE lights:</strong> OPEN green / CLOSED white.</li>
  <li><strong>FUEL SHUT-OFF VALVE lights:</strong> OPEN green / CLOSED white.</li>
  <li><strong>BTL LOW (amber):</strong> one or both bottles low/empty.</li>
  <li><strong>TEST DETECTION switch:</strong> ENGINE 1 / ENGINE 2 — exercises full warning chain. Spring-loaded centre off.</li>
  <li><strong>BAGGAGE FWD/AFT SMOKE/EXTG switchlight:</strong> illuminates on smoke detection; pressing it discharges that compartment''s bottle(s).</li>
  <li><strong>FWD/AFT ARM segments:</strong> bottle armed for that compartment.</li>
  <li><strong>FWD/AFT LOW (amber):</strong> respective bottle low pressure / discharged. LRD low light is shared between fwd and aft compartments.</li>
  <li><strong>INLT VALVE / OTLT VALVE CLOSED (white):</strong> aft baggage vent valves driven closed by smoke alarm.</li>
  <li><strong>CHECK FIRE DET (red, on C&W panel):</strong> warning level — fire or major detection fault.</li>
 </ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>APDs total:</strong> 7 (6 engine + 1 APU).</li>
  <li><strong>APDs per engine:</strong> 3 (PEZ, LEZ, MWW).</li>
  <li><strong>Smoke detectors total:</strong> 4 (2 aft baggage + 1 fwd baggage + 1 lavatory).</li>
  <li><strong>Engine fire bottles:</strong> 2 dual-port, in LEFT wing root, FWD + AFT positions.</li>
  <li><strong>Shots per engine:</strong> 2 (one from each bottle).</li>
  <li><strong>Baggage HRD bottles:</strong> 2 (one each for fwd and aft).</li>
  <li><strong>Baggage LRD bottles:</strong> 1 shared (located in AFT equipment bay).</li>
  <li><strong>Aft baggage LRD delay:</strong> 7 minutes after HRD discharge.</li>
  <li><strong>Fwd baggage discharge:</strong> simultaneous HRD + LRD (no delay).</li>
  <li><strong>Portable Halon 1211 extinguishers:</strong> 4 total (1 cockpit + 3 cabin).</li>
  <li><strong>Halon 1211:</strong> non-corrosive, non-toxic, will not freeze. Effective on electrical / oil / fuel.</li>
  <li><strong>Portable extinguisher gauge ranges:</strong> GREEN serviceable, YELLOW overcharge, RED recharge.</li>
  <li><strong>Lavatory bottle activation:</strong> thermally-fused at end-cap set point. No electrical interface, no manual cockpit discharge.</li>
  <li><strong>Lavatory smoke flight-deck indication:</strong> NONE. Cabin only.</li>
 </ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>7-APDs</strong> — 6 engine + 1 APU = 7 Advanced Pneumatic Detectors.</li>
  <li><strong>3-PER-NACELLE</strong> — PEZ + LEZ + MWW APDs per engine.</li>
  <li><strong>2-2-1-1-SMOKE</strong> — 2 aft baggage + 1 fwd baggage + 1 lavatory smoke detectors.</li>
  <li><strong>2-BOTTLES-LEFT-WING</strong> — Two dual-port engine fire bottles in the left wing root, FWD + AFT.</li>
  <li><strong>HRD-AFT-7-LRD</strong> — Aft baggage HRD discharges immediately, LRD follows automatically 7 minutes later.</li>
  <li><strong>FWD-BOTH-NOW</strong> — Fwd baggage discharges HRD and LRD simultaneously.</li>
  <li><strong>POTTY-FUSE</strong> — Lavatory bottle is thermally fused, no electrical connection.</li>
  <li><strong>LAV-CABIN-ONLY</strong> — Lavatory smoke is indicated in the cabin only, not in the flight deck.</li>
  <li><strong>HALON-1-3-411</strong> — 1 cockpit + 3 cabin = 4 Halon 1211 portables.</li>
  <li><strong>PULL-ARMS-EXTG-FIRES</strong> — T-handle PULL arms the bottles; EXTG switch FIRES the chosen bottle.</li>
  <li><strong>100%-MASK-COCKPIT</strong> — Cockpit portable use requires oxygen mask EMERGENCY at 100% positive pressure.</li>
 </ol>
<p>Engine fire chant: <em>"FLASH · PRESS · PULL · EXTG · 30-sec · OTHER if persists."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
