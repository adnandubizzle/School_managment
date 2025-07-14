<x-filament::card>
    <label for="school-switch" class="text-sm font-medium text-gray-700 mb-1 block">Switch School</label>
    <select
        id="school-switch"
        class="fi-select block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
        onchange="if(this.value) window.location.href='/switch-school/' + this.value;"
    >
        @foreach(auth()->user()->schools as $school)
            <option
                value="{{ $school->id }}"
                @if(session('school_id') == $school->id) selected @endif
            >
                {{ $school->name }}
            </option>
        @endforeach
    </select>
</x-filament::card>
