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
            $ofMonth = (new Carbon($month))->toImmutable();
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

    public function show(Thread $thread)
    {
        $thread->load([
            'posts' => function ($query) {
                $query->orderBy('created_at')
                    ->with([
                        'poster',
                        'links',
                        'comments' => function ($subQuery) {
                            $subQuery->orderBy('created_at')->with('poster');
                        }
                    ]);
            }
        ]);

        return view('threads.show', [
            'thread' => $thread,
        ]);
    }
}
