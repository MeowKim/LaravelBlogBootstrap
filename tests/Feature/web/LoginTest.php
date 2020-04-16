<?php

namespace Tests\Feature\web;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginTest extends TestCase
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

    public function testGuestShouldViewLoginForm()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User visits login page.
        $response = $this->get('login');

        // Then: User should view login form.
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function testUserShouldNotViewLoginForm()
    {
        // Given: User is autehnticated. (Already logged in)
        $this->actingAs($this->_user);

        // When: User visits login page.
        $response = $this->get('login');

        // Then: User should not view login form.
        // And: User should be redirected to index page.
        $response->assertRedirect('');
    }

    public function testGuestShouldLoginWithValidCredentials()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User logs in with valid credentials.
        $response = $this->post('login', [
            'user_id' => $this->_user->user_id,
            'password' => $this->_password,
        ]);

        // Then: User should be authenticated.
        // And: User should be redirected to index page.
        $this->assertAuthenticatedAs($this->_user);
        $response->assertRedirect('');
    }

    public function testGuestShouldNotLoginWithInvalidCredentials()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User logs in with invalid credentials.
        $response = $this->from('login')->post('login', [
            'user_id' => $this->_user->user_id,
            'password' => 'invalid-password',
        ]);

        // Then: User should be guest.
        // And: User should be redirected to login page.
        // And: Session has errors with 'user_id'.
        // And: Session has old input 'user_id'.
        // ANd: Session does not have old input 'password'.
        $this->assertGuest();
        $response->assertRedirect('login');
        $response->assertSessionHasErrors('user_id');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function testThrottleFunctionality()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User tries to logs in with invalid credentials more than 5 times per minute.
        foreach (range(0, 5) as $_) {
            $response = $this->from('login')->post('login', [
                'user_id' => $this->_user->user_id,
                'password' => 'invalid-password',
            ]);
        }

        // Then: Session has errors with 'user_id'.
        // And: Session contains 'TooManyLoginAtempt' message.
        $this->assertRegExp(
            sprintf('/^%s$/', str_replace('\:seconds', '\d+', preg_quote(__('auth.throttle'), '/'))),
            collect(
                $response
                    ->baseResponse
                    ->getSession()
                    ->get('errors')
                    ->getBag('default')
                    ->get('user_id')
            )->first(),
        );
    }

    public function testRemeberFunctionality()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User logs in with valid credentials & remeber turned on.
        $response = $this->post('login', [
            'user_id' => $this->_user->user_id,
            'password' => $this->_password,
            'remember' => 'on',
        ]);

        // Then: User should be authenticated.
        // And: User should be redirected to index page.
        // And: Cookie should be matched with given credentials.
        $this->assertAuthenticatedAs($this->_user);
        $response->assertRedirect('');
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $this->_user->user_id,
            $this->_user->getRememberToken(),
            $this->_user->password,
        ]));
    }

    public function testUserShouldLogout()
    {
        // Given: User is autehnticated. (Already logged in)
        $this->actingAs($this->_user);

        // When: User logs out.
        $response = $this->post('logout');

        // Then: User Should be guest.
        // And: User should be redirected to index page.
        $this->assertGuest();
        $response->assertRedirect('');
    }



    public function testSendPasswordResetLink()
    {
        Notification::fake();

        // Given: User is a guest. (Not logged in yet)
        // When: User send password reset link to email.
        $response = $this->post('password/email', [
            'email' => $this->_user->email,
        ]);

        // Then: Token(in password_resets) should be generated successfully.
        // And: Token should be matched with notification's token.
        $password_resets = DB::table('password_resets')->where('email', '=', $this->_user->email);
        $generated_token = $password_resets->first();
        $this->assertNotNull($generated_token);

        Notification::assertSentTo($this->_user, ResetPassword::class, function ($Notification, $channels) use ($generated_token) {
            return Hash::check($Notification->token, $generated_token->token) === true;
        });
    }
}
