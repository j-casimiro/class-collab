<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Welcome back, {{ Auth::user()->name }}!</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        You are logged in as
                        <span
                            class="inline-block px-2 py-0.5 rounded text-xs font-semibold
                            {{ Auth::user()->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-indigo-100 text-indigo-700' }}">
                            {{ Auth::user()->role }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('notes.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    New Note
                </a>
            </div>

            {{-- My recent notes --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">My Recent Notes</h3>
                    <a href="{{ route('notes.index') }}" class="text-sm text-indigo-600 hover:underline">View all notes
                        &rarr;</a>
                </div>

                @php
                    $myNotes = Auth::user()->notes()->latest()->take(5)->get();
                @endphp

                @if ($myNotes->isEmpty())
                    <p class="text-sm text-gray-500">You haven't added any notes yet.
                        <a href="{{ route('notes.create') }}" class="text-indigo-600 hover:underline">Add your first
                            note</a>.
                    </p>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($myNotes as $note)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('notes.show', $note) }}"
                                        class="text-sm font-medium text-gray-800 hover:text-indigo-600">
                                        {{ $note->title }}
                                    </a>
                                    <span class="ml-2 text-xs text-gray-400">{{ $note->subject }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs">
                                    <a href="{{ route('notes.edit', $note) }}"
                                        class="text-yellow-600 hover:underline">Edit</a>
                                    <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                        onsubmit="return confirm('Delete this note?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
