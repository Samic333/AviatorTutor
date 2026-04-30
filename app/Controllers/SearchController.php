<?php
/**
 * Search Controller
 *
 * Handles content search across the application
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\DB;

class SearchController extends Controller
{
    /**
     * Search for content
     */
    public function index(Request $request, Response $response): void
    {
        $this->requireActiveSubscription();

        $query = trim($this->query('q', ''));
        $results = [];

        if (!empty($query) && strlen($query) >= 2) {
            $db = DB::instance();
            $searchTerm = "%{$query}%";

            $systems = $db->query(
                "SELECT id, name, description, ata_code FROM systems
                 WHERE (name LIKE ? OR description LIKE ? OR ata_code LIKE ?)
                 LIMIT 5",
                [$searchTerm, $searchTerm, $searchTerm]
            );

            foreach ($systems as $system) {
                $results[] = [
                    'type' => 'system',
                    'id' => $system['id'],
                    'title' => $system['name'],
                    'system_name' => $system['name'],
                    'excerpt' => substr(strip_tags($system['description'] ?? ''), 0, 150),
                    'url' => '/systems/' . $system['id'],
                ];
            }

            $lessons = $db->query(
                "SELECT l.id, l.title, l.body, s.name as system_name, s.id as system_id
                 FROM lessons l
                 JOIN systems s ON l.system_id = s.id
                 WHERE (l.title LIKE ? OR l.body LIKE ?) AND l.is_published = 1
                 LIMIT 10",
                [$searchTerm, $searchTerm]
            );

            foreach ($lessons as $lesson) {
                $results[] = [
                    'type' => 'lesson',
                    'id' => $lesson['id'],
                    'title' => $lesson['title'],
                    'system_name' => $lesson['system_name'],
                    'excerpt' => substr(strip_tags($lesson['body'] ?? ''), 0, 150),
                    'url' => '/systems/' . $lesson['system_id'] . '#lesson-' . $lesson['id'],
                ];
            }

            $flashcards = $db->query(
                "SELECT f.id, f.front, f.back, s.name as system_name, s.id as system_id
                 FROM flashcards f
                 JOIN systems s ON f.system_id = s.id
                 WHERE (f.front LIKE ? OR f.back LIKE ?)
                   AND (f.status IS NULL OR f.status = 'published')
                 LIMIT 5",
                [$searchTerm, $searchTerm]
            );

            foreach ($flashcards as $flashcard) {
                $results[] = [
                    'type' => 'flashcard',
                    'id' => $flashcard['id'],
                    'title' => $flashcard['front'],
                    'system_name' => $flashcard['system_name'],
                    'excerpt' => substr(strip_tags($flashcard['back'] ?? ''), 0, 150),
                    'url' => '/flashcards/' . $flashcard['system_id'],
                ];
            }
        }

        $data = [
            'title' => 'Search',
            'query' => $query,
            'results' => $results,
        ];

        $html = $this->view('search/index', $data, 'pilot');
        $response->html($html);
    }
}
