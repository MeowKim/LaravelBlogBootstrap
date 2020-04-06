@extends('layouts.app')

@section('content')
<div class="profile row">
    <div class="left-container col-md-3">
        {{-- <h4>&nbsp;</h4> --}}
        <div class="row mt-1">
            <div class="col-lg-12 col-lg-3 text-center">
                <img src="{{ $user->image_path ?? config('CONST.NO_IMAGE_PROFILE') }}" alt="">
            </div>
            <div class="col-lg-12 col-lg-9 mt-5">
                <h2>{{ $user->name }}</h2>
                <h4 class="text-muted">{{ $user->email }}</h4>
            </div>
        </div>
        <div class="row mt-5 mb-5">
            <div class="col-lg-12 text-center">
                <a href="{{ route('profile.edit') }}" class="btn btn-block btn-primary">{{ __('ui/generals.edit') }}</a>
                <a href="{{ route('profile.password.change') }}" class="btn btn-link">
                    {{ __('ui/users.change_password') }}
                </a>
            </div>
        </div>
    </div>
    <div class="right-container col-md-9">
        <h4>{{ __('ui/generals.articles') }}</h4>

        @forelse ($user->articles as $article)
        <div class="card mt-3">
            <div class="row no-gutters">
                <div class="col-md-3">
                    <img src="{{ $article->image_path ?? config('CONST.NO_IMAGE_ARTICLES') }}" alt="" class="card-img">
                </div>
                <div class="col-md-9">
                    <div class="card-body">
                        <p class="card-text">
                            <small class="text-muted">{{ $article->created_at }}</small>
                            <span class="float-right">
                                {{ __('ui/articles.last_update') }} <small
                                    class="text-muted">{{ $article->updated_at }}</small>
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
            {{ $user->articles->links() }}
        </div>
    </div>
</div>
@endsection