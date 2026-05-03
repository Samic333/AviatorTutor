-- =============================================================================
-- AviatorTutor — Phase 14 (ATA 35 Oxygen) lesson row.
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'oxygen' LIMIT 1);
SELECT @system_id AS resolved_system_id;

DELETE FROM lessons WHERE system_id = @system_id AND slug = 'oxygen-overview';

INSERT INTO lessons
    (system_id, title, slug, content_type, summary, body,
     key_facts, must_know, exam_traps, sort_order, is_published)
VALUES
    (@system_id,
     'Oxygen — Crew Fixed, Portable, PBE',
     'oxygen-overview',
     'overview',
     'Crew fixed oxygen: 3 full-face masks (pilot, copilot, observer) on a single common cylinder in the right lower nose compartment. Green burst disc on right exterior nose ejects on over-pressurisation. Lighted gauge on COPILOT side console. Cylinder off → pressure to atmospheric (gauge still reads bottle). Capacity: descent to 14,000 ft in 4 minutes + flight at 14,000 ft for 116 minutes. Inflatable harness via red button — masks donned in less than 5 seconds. Regulator 3 positions: NORM (air/O2 mix by cabin altitude), 100% (full O2 regardless of altitude), EMER (100% O2 at positive pressure — also purges smoke goggles; do NOT leave in EMER continuously). In-line pressure indicator: GREEN good, RED low. Microphone audio on each mask. Outlets cross-compatible. Plus: portable O2 cylinders for cabin attendants and passengers, Protective Breathing Equipment (PBE) for flight deck + cabin attendants, first aid O2 in cabin.',
     '<p>Q400 oxygen is a layered system: a fixed flight-deck system with three full-face masks, separate portable systems for cabin attendants and passengers, Protective Breathing Equipment (PBE) for both crews, and first-aid oxygen for the cabin. The flight-deck masks are the captain''s key tool for cabin smoke or fire — designed to don in under 5 seconds via a red inflatable-harness button. The regulator''s three positions are the most important operational item: NORM for normal supplemental use, 100% for guaranteed pure oxygen at altitude, and EMER for 100% with positive pressure (also purges smoke goggles). EMER depletes the bottle quickly — use it for actual events, not as a default. The system capacity is sized for a single-engine descent to 14,000 ft in 4 minutes followed by 116 minutes at 14,000 ft, which covers typical depressurisation scenarios on Q400 sectors.</p>',
     JSON_ARRAY(
       'Crew fixed oxygen: 3 full-face masks — pilot, copilot, observer. Microphone audio on each',
       'Single common cylinder in the right lower nose compartment',
       'Green burst disc on right exterior nose ejects on cylinder over-pressurisation (visible from outside)',
       'Pressure gauge on cylinder + lighted flight-deck gauge on COPILOT side console',
       'Cylinder turned OFF → available mask pressure automatically reduced to atmospheric. Flight-deck gauge continues to show BOTTLE pressure',
       'Capacity: supplemental O2 for descent to 14,000 ft in 4 minutes AND flight at 14,000 ft for 116 minutes',
       'Masks stowed in cups on bulkhead behind pilot/copilot seats. Observer mask adjacent to copilot mask, plugged into the DUAL outlet on copilot supply line',
       'Outlet failover: if any outlet fails, masks can be plugged into another outlet — fully cross-compatible',
       'In-line pressure indicator on supply hose: GREEN with correct pressure / RED if pressure low. If donning gives breathing difficulty or red indicator, verify supply hose connection',
       'Mask donned via inflatable harness in less than 5 SECONDS. Red harness inflation button (momentary). Release deflates and compresses',
       'Regulator NORM position: automatic air/oxygen mixture varying with cabin altitude (supplemental at altitude)',
       'Regulator 100% position: 100% oxygen regardless of cabin altitude',
       'Regulator EMER position: 100% O2 at slight positive pressure. ALSO purges smoke from smoke goggles. CAUTION: keeping in EMER can DEPLETE the oxygen system',
       'WARNING: smoking is NOT permitted when oxygen is in use',
       'Portable passenger oxygen cylinders kept in the cabin',
       'Protective Breathing Equipment (PBE) units for flight deck crew AND cabin attendants',
       'First aid oxygen kept in the passenger compartment',
       'Single mask failure: observer mask may be used by either crew member',
       'Mic with audio connector on each crew mask — communication preserved on oxygen'
     ),
     JSON_ARRAY(
       'EMER position depletes the cylinder fast. Use only for actual smoke events; return to NORM or 100% as soon as cabin is safe.',
       'Flight-deck gauge keeps showing BOTTLE pressure even when cylinder is off (which closes the supply). The gauge is a measure of cylinder, not delivered.',
       'Donning time target: less than 5 seconds. Drill it. The inflatable harness via red button is fast.',
       'Single common cylinder = single point of failure. Loss of the cylinder means no fixed crew oxygen — divert if depressurisation is in progress.',
       'On a real cockpit smoke event: oxygen masks on, EMER position, 100% positive pressure flushes the smoke goggles.',
       'Outlet cross-compatibility: a failed outlet is recoverable by plugging into another. Brief the FO on this.',
       'No smoking on oxygen: not just rule-following, it''s a fire-prevention discipline. Pure O2 + ignition source is bad.'
     ),
     JSON_ARRAY(
       'Capacity numbers: 4 minutes (descent to 14,000) and 116 minutes (level at 14,000). Both are specific.',
       'Burst disc location: RIGHT side exterior of nose. Green disc.',
       'Cylinder location: RIGHT LOWER NOSE compartment. Not left, not aft.',
       'Flight-deck gauge location: COPILOT side console. Not pilot.',
       'Three regulator positions: NORM / 100% / EMER. Three is the count.',
       'EMER provides 100% O2 at POSITIVE pressure. Also purges smoke goggles. Two functions.',
       'In-line pressure indicator: GREEN good, RED low. Don''t swap.',
       'Cylinder OFF gives atmospheric pressure to masks but the GAUGE still reads bottle. Two different things.',
       'Less than 5 SECONDS for don. Not 10, not 15.',
       'PBE is for both flight-deck crew AND cabin attendants — different from the fixed crew system.'
     ),
     10, 1);

SELECT id AS resolved_lesson_id, title FROM lessons WHERE system_id = @system_id AND slug = 'oxygen-overview';
