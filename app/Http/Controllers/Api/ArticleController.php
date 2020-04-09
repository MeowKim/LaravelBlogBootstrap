<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Requests\DeleteArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\Article as ArticleResource;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // Traits
    use FileUploadTrait;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $articles = Article::when(request('keyword'), function ($query, $keyword) {
            return $query->where('title', 'like', '%' . $keyword . '%')->orWhere('content', 'like', '%' . $keyword . '%');
        })
            ->with('creator', 'updater')
            ->orderBy('created_at', 'desc')
            ->paginate(request()->input('per_page'));

        return new ArticleCollection($articles);
    }

    public function store(CreateArticleRequest $request)
    {
        $article = new Article();
        $article->title = $request->title;
        $article->content = $request->content;
        $article->created_by = auth()->user()->id;
        $article->updated_by = auth()->user()->id;


        if ($request->has('image')) {
            $uploaded_file = $request->file('image');
            $stored_file_path = $this->uploadFile($uploaded_file, config('CONST.UPLOAD_PATH_ARTICLES'), config('CONST.DISK'), auth()->user()->name);
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_ARTICLES') . '/' . $article->image;

            if (Storage::exists($current_file_path)) {
                Storage::delete($current_file_path);
            }

            $article->image_name = $uploaded_file->getClientOriginalName();
            $article->image = basename($stored_file_path);
        }

        $article->save();

        return (new ArticleResource($article))->response()->setStatusCode(201);
    }

    public function show()
    {
        return new ArticleReSource(Article::findOrFail(request()->route('article')));
    }

    public function update(UpdateArticleRequest $request)
    {
        $article = Article::findOrFail($request->route('article'));
        $article->title = $request->title;
        $article->content = $request->content;
        $article->updated_by = auth()->user()->id;

        if (request()->has('image')) {
            $uploaded_file = $request->file('image');
            $stored_file_path = $this->uploadFile($uploaded_file, config('CONST.UPLOAD_PATH_ARTICLES'), config('CONST.DISK'), auth()->user()->name);
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_ARTICLES') . '/' . $article->image;

            if (Storage::exists($current_file_path)) {
                Storage::delete($current_file_path);
            }

            $article->image_name = $uploaded_file->getClientOriginalName();
            $article->image = basename($stored_file_path);
        }

        $article->save();

        return (new ArticleResource($article))->response()->setStatusCode(200);
    }

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

        return response()->noContent();
    }
}
