<?php

namespace App\Http\Requests;

use App\Models\Article;
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
        $article = Article::findOrFail($this->route('article'));
        $is_api = $this->route()->getPrefix() === 'api';

        return $is_api || $this->user()->can('update', $article);
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
