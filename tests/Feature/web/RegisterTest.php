<?php

namespace Tests\Feature\web;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    protected $_user_info;
    protected User $_user;

    // Setup before each testing
    public function setup(): void
    {
        parent::setUp();

        $this->_user_info = [
            'user_id' => 'john',
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'password' => 'correct-password',
            'password_confirmation' => 'correct-password',
        ];
    }

    public function testGuestShouldViewRegisterForm()
    {
        // Given: User is a guest.

        // When: User visits register page.
        $response = $this->get('register');

        // Then: Response status should be '200 OK'.
        // And: User should view register form.
        $response->assertOk();
        $response->assertViewIs('auth.register');
    }

    public function testUserShouldNotViewRegisterForm()
    {
        // Given: User is autehnticated.
        $this->actingAs(factory(User::class)->make());

        // When: User visits register page.
        $response = $this->get('register');

        // Then: User should be redirected to index page.
        $response->assertRedirect('');
    }

    public function testGuestShouldRegister()
    {
        // Given: User is a guest.

        // When: User requests to register.
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should be created successfully.
        // And: User should be authenticated.
        // And: User should be redirected to index page.
        $this->assertDatabaseHas('users', ['user_id' => $this->_user_info['user_id']]);
        $this->assertAuthenticatedAs($this->_user = User::where('user_id', '=', $this->_user_info['user_id'])->first());
        $response->assertRedirect('');
    }

    public function testGuestShouldNotRegisterWithoutUserId()
    {
        // Given: User is a guest.

        // When: User requests to register without 'user_id'.
        $this->_user_info['user_id'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should still be a guest.
        // And: User should be redirected to register page.
        // And: Session should have errors with 'user_id'.
        // And: Session should have old input 'name', 'email'.
        // And: Session should not have old input 'password'.
        $this->assertDatabaseMissing('users', ['user_id' => $this->_user_info['user_id']]);
        $this->assertGuest();
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('user_id');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function testGuestShouldNotRegisterWithoutName()
    {
        // Given: User is a guest.

        // When: User requests to register without 'name'.
        $this->_user_info['name'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should still be a guest.
        // And: User should be redirected to register page.
        // And: Session should have errors with 'name'.
        // And: Session should have old input 'user_id', 'email'.
        // And: Session should not have old input 'password'.
        $this->assertDatabaseMissing('users', ['user_id' => $this->_user_info['user_id']]);
        $this->assertGuest();
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('name');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function testGuestShouldNotRegisterWithoutEmail()
    {
        // Given: User is a guest.

        // When: User requests to register without 'email'.
        $this->_user_info['email'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should still be a guest.
        // And: User should be redirected to register page.
        // And: Session should have errors with 'email'.
        // And: Session should have old input 'user_id', 'name'.
        // And: Session should not have old input 'password'.
        $this->assertDatabaseMissing('users', ['user_id' => $this->_user_info['user_id']]);
        $this->assertGuest();
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function testGuestShouldNotRegisterWithoutPassword()
    {
        // Given: User is a guest.

        // When: Userrequests to register without 'password'.
        $this->_user_info['password'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should still be a guest.
        // And: User should be redirected to register page.
        // And: Session should have errors with 'password'.
        // And: Session should have old input 'user_id', 'name', 'email'.
        // And: Session should not have old input 'password'.
        $this->assertDatabaseMissing('users', ['user_id' => $this->_user_info['user_id']]);
        $this->assertGuest();
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function testGuestShouldNotRegisterWithoutPasswordConfirmation()
    {
        // Given: User is a guest.

        // When: User requests to register without 'password_confirmation'.
        $this->_user_info['password_confirmation'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should still be a guest.
        // And: User should be redirected to register page.
        // And: Session should have errors with 'password'.
        // And: Session should have old input 'user_id', 'name', 'email'.
        // And: Session should not have old input 'password'.
        $this->assertDatabaseMissing('users', ['user_id' => $this->_user_info['user_id']]);
        $this->assertGuest();
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    public function testGuestShouldNotRegisterWithPasswordsNotMatching()
    {
        // Given: User is a guest.

        // When: User requests to register with passwords not matching.
        $this->_user_info['password_confirmation'] = 'another-password';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should still be a guest.
        // And: User should be redirected to register page.
        // And: Session should have errors with 'password'.
        // And: Session should have old input 'user_id', 'name', 'email'.
        // And: Session should not have old input 'password'.
        $this->assertDatabaseMissing('users', ['user_id' => $this->_user_info['user_id']]);
        $this->assertGuest();
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }
}
