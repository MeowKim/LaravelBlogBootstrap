<?php

namespace App\Http\Controllers;

use App\Models\Article;

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
                            ->leftJoin('users as created_user', 'articles.created_by', '=', 'created_user.id')
                            ->leftJoin('users as updated_user', 'articles.updated_by', '=', 'updated_user.id')
                            ->select('articles.*', 'created_user.name as created_by_name', 'updated_user.name as updated_by_name')
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
        // 밸리데이션 체크
        request()->validate($this->validate_cond);

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
        $article = Article::leftJoin('users as created_user', 'articles.created_by', '=', 'created_user.id')
                            ->leftJoin('users as updated_user', 'articles.updated_by', '=', 'updated_user.id')
                            ->select('articles.*', 'created_user.name as created_by_name', 'updated_user.name as updated_by_name')
                            ->find($id);

        // 존재여부 체크
        if ($article == NULL)
            return abort('404');

        return view('article.show', compact('article'));
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

        return view('article.edit', compact('article'));
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
        request()->validate($this->validate_cond);

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
