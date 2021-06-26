<?php

namespace Tests\Feature;

use Tests\FixturableTestCase as TestCase;
use App\Models\User;

class AuthTest extends TestCase
{

    protected static $userId;
    protected static $language = 'en';
    protected User $user;


    /**
     * Crea un modelo de usuario para ser usado en los tests.
     *
     * @return void
     */
    protected static function beforeAll(): void
    {

        \Illuminate\Testing\TestResponse::macro('getStatusText', function () {
            return \Illuminate\Http\Response::$statusTexts[$this->getStatusCode()];
        });

        self::$userId = User::factory()->create([
            'language' => self::$language
        ])->id;
    }

    public function logStatus($response, int $expected = 200): void
    {
        $statusCode = $response->getStatusCode();
        $this->log("$statusCode: {$response->getStatusText()}", $statusCode == $expected);
    }

    /**
     * Limpia la BD del modelo de usuario creado.
     *
     * @return void
     */
    protected static function afterAll(): void
    {
        User::find(self::$userId)->delete();
    }

    /**
     * Obtiene el usuario y lo guarda para uso posterior.
     *
     * @return void
     */
    public function beforeEach(): void
    {
        $this->user = User::find(self::$userId);
    }

    /**
     * Redirects correctly after login attempt.
     *
     * @return void
     */
    public function testRedirectsAfterLoginAttempt(): void
    {

        $route = route('auth.login', [
            'locale' => app('locale')->getLocale()
        ]);
        $this->log($route);
        $response = $this->post($route, [
                             'email' => $this->user->email,
                             'password' => 'password'
                         ]);
        $response->assertRedirect(route('home', ['locale' => $this->user->language]));
    }
}
