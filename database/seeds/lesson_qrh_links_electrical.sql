-- =============================================================================
-- AviatorTutor — Phase 5: ATA 24 Electrical Power — QRH cross-references
-- =============================================================================

SET @system_id := (SELECT id FROM systems WHERE slug = 'electrical' LIMIT 1);
SET @lesson_id := (SELECT id FROM lessons WHERE system_id = @system_id AND slug = 'electrical-overview' LIMIT 1);

DELETE FROM lesson_qrh_links WHERE lesson_id = @lesson_id;

INSERT INTO lesson_qrh_links (lesson_id, slide_id, qrh_section_title, qrh_excerpt, memory_item, ops_meaning, recognition_cue, memory_trigger, sort_order) VALUES
(@lesson_id, NULL,
 'DC BUS Caution — Five-Second Timing Window',
 'A DC BUS caution indicates a main bus fault has been detected and the EPCU is in the process of isolating the bus. For approximately 5 seconds the EPCU prevents the upper horizontal and two vertical bus ties from closing. If the fault clears within that window, the caution self-clears. If the fault PERSISTS past 5 seconds, the EPCU sends a TRIP signal to the affected GCU, isolating the generator, AND opens / locks-out the contactors connecting the batteries to the affected main bus. MAIN BATTERY (or AUX and STBY BATTERY) caution lights and the related DC GEN caution will then illuminate. All main DC services on the faulted bus side will not function.',
 0,
 'When you see DC BUS, START THE TIMING. A transient caution that clears within 5 seconds is a non-event. A persistent caution is the start of a cascade — within seconds the panel will light up with battery and generator cautions and the affected bus will be dead. Brief the FO during normal operations: "DC BUS — count to five — if it stays, run the QRH."',
 'DC BUS caution illuminates on the ESCP / annunciator panel. Possible bus voltage anomaly on the MFD electrical page.',
 'BUS · 5-sec · GEN trip · BATT lockout.',
 10),

(@lesson_id, NULL,
 'DC GEN Inop — Single Generator Loss',
 'A single DC generator failure (mechanical, control, or excitation) causes the EPCU to re-configure bus ties automatically. The surviving DC generator plus both TRUs feed all DC buses. The crew sees a DC GEN caution; the cabin notices nothing. QRH actions: confirm switch positions correct; verify reconfiguration on MFD electrical page; consider deferring the defect per MEL if defects allow continuation; brief approach for reduced electrical redundancy. Continue flight; this is not a divert event by itself.',
 0,
 'A clean single-gen failure is operationally manageable on the Q400 because of the four-source DC architecture. Note the defect, monitor the surviving sources, brief the FO. Diversion only if a SECOND failure or compounding factor appears.',
 'DC GEN 1 or DC GEN 2 caution illuminates. Affected generator output drops on the MFD; bus tie status changes.',
 'DC GEN INOP · EPCU reconfigures · QRH · Continue.',
 20),

(@lesson_id, NULL,
 'BATTERY Caution (MAIN / AUX / STBY)',
 'A discrete fault on the named battery: MAIN BATTERY, AUX BATTERY, or STBY BATTERY caution. Possible causes include contactor lockout (after a bus fault), low charge state, or internal battery failure. QRH actions: check charge state on MFD; confirm BATTERY MASTER switch position; consider isolating the affected battery; brief approach for reduced electrical redundancy. The standby battery (17 Ahr) is the most critical for ESS standby bus power on a complete generator loss — protect it.',
 0,
 'A standalone battery caution at altitude is rarely time-critical, but it removes a layer of redundancy. If you also have any generator caution active, treat it as the second card in a cascade and act accordingly.',
 'MAIN BATTERY / AUX BATTERY / STBY BATTERY caution. Charge state anomaly on MFD electrical page.',
 'Battery caution · Check charge · Brief reduced redundancy.',
 30),

(@lesson_id, NULL,
 'COMPLETE DC GENERATION LOSS (Battery-Only)',
 'Extreme failure scenario: all four DC sources (both gens + both TRUs) lost. Battery-only operation. The 40 Ahr main and aux batteries serve essential buses; the 17 Ahr standby covers the ESS standby bus longest because of its lower load. QRH actions: load-shed per published list (non-essential lighting, galley, recirc fan, etc. OFF); MAYDAY or PAN-PAN per QRH; descend to a comfortable altitude; nearest suitable airport. Battery time is FINITE — there is no "fly to destination" option in this case.',
 1,
 'This is the type of event that ends a sector. The captain decision is fast: nearest suitable, brief the cabin, prepare for possible engine restart attempt if the AC gens are also lost. Memorise the load-shed list.',
 'Multiple cautions cascade: DC GEN 1, DC GEN 2, plus possibly AC GEN cautions and BUS FAULT. MFD electrical page shows minimal sources.',
 'Load shed · MAYDAY · Nearest suitable.',
 40),

(@lesson_id, NULL,
 'AC GEN Inop or Overload (+1.xx)',
 'AC GEN 1 or AC GEN 2 caution, OR MFD AC GEN LOAD shows a "+" prefix indicating overload. Causes: generator drive failure, control issue, or load distribution anomaly. QRH actions: confirm generator switch position; consider load-shed if overload persists (turning off non-essential AC loads to reduce demand); if generator off-line, confirm bus tie reconfiguration. Loss of AC also affects the corresponding TRU — the DC bus may rely on the surviving TRU + DC gen.',
 0,
 'AC overload is a slow problem. Watch the trend on the MFD load display. If you see "+" appear, identify which loads are growing — galley, anti-ice, recirc fan are common contributors.',
 'AC GEN 1 / AC GEN 2 caution. MFD AC GEN LOAD with "+" prefix.',
 'AC OVL · Identify load · Shed if persistent.',
 50);

SELECT COUNT(*) AS qrh_links_inserted FROM lesson_qrh_links WHERE lesson_id = @lesson_id;
