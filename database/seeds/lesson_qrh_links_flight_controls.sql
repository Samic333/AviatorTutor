-- =============================================================================
-- AviatorTutor — Phase 7: ATA 27 Flight Controls — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'flight-controls' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'flight-controls-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'ROLL CONTROL JAM',
 'Indications: control wheel resists rotation in one or both directions; possible SPLR caution. Memory action: identify pilot with the unjammed wheel; pull the ROLL DISC handle out to its limit and rotate 90° clockwise or counter-clockwise. The clutch at the base of the copilot column disengages. Pilot now has SPOILERS only; copilot has AILERONS only. Pilot with the UNJAMMED wheel takes roll control. After memory items: aviate, navigate, communicate; refer to QRH non-normal; brief approach for reduced roll authority; consider divert to nearest suitable airport. Left wheel free → spoilers only, low forces, avoid over-control. Right wheel free → ailerons only; if rotated >50° to maintain wings level, SPLR 1/2 may illuminate (stuck spoilers).',
 1,
 'A roll jam is one of the rare events where the disconnect handle is the FIRST action — there is no QRH page to read while the wheel is fighting you. Drill the handle in the sim until it is reflexive.',
 'Control wheel resists rotation; possible SPLR1 or SPLR2 PUSH OFF switchlights illuminated.',
 'PULL OUT · TURN 90° · UNJAMMED WHEEL FLIES.',
 10),

(@lesson_id, NULL,
 'PITCH CONTROL JAM',
 'Indications: control column resists fore/aft movement. Memory action: identify pilot with the FREE column; pull the pitch disconnect handle (left side of centre console) out and rotate 90°. The clutch disconnects pilot and copilot columns. Pilot with the free column has pitch control. After memory items: pitch trim is via the working column''s electric trim switches; brief manual flying; declare PAN-PAN; divert to nearest suitable. Both columns are mechanically independent until the disconnect — the pilot whose column was already free at the moment of jam has uninterrupted pitch authority.',
 1,
 'Pitch jams are extremely rare on the Q400. The disconnect is straightforward but easy to forget under stress because it lives on the centre console rather than the pedestal.',
 'Control column resists fore/aft movement; possible PITCH TRIM caution.',
 'PITCH-DISC-90 · FREE COLUMN FLIES.',
 20),

(@lesson_id, NULL,
 'RUD 1 / RUD 2 PUSH OFF — Rudder PCU Jam',
 'Indications: amber RUD 1 PUSH OFF (lower PCU jam) or RUD 2 PUSH OFF (upper PCU jam) switchlight on the PFCS panel. Action: push the affected switchlight ONCE — the PUSH legend extinguishes, the OFF legend remains as a reminder. The corresponding #1 RUD HYD or #2 RUD HYD caution illuminates. The FCECU re-schedules pressure to the surviving PCU to maintain rudder authority. AFM 4.18.12: ONLY ONE RUD PUSH OFF may be pushed at a time. If both are inadvertently pressed, both OFF legends extinguish, both PUSH legends illuminate, and the previously-pushed PCU re-pressurises. To recover from the dual press: push the NON-jammed switchlight again — it will turn off both PUSH legends and re-establish the correct depressurised state on the jammed side. Note: under strong tailwinds on the ground with engines off, one or both switchlights may illuminate from PCU bungee compression — they will self-clear once hydraulic pressure is established at engine start.',
 0,
 'A real PCU jam in flight is rare. The more common scenario in the sim is a check captain pressing both switchlights and watching to see if you can recover the correct depressurisation. Practice the recovery.',
 'Amber RUD 1 or RUD 2 PUSH OFF switchlight; #1 or #2 RUD HYD caution.',
 'ONE AT A TIME · PUSH NON-JAMMED TO RECOVER.',
 30),

(@lesson_id, NULL,
 'ELEVATOR TRIM RUNAWAY (3-Second Rule)',
 'Indications: aural clicking; ELEVATOR TRIM SHUTOFF switchlight on the glareshield illuminates; elevator trim indicator moving uncommanded; control column loading nose-up or nose-down. Memory action: PUSH either left or right ELEVATOR TRIM SHUTOFF switchlight. Trim deactivates; aural stops. Manually retrim using the working column''s electric trim switches (or per QRH alternate-trim path) to a position that minimises column force. After memory items: refer to QRH non-normal; brief approach for higher pitch forces; declare PAN-PAN; divert to nearest suitable airport with maintenance. The 3-second rule is the FCECU watchdog — any manual pitch trim command longer than 3 seconds triggers this protection.',
 1,
 'Holding the column against a runaway tires the PF in seconds. Push the SHUTOFF in the first 5 seconds — do not try to ride it out. The aural and switchlight are unmistakable.',
 'Aural clicking + ELEVATOR TRIM SHUTOFF switchlight illuminated + uncommanded trim movement.',
 '3-SEC-TRIM · PUSH SHUTOFF · MANUALLY RETRIM.',
 40),

(@lesson_id, NULL,
 'IAS MISMATCH (>17 KTS) — Four-Light Cascade',
 'Indications: simultaneous illumination of FOUR cautions — RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM — plus IAS MISMATCH on the PFD. Triggered when Airspeed #1 differs from Airspeed #2 by more than ±17 knots. The FCECU cannot determine which ADU is correct, so it conservatively flags every airspeed-dependent function. Action: REDUCE AIRSPEED BELOW 200 KIAS; identify the bad ADU using the standby airspeed and crosschecking against GPS groundspeed plus expected pitch attitude for current configuration; run the QRH non-normal. This is a degraded-control event but not necessarily an immediate divert — system control is still available, just with reduced authority and degraded automation.',
 0,
 'Recognising the four-light cascade pattern in 2 seconds is what keeps this from becoming an upset. Drill the pattern: "FOUR LIGHTS = AIRSPEED."',
 'RUD CTRL + SPLR OUTBD + ELEV FEEL + PITCH TRIM cautions ALL illuminated together; IAS MISMATCH on PFD.',
 'FOUR LIGHTS = AIRSPEED · BELOW 200 · IDENTIFY BAD ADU.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
