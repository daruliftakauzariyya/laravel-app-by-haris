<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;

class HomeController extends Controller
{
    public function index()
    {
        // $stats = [
        //     'total'     => Question::count(),
        //     'answered'  => Question::where('status', 'answered')->orWhere('status', 'published')->count(),
        //     'published' => Fatwa::count(),
        // ];
        $stats = [
            'total' => Database::query("SELECT COUNT(*) as count FROM questions")->fetch()->count,
            'answered' => Database::query("SELECT COUNT(*) as count FROM questions WHERE status IN ('answered', 'published')")->fetch()->count,
            'published' => Database::query("SELECT COUNT(*) as count FROM fatwas")->fetch()->count,
        ];

        // $latestFatwas = Fatwa::with(['question', 'muftiAnswer.mufti'])
        //                      ->latest('published_at')
        //                      ->take(6)
        //                      ->get();
        // Since we are moving to PDO, we just write a raw join:
        $latestFatwasQuery = "
            SELECT f.*, 
                   q.title as question_title, q.body as question_body,
                   m.answer as mufti_answer, u.name as mufti_name
            FROM fatwas f
            JOIN questions q ON f.question_id = q.id
            LEFT JOIN mufti_answers m ON f.mufti_answer_id = m.id
            LEFT JOIN users u ON m.mufti_id = u.id
            ORDER BY f.published_at DESC
            LIMIT 6
        ";
        $latestFatwas = Database::query($latestFatwasQuery)->fetchAll();

        return $this->view('home', [
            'title' => 'Home',
            'stats' => $stats,
            'latestFatwas' => $latestFatwas
        ]);
    }

    public function contact()
    {
        return $this->view('contact', ['title' => 'Contact']);
    }
}
