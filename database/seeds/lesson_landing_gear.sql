-- =============================================================================
-- AviatorTutor — Phase 11 (ATA 32 Landing Gear) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'landing-gear' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'landing-gear-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Landing Gear — Retraction, Steering, Brakes, Alt Extension',
     'landing-gear-overview',
     'overview',
     'Tricycle retractable dual-wheel gear, electrically controlled and hydraulically operated. Mains retract AFT into nacelles; nose retracts FORWARD into nose section. No.2 hyd drives gear extension/retraction and nosewheel steering; No.1 hyd drives the anti-skid brakes. PSEU monitors and controls. Nosewheel steering: ±70° via hand control (low-speed taxi), ±8° via rudder pedals (high-speed taxi/T-O/landing roll), ±120° passive caster. Anti-skid armed >10 kts wheel speed; self-test prevented >17 kts; 5-sec brake delay if wheels haven''t spun up at touchdown (cancels at 35 kts). Alternate gear extension via INHIBIT switch + overhead RELEASE door + floor EXTENSION door + hand pump. EMERG BRAKE: No.2 hyd or accumulator (~6 applications). Park brake minimum 500 PSI before start. Gear warning tone has 3 trigger logic groups; only case 3 (single-engine failure <156 KIAS) may be MUTED.',
     '<p>The Q400 landing gear is a fairly conventional tricycle dual-wheel retractable installation, but the operating logic is precise. No.2 hydraulic system drives gear retraction, extension, and nosewheel steering — and the PTU backs up No.2. No.1 hydraulic system drives the multi-disc anti-skid brakes. The Proximity Sensor Electronics Unit (PSEU) monitors weight-on-wheels, gear position, and lift-dump signals — it is the brain that knows whether the aeroplane is on the ground or in the air. The alternate extension architecture is a three-step sequence (inhibit, release, extend) that any captain must know cold. The brake system has two separate paths: normal (No.1, anti-skid) and emergency/park (No.2 or accumulator, no anti-skid, no differential). Memorise the airspeed/altitude thresholds for the gear warning tone and the caution-vs-warning logic for nosewheel steering.</p>',
     JSON_ARRAY(
       'Tricycle dual-wheel retractable gear. Mains retract AFT into nacelles. Nose retracts FORWARD into nose section',
       'No.2 hydraulic system: gear extension/retraction AND nosewheel steering',
       'No.1 hydraulic system: anti-skid brakes (multi-disc per main wheel)',
       'PTU (Power Transfer Unit) provides backup hydraulic to No.2 system',
       'PSEU (Proximity Sensor Electronics Unit) monitors and controls landing gear; supplies WOW + gear up/locked signals to ASCU',
       'Doors: hydraulic gear doors fully enclose gear when retracted, partially when down. Aft nose doors mechanically linked to nose gear. Forward main doors mechanically linked to main gear',
       'Gear advisory lights: amber L./N./R. DOOR (door open), green LEFT/NOSE/RIGHT (down and locked), red LEFT/NOSE/RIGHT (unsafe), amber HANDLE (gear handle vs gear disagree)',
       'Selector lever LOCK RELEASE button: must be held down to move the gear handle UP or DN',
       'Nosewheel steering: ±70° via hand control (low-speed taxi), ±8° via rudder pedals (high-speed taxi, T-O, landing roll), ±120° passive caster mode',
       'Passive caster mode triggers: nosewheel angle >70°, OR SCU detects failure, OR STEERING switch OFF',
       'NOSE STEERING caution: SCU fault with STEERING ON, OR hydraulic pressure detected with STEERING OFF',
       'Reverse taxi: STEERING switch MUST be ON, but no steering input is allowed via tiller or rudder pedals',
       'Caution: never set STEERING to STEERING with tow bar connected',
       'Alternate extension triggers: LDG GEAR INOP caution, gear indication failure, OR loss of No.2 hydraulic pressure',
       'Alternate extension steps: (1) INHIBIT switch isolates hyd from gear, (2) MAIN L/G RELEASE handle (overhead) releases main doors + uplocks (gear free-falls), (3) NOSE L/G RELEASE handle (floor) releases nose, (4) hand pump (behind copilot seat, into socket) until stiff for MLG downlock, (5) ALTERNATE DOWNLOCK VERIFICATION switch AFT, (6) both doors left FULLY OPEN',
       'Gear warning tone group 1: gear up + flaps >8.5° + either engine torque <50% + both PLA <RATING detent',
       'Gear warning tone group 2: gear up + both PLA <FLIGHT IDLE +12° + KIAS <156 + RA <1053 ft (321 m) if valid',
       'Gear warning tone group 3: gear up + one PLA <FLIGHT IDLE +12° + both PLA <RATING detent + HORN switch not at MUTE + KIAS <156 + RA <1053 ft',
       'ONLY case 3 (single-engine failure <156 KIAS) may be MUTED via the HORN switch',
       'Normal braking: ANTI SKID switch ON arms anti-skid above 10 kts wheel speed. Self-test prevented above 17 kts. TEST on ground: caution lights 6 sec. TEST in air with gear extended: caution lights 3 sec',
       'ASCU 5-second brake delay: applied if main wheels have not spun up after touchdown; immediately cancelled when wheel speed >35 kts',
       'Emergency/parking brake: lever on engine-control quadrant. No.2 hyd OR accumulator. NO differential braking, NO anti-skid',
       'Park brake accumulator: ~6 applications when fully charged. Minimum 500 PSI before engine start',
       'Park brake increase methods: hand pump in right main wheel well, OR AC power running SPU + PTU',
       'PARK detent on EMERG BRAKE lever: triggers PARKING BRAKE caution light + (with engine power) T/O warning horn',
       'Tire fill pressure gauges: customer option, integral with inflation valve, dial gauge with shaded "proper inflation +5% tolerance"',
       'Nose Gear Ground Lock Control Handle: IN (flush) = nose gear unlocked / OUT not rotated = downlock disengaged / OUT rotated CW = downlock engaged'
     ),
     JSON_ARRAY(
       'No.2 hyd drives gear AND steering. No.1 hyd drives brakes. Memorise the split.',
       'Mains retract AFT into nacelles; nose retracts FORWARD. Easy to swap.',
       'Nosewheel hand control gives 70° each way; pedals give 8° each way. Don''t mix the values.',
       'Passive caster mode is 120° each way. Differential brake/power for directional control in caster.',
       'Reverse taxi: STEERING must be ON but no tiller or pedal input. Different from "STEERING OFF for reverse."',
       'Alternate extension is a 3-DOOR sequence: INHIBIT switch + RELEASE door (overhead, main gear) + EXTENSION door (floor, nose + pump). Forgetting INHIBIT first causes incomplete isolation.',
       'After alternate extension, BOTH doors must be left FULLY OPEN — closing them defeats the alternate path.',
       'Anti-skid 5-sec brake delay only fires if wheels haven''t spun up at touchdown. On a contaminated runway, this delay is what saves you from immediate lock-up.',
       'Park brake accumulator gives ~6 applications. After 6 you''re relying on hand pump or PTU/SPU recharge.',
       '500 PSI minimum park brake pressure before start. Lower = no reliable holding.'
     ),
     JSON_ARRAY(
       'No.1 = brakes; No.2 = gear + steering. NOT the other way.',
       'Mains retract AFT (NOT forward). Nose retracts FORWARD (NOT aft).',
       'Hand control 70° each side; pedal 8° each side. Easy to confuse.',
       'Caster mode 120° each side (not 100°, not 90°).',
       'NOSE STEERING caution: ON+fault, OR OFF+pressure detected. Two distinct trigger conditions.',
       'Anti-skid arms at >10 kts; self-test prevented above 17 kts. Brake delay cancels at 35 kts. Three different speeds.',
       'Park brake: ~6 accumulator applications, 500 PSI minimum. Two different numbers.',
       'Gear warning tone: ONLY case 3 may be muted. Cases 1 and 2 cannot.',
       '156 KIAS / 1053 ft RA — common tone-trigger thresholds. Memorise.',
       'Gear extension is INITIATED by hyd, but PRIMARY DOWNLOCK is by overcenter mechanical locks. Continuous hyd pressure acts on the gear when down + locked.',
       'Alternate extension: gear FREE FALLS — may not fully extend on its own. Hand pump completes MLG downlock if needed.',
       'Tire pressure gauges are CUSTOMER OPTION (not standard). Don''t assume they''re always fitted.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'landing-gear-overview';
