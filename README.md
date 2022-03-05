<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Curso Básico Laravel 9

## Trabajo con Laravel Sail
https://www.cursosdesarrolloweb.es/blog/laravel-sail

## Crear proyecto
```shell
curl -s https://laravel.build/curso-basico-laravel9 | bash
cd curso-basico-laravel9
sail up
```

## Modelo de datos
```shell
sail artisan make:model Category -m
```
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("name", 40)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
```
```shell
sail artisan make:model Article -m
```
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->foreignIdFor(\App\Models\Category::class)->constrained();
            $table->string("title", 80)->unique();
            $table->text("content");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
```

## Modelo Category
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
    ];

    public function articles(): HasMany {
        return $this->hasMany(Article::class);
    }
}
```
## Modelo Article
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "category_id", "title", "content",
    ];

    protected $perPage = 5;

    protected static function boot() {
        parent::boot();
        self::creating(function (Article $article) {
            $article->user_id = auth()->id();
        });
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function getCreatedAtFormattedAttribute(): string {
        return \Carbon\Carbon::parse($this->created_at)->format('d-m-Y H:i');
    }

    public function getExcerptAttribute(): string {
        return Str::excerpt($this->content);
    }
}
```
## Modelo User
```php
public function articles(): HasMany {
    return $this->hasMany(Article::class);
}
```

## Ejecutar migraciones
```shell
sail artisan migrate
```

## Seeder Category
```shell
sail artisan make:seed CategorySeeder
```
```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::insert([
           ["name" => "Php",],
           ["name" => "Laravel",],
           ["name" => "Vue",],
           ["name" => "Docker",],
        ]);
    }
}
```

## Article Factory
```shell
sail artisan make:factory ArticleFactory
```
```php
<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->text(30),
            "content" => $this->faker->text,
            "user_id" => User::all()->random(1)->first()->id,
            "category_id" => Category::all()->random(1)->first()->id,
            "created_at" => now(),
        ];
    }
}
```
## Actualizar DatabaseSeeder
```php
<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            "name" => "Cursosdesarrolloweb",
            "email" => "laravel9@cursosdesarrolloweb.es",
        ]);
        $this->call(CategorySeeder::class);
        Article::factory(20)->create();
    }
}
```

## Ejecutar seeder
```shell
sail artisan db:seed
```

## Añadir Breeze
```shell
sail composer require laravel/breeze --dev
sail artisan breeze:install
sail artisan route:list
sail yarn && sail yarn watch
```

## ArticleController
```shell
artisan make:controller ArticleController -r --model=Article
```

## Actualizar rutas web
```php
Route::resource("articles", \App\Http\Controllers\ArticleController::class)
    ->middleware("auth");
```

## Listar artículos
```php
/**
 * Display a listing of the resource.
 *
 */
public function index(): Renderable {
    $articles = Article::with("category")->latest()->paginate();
    return view("articles.index", compact("articles"));
}
```
```html
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
```

## Crear artículos
```php
/**
 * Show the form for creating a new resource.
 *
 */
public function create(): Renderable {
    $article = new Article;
    $title = __("Crear artículo");
    $action = route("articles.store");
    return view("articles.form", compact("article", "title", "action"));
}
```
```html
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
                        <label for="category_id" class="leading-7 text-sm text-gray-600">{{ __("Título") }}</label>
                        <select id="category_id" name="category_id" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                            @foreach(\App\Models\Category::get() as $category)
                                <option {{ (int) old("category_id", $article->category_id) === $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative mb-4">
                        <label for="content" class="leading-7 text-sm text-gray-600">{{ __("Artículo") }}</label>
                        <textarea id="content" name="content" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">{{ old("content", $article->content) }}</textarea>
                    </div>
                    <button type="submit" class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">{{ $title }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
```

## Validar formulario
```shell
sail artisan make:request ArticleRequest
```
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return match ($this->method()) {
            "POST" => [
                "title" => "required|min:2|max:40|unique:articles",
                "content" => "required|min:10",
                "category_id" => "required|exists:categories,id",
            ],
            "PUT" => [
                "title" => "required|min:2|max:40|unique:articles,title," . $this->route("article")->id,
                "content" => "required|min:10",
                "category_id" => "required|exists:categories,id",
            ],
        };
    }
}
```
## Procesar nuevos artículos
```php
/**
 * Store a newly created resource in storage.
 *
 * @param ArticleRequest $request
 * @return RedirectResponse
 */
public function store(ArticleRequest $request) {
    $validated = $request->safe()->only(['title', 'content', 'category_id']);
    Article::create($validated);

    session()->flash("success", __("El artículo ha sido creado correctamente"));
    return redirect(route("articles.index"));
}
```

## Añadir flash en app.blade.php
```html
@if (session()->has("success"))
    <div class="bg-green-500 text-white p-4">
        <ul>
            <li>{{ session("success") }}</li>
        </ul>
    </div>
@endif
```

## Editar artículos
```php
/**
 * Show the form for editing the specified resource.
 *
 * @param Article $article
 * @return Renderable
 */
public function edit(Article $article): Renderable {
    $title = __("Actualizar artículo");
    $action = route("articles.update", ["article" => $article]);
    return view("articles.form", compact("article", "title", "action"));
}
```

## Procesar edición artículos
```php
/**
 * Update the specified resource in storage.
 *
 * @param  ArticleRequest $request
 * @param Article $article
 * @return RedirectResponse
 */
public function update(ArticleRequest $request, Article $article) {
    $validated = $request->safe()->only(['title', 'content', 'category_id']);
    $article->update($validated);

    session()->flash("success", __("El artículo ha sido actualizado correctamente"));
    return redirect(route("articles.index"));
}
```

## Detalle artículo
```php
/**
 * Display the specified resource.
 *
 * @param Article $article
 * @return Renderable
 */
public function show(Article $article): Renderable {
    $article->load("user:id,name", "category:id,name");
    return view("articles.show", compact("article"));
}
```
```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle artículo') }}
        </h2>
    </x-slot>

    <div class="container px-5 py-24 mx-auto flex flex-col">
        <div class="lg:w-4/6 mx-auto">
            <div class="rounded-lg overflow-hidden">
                <h1 class="text-3xl">{{ $article->title }}</h1>
            </div>
            <div class="flex flex-col sm:flex-row mt-10">
                <div class="sm:w-1/3 text-center sm:pr-8 sm:py-8">
                    <div class="w-20 h-20 rounded-full inline-flex items-center justify-center bg-gray-200 text-gray-400">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-10 h-10" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center text-center justify-center">
                        <h2 class="font-medium title-font mt-4 text-gray-900 text-lg">{{ $article->user->name }}</h2>
                        <div class="w-12 h-1 bg-indigo-500 rounded mt-2 mb-4"></div>
                    </div>
                </div>
                <div class="sm:w-2/3 sm:pl-8 sm:py-8 sm:border-l border-gray-200 sm:border-t-0 border-t mt-4 pt-4 sm:mt-0 text-center sm:text-left">
                    <span class="font-semibold title-font text-gray-400 underline">{{ $article->category->name }}</span>
                    <p class="leading-relaxed text-lg mb-4">{{ $article->content }}</p>
                    <a href="{{ route("articles.index") }}" class="text-indigo-500 inline-flex items-center">{{ __("Volver") }}
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
</x-app-layout>
```

## Eliminar artículos
```php
/**
 * Remove the specified resource from storage.
 *
 * @param Article $article
 * @return RedirectResponse
 */
public function destroy(Article $article) {
    $article->delete();
    session()->flash("success", __("El artículo ha sido eliminado correctamente"));
    return redirect(route("articles.index"));
}
```
