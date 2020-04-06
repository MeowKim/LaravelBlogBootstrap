@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <form action="" method="get">
            <input type="text" class="form-control col-md-6 d-inline mt-1 mr-1" placeholder="Search.." name="keyword"
                id="keyword" value="{{ request('keyword') }}">
            <button type="submit" class="btn btn-info text-white mt-1 mr-1">{{ __('ui/generals.search') }}</button>
        </form>
        <a href="{{ route('articles.index') }}"
            class="btn bg-secondary text-white mt-1">{{ __('ui/generals.reset') }}</a>
    </div>

    <div class="col-md-4 text-right">
        @can('create', \App\Models\Article::class)
        <a href="{{ route('articles.create') }}"
            class="btn btn-primary text-white mt-1">{{ __('ui/generals.write') }}</a>
        @endcan
    </div>
</div>

@forelse ($articles as $article)
<div class="card mt-3">
    <div class="row no-gutters">
        <div class="col-md-3">
            <img src="{{ $article->image_path ?? config('CONST.NO_IMAGE_ARTICLES') }}" alt="" class="card-img">
        </div>
        <div class="col-md-9">
            <div class="card-body">
                <p class="card-text">
                    <small class="text-muted">{{ $article->creator->name }} {{ $article->created_at }}</small>
                    <span class="float-right">
                        {{ __('ui/articles.last_update') }} <small class="text-muted">{{ $article->updater->name }}
                            {{ $article->updated_at }}</small>
                    </span>
                </p>
                <h5 class="card-title">
                    <a href="{{ route('articles.show', $article->id) }}">{{ $article->title }}</a>
                </h5>
                <p class="card-text">{{ $article->content }}</p>
            </div>
        </div>
    </div>
</div>
@empty
<p>{{ __('ui/articles.empty') }}</p>
@endforelse

<div class="text-center mt-3">
    {{ $articles->links() }}
</div>
@endsection