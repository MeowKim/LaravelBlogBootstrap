@extends('layouts.app')

@section('content')
<form action="{{ route('articles.store') }}" method="post">
    @csrf

    <div class="form-group">
        <label for="title">{{ __('ui/articles.title') }}</label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
            placeholder="" value="{{ old('title') }}">
        @error('title')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="content">{{ __('ui/articles.content') }}</label>
        <textarea name="content" id="content" rows="8"
            class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
        @error('content')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="img">{{ __('ui/articles.image') }}</label>
        <input type="file" name="img" id="img" class="form-control @error('img') is-invalid @enderror">
        @error('img')
        <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>

    <div class="mt-5 text-right">
        <button type="submit" class="btn btn-primary">{{ __('ui/generals.submit') }}</button>
        <a href="{{ route('articles.index') }}"
            class="btn bg-secondary text-white ml-2">{{ __('ui/generals.list') }}</a>
    </div>

</form>
@endsection