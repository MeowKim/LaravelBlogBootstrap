@extends('layouts.app')

@section('content')
<form action="{{ route('profile.password.update') }}" method="post">
    @csrf
    @method('put')
  
  <div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
          <div class="card-header">{{ __('ui/generals.profile') }}</div>
          <div class="card-body">

            <div class="form-group">
              <label for="current_password">{{ __('ui/users.current_password') }}</label>
              <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="">
              @error('current_password')
              <p class="invalid-feedback">{{ $message }}</p>
              @enderror
            </div>
            <div class="form-group">
              <label for="new_password">{{ __('ui/users.new_password') }}</label>
              <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="">
              @error('new_password')
              <p class="invalid-feedback">{{ $message }}</p>
              @enderror
            </div>
            <div class="form-group">
              <label for="new_password_confirmation">{{ __('ui/users.confirm_password') }}</label>
              <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" placeholder="">
              @error('new_password_confirmation')
              <p class="invalid-feedback">{{ $message }}</p>
              @enderror
            </div>
            
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
