<?php
/**
 * Diagram Controller
 *
 * Displays system diagrams and technical illustrations
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;

class DiagramController extends Controller
{
    /**
     * Show a specific diagram
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function show(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $id = (int) $this->param('id');
        $db = DB::instance();

        $diagram = $db->queryOne(
            'SELECT * FROM diagrams WHERE id = ?',
            [$id]
        );

        if (!$diagram) {
            $response->status(404);
            $response->html('<h1>Diagram Not Found</h1>');
            return;
        }

        $system = $db->queryOne(
            'SELECT id, name, color_hex FROM systems WHERE id = ?',
            [$diagram['system_id']]
        );

        $hotspots = $db->query(
            'SELECT * FROM diagram_hotspots WHERE diagram_id = ? ORDER BY id',
            [$id]
        );

        $states = $db->query(
            'SELECT id, state_name, state_label AS label, description, hotspot_overrides
             FROM diagram_states WHERE diagram_id = ? ORDER BY id',
            [$id]
        );

        $data = [
            'title'    => $diagram['title'],
            'diagram'  => $diagram,
            'system'   => $system,
            'hotspots' => $hotspots,
            'states'   => $states,
        ];

        $html = $this->view('diagrams/show', $data);
        $response->html($html);
    }
}
