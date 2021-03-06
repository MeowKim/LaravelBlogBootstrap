@extends('layouts.app')

@section('content')
<div class="article">
    <h1>{{ $article->title }}</h1>
    <div>
        {{ __('ui/articles.created') }} : <small class="small text-muted">{{ $article->creator->name }}
            {{ $article->created_at }}</small>
    </div>
    <div>
        {{ __('ui/articles.updated') }} : <small class="small text-muted">{{ $article->updater->name }}
            {{ $article->updated_at }}</small>
    </div>
    <div class="row mt-3 pt-3 pb-3 border-top border-bottom">
        @if ($article->image_path)
        <div class="{{ $article->image_type==='vertical' ? 'col-md-3' : 'col-md-12' }}">
            <img src="{{ $article->image_path ?? config('CONST.NO_IMAGE_ARTICLES') }}" alt="">
        </div>
        @endif
        <div class="{{ $article->image_type==='vertical' ? 'col-md-9' : 'col-md-12' }}">
            <pre>{{ $article->content }}</pre>
        </div>
    </div>
</div>

<div class="mt-3 text-right">
    @can('update', $article)
    <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-primary">{{ __('ui/generals.edit') }}</a>
    @endcan
    @can('delete', $article)
    <form action="{{ route('articles.destroy', $article->id) }}" method="post">
        @csrf
        @method('delete')

        <button type="submit" class="btn btn-danger ml-2" onclick="event.preventDefault(); Swal.fire({
                icon: 'question', 
                title: null, 
                text: '{{ __('msg/articles.confirm_delete') }}', 
                showCancelButton: true,
            })
            .then((result) => { 
                if(result.value === true) {
                    $(this).parent('form').submit();;
                }
            })">{{ __('ui/generals.delete') }}</button>
    </form>
    @endcan
    <a href="{{ route('articles.index') }}" class="btn bg-secondary text-white ml-2">{{ __('ui/generals.list') }}</a>
</div>
@endsection