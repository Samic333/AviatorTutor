-- Q400 Aircraft Systems Study Database Seed Data
-- Realistic sample data for development and testing

USE q400_study;

-- ============================================================================
-- USERS
-- ============================================================================

INSERT INTO users (id, name, email, password_hash, role, study_streak) VALUES
(1, 'Admin User', 'admin@q400study.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0),
(2, 'Samuel', 'samuel@q400study.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 3),
(3, 'Alice Cooper', 'alice@q400study.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 1),
(4, 'Bob Newman', 'bob@q400study.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 0);

-- ============================================================================
-- SYSTEMS (Q400 ATA 22 Systems)
-- ============================================================================

INSERT INTO systems (id, name, slug, ata_code, description, color_hex, icon, sort_order, is_published) VALUES
(1, 'Electrical Power', 'electrical', 'ATA24', 'DC and AC generation, distribution, batteries, TRUs and external power', '#F59E0B', 'bolt', 1, 1),
(2, 'Hydraulic Power', 'hydraulic', 'ATA29', 'Four hydraulic systems powering flight controls, landing gear and brakes', '#3B82F6', 'droplets', 2, 1),
(3, 'Fuel', 'fuel', 'ATA28', 'Wing tank storage, feed systems, transfer and quantity management', '#10B981', 'fuel', 3, 1),
(4, 'Powerplant', 'powerplant', 'ATA71', 'PW150A turboprop engines, controls, and indication systems', '#EF4444', 'settings', 4, 1),
(5, 'Propeller', 'propeller', 'ATA61', 'Six-blade composite propeller, pitch control and NTS protection', '#8B5CF6', 'rotate-ccw', 5, 1),
(6, 'Flight Controls', 'flight-controls', 'ATA27', 'Primary and secondary flight control surfaces and actuation', '#06B6D4', 'navigation', 6, 1),
(7, 'Landing Gear', 'landing-gear', 'ATA32', 'Retractable tricycle gear, extension/retraction, steering and brakes', '#F97316', 'circle', 7, 1),
(8, 'Air Conditioning & Pressurization', 'air-cond-press', 'ATA21', 'ECS bleed air, cabin pressurization and temperature control', '#84CC16', 'wind', 8, 1),
(9, 'Pneumatics', 'pneumatics', 'ATA36', 'Bleed air source selection, ducting and distribution', '#14B8A6', 'activity', 9, 1),
(10, 'Ice & Rain Protection', 'ice-rain', 'ATA30', 'Pneumatic boot de-icing, windshield and probe heating', '#93C5FD', 'cloud-snow', 10, 1),
(11, 'Fire Protection', 'fire-protection', 'ATA26', 'Engine and APU fire detection, extinguishing systems', '#DC2626', 'flame', 11, 1),
(12, 'Autoflight', 'autoflight', 'ATA22', 'Autopilot, flight director, autothrottle systems', '#7C3AED', 'cpu', 12, 1),
(13, 'Navigation', 'navigation', 'ATA34', 'FMS, IRS, VOR, ILS, GPS and TCAS navigation systems', '#2563EB', 'compass', 13, 1),
(14, 'Communications', 'communications', 'ATA23', 'VHF, HF, SATCOM, interphone and audio systems', '#059669', 'radio', 14, 1),
(15, 'Indicating & Recording', 'indicating-recording', 'ATA31', 'EFIS, EICAS, digital flight data and cockpit voice recorders', '#D97706', 'monitor', 15, 1),
(16, 'Oxygen', 'oxygen', 'ATA35', 'Crew and passenger oxygen systems', '#EC4899', 'wind', 16, 1),
(17, 'Lighting', 'lighting', 'ATA33', 'Interior and exterior aircraft lighting systems', '#FBBF24', 'sun', 17, 1),
(18, 'Aeroplane General', 'aeroplane-general', 'ATA21', 'Aircraft general description, dimensions and structural overview', '#6B7280', 'airplay', 18, 1),
(19, 'FMS', 'fms', 'ATA22B', 'Flight Management System operations and database', '#4F46E5', 'database', 19, 1),
(20, 'Caution & Warning', 'caution-warning', 'CW', 'Master Warning/Caution messages, alerts and crew responses', '#EF4444', 'alert-triangle', 20, 1),
(21, 'DU Messages', 'du-messages', 'DU', 'Display Unit system messages reference', '#78716C', 'message-square', 21, 1),
(22, 'Quick Reference Handbook', 'qrh', 'QRH', 'QRH procedures, memory items and limitations', '#BE185D', 'book-open', 22, 1);

-- ============================================================================
-- SUBTOPICS (For Electrical System - System ID 1)
-- ============================================================================

INSERT INTO subtopics (system_id, title, slug, sort_order, is_published) VALUES
(1, 'DC Generation System', 'dc-generation', 1, 1),
(1, 'AC Generation System', 'ac-generation', 2, 1),
(1, 'Battery System', 'battery-system', 3, 1),
(1, 'External Power', 'external-power', 4, 1),
(1, 'Distribution Buses', 'distribution-buses', 5, 1),
(1, 'TRU System', 'tru-system', 6, 1);

-- ============================================================================
-- LESSONS
-- ============================================================================

-- Electrical System Overview
INSERT INTO lessons (system_id, subtopic_id, title, slug, content_type, body, summary, key_facts, sort_order, is_published) VALUES
(1, NULL, 'Electrical Power System - Overview', 'electrical-overview', 'overview',
'The Q400 electrical system consists of two independent electrical power sources - the engine-driven alternators and the battery system. The primary power source is the engine-driven alternators which provide both AC and DC electrical power throughout the aircraft during normal operations. The battery system provides emergency power and is used for engine starting. Two nickel-cadmium batteries provide backup power when the alternators are not available. The electrical system is protected by numerous circuit breakers and relays to ensure safe operation and prevent overloading of critical circuits. The system operates at 28 VDC and maintains AC power at 115V AC for various aircraft systems.',
'Two independent electrical power sources provide reliable power to all aircraft systems.',
'["Two alternators provide AC and DC power", "28 VDC system for DC distribution", "Two Nickel-Cadmium batteries for backup", "115V AC for AC systems", "Comprehensive circuit protection"]',
1, 1);

-- Hydraulic System Overview
INSERT INTO lessons (system_id, subtopic_id, title, slug, content_type, body, summary, key_facts, sort_order, is_published) VALUES
(2, NULL, 'Hydraulic Power System - Overview', 'hydraulic-overview', 'overview',
'The Q400 is equipped with four independent hydraulic systems: No.1 Main System, No.2 Main System, No.3 Main System, and the Emergency System (hand-operated). Each main hydraulic system operates at 3000 PSI and provides power for flight control actuation, landing gear extension/retraction, and wheel brake operation. The No.1 and No.2 systems are the primary hydraulic sources, with No.3 providing redundancy. The emergency system can be used for manual landing gear extension and brake application in the event of main hydraulic system failure. All systems use phosphate ester fire-resistant fluid (MIL-H-46000) for safety.',
'Four independent hydraulic systems at 3000 PSI power all aircraft critical systems.',
'["Four independent systems provide redundancy", "3000 PSI operating pressure", "Fire-resistant fluid (MIL-H-46000)", "Main and emergency systems", "Powers flight controls and landing gear"]',
2, 1);

-- Fuel System Overview
INSERT INTO lessons (system_id, subtopic_id, title, slug, content_type, body, summary, key_facts, sort_order, is_published) VALUES
(3, NULL, 'Fuel System - Overview', 'fuel-overview', 'overview',
'The Q400 fuel system consists of two main fuel tanks located in the wings and an optional fuselage auxiliary tank. The main fuel tanks provide a total capacity of 3,050 gallons with the main tanks alone holding 2,650 gallons. The fuel system includes mechanically-driven fuel pumps, engine feed lines, crossfeed capability, and a fuel management system that monitors and displays fuel quantity. The fuel jettison system allows rapid fuel dumping in emergency situations. All fuel tanks are equipped with low-level warning switches and fuel quantity indicating systems for accurate monitoring.',
'Two main wing tanks store fuel with optional auxiliary capacity and full management systems.',
'["Two main tanks in wings", "3050 gallons total capacity", "Mechanical fuel pumps", "Crossfeed capability for redundancy", "Fuel jettison for emergencies"]',
3, 1);

-- Powerplant Overview
INSERT INTO lessons (system_id, subtopic_id, title, slug, content_type, body, summary, key_facts, sort_order, is_published) VALUES
(4, NULL, 'Powerplant Systems - Overview', 'powerplant-overview', 'overview',
'The Q400 is powered by two Pratt & Whitney PT6A-114A turboprop engines. Each engine produces 2,500 shaft horsepower with automatic power limiting during takeoff and climb. The engines are equipped with fuel heaters, inlet particle separators, and oil cooling systems. Engine controls include the propeller governor, power lever, and fuel cutoff. The engine indication and crew alerting system (EICAS) provides comprehensive engine monitoring including N1, N2, ITT, fuel flow, and oil temperature. Fire protection is provided through early detection and suppression systems.',
'Two PT6A turboprop engines produce reliable power with comprehensive monitoring.',
'["Two PT6A-114A turboprop engines", "2500 shaft horsepower each", "Automatic power limiting", "Comprehensive EICAS monitoring", "Integrated fire protection"]',
4, 1);

-- ============================================================================
-- FLASHCARDS (Electrical and Hydraulic Systems)
-- ============================================================================

-- Electrical System Flashcards
INSERT INTO flashcards (system_id, subtopic_id, front, back, hint, difficulty, tags, created_by, created_at) VALUES
(1, 1, 'What is the DC operating voltage of the Q400 electrical system?', '28 VDC', 'Standard aircraft DC voltage', 'easy', '["electrical","basics"]', 1, NOW()),
(1, 1, 'What type of batteries does the Q400 use?', 'Three Nickel-Cadmium (NiCad) batteries', 'Type: Ni-Cd', 'easy', '["electrical","battery"]', 1, NOW()),
(1, 2, 'What does TRU stand for and what is its function?', 'Transformer Rectifier Unit - converts AC power to 28 VDC', 'Rectifies AC to DC', 'medium', '["electrical","tru"]', 1, NOW()),
(1, 2, 'What AC voltage is used in the Q400?', '115V AC', 'Standard aircraft AC voltage', 'easy', '["electrical","ac-power"]', 1, NOW()),
(1, 1, 'How many engine-driven alternators does the Q400 have?', 'Two alternators, one on each engine', 'One per engine', 'easy', '["electrical","generation"]', 1, NOW()),
(1, 1, 'What is the purpose of circuit breakers in the electrical system?', 'Protect electrical circuits from overcurrent and prevent damage or fire', 'Safety function', 'medium', '["electrical","protection"]', 1, NOW()),
(1, 3, 'What is the voltage output of the main alternators?', '115V AC primary output which is rectified to DC and used for AC loads', 'AC output first', 'medium', '["electrical","alternator"]', 1, NOW()),
(1, 1, 'In case of both alternators failing, what provides electrical power?', 'The battery system provides emergency power', 'Emergency source', 'medium', '["electrical","battery","emergency"]', 1, NOW()),
(1, 2, 'What are the main differences between AC and DC systems in the aircraft?', 'AC systems (115V) power heavier loads; DC system (28V) powers essential flight instruments and controls', 'Different voltages, different loads', 'hard', '["electrical","ac-dc"]', 1, NOW()),
(1, 1, 'What actions does the pilot take if an alternator fails?', 'Monitor system load, reduce non-essential electrical loads, land at nearest suitable airfield', 'Load reduction critical', 'hard', '["electrical","emergency-procedures"]', 1, NOW());

-- Hydraulic System Flashcards
INSERT INTO flashcards (system_id, subtopic_id, front, back, hint, difficulty, tags, created_by, created_at) VALUES
(2, NULL, 'How many hydraulic systems does the Q400 have?', 'Four: No.1 Main, No.2 Main, No.3 Main, and Emergency (hand-operated)', 'Include emergency', 'easy', '["hydraulic","systems"]', 1, NOW()),
(2, NULL, 'What is the operating pressure of the main hydraulic systems?', '3000 PSI', 'Standard pressure', 'easy', '["hydraulic","pressure"]', 1, NOW()),
(2, NULL, 'What type of fluid is used in Q400 hydraulic systems?', 'Phosphate ester fire-resistant fluid (MIL-H-46000)', 'Fire-resistant type', 'medium', '["hydraulic","fluid"]', 1, NOW()),
(2, NULL, 'What are the three main functions of the hydraulic systems?', 'Flight control actuation, landing gear extension/retraction, and wheel braking', 'Three critical functions', 'medium', '["hydraulic","functions"]', 1, NOW()),
(2, NULL, 'How is the emergency hydraulic system operated?', 'Hand-operated pump (manual backup system)', 'Manual operation', 'medium', '["hydraulic","emergency"]', 1, NOW()),
(2, NULL, 'What provides redundancy in the hydraulic system?', 'Four independent systems - any three can support full aircraft operation', 'Multiple systems', 'hard', '["hydraulic","redundancy"]', 1, NOW()),
(2, NULL, 'What is the consequence of losing the No.1 hydraulic system?', 'No.2 and No.3 systems can provide full aircraft operation, but reduced performance', 'Degraded operation', 'hard', '["hydraulic","failure-analysis"]', 1, NOW()),
(2, NULL, 'How are hydraulic system failures detected?', 'Pressure gauges, warning lights, and EICAS alerts notify crew of system degradation', 'Multiple indications', 'medium', '["hydraulic","indication"]', 1, NOW()),
(2, NULL, 'What must be checked before each flight regarding hydraulic systems?', 'Fluid level, system pressure, and proper functioning of all flight controls', 'Preflight checks', 'medium', '["hydraulic","preflight"]', 1, NOW()),
(2, NULL, 'What is the most critical action if all three main hydraulic systems fail in flight?', 'Deploy emergency hydraulic system for manual landing gear extension and braking', 'Emergency backup', 'hard', '["hydraulic","emergency-procedures"]', 1, NOW());

-- Fuel System Flashcards
INSERT INTO flashcards (system_id, subtopic_id, front, back, hint, difficulty, tags, created_by, created_at) VALUES
(3, NULL, 'What is the total fuel capacity of the Q400?', '3050 gallons (2650 in main tanks + 400 in auxiliary tank if installed)', 'Includes auxiliary', 'easy', '["fuel","capacity"]', 1, NOW()),
(3, NULL, 'How many main fuel tanks does the Q400 have?', 'Two main tanks located in the wings', 'Wing location', 'easy', '["fuel","tanks"]', 1, NOW()),
(3, NULL, 'What powers the fuel pumps in the Q400?', 'Mechanical drive from the engines and electric backup pumps', 'Dual power source', 'medium', '["fuel","pumps"]', 1, NOW()),
(3, NULL, 'What is the purpose of fuel crossfeed?', 'Allows transfer of fuel between tanks to maintain balance and manage fuel quantity', 'Balance management', 'medium', '["fuel","crossfeed"]', 1, NOW());

-- Powerplant Flashcards
INSERT INTO flashcards (system_id, subtopic_id, front, back, hint, difficulty, tags, created_by, created_at) VALUES
(4, NULL, 'What is the engine model on the Q400?', 'Pratt & Whitney PT6A-114A turboprop', 'PT6A variant', 'easy', '["powerplant","engines"]', 1, NOW()),
(4, NULL, 'How much shaft horsepower does each Q400 engine produce?', '2500 shaft horsepower', 'Per engine', 'easy', '["powerplant","power"]', 1, NOW()),
(4, NULL, 'What is automatic power limiting and when does it operate?', 'System that limits engine power to 2500 SHP during takeoff and climb to prevent over-torque', 'Protection feature', 'hard', '["powerplant","apl","protection"]', 1, NOW()),
(4, NULL, 'What does EICAS stand for?', 'Engine Indication and Crew Alerting System', 'Monitoring system', 'easy', '["powerplant","eicas","monitoring"]', 1, NOW());

-- ============================================================================
-- QUIZZES
-- ============================================================================

-- Electrical System Quiz
INSERT INTO quizzes (system_id, title, description, quiz_type, time_limit_mins, pass_score, is_published, created_at) VALUES
(1, 'Electrical System Fundamentals', 'Test your knowledge of the Q400 electrical power generation and distribution system', 'practice', 15, 70, 1, NOW());

-- Quiz Questions for Electrical System
INSERT INTO quiz_questions (quiz_id, question_text, question_type, options, correct_answer, explanation, difficulty, sort_order, created_at) VALUES
(1, 'What is the DC operating voltage of the Q400?', 'mcq',
'["28 VDC", "12 VDC", "115 VDC", "230 VDC"]',
'["28 VDC"]',
'The Q400 uses the standard aircraft DC system operating at 28 VDC for all DC-powered systems.',
'easy', 1, NOW()),

(1, 'How many batteries does the Q400 have?', 'mcq',
'["Two Nickel-Cadmium batteries", "Three Nickel-Cadmium batteries", "One Lithium battery", "Four Lead-Acid batteries"]',
'["Three Nickel-Cadmium batteries"]',
'The Q400 is equipped with three independent Nickel-Cadmium batteries for redundancy and reliability.',
'easy', 2, NOW()),

(1, 'What does TRU stand for?', 'mcq',
'["Transformer Rectifier Unit", "Turbine Revolutions Unit", "Thrust Reversing Utility", "Thermal Regulation Unit"]',
'["Transformer Rectifier Unit"]',
'The TRU converts AC power from the alternators to 28 VDC for use throughout the aircraft.',
'medium', 3, NOW()),

(1, 'In case of complete alternator failure, what is the primary source of electrical power?', 'mcq',
'["Battery system", "Ground power unit only", "APU generator", "External AC source"]',
'["Battery system"]',
'When both alternators are inoperative, the battery system provides essential electrical power for aircraft systems and emergency procedures.',
'medium', 4, NOW()),

(1, 'True or False: The Q400 has a single alternator that powers the entire aircraft.', 'true_false',
'["True", "False"]',
'["False"]',
'The Q400 has two independent alternators (one on each engine) providing redundancy and ensuring continuous power.',
'hard', 5, NOW());

-- ============================================================================
-- USER PROGRESS
-- ============================================================================

INSERT INTO user_progress (user_id, system_id, lesson_id, status, confidence, time_spent_secs, last_studied, created_at) VALUES
(2, 1, 1, 'in_progress', 65, 1800, NOW(), NOW()),
(2, 2, 2, 'completed', 85, 2400, NOW() - INTERVAL 1 DAY, NOW()),
(2, 3, 3, 'not_started', 0, 0, NULL, NOW()),
(3, 1, 1, 'completed', 75, 3600, NOW() - INTERVAL 2 DAY, NOW()),
(3, 4, 4, 'in_progress', 55, 1200, NOW(), NOW());

-- ============================================================================
-- REVISION SCHEDULE
-- ============================================================================

INSERT INTO revision_schedule (user_id, system_id, lesson_id, next_review_date, interval_days, priority, created_at) VALUES
(2, 1, 1, CURDATE() + INTERVAL 1 DAY, 1, 8, NOW()),
(2, 2, 2, CURDATE() + INTERVAL 3 DAY, 3, 5, NOW()),
(2, 3, NULL, CURDATE() + INTERVAL 2 DAY, 2, 7, NOW()),
(3, 1, 1, CURDATE(), 1, 9, NOW()),
(3, 4, 4, CURDATE() + INTERVAL 1 DAY, 1, 6, NOW());

-- ============================================================================
-- FLASHCARD REVIEWS (for spaced repetition tracking)
-- ============================================================================

INSERT INTO flashcard_reviews (flashcard_id, user_id, rating, next_review_at, interval_days, ease_factor, review_count, reviewed_at) VALUES
(1, 2, 4, NOW() + INTERVAL 1 DAY, 1, 2.50, 1, NOW()),
(2, 2, 3, NOW() + INTERVAL 3 DAY, 3, 2.36, 1, NOW()),
(3, 2, 4, NOW() + INTERVAL 5 DAY, 5, 2.70, 2, NOW()),
(4, 2, 2, NOW() + INTERVAL 1 DAY, 1, 1.96, 1, NOW()),
(5, 3, 5, NOW() + INTERVAL 10 DAY, 10, 2.80, 3, NOW()),
(6, 3, 4, NOW() + INTERVAL 3 DAY, 3, 2.56, 2, NOW()),
(11, 2, 3, NOW() + INTERVAL 2 DAY, 2, 2.36, 1, NOW()),
(12, 3, 5, NOW() + INTERVAL 7 DAY, 7, 2.80, 2, NOW());

-- ============================================================================
-- STUDY SESSIONS
-- ============================================================================

INSERT INTO study_sessions (user_id, system_id, session_type, started_at, ended_at, duration_secs, notes, created_at) VALUES
(2, 1, 'detail', NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 105 MINUTE, 900, 'Completed electrical system overview', NOW()),
(2, 2, 'flashcard', NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 45 MINUTE, 900, 'Reviewed hydraulic flashcards', NOW()),
(3, 1, 'detail', NOW() - INTERVAL 3 HOUR, NOW() - INTERVAL 2 HOUR, 3600, 'Detailed study of DC system', NOW()),
(3, 4, 'revision', NOW() - INTERVAL 30 MINUTE, NOW(), 1800, 'Quick revision session', NOW());

-- ============================================================================
-- STUDY PLANS
-- ============================================================================

INSERT INTO study_plans (user_id, title, exam_date, daily_minutes, status, created_at) VALUES
(2, 'Type Rating Study Plan', DATE_ADD(CURDATE(), INTERVAL 60 DAY), 120, 'active', NOW()),
(3, 'Systems Mastery Program', DATE_ADD(CURDATE(), INTERVAL 90 DAY), 90, 'active', NOW());

-- ============================================================================
-- TAGS
-- ============================================================================

INSERT INTO tags (name, slug, color_hex, created_at) VALUES
('electrical', 'electrical', '#F59E0B', NOW()),
('hydraulic', 'hydraulic', '#3B82F6', NOW()),
('emergency', 'emergency', '#EF4444', NOW()),
('procedures', 'procedures', '#06B6D4', NOW()),
('basics', 'basics', '#10B981', NOW()),
('advanced', 'advanced', '#7C3AED', NOW()),
('memory-item', 'memory-item', '#BE185D', NOW()),
('normal-ops', 'normal-ops', '#14B8A6', NOW());
