@extends('layouts.app')

@section('content')
<div class="text-right">
    <form action="" method="get">
        <input type="text" class="form-control col-md-4 float-left" placeholder="Search.." name="keyword" id="keyword" value="{{ request('keyword') }}"> 
        <button type="submit" class="btn btn-info text-white float-left ml-1">{{ __('Search') }}</button>
    </form>
    <a href="{{ route('articles.index') }}" class="btn bg-secondary text-white float-left ml-1">{{ __('Reset') }}</a>
    <a href="{{ route('articles.create') }}" class="btn btn-primary text-white">{{ __('Write') }}</a>
</div>

@forelse ($articles as $article)
<div class="card mt-3">
    <div class="row no-gutters">
        <div class="col-md-3">
            <img src="https://via.placeholder.com/300x300.png?text=No Image" alt="No image" class="card-img">
        </div>
        <div class="col-md-9">
            <div class="card-body">
                <p class="card-text">
                    <small class="text-muted">{{ $article->creator->name }} {{ $article->created_at }}</small>
                    <span class="float-right">
                        {{ __('Last Update') }} <small class="text-muted">{{ $article->updater->name }} {{ $article->updated_at }}</small>
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
<p>@lang('articles.empty')</p>
@endforelse

<div class="text-center mt-3">
    {{ $articles->links() }}
</div>
@endsection