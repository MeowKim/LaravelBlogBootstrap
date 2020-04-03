<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateArticleRequest;
use App\Http\Requests\DeleteArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // Traits
    use FileUpload;

    // 인증 체크
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 목록
    public function index()
    {
        $articles = Article::when(request('keyword'), function ($query, $keyword) {
            return $query->where('title', 'like', '%' . $keyword . '%')->orWhere('content', 'like', '%' . $keyword . '%');
        })
            ->with('creator', 'updater')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        $articles->appends(request()->input());

        return view('articles.index', compact('articles'));
    }

    // 작성 폼
    public function create()
    {
        return view('articles.create');
    }

    // 저장
    public function store(CreateArticleRequest $request)
    {
        $user = $request->user();
        $article = new Article();
        $article->title = $request->title;
        $article->content = $request->content;
        $article->created_by = $user->id;
        $article->updated_by = $user->id;

        if (request()->has('image')) {
            $uploaded_file = $request->file('image');
            $stored_file_path = $this->uploadFile($uploaded_file, config('CONST.UPLOAD_PATH_ARTICLES'), config('CONST.DISK'), $user->name);
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_ARTICLES') . '/' . $article->image;

            if (Storage::exists($current_file_path)) {
                Storage::delete($current_file_path);
            }

            $article->image_name = $uploaded_file->getClientOriginalName();
            $article->image = basename($stored_file_path);
        }

        $article->save();

        return redirect()->route('articles.index');
    }

    // 상세
    public function show()
    {
        $article = Article::with('creator', 'updater')
            ->findOrFail(request()->route('article'));

        return view('articles.show', compact('article'));
    }

    // 수정 폼
    public function edit()
    {
        $article = Article::findOrFail(request()->route('article'));

        return view('articles.edit', compact('article'));
    }

    // 업데이트
    public function update(UpdateArticleRequest $request)
    {
        $user = $request->user();
        $article = Article::findOrFail($request->route('article'));
        $article->title = $request->title;
        $article->content = $request->content;
        $article->updated_by = $user->id;

        if (request()->has('image')) {
            $uploaded_file = $request->file('image');
            $stored_file_path = $this->uploadFile($uploaded_file, config('CONST.UPLOAD_PATH_ARTICLES'), config('CONST.DISK'), $user->name);
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_ARTICLES') . '/' . $article->image;

            if (Storage::exists($current_file_path)) {
                Storage::delete($current_file_path);
            }

            $article->image_name = $uploaded_file->getClientOriginalName();
            $article->image = basename($stored_file_path);
        }

        $article->save();

        return redirect()->route('articles.show', $article->id);
    }

    // 삭제
    public function destroy(DeleteArticleRequest $request)
    {
        $article = Article::findOrFail($request->route('article'));

        if ($article->image) {
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_ARTICLES') . '/' . $article->image;

            if (Storage::exists($current_file_path)) {
                Storage::delete($current_file_path);
            }
        }

        $article->delete();

        return redirect()->route('articles.index');
    }
}
