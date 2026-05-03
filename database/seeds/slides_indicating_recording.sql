-- =============================================================================
-- AviatorTutor — Phase 10: ATA 31 Indicating & Recording — 14-slide deck.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'indicating-recording' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'indicating-recording-overview' LIMIT 1);

SELECT @system_id AS resolved_system_id, @lesson_id AS resolved_lesson_id;

DELETE FROM lesson_slides WHERE lesson_id = @lesson_id;

INSERT INTO lesson_slides (lesson_id, sort_order, slide_type, title, body, media_type, media_url, media_alt, key_point, ops_relevance, question) VALUES
(@lesson_id, 10, 'intro',
 'Five Identical Glass Panes',
 'Everything you see in the Q400 cockpit lives on five identical liquid-crystal Display Units. PFD1, MFD1, ED, MFD2, PFD2. Each one is interchangeable with any other — the design intent is that on a DU failure, any other DU can take over its content via reversion. The DUs split into two logical groups: EFIS (the 4 PFDs + MFDs) handles primary flight + nav, and ESID (the MFDs + ED) handles engine + system pages. The control panels, the warning system, and the recorders are layered on top. By the end of this lesson you will know which selector dies on ESCP power loss, what the ALL pushbutton cycles through, what colour means what, and the five conditions that trigger the T/O warning horn.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'Q400 EIS overview — 5 identical Display Units',
 '5-DU-IDENTICAL · EFIS-4-ESID-3. Five glass panes; any can take over for another.',
 'On a real DU failure your reversion path is the difference between continuing and diverting. Drill the EFCP/ESCP selector menu in the sim until it''s reflexive.',
 NULL),

(@lesson_id, 20, 'concept',
 'EFIS vs ESID — Which DUs Belong to Which?',
 'EFIS and ESID share the two MFDs. <strong>EFIS = 4 DUs</strong>: PFD1 + MFD1 + MFD2 + PFD2. EFIS handles primary flight + navigation — the FMA, ASI, EADI, ALT, EHSI, IVSI, TCAS, FMS, GPS data. <strong>ESID = 3 DUs</strong>: MFD1 + MFD2 + ED. ESID handles engine page + the system pages (ELEC / ENG / FUEL / DOORS) when you call them up. The MFDs are the bridge between the two systems — that is why they receive selectors from both EFCP and ESCP. EFCP1 owns PFD1 + MFD1; EFCP2 owns PFD2 + MFD2; ESCP owns MFD1 + MFD2 + ED. Press a system pushbutton on the ESCP and it lights up the upper area of whichever MFD is set to SYS.',
 'diagram', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'EFIS vs ESID DU mapping with control panel ownership',
 'EFIS-4-ESID-3 · MFDs are shared. EFCP1/2 + ESCP own different DUs.',
 'When you brief the FO before a system page review, name which DU you''re using and which control panel owns it. It builds the mental model.',
 JSON_OBJECT(
   'prompt', 'How many Display Units are in the Q400 Electronic Instrument System (EIS), and how many are EFIS DUs vs ESID DUs?',
   'options', JSON_ARRAY(
     '4 total: 2 EFIS + 2 ESID',
     '5 total: all identical; EFIS uses 4 (PFDs + MFDs), ESID uses 3 (MFDs + ED), MFDs are shared',
     '6 total: 4 EFIS + 2 ESID + 1 standby',
     '5 total: 2 EFIS + 3 ESID, no overlap'
   ),
   'correct_index', 1,
   'explanation', 'Five identical interchangeable DUs. EFIS uses 4 (PFDs + MFDs). ESID uses 3 (MFDs + ED). MFDs are shared. Mnemonic: 5-DU-IDENTICAL · EFIS-4-ESID-3.'
 )),

(@lesson_id, 30, 'concept',
 'The Defining Quirk — MFD2 Dies on ESCP Power Loss',
 'On the ESCP, both MFD selectors look the same — 4-position rotaries with PFD/NAV/SYS/ENG. But there is one critical asymmetry that check captains love. <strong>After ESCP power loss, the MFD1 selector remains operative.</strong> You can still rotate it through PFD/NAV/SYS/ENG and the MFD1 will respond. <strong>The MFD2 selector does NOT operate after ESCP power loss</strong> — the MFD2 drops to its default page and cannot be reselected from the ESCP. Plan around this: when the ESCP fails, MFD1 becomes your master reversion path. If you need a system page on MFD2 after ESCP power loss, you cannot get it directly — use MFD1 for system pages and reserve MFD2 for navigation.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'MFD1 vs MFD2 ESCP power loss asymmetry',
 'MFD2-DIES-WITH-ESCP. MFD1 keeps working; MFD2 doesn''t.',
 'On a real ESCP power loss, the FO''s side becomes a navigation-only display. Brief the FO: "I''ll work the system pages on my MFD1; you keep MFD2 on NAV."',
 NULL),

