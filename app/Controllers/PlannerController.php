<?php
/**
 * Planner Controller
 *
 * Manages study planning and scheduling
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Core\DB;

class PlannerController extends Controller
{
    /**
     * Show study planner
     */
    public function index(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $userId = $this->user()['id'];
        $db = DB::instance();

        $plans = $db->query(
            "SELECT id, title, exam_date, daily_minutes, status, created_at
             FROM study_plans
             WHERE user_id = ? AND status = 'active'
             ORDER BY created_at DESC",
            [$userId]
        );

        $upcomingItems = $db->query(
            "SELECT s.name as system_name, spi.scheduled_date, spi.duration_mins, spi.status, spi.plan_id
             FROM study_plan_items spi
             JOIN systems s ON spi.system_id = s.id
             JOIN study_plans sp ON spi.plan_id = sp.id
             WHERE sp.user_id = ? AND spi.scheduled_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND spi.scheduled_date >= CURDATE()
             ORDER BY spi.scheduled_date ASC",
            [$userId]
        );

        $systems = $db->query(
            "SELECT id, name, ata_code, color_hex FROM systems ORDER BY sort_order ASC"
        );

        $data = [
            'title' => 'Study Planner',
            'plans' => $plans,
            'systems' => $systems,
            'upcomingItems' => $upcomingItems,
            'csrf_token' => CSRF::generate(),
        ];

        $html = $this->view('planner/index', $data);
        $response->html($html);
    }

    /**
     * Create a new study plan
     */
    public function create(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        if (!CSRF::check($request)) {
            $response->status(419);
            $response->json(['error' => 'CSRF token mismatch']);
            return;
        }

        $userId = $this->user()['id'];
        $title = trim($this->input('title', ''));
        $examDate = $this->input('exam_date');
        $dailyMins = (int)$this->input('daily_minutes', $this->input('daily_mins', 60));

        if (empty($title)) {
            $response->json(['error' => 'Title is required'], 422);
            return;
        }

        if ($dailyMins < 10 || $dailyMins > 480) {
            $response->json(['error' => 'Daily minutes must be between 10 and 480'], 422);
            return;
        }

        if (!empty($examDate)) {
            $examDateTime = strtotime($examDate);
            $today = strtotime('today');
            if ($examDateTime === false || $examDateTime <= $today) {
                $response->json(['error' => 'Exam date must be in the future'], 422);
                return;
            }
        }

        $db = DB::instance();

        $planId = $db->insert(
            "INSERT INTO study_plans (user_id, title, exam_date, daily_minutes, status, created_at)
             VALUES (?, ?, ?, ?, 'active', NOW())",
            [$userId, $title, !empty($examDate) ? $examDate : null, $dailyMins]
        );

        if (!$planId) {
            header('Location: /planner?error=create_failed');
            exit;
        }

        $selectedSystems = $this->input('system_ids', $this->input('systems', []));
        if (!empty($selectedSystems) && is_array($selectedSystems)) {
            $this->generateStudyPlanItems($planId, $selectedSystems, !empty($examDate) ? $examDate : null);
        }

        header('Location: /planner');
        exit;
    }

    /**
     * Generate study plan items
     */
    private function generateStudyPlanItems(int $planId, array $systemIds, ?string $examDate): void
    {
        if (empty($systemIds)) {
            return;
        }

        $db = DB::instance();
        $today = date('Y-m-d');
        $endDate = $examDate ? $examDate : date('Y-m-d', strtotime('+30 days'));

        $systemCount = count($systemIds);
        $daysAvailable = max(1, (strtotime($endDate) - strtotime($today)) / 86400);
        $systemsPerDay = max(1, ceil($systemCount / $daysAvailable));

        $currentDate = $today;
        $systemIndex = 0;

        while ($currentDate <= $endDate && $systemIndex < $systemCount) {
            for ($i = 0; $i < $systemsPerDay && $systemIndex < $systemCount; $i++) {
                $db->execute(
                    "INSERT INTO study_plan_items (plan_id, system_id, scheduled_date, duration_mins, status)
                     VALUES (?, ?, ?, 60, 'pending')",
                    [$planId, $systemIds[$systemIndex], $currentDate]
                );
                $systemIndex++;
            }
            $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
        }
    }
}
