<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;

final class ArticleController extends Controller
{
    final public function index(): Renderable
    {
        $articles = Article::with(relations: "category")->latest()->paginate();
        return view(view: "articles.index", data: compact(var_name: "articles"));
    }

    final public function create(): Renderable
    {
        $article = new Article;
        $title = __(key: "Crear artículo");
        $action = route(name: "articles.store");
        return view(view: "articles.form", data: compact("article", "title", "action"));
    }

    final public function store(ArticleRequest $request): RedirectResponse
    {
        $validated = $request->safe()->only(keys: ['title', 'content', 'category_id']);
        $validated['user_id'] = auth()->id();
        Article::create(attributes: $validated);

        session()->flash(key: "success", value: __(key: "El artículo ha sido creado correctamente"));
        return redirect(to: route(name: "articles.index"));
    }

    final public function show(Article $article): Renderable
    {
        $article->load(relations: ["user:id,name", "category:id,name"]);
        return view(view: "articles.show", data: compact("article"));
    }

    final public function edit(Article $article): Renderable
    {
        $title = __(key: "Actualizar artículo");
        $action = route(name: "articles.update", parameters: ["article" => $article]);
        return view(view: "articles.form", data: compact("article", "title", "action"));
    }

    final public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->safe()->only(keys: ['title', 'content', 'category_id']);
        $article->update(attributes: $validated);

        session()->flash(key: "success", value: __(key: "El artículo ha sido actualizado correctamente"));
        return redirect(to: route(name: "articles.index"));
    }

    final public function destroy(Article $article): RedirectResponse
    {
        $article->delete();
        session()->flash(key: "success", value: __(key: "El artículo ha sido eliminado correctamente"));
        return redirect(to: route(name: "articles.index"));
    }
}