(@lesson_id, 40, 'system',
 'System Pages — ELEC / ENG / FUEL / DOORS / ALL',
 'Above each MFD selector on the ESCP is a row of system-page pushbuttons: ELEC SYS, ENG SYS, FUEL SYS, DOORS SYS, and ALL. With a MFD set to SYS, pressing any of these momentary buttons brings that system page to the upper area of that MFD. The ALL pushbutton has a special role: each press cycles through the system pages in order — <strong>ENG → FUEL → DOORS → ELEC → ENG</strong> — useful for a quick scan during flow checks. There is also a special reversion: if both MFDs are failed (or none is set to SYS), pressing-and-HOLDING any of these system pushbuttons displays that page on the ED in composite format. Releasing the button returns the ED to its normal engine display. The ED itself has no rotary selector — its content is driven by the ESCP system pushbuttons in this hold mode.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'ESCP system pushbuttons + ALL cycle order',
 'ALL-CYCLE-EFDD-E. Press-hold for ED reversion when MFDs failed.',
 'On a flow check, hit ALL three or four times in succession to scan all four system pages — it''s a fast way to clear all four trees at once.',
 NULL),

(@lesson_id, 50, 'system',
 'Color Logic — Six Colours, Six Meanings',
 'Q400 EIS uses six colours with disciplined meanings. <strong>RED</strong> = WARNING — immediate action required (e.g. engine red-line exceedance, VMO, TCAS Resolution Advisory). <strong>YELLOW</strong> = CAUTION — awareness + subsequent action (e.g. AFCS caution, altitude alert, mismatch, cross-side source selection, TCAS Traffic Advisory). <strong>WHITE</strong> = actual aircraft parameter, status, scales, AFCS armed modes, bearing pointer 1, units. <strong>GREEN</strong> = active controlling mode/function, AFCS active mode, passed test, bearing pointer 2. <strong>CYAN</strong> = pilot-SELECTABLE values — Hdg/Crs/Alt, speed/torque bugs, baro-correction, DH setting. <strong>MAGENTA</strong> = TCAS proximate/other traffic, VOR/ILS/DME data, FMS data, flight director commands. Memorise the swap traps: CYAN is what YOU set; MAGENTA is what the FMS or TCAS says.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'Six colour rules with example messages',
 'RED-YELLOW-WHITE-GREEN-CYAN-MAGENTA. Cyan = mine; Magenta = FMS/TCAS.',
 'On a recurrent question "is this magenta or cyan?" the rule is: if you set it, cyan; if the FMS or TCAS told you, magenta.',
 JSON_OBJECT(
   'prompt', 'Which colour is used on the Q400 EIS for pilot-SELECTABLE parameters such as selected heading, course, altitude bugs, and baro correction?',
   'options', JSON_ARRAY(
     'GREEN',
     'WHITE',
     'CYAN',
     'MAGENTA'
   ),
   'correct_index', 2,
   'explanation', 'CYAN = pilot-selectable parameters. MAGENTA = TCAS / VOR / ILS / DME / FMS data. WHITE = actual A/C parameters and scales. GREEN = active modes / passed test.'
 )),

(@lesson_id, 60, 'system',
 'Display Attributes — Flash, Reverse Video, Brackets',
 'Three special display attributes carry meaning. <strong>FLASHING</strong> at 1 Hz, 50% duty cycle, attention-getting. Time-limited to 5 seconds in most cases, OR maintained until crew action. New cautions, new warnings, new state-change messages. <strong>REVERSE VIDEO</strong> — black on a coloured rectangle of the same colour as the indication would be in normal video. Indicates a change in operating state that was NOT pilot-initiated. By design time-limited (~5 sec). For example: ICE DETECTED appears in white reverse video for 5 seconds, then transitions to normal white video. <strong>BRACKETS</strong> — messages enclosed in square brackets [LIKE THIS] correspond to required crew action or instruction. Read the brackets as "do something."',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'Flash + reverse video + brackets attribute logic',
 'FLASH-1HZ-5SEC · REVERSE VIDEO = not pilot-initiated · BRACKETS = action required.',
 'When you see brackets, find the action — it''s the system telling you something needs to be done now.',
 NULL),

