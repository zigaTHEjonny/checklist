<?php
namespace Tests\Feature;

use Tests\Support\TestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\UserModel;

class UserAuthTest extends TestCase
{
    use FeatureTestTrait;

    public function testRegisterPageLoads()
    {
        $result = $this->get('/register');
        $result->assertOK();
        $result->assertSee('Register');
    }

    public function testRegisterValidationFails()
    {
        
        $post = [
            'name'             => 'ab',
            'email'            => 'not-an-email',
            'password'         => '123',
            'password_confirm' => '321',
        ];

        $result = $this->post('/register', $post);
        $result->assertStatus(200);
        $result->assertSee('The name field must be at least 3 characters in length');
        $result->assertSee('The email field must contain a valid email address');
        $result->assertSee('The password_confirm field does not match the password field');
    }

    public function testRegisterSuccessRedirectsAndCreatesUser()
{
    $model = new UserModel();
    $model->where('email', 'test@example.com')->delete(); 

    $post = [
        'name'             => 'Test User',
        'email'            => 'test@example.com',
        'password'         => 'password123',
        'password_confirm' => 'password123',
    ];

    $result = $this->post('/register', $post);

    if (!$result->isRedirect()) {
        echo $result->getBody(); 
    }

    $result->assertRedirectTo('/login');

    $user = $model->where('email', 'test@example.com')->first();
    $this->assertNotEmpty($user, 'User record was not created');
}


    

    public function testLoginPageLoads()
    {
        $result = $this->get('/login');
        $result->assertOK();
        $result->assertSee('Login');
    }

    public function testLoginWithInvalidCredentialsFails()
    {
        $post = [
            'email'    => 'nouser@example.com',
            'password' => 'wrongpass',
        ];

        $result = $this->post('/login', $post);
        $result->assertStatus(200);
        $result->assertSee('Email or password incorrect');
    }

    public function testLoginSuccessSetsSessionAndRedirects()
    {
        $model = new UserModel();
        $model->where('email', 'user@example.com')->delete();

        $model->save([
            'name'     => 'Existing User',
            'email'    => 'user@example.com',
            'password' => 'securePass123',
        ]);

        $post = [
            'email'    => 'user@example.com',
            'password' => 'securePass123',
        ];

        $result = $this->post('/login', $post);
        $result->assertRedirectTo('/');

        $session = session();
        $this->assertTrue($session->get('isLoggedIn'), 'Session was not set to logged in');
    }


    public function testLogoutDestroysSessionAndRedirects()
    {
        // Set up a session first
        $this->withSession([
            'isLoggedIn' => true,
            'name'       => 'Test User',
            'email'      => 'test@example.com',
        ]);
        $result = $this->post('/logout');

        $result->assertRedirectTo('/');

        $this->assertNull(session()->get('isLoggedIn'), 'Session key "isLoggedIn" should be null after logout');
        $this->assertNull(session()->get('email'), 'Session key "email" should be null after logout');
        $this->assertNull(session()->get('name'), 'Session key "name" should be null after logout');
    }


}
