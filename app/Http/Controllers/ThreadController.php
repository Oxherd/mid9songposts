<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Carbon\Carbon;
use Exception;

class ThreadController extends Controller
{
    public function index($month = null)
    {
        try {
            $ofMonth = Carbon::createFromFormat('Y-m', $month)->toImmutable();
        } catch (Exception $e) {
            $ofMonth = today()->toImmutable();
        }

        $threads = Thread::withCount('posters')
            ->whereYear('date', $ofMonth->year)
            ->whereMonth('date', $ofMonth->month)
            ->get();

        return view('threads.month', [
            'ofMonth' => $ofMonth,
            'threads' => $threads,
        ]);
    }
}
