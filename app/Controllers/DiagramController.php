<?php
/**
 * Diagram Controller
 *
 * Displays system diagrams and technical illustrations
 */

namespace App\Controllers;

use App\Core\Controller;
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
        $this->requireAuth();

        $id = $this->param('id');

        $data = [
            'title' => 'System Diagram',
            'diagram_id' => $id,
            // 'diagram' => Diagram::find($id), // TODO: Load from database
        ];

        $html = $this->view('diagrams/show', $data);
        $response->html($html);
    }
}
