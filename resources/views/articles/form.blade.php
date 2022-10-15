<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-500 text-white p-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ $action }}">
                @csrf
                @if($article->id)
                    @method("PUT")
                @endif
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">{{ __("Escribe tu artículo") }}</h2>
                    <div class="relative mb-4">
                        <label for="name" class="leading-7 text-sm text-gray-600">{{ __("Título") }}</label>
                        <input type="text" id="title" name="title" value="{{ old("title", $article->title) }}" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    </div>
                    <div class="relative mb-4">
                        <label for="category_id" class="leading-7 text-sm text-gray-600">{{ __("Categoría") }}</label>
                        <select id="category_id" name="category_id" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                            @foreach(\App\Models\Category::get() as $category)
                                <option {{ (int) old("category_id", $article->category_id) === $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative mb-4">
                        <label for="content" class="leading-7 text-sm text-gray-600">{{ __("Contenido") }}</label>
                        <textarea id="content" name="content" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">{{ old("content", $article->content) }}</textarea>
                    </div>
                    <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">{{ $title }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
