@extends('layouts.app')

@section('content')
<form action="{{ route('profile.update') }}" method="post">
    @csrf
    @method('put')
  
  <div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
          <div class="card-header">{{ __('Profile') }}</div>
          <div class="card-body">
            <div class="form-group">
              <label for="name">name</label>
              <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="" aria-describedby="name" value="{{ $errors->has('name') ? old('name') : $user->name }}">
              @error('name')
              <p class="invalid-feedback">{{ $message }}</p>
              @enderror
            </div>
            <div class="form-group">
              <label for="email">Content</label>
              <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="" aria-describedby="email" value="{{ $errors->has('email') ? old('email') : $user->email }}">
              @error('email')
              <p class="invalid-feedback">{{ $message }}</p>
              @enderror
            </div>
            
            <div class="mt-3 text-right">     
              <button type="submit" class="btn btn-primary ml-2">Update</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</form>
@endsection