(@lesson_id, 70, 'system',
 'Recording — Two Clocks, Two Recorders, One Reality',
 'Two digital clocks live on the instrument panels. <strong>Clock No.1 (pilot side)</strong> drives the CVR DIRECTLY for real-time stamping AND the FDR via the FDPS. <strong>Clock No.2 (copilot side)</strong> drives only the FDR via the FDPS. The FDR normally records using clock No.1, but auto-switches to No.2 if No.1 fails. The CVR has only the No.1 path — if No.1 fails, the CVR keeps recording but loses real-time sync from that point. Both recorders use real time on their tape so post-event sync between FDR and CVR is by timestamp matching. Practical implication: clock No.1 is the priority maintenance item — its health affects post-event reconstruction quality.',
 'video', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'Two-clock recording architecture',
 'TWO-CLOCKS-FDR-CVR. #1 → CVR direct + FDR. #2 → FDR only. FDR auto-switches.',
 'A clock fault on the No.1 clock is the kind of write-up that auto-mover from "no big deal" to "must fix" if you''re flying for a high-reg airline.',
 NULL),

(@lesson_id, 80, 'normal_op',
 'Pre-Flight — Stall Warn TEST 1, TEST 2, GPWS, T/O Horn',
 'The pre-flight checks that touch this lesson: <strong>(1) STALL WARN test:</strong> three-position spring-loaded switch. Hold TEST 1 for &gt;10 seconds — RA on pilot PFD increases above 500 ft, L stick shaker activates, #1 STALL SYST FAIL + PUSHERSYSTFAIL caution illuminate, RA decreases to 50 ft. Then TEST 2 — same but R shaker on the copilot side. Confirm both faults clear when the switch returns to OFF. <strong>(2) GPWS test:</strong> push-and-HOLD the PULL UP GPWS TEST. Observe GPWS C/W light, FLAP OVERRIDE annunciator, BELOW G/S, GLIDESLOPE aural, PULL UP GPWS TEST annunciators after 2 sec, PULL UP aural twice, cycles through all GPWS aurals. <strong>(3) T/O warning horn:</strong> with engine running, push TEST. Horn sounds if any of: spoilers extended, elevator trim out of TO range, parking brake set, condition lever not at MAX/1020, or flaps &gt;20° or &lt;3.5°. Five conditions; horn fires on ANY of them.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'Pre-flight test sequence',
 'TEST1-LSHAKE-TEST2-RSHAKE · 5-COND-TO-HORN.',
 'The 10-second hold on stall warn test is mandatory. Quick taps will fail the test silently.',
 JSON_OBJECT(
   'prompt', 'How many conditions trigger the Q400 T/O Warning Horn (when TEST is pressed with engine running)?',
   'options', JSON_ARRAY(
     '3 conditions: trim, brake, flaps',
     '4 conditions: spoilers, trim, brake, flaps',
     '5 conditions: spoilers + trim + brake + condition lever + flaps',
     '6 conditions: + airspeed and weight'
   ),
   'correct_index', 2,
   'explanation', 'Five conditions: spoilers extended, elevator trim out of TO range, parking brake set, condition lever not at MAX/1020, or flaps >20° or <3.5°. Mnemonic: 5-COND-TO-HORN.'
 )),

(@lesson_id, 90, 'normal_op',
 'Master Warning vs Master Caution — Reset Discipline',
 'Two switchlights live prominently on the glareshield: <strong>MASTER WARNING (flashing RED, momentary action)</strong> — immediate action required. <strong>MASTER CAUTION (flashing AMBER, momentary action)</strong> — awareness and subsequent action required. Critical reset discipline: pushing either switchlight resets THE FLASH. The underlying caution/warning panel light remains illuminated steady if the fault persists. So the reset only silences the audio and stops the flashing — it does NOT clear the underlying condition. Both pilots have a duplicate set of switchlights (DUAL master warning/caution) on the glareshield — eliminates the need to reach across the cockpit. Press the side closest to whichever pilot is more available.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'Master Warning + Master Caution reset discipline',
 'MASTER-RED-AMBER. Switchlight resets flash; underlying lights persist.',
 'After resetting, your eyes go to the C/W panel to identify the source. The reset is just the audio/visual silencer.',
 NULL),

