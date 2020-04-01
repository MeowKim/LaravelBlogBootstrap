<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    // 밸리데이션 조건
    private $validation_rules_article = [
        'title' => 'bail|required|max:255',
        'content' => 'required',
    ];

    // 인증 체크
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 목록
    public function index() {
        $articles = Article::when(request('keyword'), function($query, $keyword){
                                return $query->where('title', 'like', '%'.$keyword.'%')->orWhere('content', 'like', '%'.$keyword.'%');
                            })
                            ->with('creator', 'updater')
                            ->orderBy('created_at', 'desc')
                            ->paginate(5);

        return view('articles.index', compact('articles'));
    }

    // 작성 폼
    public function create() {
        return view('articles.create');
    }

    // 저장
    public function store() {
        // 밸리데이션 체크
        request()->validate($this->validation_rules_article);

        $article = new Article();
        $article->title = request('title');
        $article->content = request('content');
        $article->created_by = auth()->user()->id;
        $article->updated_by = auth()->user()->id;
        $article->save();

        return redirect()->route('articles.index');
    }

    // 상세
    public function show($id) {
        $article = Article::with('creator', 'updater')
                            ->find($id);

        // 존재여부 체크
        if ($article == NULL)
            return abort('404');

        return view('articles.show', compact('article'));
    }

    // 수정 폼
    public function edit($id) {
        $article = Article::find($id);

        // 존재여부 체크
        if ($article == NULL)
            return abort('404');

        // 오너 체크
        if (auth()->user()->id != $article->created_by)
            return abort('403');

        return view('articles.edit', compact('article'));
    }

    // 업데이트
    public function update($id) {
        $article = Article::find($id);

        // 존재여부 체크
        if ($article == NULL)
            return abort('404');

        // 오너 체크
        if (auth()->user()->id != $article->created_by)
            return abort('403');

        // 밸리데이션 체크
        request()->validate($this->validation_rules_article);

        $article->title = request('title');
        $article->content = request('content');
        $article->updated_by = auth()->user()->id;
        $article->save();

        return redirect()->route('articles.show', $article->id);
    }

    // 삭제
    public function destroy($id) {
        $article = Article::find($id);

        // 존재여부 체크
        if ($article == NULL)
            return abort('404');
        
        // 오너 체크
        if (auth()->user()->id != $article->created_by)
            return abort('403');
        
        $article->delete();

        return redirect()->route('articles.index');
    }
}
