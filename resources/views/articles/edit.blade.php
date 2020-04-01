@extends('layouts.app')

@section('content')
<form action="{{ route('articles.update', $article->id) }}" method="post">
    @csrf
    @method('put')
  
    <div class="form-group">
      <label for="title">{{ __('Title') }}</label>
      <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="" aria-describedby="title" value="{{ $errors->has('title') ? old('title') : $article->title }}">
      @error('title')
      <p class="invalid-feedback">{{ $message }}</p>
      @enderror
    </div>
    <div class="form-group">
      <label for="content">{{ __('Content') }}</label>
      <textarea name="content" id="content" rows="8" class="form-control @error('content') is-invalid @enderror">{{ $errors->has('content') ? old('content') : $article->content }}</textarea>
      @error('content')
      <p class="invalid-feedback">{{ $message }}</p>
      @enderror
    </div>
    
    <div class="mt-3 text-right">
      @if (Auth::user()->id == $article->created_by)
      <button type="submit" class="btn btn-primary ml-2">{{ __('Confirm') }}</button>
      @endif
      <a href="{{ route('articles.index') }}" class="btn bg-secondary text-white">List</a>
    </div>
    
</form>
@endsection