(@lesson_id, 100, 'abnormal',
 'Single DU Failure — Reversion Logic',
 'A DU goes dark in cruise — say PFD1 turns black. The 5-identical-interchangeable architecture is the design choice that saves you. (1) Adjust the EFCP1 brightness and verify it''s a true failure (not a brightness glitch). (2) Use the ESCP MFD1 selector to set MFD1 to PFD — your PFD content now shows on MFD1. (3) The failed PFD1 displays AVAIL (white) in its centre to confirm reversion is available. (4) Continue the flight with primary flight info on MFD1. (5) For system pages: use MFD2 (set to SYS via ESCP) for ELEC/ENG/FUEL/DOORS pages. Brief the FO: "I''ll fly off MFD1 with primary flight; you scan MFD2 for systems." Document the failure; consider divert based on workload, weather, route remaining, and dispatch MEL.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'Single DU failure recovery via MFD reversion',
 'Failed DU → AVAIL white in centre. Adjacent MFD takes the content via selector.',
 'A DU failure is a workload event but rarely an emergency — except in IMC where losing the PFD increases workload substantially.',
 NULL),

(@lesson_id, 110, 'abnormal',
 'ESCP Power Loss — MFD2 Dies',
 'ESCP power loss is an unusual but check-ride-favourite failure. The defining consequence: MFD1 selector REMAINS OPERATIVE — you can still rotate through PFD/NAV/SYS/ENG and MFD1 responds. MFD2 selector does NOT operate — the MFD2 stays on whatever default page it was on at the time of failure. Practical handling: (1) MFD1 becomes your reversion master. Use it for whatever page you need next. (2) MFD2 is locked to its current display — typically the SYS page or NAV page. (3) System pushbuttons (ELEC/ENG/FUEL/DOORS/ALL) still work for MFD1 if it''s set to SYS. (4) For ED reversion, press-and-HOLD a system pushbutton — page appears on ED in composite format. Plan a divert if the failure prevents the FO from monitoring the system pages.',
 'video', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'ESCP power loss handling',
 'MFD1 keeps working · MFD2 locked. Use MFD1 as reversion master.',
 'On a real ESCP power loss the cockpit becomes one-sided. Brief the FO; consider divert if instrument workload is too asymmetric.',
 JSON_OBJECT(
   'prompt', 'After an ESCP power loss, what happens to the MFD1 and MFD2 selectors?',
   'options', JSON_ARRAY(
     'Both stop working — both MFDs lock to default page',
     'MFD1 selector remains operative; MFD2 selector does NOT operate',
     'Both keep working — ESCP has independent power',
     'MFD2 keeps working; MFD1 stops'
   ),
   'correct_index', 1,
   'explanation', 'MFD1 selector remains operative after ESCP power loss. MFD2 selector does NOT operate — drops to default. MFD1 becomes the master reversion path. Mnemonic: MFD2-DIES-WITH-ESCP.'
 )),

(@lesson_id, 120, 'qrh',
 'QRH Connection: EIS / Recorder Non-Normals',
 'QRH non-normals for indicating + recording cluster into five groups. (1) Single DU failure: use ESCP/EFCP reversion to display content on adjacent DU; AVAIL white confirms. (2) ESCP power loss: MFD1 master, MFD2 locked. Use ED press-and-hold for system pages if needed. (3) AHRS or ADC fault (one side): cross-side source selection — yellow indication on PFD signals you''re on the other side. (4) Stall warn channel fault (#1 or #2 STALL SYST FAIL): one channel of SPS lost, other continues. (5) PUSHER SYST FAIL: pusher manually disabled OR system fault. Stall shaker still works. Most are not memory items but reversion paths must be reflexive — you cannot read a QRH while the panel is rebooting.',
 'image', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'QRH EIS non-normal cluster',
 'Single DU · ESCP · AHRS/ADC · STALL SYST · PUSHER SYST.',
 'Drill the reversion paths in the sim until they''re reflexive. The QRH is for the systems you didn''t expect to fail — not for the EIS.',
 NULL),

