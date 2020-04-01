@extends('layouts.app')

@section('content')
<form action="{{ route('articles.store') }}" method="post">
  @csrf

  <div class="form-group">
    <label for="title">{{ __('Title') }}</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="" aria-describedby="title" value="{{ old('title') }}">
    @error('title')
    <p class="invalid-feedback">{{ $message }}</p>
    @enderror
  </div>
  <div class="form-group">
    <label for="content">{{ __('Content') }}</label>
    <textarea name="content" id="content" rows="8" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
    @error('content')
    <p class="invalid-feedback">{{ $message }}</p>
    @enderror
  </div>
  
  <div class="mt-5 text-right">
    <button type="submit" class="btn btn-primary">{{ __('Confirm') }}</button>
    <a href="{{ route('articles.index') }}" class="btn bg-secondary text-white ml-2">{{ __('List') }}</a>    
  </div>
  
</form>
@endsection