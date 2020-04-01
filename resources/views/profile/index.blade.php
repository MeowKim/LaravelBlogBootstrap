@extends('layouts.app')

@section('content')
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="profile row">
  <div class="left-container col-md-3">
    <img src="https://via.placeholder.com/260x260.png?text=x" alt="">
    <div class="mt-5">
      <h2>{{ $user->name }}</h2>
      <h4><span class="text-muted">{{ $user->email }}</span></h4>      
    </div>
    <div class="mt-5 text-center">
        <a href="{{ route('profile.edit') }}" class="btn btn-block btn-primary mt-3">{{ __('ui/generals.edit') }}</a>
        <a class="btn btn-link" href="{{ route('profile.password.change') }}">
            {{ __('ui/users.change_password') }}
        </a>
    </div>
  </div>
  <div class="right-container col-md-9">
    <h4>{{ __('ui/generals.articles') }}</h4>
    
    @forelse ($user->articles as $article)
    <div class="card mt-3">
        <div class="row no-gutters">
            <div class="col-md-3">
                <img src="https://via.placeholder.com/300x300.png?text=No Image" alt="No image" class="card-img">
            </div>
            <div class="col-md-9">
                <div class="card-body">
                    <p class="card-text">
                        <small class="text-muted">{{ $article->created_at }}</small>
                        <span class="float-right">
                            {{ __('ui/articles.last_update') }} <small class="text-muted">{{ $article->updated_at }}</small>
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
