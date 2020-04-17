<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JWTAuth;
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

    // Override method to provide authorization header
    public function actingAs(Authenticatable $user, $driver = null)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }

    public function testUserShouldGetArticleList()
    {
        // Given: User is autehnticated.
        $this->actingAs($this->_user, 'api');

        // When: User requests to get article list.
        $response = $this->json('get', 'api/articles');

        // Then: Response status should be '200 OK'.
        // And: Response has following structure.
        //      'data' has array of 'id', 'title', 'content', 'image_path', 'created_at', 'updated_at', 'creator', 'updater'.
        //      'creator' & 'updater' has 'user_id', 'name', 'email'.
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'content',
                    'image_path',
                    'created_at',
                    'updated_at',
                    'creator' => [
                        'user_id',
                        'name',
                        'email',
                    ],
                    'updater' => [
                        'user_id',
                        'name',
                        'email',
                    ],
                ]
            ]
        ]);
    }

    public function testGuestShouldNotGetArticleList()
    {
        // Given: User is a guest.

        // When: User requests to get article list.
        $response = $this->json('get', 'api/articles');

        // Then: Response status should be '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

    public function testUserShouldCreateArticle()
    {
        // Given: User is autehnticated.
        $this->actingAs($this->_user, 'api');

        // When: User requests to create article.
        $response = $this->json('post', 'api/articles', $this->_article_info);

        // Then: Response status should be '201 Created'.
        // And: Article should be created successfully.
        // And: Response has following structure.
        //      'data' has 'id', 'title', 'content', 'image_path', 'created_at', 'updated_at', 'creator', 'updater'.
        //      'creator' & 'updater' has 'user_id', 'name', 'email'.


        $response->assertCreated();
        $this->assertDatabaseHas('articles', [
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
            'created_by' => $this->_user->user_id,
        ]);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'content',
                'image_path',
                'created_at',
                'updated_at',
                'creator' => [
                    'user_id',
                    'name',
                    'email',
                ],
                'updater' => [
                    'user_id',
                    'name',
                    'email',
                ],
            ]
        ]);
    }

    public function testGuestShouldNotCreateArticle()
    {
        // Given: User is a guest.

        // When: User requests to create article.
        $response = $this->json('post', 'api/articles', $this->_article_info);

        // Then: Response status should be '401 Unauthorized'.
        // And: Article should not be created.
        $response->assertUnauthorized();
        $this->assertDatabaseMissing('articles', [
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
            'created_by' => $this->_user->user_id,
        ]);
    }

    public function testUserShouldNotCreateArticleWithoutTitle()
    {
        // Given: User is autehnticated.
        $this->actingAs($this->_user, 'api');

        // When: User requests to create article without 'title'.
        $this->_article_info['title'] = '';
        $response = $this->json('post', 'api/articles', $this->_article_info);

        // Then: Response status should be '422 Unprocessable Entity'.
        // And: Article should not be created.
        $response->assertStatus(422);
        $this->assertDatabaseMissing('articles', [
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
            'created_by' => $this->_user->user_id,
        ]);
    }

    public function testUserShouldNotCreateArticleWithoutContent()
    {
        // Given: User is autehnticated.
        $this->actingAs($this->_user, 'api');

        // When: User requests to create article without 'content'.
        $this->_article_info['content'] = '';
        $response = $this->json('post', 'api/articles', $this->_article_info);

        // Then: Response status should be '422 Unprocessable Entity'.
        // And: Article should not be created.
        $response->assertStatus(422);
        $this->assertDatabaseMissing('articles', [
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
            'created_by' => $this->_user->user_id,
        ]);
    }

    public function testUserShouldGetArticle()
    {
        // Given: User is autehnticated.
        // And: There is an article.
        $this->actingAs($this->_user, 'api');
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to get article.
        $response = $this->json('get', 'api/articles/' . $article->id);

        // Then: Response status should be '200 OK'.
        // And: Response has following structure.
        //      'data' has 'id', 'title', 'content', 'image_path', 'created_at', 'updated_at', 'creator', 'updater'.
        //      'creator' & 'updater' has 'user_id', 'name', 'email'.
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'content',
                'image_path',
                'created_at',
                'updated_at',
                'creator' => [
                    'user_id',
                    'name',
                    'email',
                ],
                'updater' => [
                    'user_id',
                    'name',
                    'email',
                ],
            ]
        ]);
    }

    public function testGuestShouldNotGetArticle()
    {
        // Given: User is a guest.
        // And: There is an article.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to get article.
        $response = $this->json('get', 'api/articles/' . $article->id);

        // Then: Response status should be '401 Unauthorized'.
        $response->assertUnauthorized();
    }

    public function testUserShouldUpdateOwnArticle()
    {
        // Given: User is autehnticated.
        // And: There is an article that is created by given user.
        $this->actingAs($this->_user, 'api');
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to update article.
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $response = $this->put('api/articles/' . $article->id, $this->_article_info);

        // Then: Response status should be '200 OK'.
        // And: Article should be updated successfully.
        $response->assertOk();
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
        ]);
    }

    public function testUserShouldNotUpdateOthersArticle()
    {
        // Given: User is autehnticated.
        // And: There is an article that is created by other user.
        $this->actingAs($this->_user, 'api');
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $other_user->user_id;
        $this->_article_info['updated_by'] = $other_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to update article.
        $this->_article_info['title'] = 'updated title';
        $this->_article_info['content'] = 'updated content';
        $response = $this->put('api/articles/' . $article->id, $this->_article_info);

        // Then: Response status should be '403 Frobidden'.
        // And: Article should not be updated.
        $response->assertForbidden();
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
        ]);
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
        $response = $this->put('api/articles/' . $article->id, $this->_article_info);

        // Then: Response status should be '401 Unauthorized'.
        // And: Article should not be updated.
        $response->assertUnauthorized();
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
            'title' => $this->_article_info['title'],
            'content' => $this->_article_info['content'],
        ]);
    }

    public function testUserShouldDeleteOwnArticle()
    {
        // Given: User is authenticated.
        // And: There is an article that is created by given user.
        $this->actingAs($this->_user, 'api');
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to delete article.
        $response = $this->delete('api/articles/' . $article->id);

        // Then: Response status should be '204 No Content'.
        // And: Article should be deleted successfully.
        $response->assertNoContent();
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function testUserShouldNotDeleteOthersArticle()
    {
        // Given: User is authenticated.
        // And: There is an article that is created by other user.
        $this->actingAs($this->_user, 'api');
        $other_user = factory(User::class)->make();
        $this->_article_info['created_by'] = $other_user->user_id;
        $this->_article_info['updated_by'] = $other_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to delete article.
        $response = $this->delete('api/articles/' . $article->id);

        // Then: Response should be '403 Forbidden'.
        // And: Article should not be deleted.        
        $response->assertForbidden();
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }

    public function testGuestShouldNotDeleteArticle()
    {
        // Given: User is a guest.
        // And: There is an article.
        $this->_article_info['created_by'] = $this->_user->user_id;
        $this->_article_info['updated_by'] = $this->_user->user_id;
        $article = Article::create($this->_article_info);

        // When: User requests to delete article.
        $response = $this->delete('api/articles/' . $article->id);

        // Then: Response status should be '401 Unauthorized'.
        // And: Article should not be deleted.
        $response->assertUnauthorized();
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }
}
