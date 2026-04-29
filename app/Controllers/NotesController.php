<?php
/**
 * Notes Controller
 *
 * Lists every note the user has saved, grouped by system. Edit/save flows
 * are still served by ApiController::saveNotes() and the per-system note
 * widget on study pages — this controller adds the dedicated /notes page
 * and a delete endpoint.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Core\DB;

class NotesController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        $this->requireAuth();
        $userId = (int) ($this->user()['id'] ?? 0);

        $rows = DB::instance()->query(
            'SELECT n.id, n.system_id, n.lesson_id, n.content, n.updated_at,
                    s.name AS system_name, s.color_hex AS system_color, s.icon AS system_icon,
                    l.title AS lesson_title
               FROM notes n
               LEFT JOIN systems s ON s.id = n.system_id
               LEFT JOIN lessons l ON l.id = n.lesson_id
              WHERE n.user_id = ?
              ORDER BY n.updated_at DESC',
            [$userId]
        );

        // Group by system.
        $bySystem = [];
        foreach ($rows as $r) {
            $key = (int) ($r['system_id'] ?? 0);
            if (!isset($bySystem[$key])) {
                $bySystem[$key] = [
                    'system_id'    => $key,
                    'system_name'  => $r['system_name'] ?? 'General',
                    'system_color' => $r['system_color'] ?? '#38BDF8',
                    'system_icon'  => $r['system_icon']  ?? 'book',
                    'notes'        => [],
                ];
            }
            $bySystem[$key]['notes'][] = $r;
        }

        $response->html($this->view('pilot/notes', [
            'title'      => 'My Notes',
            'bySystem'   => array_values($bySystem),
            'totalNotes' => count($rows),
            'csrf_token' => CSRF::generate(),
            'flashOk'    => $this->popFlash('flash_ok'),
            'flashError' => $this->popFlash('flash_error'),
        ], 'pilot'));
    }
}
