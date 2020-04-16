<?php

namespace Tests\Feature\web;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
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
        // Given: User is a guest. (Not logged in yet)
        // When: User visits register page.
        $response = $this->get('register');

        // Then: User should view register form.
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function testUserShouldNotViewRegisterForm()
    {
        // Given: User is autehnticated. (Already logged in)
        $this->actingAs(factory(User::class)->make());

        // When: User visits register page.
        $response = $this->get('register');

        // Then: User should be redirected to index page.
        $response->assertRedirect('');
    }

    public function testGuestShouldRegister()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User posts user's information.
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should be created successfully.
        // And: User should be authenticated.
        // And: User should be redirected to index page.
        // And: Created user's information should be same as given user's information (user_id, name, email, password)
        $this->assertDatabaseHas('users', ['user_id' => $this->_user_info['user_id']]);
        $this->_user = User::where('user_id', '=', $this->_user_info['user_id'])->first();
        $this->assertAuthenticatedAs($this->_user);
        $response->assertRedirect('');
        $this->assertEquals($this->_user_info['user_id'], $this->_user->user_id);
        $this->assertEquals($this->_user_info['name'], $this->_user->name);
        $this->assertEquals($this->_user_info['email'], $this->_user->email);
        $this->assertTrue(Hash::check($this->_user_info['password'], $this->_user->password));
    }

    public function testGuestShouldNotRegisterWithoutUserId()
    {
        // Given: User is a guest. (Not logged in yet)
        // When: User posts user's information without 'user_id'.
        $this->_user_info['user_id'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should be guest.
        // And: User should be redirected to register page.
        // And: Session has errors with 'user_id'
        // And: Session has old input 'name', 'email'
        // And: Session does not have old input 'password'
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
        // Given: User is a guest. (Not logged in yet)
        // When: User posts user's information without 'name'.
        $this->_user_info['name'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should be guest.
        // And: User should be redirected to register page.
        // And: Session has errors with 'name'
        // And: Session has old input 'user_id', 'email'
        // And: Session does not have old input 'password'
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
        // Given: User is a guest. (Not logged in yet)
        // When: User posts user's information without 'email'.
        $this->_user_info['email'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should be guest.
        // And: User should be redirected to register page.
        // And: Session has errors with 'email'
        // And: Session has old input 'user_id', 'name'
        // And: Session does not have old input 'password'
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
        // Given: User is a guest. (Not logged in yet)
        // When: User posts user's information without 'password'.
        $this->_user_info['password'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should be guest.
        // And: User should be redirected to register page.
        // And: Session has errors with 'password'
        // And: Session has old input 'user_id', 'name', 'email'
        // And: Session does not have old input 'password'
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
        // Given: User is a guest. (Not logged in yet)
        // When: User posts user's information without 'password_confirmation'.
        $this->_user_info['password_confirmation'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should be guest.
        // And: User should be redirected to register page.
        // And: Session has errors with 'password'
        // And: Session has old input 'user_id', 'name', 'email'
        // And: Session does not have old input 'password'
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
        // Given: User is a guest. (Not logged in yet)
        // When: User posts user's information passwords not matching.
        $this->_user_info['password_confirmation'] = 'another-password';
        $response = $this->from('register')->post('register', $this->_user_info);

        // Then: Given user's information should not be created.
        // And: User should be guest.
        // And: User should be redirected to register page.
        // And: Session has errors with 'password'
        // And: Session has old input 'user_id', 'name', 'email'
        // And: Session does not have old input 'password'
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
