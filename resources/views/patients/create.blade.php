<x-layout>
    <div class="container">
        <h2 class="text-info mb-4">Register New Patient</h2>

        <form method="POST" action="{{ route('patients.store') }}">
            @csrf

            @include('patients.form', ['patient' => null])
            
            <button class="btn btn-success">Save Patient</button>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-layout>
