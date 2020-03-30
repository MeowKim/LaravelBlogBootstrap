<?php

namespace App\Http\Controllers;

use App\Article;

class ArticleController extends Controller
{
    private $validate_cond = [
        'title' => 'bail|required|max:255',
        'content' => 'required'
    ];

    // 세션 체크
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 목록
    public function index() {
        $articles = Article::when(request('keyword'), function($query, $keyword){
                        return $query->where('title', 'like', '%'.$keyword.'%')->orWhere('content', 'like', '%'.$keyword.'%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);

        return view('article.index', compact('articles'));
    }

    // 작성 폼
    public function create() {
        return view('article.create');
    }

    // 저장
    public function store() {
        request()->validate($this->validate_cond);

        $article = new Article();
        $article->title = request('title');
        $article->content = request('content');
        $article->save();

        return redirect()->route('article.index');
    }

    // 상세
    public function show($id) {
        $article = Article::find($id);
        return view('article.show', compact('article'));
    }

    // 수정 폼
    public function edit($id) {
        $article = Article::find($id);
        return view('article.edit', compact('article'));
    }

    // 업데이트
    public function update($id) {
        request()->validate($this->validate_cond);

        $article = Article::find($id);
        $article->title = request('title');
        $article->content = request('content');
        $article->save();

        return redirect()->route('article.show', $article->id);
    }

    // 삭제
    public function destroy($id) {
        $article = Article::find($id);
        $article->delete();

        return redirect()->route('article.index');
    }
}
