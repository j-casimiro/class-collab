<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Class Notes') }}
            </h2>
            <a href="{{ route('notes.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                Upload Note
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($notes->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    No notes yet. Be the first to upload one!
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($notes as $note)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between mb-2">
                                    <span
                                        class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded">
                                        {{ $note->subject }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mt-2">
                                    <a href="{{ route('notes.show', $note) }}" class="hover:text-indigo-600">
                                        {{ $note->title }}
                                    </a>
                                </h3>
                                @if ($note->description)
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $note->description }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-2">by {{ $note->user->name }}</p>
                            </div>

                            <div class="flex items-center gap-2 mt-4 flex-wrap">
                                <a href="{{ route('notes.show', $note) }}"
                                    class="text-xs text-indigo-600 hover:underline">View</a>

                                @can('update', $note)
                                    <a href="{{ route('notes.edit', $note) }}"
                                        class="text-xs text-yellow-600 hover:underline">Edit</a>
                                @endcan

                                @can('delete', $note)
                                    <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                        onsubmit="return confirm('Delete this note?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $notes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
