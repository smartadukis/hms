<x-layout>
    <div class="container">
        <h2 class="text-info mb-4">Edit Patient</h2>

        <form method="POST" action="{{ route('patients.update', $patient) }}">
            @csrf
            @method('PUT')

            @include('patients.form', ['patient' => $patient])

            <button class="btn btn-info">Update Patient</button>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-layout>