(@lesson_id, 130, 'scenario',
 'Captain Decision: PFD1 Goes Black at FL230 in IMC',
 'Setup: cruise at FL230 in solid IMC en route to a regional field, 90 minutes out. The FO reports "PFD1 dark" — the pilot side primary flight display has gone black. The MFD1 still works; PFD2 still works. The MFD1 currently shows NAV.\n\nFirst 30 seconds: PF maintains aircraft control via the MFD1 (which still has bug indications) and reaches for the ESCP MFD1 selector. Set MFD1 to PFD — primary flight content appears on MFD1. Check: AVAIL (white) in the centre of the failed PFD1 confirms reversion available. Adjust brightness if needed.\n\nNext 60 seconds: Brief the FO — "I have PFD on MFD1; you keep PFD2 + MFD2 on systems." Run the QRH; consider divert. Workload is higher because system pages are now only on MFD2; the FO carries them. Decision: continue to destination is reasonable IF weather at destination is good and the workload split is sustainable; divert to nearest suitable if either factor is unfavourable. Captain''s call.',
 'animation', '/assets/aircraft/q400/indicating-recording-flow.svg',
 'PFD1 failure scenario in IMC',
 'AVAIL white · MFD1 to PFD · brief FO · captain divert call.',
 'The ability to reroute primary flight to MFD1 is a core Q400 reversion path. Drill it in the sim regularly.',
 JSON_OBJECT(
   'prompt', 'PFD1 fails in cruise. Where does PFD content reroute, and how do you confirm reversion is available?',
   'options', JSON_ARRAY(
     'Content reroutes automatically to ED; AVAIL appears on ED',
     'Set MFD1 to PFD via ESCP rotary; AVAIL (white) appears in centre of failed PFD1',
     'PFD content cannot be rerouted; flight must continue on standby instruments',
     'Only PFD2 is operational; copilot flies the rest of the leg'
   ),
   'correct_index', 1,
   'explanation', 'Set MFD1 to PFD on the ESCP rotary; PFD content appears on MFD1. The failed PFD1 shows AVAIL (white) in centre to confirm reversion is available.'
 )),

(@lesson_id, 140, 'revision',
 'Lesson Recap: Indicating & Recording in 60 Seconds',
 'Recap:\n  • 5 identical interchangeable LCD Display Units: PFD1, MFD1, ED, MFD2, PFD2.\n  • EFIS = 4 DUs (PFDs+MFDs); ESID = 3 DUs (MFDs+ED).\n  • EFCP1 owns PFD1+MFD1; EFCP2 owns PFD2+MFD2; ESCP owns MFD1+MFD2+ED.\n  • After ESCP power loss: MFD1 selector keeps working; MFD2 selector dies.\n  • System pushbuttons: ELEC/ENG/FUEL/DOORS/ALL. ALL cycle: ENG → FUEL → DOORS → ELEC.\n  • Press-hold a system button when MFDs failed: page appears on ED in composite format.\n  • Color rules: RED-warn / YELLOW-caution / WHITE-status / GREEN-active / CYAN-pilot-set / MAGENTA-FMS+TCAS.\n  • Flashing 1 Hz / 50% duty / 5 sec usual. Reverse video = not pilot-initiated, time-limited. Brackets = required action.\n  • 2 clocks: #1 to CVR direct + FDR; #2 to FDR via FDPS only. FDR auto-switches.\n  • Master Warning RED, Master Caution AMBER. Reset only flash; underlying lights persist.\n  • Stall warn TEST 1 (L shaker / channel 1) + TEST 2 (R shaker / channel 2), each held >10 sec.\n  • T/O warning horn: 5 conditions — spoilers, trim, brake, condition lever, flaps.\n  • TERRAIN INHIBIT (white) inhibits TAD/TCF. GPWS FLAP OVERRIDE (amber) inhibits mode 4B only.\n\nClick Next to mark this lesson complete.',
 'none', NULL, NULL,
 '5-DU-IDENTICAL · EFIS-4-ESID-3 · MFD2-DIES-WITH-ESCP · ALL-CYCLE-EFDD-E · RED-YELLOW-WHITE-GREEN-CYAN-MAGENTA · FLASH-1HZ-5SEC · TWO-CLOCKS-FDR-CVR · TEST1-LSHAKE-TEST2-RSHAKE · 5-COND-TO-HORN · TERRAIN-INHIBIT-WHITE · MASTER-RED-AMBER',
 'Eleven mnemonics carry every indicating/recording question. Drill them.',
 NULL);

UPDATE IGNORE lesson_slides SET show_beginner = 0 WHERE lesson_id = @lesson_id AND slide_type IN ('abnormal', 'qrh', 'scenario', 'quiz');
UPDATE IGNORE lesson_slides SET show_beginner = 0, show_intermediate = 0 WHERE lesson_id = @lesson_id AND title LIKE 'Captain Decision%';

SELECT COUNT(*) AS slides_inserted FROM lesson_slides WHERE lesson_id = @lesson_id;
