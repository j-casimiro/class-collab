<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload a Note') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('notes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-4">
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                            value="{{ old('title') }}" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- Subject --}}
                    <div class="mb-4">
                        <x-input-label for="subject" :value="__('Subject')" />
                        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full"
                            value="{{ old('subject') }}" required />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description (optional)')" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- File --}}
                    <div class="mb-6">
                        <x-input-label for="file" :value="__('File (PDF, DOCX, PPTX — max 20 MB)')" />
                        <input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx"
                            class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100"
                            required />
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Upload</x-primary-button>
                        <a href="{{ route('notes.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
