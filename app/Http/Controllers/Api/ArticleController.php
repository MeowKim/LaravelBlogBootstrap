<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Resources\Article as ArticleResource;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // Traits
    use FileUploadTrait;

    public function index()
    {
        $articles = Article::when(request('keyword'), function ($query, $keyword) {
            return $query->where('title', 'like', '%' . $keyword . '%')->orWhere('content', 'like', '%' . $keyword . '%');
        })
            ->with('creator', 'updater')
            ->orderBy('created_at', 'desc')
            ->paginate(request()->input('pagination'));

        return new ArticleCollection($articles);
    }

    public function store(CreateArticleRequest $request)
    {
        $user = $request->user();
        $article = new Article();
        $article->title = $request->title;
        $article->content = $request->content;
        $article->created_by = $request->created_by;
        $article->updated_by = $request->updated_by;

        if ($request->has('image')) {
            // print_r($request->file('image'));
            // die();
            $uploaded_file = $request->file('image');
            $stored_file_path = $this->uploadFile($uploaded_file, config('CONST.UPLOAD_PATH_ARTICLES'), config('CONST.DISK'), $user->name ?? 'guest');
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_ARTICLES') . '/' . $article->image;

            if (Storage::exists($current_file_path)) {
                Storage::delete($current_file_path);
            }

            $article->image_name = $uploaded_file->getClientOriginalName();
            $article->image = basename($stored_file_path);
        }

        $article->save();

        return response()->json(null, 201);
    }

    public function show()
    {
        return new ArticleReSource(Article::find(request()->route('article')));
    }
}
