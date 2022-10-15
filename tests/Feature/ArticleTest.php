<?php

use App\Models\{Category, User};
use function Pest\Laravel\{actingAs, get};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => User::factory()->create());

it('has author')->assertDatabaseHas('users', [
    'id' => 1,
]);

it('user not logged cannot access to articles page', function ()
{
    get('/articles')
        ->assertRedirect('/login');
});

it('user logged can access to articles page', function ()
{
    actingAs(User::first())
        ->get('/articles')
        ->assertStatus(200);
});

it('user logged can access to create article page', function ()
{
    actingAs(User::first())
        ->get('/articles/create')
        ->assertStatus(200);
});

it('user logged can create article', function ()
{
    actingAs(User::first())
        ->post('/articles', [
            'title' => 'Article title',
            'content' => 'Article content',
            'category_id' => Category::factory()->create()->id,
        ])
        ->assertRedirect('/articles')
        ->assertSessionHas('success', 'El artículo ha sido creado correctamente');
});

it('user logged can access to edit article page', function ()
{
    $user = User::first();
    $article = $user->articles()->create([
        'title' => 'Article title',
        'content' => 'Article content',
        'category_id' => Category::factory()->create()->id,
    ]);

    actingAs($user)
        ->get("/articles/{$article->id}/edit")
        ->assertStatus(200);
});

it('user logged can edit article', function ()
{
    $user = User::first();
    $article = $user->articles()->create([
        'title' => 'Article title',
        'content' => 'Article content',
        'category_id' => Category::factory()->create()->id,
    ]);

    actingAs($user)
        ->put("/articles/{$article->id}", [
            'title' => 'Article title updated',
            'content' => 'Article content updated',
            'category_id' => Category::factory()->create()->id,
        ])
        ->assertRedirect('/articles')
        ->assertSessionHas('success', 'El artículo ha sido actualizado correctamente');
});

it('user logged can delete article', function ()
{
    $user = User::first();
    $article = $user->articles()->create([
        'title' => 'Article title',
        'content' => 'Article content',
        'category_id' => Category::factory()->create()->id,
    ]);

    actingAs($user)
        ->delete("/articles/{$article->id}")
        ->assertRedirect('/articles')
        ->assertSessionHas('success', 'El artículo ha sido eliminado correctamente');
});
