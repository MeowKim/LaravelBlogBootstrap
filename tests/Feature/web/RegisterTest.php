<?php

namespace Tests\Feature\web;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    protected $_user_info;
    protected User $_user;

    // setup before each testing
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

    // clear something remaining permanently after each testing
    public function tearDown(): void
    {
        if (isset($this->_user))
            $this->_user->delete();

        parent::tearDown();
    }

    public function testGuestCanViewRegisterForm()
    {
        $response = $this->get('register');

        // guest can view register form
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function testUserCannotViewRegisterForm()
    {
        $response = $this->actingAs(factory(User::class)->make())->get('register');

        // authenticated user cannot view register form
        // will be redirected to index
        $response->assertRedirect('');
    }

    public function testGuestCanRegister()
    {
        Event::fake();

        $response = $this->from('register')->post('register', $this->_user_info);
        $this->_user = User::where('user_id', '=', $this->_user_info['user_id'])->first();

        // guest can register
        // will be redirected to index
        // authenticated user's information must be same as given user's information
        // (user_id, name, email, password)
        // also registered event's user information must be same.
        $response->assertRedirect('');
        $this->assertAuthenticatedAs($this->_user);
        $this->assertEquals($this->_user_info['user_id'], $this->_user->user_id);
        $this->assertEquals($this->_user_info['name'], $this->_user->name);
        $this->assertEquals($this->_user_info['email'], $this->_user->email);
        $this->assertTrue(password_verify($this->_user_info['password'], $this->_user->password));
        Event::assertDispatched(Registered::class, function ($e) {
            return $e->user->user_id === $this->_user->user_id;
        });
    }

    public function testGuestCannotRegisterWithoutUserId()
    {
        $this->_user_info['user_id'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // guest cannot register without 'user_id'
        // will be redirected to register form    
        // session has errors with 'user_id'
        // has old input 'name', 'email'
        // does not have old input 'password'    
        // must be still guest
        // given user's information must not be created
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('user_id');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
        $this->assertCount(0, User::where('user_id', '=', $this->_user_info['user_id'])->get());
    }

    public function testGuestCannotRegisterWithoutName()
    {
        $this->_user_info['name'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // guest cannot register without 'name'
        // will be redirected to register form    
        // session has errors with 'name'
        // has old input 'user_id', 'email'
        // does not have old input 'password'    
        // must be still guest
        // given user's information must not be created
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('name');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
        $this->assertCount(0, User::where('user_id', '=', $this->_user_info['user_id'])->get());
    }

    public function testGuestCannotRegisterWithoutEmail()
    {
        $this->_user_info['email'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // guest cannot register without 'email'
        // will be redirected to register form    
        // session has errors with 'email'
        // has old input 'user_id', 'name'
        // does not have old input 'password'    
        // must be still guest
        // given user's information must not be created
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
        $this->assertCount(0, User::where('user_id', '=', $this->_user_info['user_id'])->get());
    }

    public function testGuestCannotRegisterWithoutPassword()
    {
        $this->_user_info['password'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // guest cannot register without 'password'
        // will be redirected to register form    
        // session has errors with 'password'
        // has old input 'user_id', 'name', 'email'
        // does not have old input 'password'    
        // must be still guest
        // given user's information must not be created
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
        $this->assertCount(0, User::where('user_id', '=', $this->_user_info['user_id'])->get());
    }

    public function testGuestCannotRegisterWithoutPasswordConfirmation()
    {
        $this->_user_info['password_confirmation'] = '';
        $response = $this->from('register')->post('register', $this->_user_info);

        // guest cannot register without 'password_confirmation'
        // will be redirected to register form    
        // session has errors with 'password'
        // has old input 'user_id', 'name', 'email'
        // does not have old input 'password'    
        // must be still guest
        // given user's information must not be created
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
        $this->assertCount(0, User::where('user_id', '=', $this->_user_info['user_id'])->get());
    }

    public function testGuestCannotRegisterWithPasswordsNotMatching()
    {
        $this->_user_info['password_confirmation'] = 'another-password';
        $response = $this->from('register')->post('register', $this->_user_info);

        // guest cannot register with passwords not matching
        // will be redirected to register form    
        // session has errors with 'password'
        // has old input 'user_id', 'name', 'email'
        // does not have old input 'password'    
        // must be still guest
        // given user's information must not be created
        $response->assertRedirect('register');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('user_id'));
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
        $this->assertCount(0, User::where('user_id', '=', $this->_user_info['user_id'])->get());
    }
}
