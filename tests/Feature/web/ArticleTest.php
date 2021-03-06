<?php

namespace Tests\Feature\web;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected $_user;
    protected $_article_info;

    // Setup before each testing
    public function setup(): void
    {
        parent::setUp();

        Storage::fake();
        $this->_user = User::where('user_id', '=', 'test')->first();
        $this->_article_info = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'image' => UploadedFile::fake()->image('test.jpg'),
        ];
    }

    public function testUserShouldViewArticlesIndex()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User visits index page.
        $response = $this->get('articles');

        // Then: Response should be '200 OK'.
        // And: User should view index page.
        $response->assertOk();
        $response->assertViewIs('articles.index');
    }

    public function testGuestShouldNotViewArticlesIndex()
    {
        // Given: User is a guest.

        // When: User visits index page.
        $response = $this->get('articles');

        // Then: User should be redirected to login page.
        $response->assertRedirect('login');
    }

    public function testUserShouldViewCreateForm()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User visits create page.
        $response = $this->get('articles/create');

        // Then: Response should be '200 OK'.
        // And: User should view create form.
        $response->assertOk();
        $response->assertViewIs('articles.create');
    }

    public function testGuestShouldNotViewCreateForm()
    {
        // Given: User is a guest.

        // When: User visits create page.
        $response = $this->get('articles/create');

        // Then: User should be redirected to login page.
        $response->assertRedirect('login');
    }

    public function testUserShouldCreateArticle()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User requests to create article.
        $response = $this->from('articles/create')->post('articles', $this->_article_info);

        // Then: Article should be created successfully.
        // And: User should be redirected to index page.
        // And: Uploaded image should be exist in storage.
        $this->assertDatabaseHas('articles', [
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
            'image_name' => $this->_article_info['image']->name,
            'created_by' => $this->_user->user_id,
        ]);
        $response->assertRedirect('articles');
        Storage::assertExists(Article::find(Article::orderBy('id', 'desc')->first()->id)->image_path);
    }

    public function testGuestShouldNotCreateArticle()
    {
        // Given: User is a guest.

        // When: User requests to create article.
        $response = $this->from('articles/create')->post('articles', $this->_article_info);

        // Then: Article should not be created.
        // And: User should be redirected to login page.
        $this->assertDatabaseMissing('articles', [
            'title' => $this->_article_info['title'],
            'created_by' => $this->_user->user_id,
        ]);
        $response->assertRedirect('login');
    }

    public function testUserShouldNotCreateArticleWithoutTitle()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User requests to create article without 'title'.
        $this->_article_info['title'] = '';
        $response = $this->from('articles/create')->post('articles', $this->_article_info);

        // Then: Article should not be created.
        // And: User should be redirected to create page.
        $this->assertDatabaseMissing('articles', [
            'title' => $this->_article_info['title'],
            'created_by' => $this->_user->user_id,
        ]);
        $response->assertRedirect('articles/create');
    }

    public function testUserShouldNotCreateArticleWithoutContent()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User requests to create article without 'content'.
        $this->_article_info['content'] = '';
        $response = $this->from('articles/create')->post('articles', $this->_article_info);

        // Then: Article should not be created.
        // And: User should be redirected to create page.
        $this->assertDatabaseMissing('articles', [
            'title' => $this->_article_info['title'],
            'created_by' => $this->_user->user_id,
        ]);
        $response->assertRedirect('articles/create');
    }

    public function testUserShouldViewArticle()
    {
        // Given: User is authenticated.
        // And: There is an article.
        $this->actingAs($this->_user);
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User visits detail page.
        $response = $this->get('articles/' . $article->id);

        // Then: Response should be '200 OK'.
        // And: User should view detail page.
        $response->assertOk();
        $response->assertViewIs('articles.show');
    }

    public function testGuestShouldNotViewArticle()
    {
        // Given: User is a guest.
        // And: There is an article.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User visits detail page.
        $response = $this->get('articles/' . $article->id);

        // Then: User should be redirected to login page.
        $response->assertRedirect('login');
    }

    public function testUserShouldNotViewNonexistentArticle()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User visits nonexistent article's detail page.
        $last_article = Article::orderBy('id', 'desc')->first();
        $response = $this->get('articles/' . ++$last_article->id);

        // Then: Response should be '404 Not Found'.
        $response->assertNotFound();
    }

    public function testUserShouldViewArticleEditForm()
    {
        // Given: User is authenticated.
        // And: There is an article.
        $this->actingAs($this->_user);
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User visits edit page.
        $response = $this->get('articles/' . $article->id . '/edit');

        // Then: Response should be '200 OK'.
        // And: User should view edit form.
        $response->assertOk();
        $response->assertViewIs('articles.edit');
    }

    public function testGuestShouldNotViewArticleEditForm()
    {
        // Given: User is a guest.
        // And: There is an article.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User visits edit page.
        $response = $this->get('articles/' . $article->id . '/edit');

        // Then: User should be redirected to login page.
        $response->assertRedirect('login');
    }

    public function testUserShouldUpdateOwnArticle()
    {
        // Given: User is authenticated.
        // And: There is an article that is created by given user.
        $this->actingAs($this->_user);
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to update article.
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $this->_article_info['image'] = UploadedFile::fake()->image('updated.png');
        $response = $this->from('articles/' . $article->id . '/edit')
            ->put('articles/' . $article->id, $this->_article_info);

        // Then: Article should be updated successfully.
        // And: User should be redirected to detail page.
        // And: Uploaded image should be exist in storage.
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
            'image_name' => $this->_article_info['image']->name,
        ]);
        $response->assertRedirect('articles/' . $article->id);
        Storage::assertExists(Article::find($article->id)->image_path);
    }

    public function testGuestShouldNotUpdateArticle()
    {
        // Given: User is a guest.
        // And: There is an article.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to update article.
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $response = $this->from('articles/' . $article->id . '/edit')
            ->put('articles/' . $article->id, $this->_article_info);

        // Then: Article should not be updated.
        // And: User should be redirected to login page.
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
        ]);
        $response->assertRedirect('login');
    }

    public function testUserShouldNotUpdateOthersArticle()
    {
        // Given: User is authenticated.
        // And: There is an article that is created by other user.
        $this->actingAs($this->_user);
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $other_user->user_id;
        $this->_article_info['updated_by'] = $other_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to update article.
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $response = $this->from('articles/' . $article->id . '/edit')
            ->put('articles/' . $article->id, $this->_article_info);


        // Then: Response status should be '403 Frobidden'.
        // And: Article should not be updated.
        $response->assertForbidden();
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
        ]);
    }

    public function testUserShouldNotUpdateNonexistentArticle()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User requests to update nonexistent article.
        $last_article = Article::orderBy('id', 'desc')->first();
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $response = $this->put('articles/' . ++$last_article->id, $this->_article_info);

        // Then: Response should be '404 Not Found'.
        $response->assertNotFound();
    }

    public function testUserShouldDeleteOwnArticle()
    {
        // Given: User is authenticated.
        // And: There is an article that is created by given user.
        $this->actingAs($this->_user);
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to delete article.
        $response = $this->from('articles/' . $article->id)
            ->delete('articles/' . $article->id);

        // Then: Article should be deleted successfully.
        // And: User should be redirected to index page.
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        $response->assertRedirect('articles');
    }

    public function testGuestShouldNotDeleteArticle()
    {
        // Given: User is a guest.
        // And: There is an article.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to delete article.
        $response = $this->from('articles/' . $article->id)
            ->delete('articles/' . $article->id);

        // Then: Article should not be deleted.
        // And: User should be redirected to login page.
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
        $response->assertRedirect('login');
    }

    public function testUserShouldNotDeleteOthersArticle()
    {
        // Given: User is authenticated.
        // And: There is an article that is created by other user.
        $this->actingAs($this->_user);
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $other_user->user_id;
        $this->_article_info['updated_by'] = $other_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to delete article.
        $response = $this->from('articles/' . $article->id)
            ->delete('articles/' . $article->id);

        // Then: Response should be '403 Forbidden'.
        // And: Article should not be deleted.        
        $response->assertForbidden();
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }

    public function testUserShouldNotDeleteNonexistentArticle()
    {
        // Given: User is authenticated.
        $this->actingAs($this->_user);

        // When: User requests to delete nonexistent article.
        $last_article = Article::orderBy('id', 'desc')->first();
        $response = $this->delete('articles/' . ++$last_article->id);

        // Then: Response should be '404 Not Found'.
        $response->assertNotFound();
    }
}
