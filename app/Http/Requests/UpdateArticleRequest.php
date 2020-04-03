<?php

namespace App\Http\Requests;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = User::find($this->user()->id);
        $article = Article::find($this->route('article'));

        return $article && ($article->created_by === $user->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'bail|required|max:255',
            'content' => 'required',
            'image' => 'bail|mimes:jpeg,jpg,png,gif|max:10240',
        ];
    }
}
