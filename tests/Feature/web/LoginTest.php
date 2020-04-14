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

    // setup before each testing
    public function setup(): void
    {
        parent::setUp();

        $this->_password = 'valid-password';
        $this->_user = factory(User::class)->create([
            'password' => bcrypt($this->_password),
        ]);
    }

    public function testGuestCanViewLoginForm()
    {
        $response = $this->get('login');

        // guest can view login form
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function testUserCannotViewLoginForm()
    {
        $response = $this->actingAs($this->_user)->get('login');

        // authenticated user cannot view login form
        // will be redirected to index
        $response->assertRedirect('');
    }

    public function testGuestCanLoginWithValidCredentials()
    {
        $response = $this->post('login', [
            'user_id' => $this->_user->user_id,
            'password' => $this->_password,
        ]);

        // guest can login with valid credentials
        // will be redirected to index
        $response->assertRedirect('');
        $this->assertAuthenticatedAs($this->_user);
    }

    public function testGuestCannotLoginWithInvalidCredentials()
    {

        $response = $this->from('login')->post('login', [
            'user_id' => $this->_user->user_id,
            'password' => 'invalid-password',
        ]);

        // guest cannot login with invalid credentials
        // will be redirected to login
        // session has errors with 'user_id'
        // has old input 'user_id'
        // does not have old input 'password'
        // must be still guest
        $response->assertRedirect('login');
        $response->assertSessionHasErrors('user_id');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function testThrottleFunctionality()
    {
        foreach (range(0, 5) as $_) {
            $response = $this->from('login')->post('login', [
                'user_id' => $this->_user->user_id,
                'password' => 'invalid-password',
            ]);
        }

        // if guest tries to login more than 5 times per minute,
        // they cannot tries again for a minute with TooManyLoginAtempt message.
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
        $response = $this->post('login', [
            'user_id' => $this->_user->user_id,
            'password' => $this->_password,
            'remember' => 'on',
        ]);

        // user can login with valid credentials and remember turened on
        // will be redirected to index
        // cookie must be matched with given credentials
        $response->assertRedirect('');
        $this->assertAuthenticatedAs($this->_user);
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $this->_user->user_id,
            $this->_user->getRememberToken(),
            $this->_user->password,
        ]));
    }

    public function testUserCanLogout()
    {
        $this->actingAs($this->_user);
        $response = $this->post('logout');

        // user can logout
        // will be redirected to index
        // must be guest
        $response->assertRedirect('');
        $this->assertGuest();
    }



    public function testSendPasswordResetLink()
    {
        Notification::fake();

        $response = $this->post('password/email', [
            'email' => $this->_user->email,
        ]);

        // generated token(in password_resets) will not be null
        // must be matched with notification's token
        $password_resets = DB::table('password_resets')->where('email', '=', $this->_user->email);
        $generated_token = $password_resets->first();
        $this->assertNotNull($generated_token);
        Notification::assertSentTo($this->_user, ResetPassword::class, function ($notification, $channels) use ($generated_token) {
            return Hash::check($notification->token, $generated_token->token) === true;
        });
    }
}
