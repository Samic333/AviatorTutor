-- =============================================================================
-- AviatorTutor — Phase 5: ATA 24 Electrical Power — lesson sections (8 types).
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'electrical' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'electrical-overview' LIMIT 1);

DELETE FROM lesson_sections WHERE lesson_id = @lesson_id;

INSERT INTO lesson_sections (lesson_id, title, body, section_type, sort_order) VALUES
(@lesson_id, 'Overview — EPGDS, Brain, and Bus Topology',
 '<p>The Q400 Electrical Power Generation and Distribution System (EPGDS) is engineered for in-flight redundancy. Two halves: an AC generation system providing 115 VAC variable frequency from two engine-driven generators; a DC generation system providing 28 VDC from two engine-driven starter/generators, two Transformer Rectifier Units (TRUs), three NiCad batteries, and an optional APU starter/generator. The Electrical Power Control Unit (EPCU) is the system brain — it monitors every source and bus and re-configures the bus tie contactors automatically on any failure. Each generator has its own Generator Control Unit (GCU). The crew interface is the AC CONTROL panel, the DC CONTROL panel, and the MFD electrical page.</p>',
 'overview', 10),

(@lesson_id, 'Components — Generators, TRUs, Batteries, Buses',
 '<ul>
  <li><strong>2 × Engine-driven AC generators:</strong> 115 VAC variable frequency.</li>
  <li><strong>2 × Engine-driven starter/generators:</strong> 28 VDC. Also serve as engine starters.</li>
  <li><strong>2 × Transformer Rectifier Units (TRUs):</strong> AC → 28 VDC. Primary in-flight DC source.</li>
  <li><strong>3 × NiCad batteries:</strong> two 40 Ahr (main + aux) + one 17 Ahr standby (forward fuselage).</li>
  <li><strong>1 × APU starter/generator:</strong> in the tail cone. 28 VDC. Ground operation primarily.</li>
  <li><strong>EPCU:</strong> Electrical Power Control Unit — the brain.</li>
  <li><strong>2 × GCU:</strong> Generator Control Unit — per generator.</li>
  <li><strong>AC PPU:</strong> External Power Protection Unit — quality monitor on AC GPU.</li>
  <li><strong>DC GPU receptacle:</strong> LEFT side of forward fuselage.</li>
  <li><strong>AC GPU receptacle:</strong> RIGHT side of forward fuselage near nose cone.</li>
  <li><strong>Bus topology:</strong> two main buses + two secondary buses + ESS bus + standby bus, interconnected by bus tie contactors managed by the EPCU.</li>
</ul>',
 'components', 20),

(@lesson_id, 'Operation — How Power Flows Normally',
 '<h4>Normal in-flight operation</h4>
<ul>
  <li>Each engine-driven AC generator powers its own variable-frequency bus.</li>
  <li>Each variable-frequency bus feeds one TRU.</li>
  <li>Each TRU feeds one DC bus.</li>
  <li>Each engine-driven DC starter/generator also feeds its dedicated DC bus.</li>
  <li>Result: four independent DC sources, four buses, automatic bus tie reconfiguration on any failure.</li>
</ul>
<h4>Battery operation</h4>
<ul>
  <li>40 Ahr nominal 24 VDC under load (20 cells × 1.2 VDC/cell). No-load ~28 VDC.</li>
  <li>Charging voltage 28–32 VDC. Below 28 VDC the batteries discharge into aircraft loads.</li>
  <li>Standby battery diode-isolated from left main during engine start to keep ESS bus voltage acceptable.</li>
</ul>
<h4>External power</h4>
<ul>
  <li>DC GPU connected: generator-to-bus connections INHIBITED by EPCU. Cart powers buses; generators wait.</li>
  <li>AC GPU passes through External Power Protection Unit before reaching variable-frequency buses.</li>
  <li>On engine-start with DC GPU connected: contactors close automatically, batteries assist start in parallel with cart.</li>
</ul>',
 'operation', 30),

(@lesson_id, 'Normal — Cockpit Indications and Cruise Discipline',
 '<h4>MFD Electrical Page</h4>
<ul>
  <li>AC GEN 1 and AC GEN 2 LOAD: digital display, 1.00 = 100%, "+" prefix = overload.</li>
  <li>DC GEN 1 and DC GEN 2 LOAD: similar format.</li>
  <li>TRU 1 and TRU 2 voltage indication.</li>
  <li>Battery voltage / charge state.</li>
  <li>Bus tie status and bus voltage indications.</li>
</ul>
<h4>AC CONTROL panel (overhead)</h4>
<ul>
  <li>External power switch, AC generator control.</li>
</ul>
<h4>DC CONTROL panel (overhead)</h4>
<ul>
  <li>BATTERY MASTER switch.</li>
  <li>DC generator control switches.</li>
  <li>External DC power control.</li>
</ul>
<h4>Cruise scan (every 10 minutes)</h4>
<ol>
  <li>AC GEN 1/2 loads — within band, no "+"</li>
  <li>DC GEN 1/2 loads — within band</li>
  <li>TRU 1/2 voltages stable around 28 VDC</li>
  <li>Battery indications nominal</li>
  <li>Bus tie status as expected</li>
  <li>No DC BUS caution illuminated</li>
</ol>',
 'normal', 40),

