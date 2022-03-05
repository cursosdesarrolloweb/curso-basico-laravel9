<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): Renderable {
        $articles = Article::with("category")->latest()->paginate();
        return view("articles.index", compact("articles"));
    }

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
}
