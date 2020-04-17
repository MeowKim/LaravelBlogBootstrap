<?php

namespace Tests\Feature\web;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $this->_user = User::where('user_id', '=', 'test')->first();
        $this->_article_info = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ];
    }

    public function testUserShouldViewArticlesIndexPage()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // When: User visits index page.
        $response = $this->get('articles');

        // Then: User should view index page.
        $response->assertStatus(200);
        $response->assertViewIs('articles.index');
    }

    public function testGuestShouldNotViewArticlesIndexPage()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User visits index page.
        $response = $this->get('articles');

        // Then: User should be redirected to login page.
        $response->assertRedirect('login');
    }

    public function testUserShouldViewCreateForm()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // When: User visits create page.
        $response = $this->get('articles/create');

        // Then: User should view create form.
        $response->assertStatus(200);
        $response->assertViewIs('articles.create');
    }

    public function testGuestShouldNotViewCreateForm()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User visits create page.
        $response = $this->get('articles/create');

        // Then: User should be redirected to login page.
        $response->assertRedirect('login');
    }

    public function testUserShouldCreateArticle()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // When: User posts articles information.
        $response = $this->from('articles/create')->post('articles', $this->_article_info);

        // Then: Article should be created successfully.
        // And: User should be redirected to index page.
        $this->assertDatabaseHas('articles', [
            'title' => $this->_article_info['title'],
            'created_by' => $this->_user->user_id,
        ]);
        $response->assertRedirect('articles');
    }

    public function testGuestShouldNotCreateArticle()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User posts articles information.
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
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // When: User posts articles information without 'title'.
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
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // When: User posts articles information without 'content'.
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

    public function testUserShouldViewArticleDetailPage()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // And: There is an article could be read.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User visits detail page.
        $response = $this->get('articles/' . $article->id);

        // Then: User should view create form.
        $response->assertStatus(200);
        $response->assertViewIs('articles.show');
    }

    public function testGuestShouldNotViewArticleDetailPage()
    {
        // Given: User is a guest. (Not logged in yet)
        // And: There is an article could be read.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User visits detail page.
        $response = $this->get('articles/' . $article->id);

        // Then: User should be redirected to login page.
        $response->assertRedirect('login');
    }

    public function testUserShouldViewArticleEditForm()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // And: There is an article could be read.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User visits edit page.
        $response = $this->get('articles/' . $article->id . '/edit');

        // Then: User should view edit form.
        $response->assertStatus(200);
        $response->assertViewIs('articles.edit');
    }

    public function testGuestShouldNotViewArticleEditForm()
    {
        // Given: User is a guest. (Not logged in yet)
        // And: There is an article could be read.
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
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // And: There is an article that is created by given user.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User request to update article.
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $response = $this->from('articles/' . $article->id . '/edit')
            ->put('articles/' . $article->id, $this->_article_info);

        // Then: Article should be updated successfully.
        // And: User should be redirected to detail page.
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
        ]);
        $response->assertRedirect('articles/' . $article->id);
    }

    public function testUserShouldNotUpdateOthersArticle()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // And: There is an article that is created by other user.
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $other_user->user_id;
        $this->_article_info['updated_by'] = $other_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User request to update article.
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $response = $this->from('articles/' . $article->id . '/edit')
            ->put('articles/' . $article->id, $this->_article_info);

        // Then: Article should not be updated.
        // And: Response status should be 403 Frobidden.
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
        ]);
        $response->assertStatus(403);
    }

    public function testGuestShouldNotUpdateArticle()
    {
        // Given: User is a guest. (Not logged in yet)
        // And: There is an article.
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User request to update article.
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

    public function testUserShouldDeleteOwnArticle()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // And: There is an article that is created by given user.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User request to delete article.
        $response = $this->from('articles/' . $article->id)
            ->delete('articles/' . $article->id);

        // Then: Article should be deleted successfully.
        // And: User should be redirected to index page.
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        $response->assertRedirect('articles');
    }

    public function testUserShouldNotDeleteOthersArticle()
    {
        // Given: User is authenticated. (Already logged in)
        $this->actingAs($this->_user);

        // And: There is an article that is created by other user.
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $other_user->user_id;
        $this->_article_info['updated_by'] = $other_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User request to delete article.
        $response = $this->from('articles/' . $article->id)
            ->delete('articles/' . $article->id);

        // Then: Article should not be deleted.
        // And: User should be redirected to detail page.
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
        $response->assertRedirect('articles/' . $article->id);
    }

    public function testGuestShouldNotDeleteArticle()
    {
        // Given: User is a guest. (Not logged in yet)
        // And: There is an article.
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User request to delete article.
        $response = $this->from('articles/' . $article->id)
            ->delete('articles/' . $article->id);

        // Then: Article should not be deleted.
        // And: User should be redirected to login page.
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
        $response->assertRedirect('login');
    }
}
