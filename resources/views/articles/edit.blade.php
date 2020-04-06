@extends('layouts.app')

@section('content')
<form action="{{ route('articles.update', $article->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="form-group">
        <label for="title">{{ __('ui/articles.title') }}</label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
            placeholder="" value="{{ $errors->has('title') ? old('title') : $article->title }}">
        @error('title')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="content">{{ __('ui/articles.content') }}</label>
        <textarea name="content" id="content" rows="8"
            class="form-control @error('content') is-invalid @enderror">{{ $errors->has('content') ? old('content') : $article->content }}</textarea>
        @error('content')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="image">{{ __('ui/articles.image') }}</label>
        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
        @error('image')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    @if (isset($article->image_path))
    <div class="form-group">
        <label>{{ __('ui/articles.current_image') }}</label>
        <img src="{{ $article->image_path }}" alt="" class="d-block">
        <span class="text-muted d-block">{{ $article->image_name }}</span>
    </div>
    @endif

    <div class="mt-3 text-right">
        @if (Auth::user()->id === $article->created_by)
        <button type="submit" class="btn btn-primary ml-2">{{ __('ui/generals.submit') }}</button>
        @endif
        <a href="{{ route('articles.index') }}" class="btn bg-secondary text-white">List</a>
    </div>

</form>
@endsection