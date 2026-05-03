-- =============================================================================
-- AviatorTutor — Phase 15 (ATA 36 Pneumatics — APU + Bleed Air) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'pneumatics' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'pneumatics-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Pneumatics — APU and Bleed Air System',
     'pneumatics-overview',
     'overview',
     'APU is a tailcone-mounted gas-turbine driving a 28 VDC starter-generator. Supplies bleed air for ECS + 28 VDC. CANNOT operate in flight. FADEC-controlled. Starter stays engaged until APU reaches half operating speed. Fuel from left wing collector bay through APU shutoff valve. Auto fire detection + extinguishing — bottle auto-releases 7 SECONDS after FIRE detected; once discharged, no restart until bottle replaced. APU bleed air opens via BL AIR switchlight; supplies ECS + holds CPCS aft safety valve open. Auto load priority: APU EGT limit reduces bleed air to preserve generator load. APU BL AIR auto-de-energizes if either engine BLEED switch is set to 1 or 2 (prevents dual bleed supply). Battery start: 100% charge → bus drops to ~20 V; 50% → ~18 V. Limitations: composite duct removed 30°C/ISA+25, Louvre installed 21°C ambient max.',
     '<p>The Q400 pneumatic system centres on the APU and the bleed-air supply. The APU is a tailcone-mounted gas turbine engine driving a DC starter-generator — a tightly-integrated package that replaces the standard composite tailcone with a titanium tailcone and firewall. Two clamshell doors on the bottom give access. The APU supplies two products: 28 VDC for the electrical system and bleed air for the ECS. The defining limitation is that the APU CANNOT BE OPERATED IN FLIGHT — the APU shutoff valve closes automatically when the aircraft is in flight. The bleed-air integration is also clean: APU bleed automatically de-energizes when either main engine BLEED switch is set to 1 or 2, preventing simultaneous APU + engine bleed supply. Memorise the 7-second auto-extg delay, the half-speed starter cutoff, and the no-flight rule.</p>',
     JSON_ARRAY(
       'APU is a gas-turbine engine driving a 28 VDC starter-generator. Mounted in titanium tailcone (replaces composite tailcone)',
       'APU CANNOT be operated in flight. Shutoff valve closes automatically when aircraft is in flight',
       'Two clamshell doors on bottom of tailcone for access',
       'APU FADEC controls start, normal operation, and malfunction monitoring',
       'Starter stays engaged until APU reaches HALF operating speed',
       'APU intake: screened inlet duct on RIGHT REAR of fuselage',
       'APU exhaust: ejector + upwards-pointing outlet at AFT END of titanium tailcone',
       'APU louvered air-inlet cover available for long turnarounds / overnight (snow/sleet protection)',
       'APU control panel on overhead console: PWR / START / GEN / BL AIR switchlights + GEN OHT advisory',
       'PWR switchlight arms only if: (1) aircraft on ground, (2) no fire detected, (3) EXTG switch not selected',
       'GEN OHT (amber): APU starter-generator overheat — APU auto-shuts down',
       'APU fuel: from LEFT wing collector bay through APU shutoff valve. Rigid line, outside pressurised fuselage. Gravity-fed APU-driven fuel pump',
       'APU shutoff valve closes if: (1) PWR pushed off, (2) fire detected, (3) EXTG pushed, (4) aircraft in flight',
       'Battery start (no external DC): 2×40 Ahr NiCad batteries used. 100% charge → bus voltage drops to ~20 VDC during start; 50% charge → ~18 VDC',
       'Insufficient battery charge can cause system problems and jeopardise successful start',
       'After main engine DC starter-generators on line, APU generator continues to supply power in PARALLEL to DC buses',
       'APU GEN output automatically PREVENTED if external AC or DC power is applied to the aeroplane',
       'Starter-generator overheat → automatic APU shutdown + GEN OHT advisory',
       'APU bleed air valve opens via BL AIR switchlight when APU operating. Supplies bleed for ECS + holds CPCS aft safety valve open',
       'APU bleed reduced if APU EGT reaches established temperature limit — APU generator load gets priority over bleed air',
       'APU check valve + wing duct check valves prevent APU bleed air from entering engine bleed supply (including airframe de-icing)',
       'If either main engine BLEED toggle is set to 1 or 2, APU BL AIR switchlight auto-de-energizes',
       'APU fire detection: stainless steel fire bottle, loop sensor along tailcone above APU, control circuit. Monitors whenever right ESS 28 VDC bus energised',
       'APU FIRE detected: FIRE red on FPP, MASTER WARNING + CHECK FIRE DET flash, BTL ARM amber, fuel valve closes, EXTG segment illuminates',
       'After 7 seconds of FIRE detection, fire-extinguishing agent automatically releases. BTL ARM goes out',
       'Manual extg via guarded EXTG switchlight if BTL ARM still on (auto-extg failed)',
       'Once APU fire bottle discharged, APU restart is PREVENTED until bottle is replaced',
       'BOTTLE LOW (amber): fire bottle low or empty. FAULT (amber): fire system or FPP fault',
       'APU normal shutdown sequence: close BL AIR → select GEN off → push PWR',
       'Limitations: with composite cooling duct removed, ambient temperature limit 30°C OR ISA+25°C (whichever lower). With Air Inlet Louvre installed, ambient temperature limit 21°C'
     ),
     JSON_ARRAY(
       'APU CANNOT be operated in flight. The shutoff valve closes automatically as soon as the aircraft is airborne — no in-flight start, no in-flight bleed supply.',
       '7-second auto-extg delay after FIRE detected. Manual EXTG via guarded switchlight is the backup if auto fails — only available while BTL ARM is on.',
       'APU GEN output auto-prevented when external AC/DC is applied. So plugging in ground power doesn''t fight the APU — APU stays out.',
       'Starter engaged until HALF operating speed (not full). Standard turbine start sequence — disengage well before idle.',
       'Bleed reduces under high APU EGT — the system protects the generator load over bleed air. Don''t fight it.',
       'APU BL AIR auto-de-energizes when engine BLEED is selected. So pre-flight: don''t bother fighting the indicator — it''s designed.',
       'Battery start at 50% charge gives bus voltage of 18 V — risk of brown-out. Pre-flight: confirm battery state before APU start.',
       'After APU bottle discharge, no restart until maintenance replaces the bottle. Plan the divert with that in mind.'
     ),
     JSON_ARRAY(
       'APU CANNOT operate in flight. Easy trap to think it''s available for backup electrical/bleed at altitude.',
       'APU fire bottle auto-releases after 7 SECONDS, not immediately. Crew has 7 sec to manually extg if they wish (but typically don''t).',
       'Starter cutoff at HALF operating speed (not at idle, not at 95%, not at full).',
       'APU fuel from LEFT wing collector bay — not centre, not right.',
       'Battery start voltage drops: 100% → 20 V; 50% → 18 V. Two specific numbers.',
       'Limitations: 30°C with composite duct removed; 21°C with Louvre installed. Two different temps.',
       'APU auto-de-energizes BL AIR when engine BLEED is on — auto, not crew action.',
       'GEN OHT auto-shuts the APU. Not a manual procedure.',
       'EXTG manual mode is only available if BTL ARM is still on (auto-extg failed). If auto-extg succeeded, manual is moot.',
       'PWR switchlight arms only if 3 conditions met: ground, no fire, EXTG not selected.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'pneumatics-overview';
