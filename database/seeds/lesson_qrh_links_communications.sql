-- =============================================================================
-- AviatorTutor — Phase 4: ATA 23 Communications — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'communications' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'communications-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'TWO-WAY RADIO COMMUNICATION FAILURE — Lost Comms',
 'On confirmed lost comms: (1) squawk 7600 immediately; (2) attempt comms on alternate VHF, HF, or guard 121.5; (3) try the VHF1 Standby Control and Display Unit if both ARCDUs failed; (4) follow the lost-comms procedure per filed flight plan / cleared route / destination; (5) approach as last expected; (6) descent and landing per published or briefed procedure if no clearance can be obtained.',
 0,
 'Squawk 7600 first — that is the leading-indicator action that triggers ATC to give you priority handling. Switch through every comm option you have before declaring full lost-comms. The first 2 minutes determine whether ATC can re-establish contact via relay.',
 'No response from ATC after multiple radio calls; ARCDU goes dark; persistent ATC traffic on different frequency suggests you are off-frequency.',
 '7600 · 121.5 · Filed route · Last expected.',
 10),

(@lesson_id, NULL,
 'BOTH ARCDUs FAILED — VHF1 Standby Operations',
 'Both ARCDUs unusable. Action: (1) confirm VHF1 Standby Control and Display Unit available — switch ON; (2) tune to current ATC frequency or guard 121.5; (3) declare to ATC; (4) accept reduced situational awareness — NAV audio integration is impaired; (5) brief approach as hand-flown; (6) consider precautionary divert if approach involves complex audio cues (e.g., LOC/NDB station ID identification).',
 0,
 'The single backup keeps VHF1 alive. NAV identification audio you would normally hear is impaired — confirm NAV station identification by other means (e.g., FMS, visual cross-check, ATC confirmation).',
 'Both ARCDU panels dark or frozen. Possible cabin / panel fault that took both units offline simultaneously.',
 'Standby ON · Tune VHF1 · Declare · Brief hand-fly.',
 20),

(@lesson_id, NULL,
 'ELT INADVERTENT ACTIVATION — Reset',
 'Inadvertent ELT activation after a hard landing, hard taxi, or longitudinal G event. Recognition: ELT monitor light flashing every 4 seconds (instead of 3). Action: complete taxi safely; on stand BEFORE shutdown set the ELT remote switch to RESET & TEST momentarily; confirm monitor light goes out; advise tower; tech-log entry; engineering review of inertia switch threshold if it triggers repeatedly.',
 0,
 'Tower will hear your inadvertent ELT before you do on a busy airfield. Reset on stand, not in flight. Make the post-flight ELT light check a habit.',
 'Monitor light flashing every 4 sec (not the normal 3-sec cycle). Possible 121.5 noise on guard reported by ATC.',
 'Stand · RESET & TEST · Advise · Tech log.',
 30),

(@lesson_id, NULL,
 'ELT REAL DISTRESS — Manual Activation',
 'Real distress event (forced landing, ditching, or post-impact survival). Action: set ELT remote switch to ON — this overrides the automatic inertia switch and confirms transmission. Monitor light flashes every 4 seconds confirming active transmission. Verify all three frequencies (121.5 / 243 / 406 MHz) are radiating per Kannad system status. Once on the ground, leave the ELT ON to assist SAR.',
 1,
 'In a forced landing the inertia switch may or may not have triggered. ON forces the transmission. Confirm with the cabin crew and brief that the ELT is ACTIVE and search-and-rescue is on the way.',
 'Forced landing imminent or completed. Aircraft attitude or impact suggests inertia trigger may have fired or may not have.',
 'ON · Override · Confirm · SAR is coming.',
 40),

(@lesson_id, NULL,
 'CVR PRESERVATION AFTER INCIDENT',
 'Following an incident or accident in flight: as soon as practical, pull the CVR circuit breaker BEFORE taxi-in to preserve the last 2 hours of audio. Do NOT cycle the breaker during the incident. The continuous-loop SSCVR will overwrite the recording during a normal taxi-in if not isolated. Make a tech-log entry; engineering / safety team will pull the unit for analysis.',
 0,
 'Two-hour recording captures roughly the entire incident sequence on a typical short-medium sector. Pulling the CB is the only way to stop the loop. Confirm crew agreement before pulling — once pulled, do not re-engage until safety team approves.',
 'Significant abnormal event in flight (engine failure, fire, hydraulic loss, severe turbulence injury, cabin pressurisation event, etc.).',
 'Incident · Land · Pull CVR CB · Tech log.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
