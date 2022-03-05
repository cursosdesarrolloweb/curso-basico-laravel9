<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Artículos') }}
        </h2>
    </x-slot>

    <section class="text-gray-600 body-font overflow-hidden">
        <div class="container px-5 py-24 mx-auto">
            <div class="mb-16 -my-8">
                <a href="{{ route("articles.create") }}" class="flex w-64 text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                    {{ __("Crear un nuevo artículo") }}
                </a>
            </div>

            <div class="-my-8 divide-y-2 divide-gray-100">
                @foreach($articles as $article)
                    <div class="py-8 flex flex-wrap md:flex-nowrap">
                        <div class="md:w-64 md:mb-0 mb-6 flex-shrink-0 flex flex-col">
                            <span class="font-semibold title-font text-gray-700">{{ $article->category->name }}</span>
                            <span class="mt-1 text-gray-500 text-sm">{{ $article->created_at_formatted }}</span>
                        </div>
                        <div class="md:flex-grow">
                            <h2 class="text-2xl font-medium text-gray-900 title-font mb-2">{{ $article->title }}</h2>
                            <p class="leading-relaxed">{{ $article->excerpt }}</p>
                            <a href="{{ route("articles.show", ["article" => $article]) }}" class="text-indigo-500 inline-flex items-center mt-4">{{ __("Ver detalle") }}
                                <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5l7 7-7 7"></path>
                                </svg>
                            </a> |
                            <a href="{{ route("articles.edit", ["article" => $article]) }}" class="text-indigo-500 inline-flex items-center mt-4">{{ __("Editar") }}
                                <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5l7 7-7 7"></path>
                                </svg>
                            </a> |
                            <form class="inline" method="POST" action="{{ route("articles.destroy", ["article" => $article]) }}">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="text-red-500 inline-flex items-center mt-4">{{ __("Eliminar") }}
                                    <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                {{ $articles->links() }}
            </div>
        </div>
    </section>
</x-app-layout>
