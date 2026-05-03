-- =============================================================================
-- AviatorTutor — Phase 10 (ATA 31 Indicating & Recording) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'indicating-recording' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'indicating-recording-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Indicating & Recording — EIS, EFIS, ESID, CWS, FDR/CVR',
     'indicating-recording-overview',
     'overview',
     'Five identical interchangeable LCD Display Units make up the Electronic Instrument System (EIS): pilot/copilot PFDs, pilot/copilot MFDs, and the Engine Display (ED). EFIS = the 4 EFIS DUs (PFDs + MFDs). ESID = MFDs + ED. EFCP1 controls PFD1+MFD1; EFCP2 controls PFD2+MFD2; ESCP controls MFD1+MFD2+ED. Reversion: MFD1 selector still works after ESCP power loss; MFD2 selector does NOT. System-page pushbuttons (ELEC/ENG/FUEL/DOORS/ALL) cycle through ENG-FUEL-DOORS-ELEC. Color logic: RED warning, YELLOW caution, WHITE status, GREEN active, CYAN pilot-set, MAGENTA TCAS/FMS. Flashing 1 Hz / 50% duty, time-limited 5 sec usual. Two digital clocks feed FDR + CVR for synchronization. Central Warning System: Master WARNING (red) + Master CAUTION (amber), both flash and reset via switchlight. T/O Warning Horn fires on 5 conditions. GPWS modes including TERRAIN INHIBIT and FLAP OVERRIDE.',
     '<p>The EIS is the single visual interface between the crew and the aeroplane. Five identical Display Units allow any DU to take over for a failed neighbour — that is the design intent of "interchangeable." The EIS divides into two layers: EFIS for primary flight + navigation, and ESID for engine + systems. The control panels are paired (EFCP1/EFCP2) and one shared (ESCP), and the reversion logic is critical: MFD1 keeps responding to its rotary even after ESCP fails, but MFD2 does not. The Central Warning System is layered on top — Master Warning + Master Caution + WTG aural cues + GPWS + TCAS — and the T/O warning horn protects against five distinct misconfiguration causes on the runway. The two recorders (FDR + CVR) plus two digital clocks form the post-event reconstruction layer; the synchronisation between recorders is by real time on both, with the FDR auto-switching from clock #1 to clock #2 if #1 fails.</p>',
     JSON_ARRAY(
       'Five Display Units: PFD1, MFD1, ED, MFD2, PFD2. ALL FIVE are identical and interchangeable',
       'EFIS = 4 DUs (PFD1 + MFD1 + MFD2 + PFD2). ESID = 3 DUs (MFD1 + MFD2 + ED). The MFDs are shared',
       'EFCP1 controls PFD1 and MFD1. EFCP2 controls PFD2 and MFD2. ESCP controls MFD1 + MFD2 + ED',
       'ESCP MFD selector positions: PFD / NAV / SYS / ENG (4-position rotary)',
       'After ESCP power loss: MFD1 selector remains OPERATIVE. MFD2 selector does NOT operate (drops to default)',
       'System pushbuttons on ESCP: ELEC SYS, ENG SYS, FUEL SYS, DOORS SYS, ALL — show that page on the MFD set to SYS',
       'ALL pushbutton cycle order: ENG → FUEL → DOORS → ELEC → ENG (repeating)',
       'Press-and-hold any system pushbutton with both MFDs failed: shows page on ED in composite format; releases back to ED',
       'Display data sources: AHRS (per side), ADC (per side), FMS (all EFIS DUs), IFC (per side), FADEC (per engine, 2 channels), WXR (one)',
       'Color rules: RED warning, YELLOW caution, WHITE actual parameter/status, GREEN active mode/passed test, CYAN pilot-selectable, MAGENTA TCAS/VOR/ILS/DME/FMS',
       'FLASHING: 1 Hz, 50% duty cycle. Time-limited to 5 seconds in most cases or until crew action',
       'REVERSE VIDEO: change in operating state NOT pilot-initiated. Time-limited (5 sec usual). Black on coloured rectangle',
       'BRACKETS [LIKE THIS]: messages indicate required crew action or instruction',
       'PFD content: FMA, ASI, EADI, ALT, EHSI, IVSI, TCAS II, FMS, GPS',
       'MFD1 content: NAV or SYS page with PFCS Permanent System Data Area. MFD2: SYS or NAV with Flap/Hydraulic PSDA',
       'Two digital clocks: No.1 directly to CVR + to FDR via FDPS; No.2 to FDR via FDPS only',
       'FDR clock source: normally No.1, auto-switches to No.2 on No.1 failure. Real time recorded on both FDR and CVR for synchronization',
       'Master WARNING switchlight (flashing RED): push to reset flash; warning lights on caution/warning panel remain steady if fault persists',
       'Master CAUTION switchlight (flashing AMBER): same logic. Dual switchlights on glareshield so each pilot can reset',
       'WTG = Warning Tone Generator. Computes and provides aural warnings for specific events / system failures',
       'STALL WARNING TEST switch: 3-position spring-loaded centre OFF. TEST 1 = SPS channel 1 (L shaker). TEST 2 = SPS channel 2 (R shaker)',
       'T/O WARNING HORN sounds (engine running, TEST pressed) if any of: spoilers extended OR elevator trim out of TO range OR parking brake set OR condition lever(s) not at MAX/1020 OR flaps >20° or <3.5°',
       'GPWS modes 1-5 with TERRAIN INHIBIT switchlight (white) suppressing TAD/TCF. GPWS LANDING FLAP SELECT switch (10/15/35): "TOO LOW FLAPS" aural at <200 ft AGL with flaps less than selected',
       'GPWS FLAP OVERRIDE (amber crosshatch): inhibits GPWS mode 4B — permits 0° flap landings without aural warning',
       'STICK PUSHER SHUT-OFF switchlight (alternate action): turns off stick pusher; PUSHER SYST FAIL caution illuminates'
     ),
     JSON_ARRAY(
       'All 5 DUs are identical and interchangeable. Any DU can take over for any other in reversion. Plan rev-routes when one fails.',
       'After ESCP power loss the MFD2 rotary is dead — MFD2 may end up locked on its default page (SYS). MFD1 still responds normally.',
       'Press-and-HOLD a system pushbutton when both MFDs are failed/not at SYS: ED shows that system page in composite format. Release returns to engine display.',
       'Reverse-video messages mean a state change you did NOT initiate. They are time-limited (usually 5 sec) — note them, they go away.',
       'Master Warning/Caution: pushing a switchlight resets the flash (audio drops, light stops flashing) but underlying warning/caution lights persist on the C/W panel until the fault clears. Don''t assume the situation is fixed.',
       'T/O warning horn has FIVE distinct trigger conditions. Memorise them: spoilers / trim / brake / condition levers / flaps.',
       'FDR auto-switches to No.2 clock on No.1 failure. CVR is fed by No.1 directly — if No.1 fails, real-time sync to CVR is lost on the next cassette',
       'GPWS TERRAIN INHIBIT inhibits TAD and TCF only. Other GPWS modes remain active.',
       'GPWS FLAP OVERRIDE inhibits mode 4B only. Use only when intentionally landing 0° flaps (precautionary off-airport, unusual ops).',
       'Stick pusher OFF illuminates PUSHER SYST FAIL. Crew has manually disabled the pusher; SPS warning function may continue but pusher action is off.'
     ),
     JSON_ARRAY(
       'There are 5 DUs (NOT 4 or 6). All identical.',
       'EFIS uses 4 DUs (PFDs + MFDs); ESID uses 3 DUs (MFDs + ED). MFDs are shared between EFIS and ESID.',
       'After ESCP power loss MFD1 selector keeps working; MFD2 selector does NOT (a defining quirk).',
       'ALL pushbutton cycle: ENG → FUEL → DOORS → ELEC. Don''t guess the order.',
       'Color CYAN = pilot-SELECTABLE (Hdg, Crs, Alt, bugs, baro). MAGENTA = TCAS / VOR / ILS / DME / FMS. Easy to swap.',
       'Flashing rate is 1 Hz (NOT 2 Hz). 50% duty cycle. Time-limited 5 sec usual.',
       'Reverse video means NOT pilot-initiated state change. Brackets mean required action.',
       'FDR clock: normally No.1; AUTO-switches to No.2 on No.1 fail. CVR is fed only by No.1.',
       'T/O warning horn: 5 trigger conditions, NOT 3 or 4.',
       'TEST 1 = LEFT shaker / channel 1. TEST 2 = RIGHT shaker / channel 2. Easy to mix up sides.',
       'Master Warning RED, Master Caution AMBER. Don''t swap colors.',
       'GPWS FLAP OVERRIDE inhibits mode 4B ONLY. Other modes still active.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'indicating-recording-overview';
