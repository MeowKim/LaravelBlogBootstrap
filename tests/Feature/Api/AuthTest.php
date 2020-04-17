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

    // Override method to provide authorization header
    public function actingAs(Authenticatable $user, $driver = null)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }

    public function testGuestShouldLoginWithValidCredentials()
    {
        // Given: User is a guest.
        // When: User logs in with valid credentials.
        $response = $this->json('post', 'api/auth/login', [
            'user_id' => $this->_user->user_id,
            'password' => $this->_password,
        ]);

        // Then: Response status should be '200 OK'.
        // And: Response has 'data' & 'data' has 'access_token', 'token_type', 'expires_in'.
        // And: User should be authenticated with given credentials.
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
            ]
        ]);
        $this->assertAuthenticatedAs($this->_user, 'api');
    }

    public function testGuestShouldNotLoginWithInvalidCredentials()
    {
        // Given: User is a guest.
        // When: User logs in with valid credentials.
        $response = $this->json('post', 'api/auth/login', [
            'user_id' => $this->_user->user_id,
            'password' => 'invalid-password',
        ]);

        // Then: Response status should be '401 Unauthorized'.
        // And: Response has 'message' about error.
        // And: User should still be a guest.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
        $this->assertGuest('api');
    }

    public function testUserShouldGetUserResource()
    {
        // Given: User is autehnticated.
        $this->actingAs($this->_user, 'api');

        // When: User request user resource.
        $response = $this->json('post', 'api/auth/user');

        // Then: Response status should be '200 OK'.
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
        // Given: User is a guest.
        // When: User request user resource.
        $response = $this->json('post', 'api/auth/user');

        // Then: Response status should be '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

    public function testUserShouldLogout()
    {
        // Given: User is autehnticated.
        $this->actingAs($this->_user, 'api');

        // When: User request logout.
        $response = $this->json('post', 'api/auth/logout');

        // Then: Response status should be '200 OK'.
        // And: Response has 'message' equals auth.loggedout.
        // And: User should be a guest.
        $response->assertOk();
        $response->assertJsonStructure(['message']);
        $this->assertGuest('api');
    }

    public function testGuestShouldNotLogout()
    {
        // Given: User is a guest.
        // When: User request logout.
        $response = $this->json('post', 'api/auth/logout');

        // Then: Response status should be '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

    public function testUserShouldRefreshToken()
    {
        // Given: User is autehnticated.
        $this->actingAs($this->_user, 'api');

        // When: User request to refresh token.
        $response = $this->json('post', 'api/auth/refresh');

        // Then: Response status should be '200 OK'.
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
        // Given: User is a guest.
        // When: User request to refresh token.
        $response = $this->json('post', 'api/auth/refresh');

        // Then: Response status should be '401 Unauthorized'.
        // And: Response has 'message' about error.
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }
}
