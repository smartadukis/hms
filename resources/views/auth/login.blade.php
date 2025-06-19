<x-layout>
  <div class="auth-container">
    <div class="auth-card">
      <h3 class="mb-4 text-center text-info">Login to HMS</h3>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input class="form-control" name="phone" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input class="form-control" type="password" name="password">
        </div>

        <button class="btn btn-info w-100 mb-2">Login</button>
      </form>

      <p class="text-center">
        Not registered?
        <a href="{{ route('register') }}" class="text-primary">Create an account</a>
      </p>
    </div>
  </div>
</x-layout>
