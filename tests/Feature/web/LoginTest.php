<?php

namespace Tests\Feature\web;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginTest extends TestCase
{

    public function test_guest_user_can_view_login()
    {
        $response = $this->get('login');

        // guest user can view login with 200 OK
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_authenticated_user_cannot_view_login()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('login');

        // authenticated user cannot view login form
        // will be redirected to index
        $response->assertRedirect('');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = '1234'),
        ]);

        $response = $this->post('login', [
            'user_id' => $user->user_id,
            'password' => $password,
        ]);

        // user can login with correct credentials
        // will be redirected to index
        $response->assertRedirect('');
        $this->assertAuthenticatedAs($user);

        $user->delete();
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->from('login')->post('login', [
            'user_id' => $user->user_id,
            'password' => 'invalid-password',
        ]);

        // user cannot login with incorrect password
        // will be redirected to login with error in 'user_id'
        // user_id has old input
        // password does not have old input
        // must be still guest
        $response->assertRedirect('login');
        $response->assertSessionHasErrors('user_id');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();

        $user->delete();
    }

    public function test_remember_me_functionality()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = '1234'),
        ]);

        $response = $this->post('login', [
            'user_id' => $user->user_id,
            'password' => $password,
            'remember' => 'on',
        ]);

        // user can login with correct credentials and remember turened on
        // will be redirected to index
        // cookie must be matched with credentials
        $response->assertRedirect('');
        $this->assertAuthenticatedAs($user);
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->user_id,
            $user->getRememberToken(),
            $user->password,
        ]));

        $user->delete();
    }

    public function test_send_password_reset_link()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $response = $this->post('password/email', [
            'email' => $user->email,
        ]);

        // generated token(in password_resets) will not be null
        // must be matched with notification's token
        $password_resets = DB::table('password_resets')->where('email', '=', $user->email);
        $generated_token = $password_resets->first();
        $this->assertNotNull($generated_token);
        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels) use ($generated_token) {
            return Hash::check($notification->token, $generated_token->token) === true;
        });

        $password_resets->delete();
    }
}
