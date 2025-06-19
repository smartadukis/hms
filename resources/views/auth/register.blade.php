<x-layout>
  <div class="auth-container">
    <div class="auth-card">
      <h3 class="mb-4 text-center text-info">Create Account</h3>

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <form method="POST" action="{{ url('/register') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Name</label>
          <input class="form-control" name="name" value="{{ old('name') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Phone <span class="text-danger">*</span></label>
          <input class="form-control" name="phone" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Email <small class="text-muted">(optional)</small></label>
          <input class="form-control" type="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Address <small class="text-muted">(optional)</small></label>
          <input class="form-control" name="address" value="{{ old('address') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input class="form-control" type="password" name="password">
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input class="form-control" type="password" name="password_confirmation">
        </div>

        <button class="btn btn-info w-100 mb-2">Register</button>
      </form>

      <p class="text-center">
        Already registered?
        <a href="{{ route('login') }}" class="text-primary">Login here</a>
      </p>
    </div>
  </div>
</x-layout>
