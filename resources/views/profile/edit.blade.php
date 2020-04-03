@extends('layouts.app')

@section('content')
<form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('ui/generals.profile') }}</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('ui/users.name') }}</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" placeholder=""
                            value="{{ $errors->has('name') ? old('name') : $user->name }}">
                        @error('name')
                        <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">{{ __('ui/users.email') }}</label>
                        <input type="text" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" placeholder=""
                            value="{{ $errors->has('email') ? old('email') : $user->email }}">
                        @error('email')
                        <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="image">{{ __('ui/users.profile_image') }}</label>
                        <input type="file" name="image" id="image"
                            class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                        <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                    @if (isset($user->image_path))
                    <div class="form-group">
                        <label>{{ __('ui/users.current_profile_image') }}</label>
                        <img src="{{ $user->image_path }}" alt="" class="d-block">
                        <span class="text-muted d-block">{{ $user->image_name }}</span>
                    </div>
                    @endif

                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary ml-2">{{ __('ui/generals.submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

</form>
@endsection