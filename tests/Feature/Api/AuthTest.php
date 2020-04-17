<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use JWTAuth;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    protected User $_user;
    protected string $_password;

    // Setup before each testing
    public function setup(): void
    {
        parent::setUp();

        $this->_password = 'valid-password';
        $this->_user = factory(User::class)->create([
            'password' => bcrypt($this->_password),
        ]);
    }

    // override to provide authorization header
    public function actingAs(Authenticatable $user, $driver = null)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }

    public function testGuestShouldLoginWithValidCredentials()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User logs in with valid credentials.
        $response = $this->json('post', 'api/auth/login', [
            'user_id' => $this->_user->user_id,
            'password' => $this->_password,
        ]);

        // Then: User should be authenticated with '200 OK'.
        // And: Response has 'data' & 'data' has 'access_token', 'token_type', 'expires_in'.
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
            ]
        ]);
    }

    public function testGuestShouldNotLoginWithInvalidCredentials()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User logs in with valid credentials.
        $response = $this->json('post', 'api/auth/login', [
            'user_id' => $this->_user->user_id,
            'password' => 'invalid-password',
        ]);

        // Then: User should not be authenticated with '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

    public function testUserShouldGetUserResource()
    {
        // Given: User is autehnticated. (Already logged in)
        $this->actingAs($this->_user, 'api');

        // When: User request user resource.
        $response = $this->json('post', 'api/auth/user');

        // Then: User should get user resource with '200 OK'.
        // And: Response has 'data' & 'data' has 'user_id', 'name', 'email', 'image', 'image_name'.
        // And: Fields should be matched with given credentials.
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'user_id',
                'name',
                'email',
                'image',
                'image_name',
            ]
        ]);
        $response->assertJsonPath('data.user_id', $this->_user->user_id)
            ->assertJsonPath('data.name', $this->_user->name)
            ->assertJsonPath('data.email', $this->_user->email);
    }

    public function testGuestShouldNotGetUserResource()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User request user resource.
        $response = $this->json('post', 'api/auth/user');

        // Then: User should not be authenticated with '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

    public function testUserShouldLogout()
    {
        // Given: User is autehnticated. (Already logged in)
        $this->actingAs($this->_user, 'api');

        // When: User request logout.
        $response = $this->json('post', 'api/auth/logout');

        // Then: User should be guest with '200 OK'.
        // And: Response has 'message' equals auth.loggedout.
        $response->assertOk();
        $response->assertJsonStructure(['message']);
    }

    public function testGuestShouldNotLogout()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User request logout.
        $response = $this->json('post', 'api/auth/logout');

        // Then: User should not be authenticated with '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

    public function testUserShouldRefreshToken()
    {
        // Given: User is autehnticated. (Already logged in)
        $this->actingAs($this->_user, 'api');

        // When: User request to refresh token.
        $response = $this->json('post', 'api/auth/refresh');

        // Then: Token should be refreshed with '200 OK'.
        // And: Response has 'data' & 'data' has 'access_token', 'token_type', 'expires_in'.
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
            ]
        ]);
    }

    public function testGuestShouldNotRefreshToken()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User request to refresh token.
        $response = $this->json('post', 'api/auth/refresh');

        // Then: User should not be authenticated with '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }
}
