<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $note->title }}
            </h2>
            <a href="{{ route('notes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to
                Notes</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-1 rounded mb-3">
                    {{ $note->subject }}
                </span>

                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $note->title }}</h3>
                <p class="text-sm text-gray-400 mb-4">
                    Uploaded by <span class="font-medium text-gray-600">{{ $note->user->name }}</span>
                    &middot; {{ $note->created_at->format('M d, Y') }}
                </p>

                @if ($note->description)
                    <p class="text-gray-700 mb-6">{{ $note->description }}</p>
                @endif

                <div class="prose max-w-none mb-6 whitespace-pre-wrap text-gray-800 leading-relaxed">
                    {{ $note->content }}</div>

                <div class="flex flex-wrap items-center gap-3 border-t pt-4">
                    @can('update', $note)
                        <a href="{{ route('notes.edit', $note) }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-md hover:bg-yellow-600 transition">
                            Edit
                        </a>
                    @endcan

                    @can('delete-any-note')
                        <form action="{{ route('notes.destroy', $note) }}" method="POST"
                            onsubmit="return confirm('Delete this note?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700 transition">
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
