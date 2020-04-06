<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::when(request('keyword'), function ($query, $keyword) {
            return $query->where('title', 'like', '%' . $keyword . '%')->orWhere('content', 'like', '%' . $keyword . '%');
        })
            ->with('creator', 'updater')
            ->orderBy('created_at', 'desc')
            ->paginate(request()->input('pagination', 5));
        $articles->appends(request()->input());

        return response()->json($articles, 200);
    }

    public function show()
    {
        $article = Article::with('creator', 'updater')
            ->findOrFail(request()->route('article'));

        return response()->json($article, 200);
    }
}
