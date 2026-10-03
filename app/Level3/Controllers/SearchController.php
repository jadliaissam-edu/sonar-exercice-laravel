<?php

namespace App\Level3\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController
{
    public function search(Request $request)
    {
        $term = $request->query('q', '');

        $rows = DB::select("SELECT id, title, status FROM tickets WHERE title LIKE '%" . $term . "%' ORDER BY id");

        return response()->json(['data' => $rows]);
    }

    public function byStatus(Request $request)
    {
        $status = $request->query('status', 'open');
        $rows = DB::select('SELECT id, title FROM tickets WHERE status = ?', [$status]);

        return response()->json(['data' => $rows]);
    }

    public function preview(Request $request)
    {
        $term = $request->query('q', '');

        return response('<h1>Results for ' . $term . '</h1>');
    }

    public function redirect(Request $request)
    {
        return redirect($request->query('url', '/'));
    }
}
