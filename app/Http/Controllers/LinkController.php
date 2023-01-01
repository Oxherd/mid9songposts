<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Poster;
use Illuminate\Database\Eloquent\Builder;

class LinkController extends Controller
{
    public function index()
    {
        request()->validate([
            'sort' => 'in:desc,asc'
        ]);

        if (request('account')) {
            $poster = Poster::firstOrNew(['account' => trim(request('account'))]);
        }

        $links = Link::select('links.*')
            ->join('posts', 'posts.id', '=', 'links.post_id')
            ->when(request('account'), fn (Builder $query) => $query->where('links.poster_id', $poster->id))
            ->when(request('search'), fn (Builder $query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->orderBy('posts.created_at', request('sort', 'desc'))
            ->with(['thread', 'post', 'poster'])
            ->paginate(20);

        return view('links.index', [
            'links' => $links,
        ]);
    }
}
