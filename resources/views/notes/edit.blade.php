<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Note') }}
            </h2>
            <a href="{{ route('notes.show', $note) }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('notes.update', $note) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div class="mb-4">
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                            value="{{ old('title', $note->title) }}" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- Subject --}}
                    <div class="mb-4">
                        <x-input-label for="subject" :value="__('Subject')" />
                        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full"
                            value="{{ old('subject', $note->subject) }}" required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description (optional)')" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $note->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- File replacement --}}
                    <div class="mb-6">
                        <x-input-label for="file" :value="__('Replace file (optional — PDF, DOCX, PPTX — max 20 MB)')" />
                        <p class="text-xs text-gray-500 mb-1">Current file: {{ $note->file_name }}</p>
                        <input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx"
                            class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100" />
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('notes.show', $note) }}"
                            class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