(@lesson_id, 'Abnormal — Failures and the EPCU Response',
 '<ul>
  <li><strong>Single AC generator failure:</strong> EPCU re-configures; surviving AC gen feeds both variable-frequency buses (load permitting).</li>
  <li><strong>Single DC generator failure:</strong> bus ties close; surviving DC gen + both TRUs feed all DC buses. DC GEN caution illuminates.</li>
  <li><strong>Single TRU failure:</strong> bus tie reconfiguration; surviving TRU + DC gens cover the load.</li>
  <li><strong>Main bus fault:</strong> 5-second tolerance. DC BUS caution first; if persistent, EPCU trips generator and locks out battery contactors. MAIN BATTERY / AUX-STBY BATTERY + DC GEN cautions follow.</li>
  <li><strong>Dual generator failure:</strong> TRUs continue if AC gens still turning. ESS buses remain alive. Load-shed per EPCU logic for non-essential.</li>
  <li><strong>All DC sources lost:</strong> battery-only operation. 40 Ahr main + aux serve essential buses; 17 Ahr standby covers ESS standby bus longest. NEAREST suitable airport — finite battery time.</li>
  <li><strong>External power fault during start:</strong> EPCU rejects bad power; aircraft falls back to internal sources.</li>
  <li><strong>Battery caution (MAIN / AUX / STBY):</strong> discrete fault on the named battery; QRH non-normal.</li>
</ul>',
 'abnormal', 50),

(@lesson_id, 'Indications — Caution Lights and MFD Displays',
 '<ul>
  <li><strong>DC BUS caution:</strong> bus fault detected. Counts down; persistent → generator trip and battery contactor lockout.</li>
  <li><strong>DC GEN 1 / DC GEN 2 caution:</strong> generator off-line.</li>
  <li><strong>AC GEN 1 / AC GEN 2 caution:</strong> AC generator off-line.</li>
  <li><strong>MAIN BATTERY caution:</strong> main battery contactor locked out or fault.</li>
  <li><strong>AUX BATTERY / STBY BATTERY caution:</strong> as above for auxiliary or standby.</li>
  <li><strong>BUS FAULT caution:</strong> general bus fault indication on the ESCP.</li>
  <li><strong>BUS TIE caution:</strong> bus tie contactor status.</li>
  <li><strong>MFD AC GEN load:</strong> 1.00 = 100%. "+" prefix = overload (e.g., +1.30 = 130%).</li>
  <li><strong>MFD DC GEN load:</strong> similar format.</li>
  <li><strong>TRU voltage:</strong> 28 VDC nominal. Drift indicates a TRU or upstream AC issue.</li>
</ul>',
 'indications', 60),

(@lesson_id, 'Limitations — Numbers',
 '<ul>
  <li><strong>AC generator output:</strong> 115 VAC variable frequency.</li>
  <li><strong>TRU output:</strong> 28 VDC.</li>
  <li><strong>40 Ahr battery nominal voltage under load:</strong> 24 VDC (20 cells × 1.2 VDC).</li>
  <li><strong>40 Ahr battery no-load voltage:</strong> approximately 28 VDC.</li>
  <li><strong>Charging voltage required:</strong> 28–32 VDC (1.4–1.6 VDC/cell).</li>
  <li><strong>DC GPU minimum voltage for charging:</strong> 28 VDC. Below → batteries discharge into loads.</li>
  <li><strong>Bus fault tolerance time:</strong> approximately 5 seconds before EPCU trips generator.</li>
  <li><strong>AC GEN load overload indication:</strong> "+" prefix appears on the digital display when load exceeds 1.00.</li>
  <li><strong>Number of independent DC sources in flight:</strong> 4 (2 engine-driven gens + 2 TRUs).</li>
  <li><strong>Number of NiCad batteries:</strong> 3 (40 Ahr main + 40 Ahr aux + 17 Ahr standby).</li>
</ul>',
 'limitations', 70),

(@lesson_id, 'Memory — Mnemonics for the Sim',
 '<ol>
  <li><strong>EPGDS-DC-AC</strong> — Electrical Power Generation and Distribution System has DC and AC halves.</li>
  <li><strong>4-DC-SOURCES</strong> — 2 engine-driven gens + 2 TRUs = 4 independent DC sources in flight.</li>
  <li><strong>40-40-17</strong> — three NiCad batteries: 40 Ahr main + 40 Ahr aux + 17 Ahr standby.</li>
  <li><strong>28-VDC-CHARGE</strong> — DC GPU below 28 VDC discharges the batteries; charging needs 28–32 VDC.</li>
  <li><strong>DC-LEFT-AC-RIGHT</strong> — DC GPU receptacle LEFT forward fuselage; AC GPU RIGHT forward near nose cone.</li>
  <li><strong>5-SEC-TRIP</strong> — DC BUS caution: 5 seconds of fault persistence → EPCU trips generator and opens battery contactors.</li>
  <li><strong>+1.00-OVERLOAD</strong> — MFD AC GEN LOAD: 1.00 = 100%; "+" prefix = overload (e.g., +1.30 = 130%).</li>
  <li><strong>EPCU-BRAIN</strong> — EPCU is the system brain; GCU is per-generator.</li>
</ol>
<p>Bus-fault chant: <em>"BUS · 5-sec · GEN trip · BATT lockout."</em></p>',
 'memory', 80);

SELECT COUNT(*) AS sections_inserted FROM lesson_sections WHERE lesson_id = @lesson_id;
