@extends('layouts.app')

@section('content')
<h1>{{ $article->title }}</h1>
<small class="small text-muted">{{ $article->updated_at }}</small>
<div class="mt-3 pt-3 border-top"><pre>{{ $article->content }}</pre></div>

<div class="mt-5 text-right">
  @if (Auth::user()->email == $article->created_by)
  <a href="{{ route('article.edit', $article->id) }}" class="btn btn-primary">Edit</a>
  <form action="{{ route('article.destroy', $article->id) }}" method="post">
    @csrf
    @method('delete')
    <button type="submit" class="btn btn-danger ml-2">Delete</button>
  </form>
  @endif

  <a href="{{ route('article.index') }}" class="btn bg-secondary text-white ml-2">List</a>
</div>
@endsection