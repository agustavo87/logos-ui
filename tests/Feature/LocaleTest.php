<?php

namespace Tests\Feature;

use Tests\FixturableTestCase as TestCase;
use App\Models\User;
use Illuminate\Support\Facades\App;
use App\Utils;
use App\Logos\Locale;

class LocaleTest extends TestCase
{
    protected static string $languageA = 'es';
    protected static string $languageB = 'en';
    protected static $userId;
    public static bool $verbose = false;
    public static bool $debug = false;

    protected User $user;
    protected Locale $locale;

    public function logStatus($response, int $expected = 200): void {

        $statusCode = $response->getStatusCode();
        $this->log("$statusCode: {$response->getStatusText()}", $statusCode == $expected);
    }

    /**
     * Inicializa el entorno de todos los tests.
     * 
     * - Crea un macro para obtener el texto del status.
     * - Crea un usuario a ser usado durante los tests.
     *
     * @return void
     */
    protected static function beforeAll(): void
    {

        \Illuminate\Testing\TestResponse::macro('getStatusText', function () {
            return \Illuminate\Http\Response::$statusTexts[$this->getStatusCode()];
        });

        self::$userId = User::factory()->create([
            'language' => self::$languageA
        ])->id;
    }

    /**
     * Ejectua acciones luego de terminados los tests.
     * 
     * - Limpia la BD del modelo de usuario creado.
     *
     * @return void
     */
    protected static function afterAll(): void
    {
        User::find(self::$userId)->delete();
    }

    /**
     * Ejectua acciones antes de cada test.
     * 
     * - Obtiene el usuario y lo guarda para uso posterior.
     *
     * @return void
     */
    public function beforeEach(): void
    {
        $this->user = User::find(self::$userId);
        $this->locale = $this->app->make('locale');
    }

    /**
     * Obtiene el lenguaje en la URI.
     *
     * @return void
     */
    public function testIdentificaLenguageEnUri(): void
    {
        $testLanguage = 'es';
        $response = $this->get('/' . $testLanguage);

        $locale = $this->app->make('locale');
        $URL_Language = $locale->inURL();
        $this->assertEquals($URL_Language, $testLanguage);
    }

    /**
     * Reemplaza el lenguaje en la Path que se le pasa
     *
     * @return void
     */
    public function testRemplazaLenguajeEnPath(): void
    {
        $path = route('home', 'es', false);
        $language = 'en';
        $newPath = $this->locale->replaceLanguageInPath($path, $language);
        $this->log($path);
        $this->log($newPath);
        $this->assertEquals($newPath, route('home', $language, false));

        $path = '/';
        $newPath = $this->locale->replaceLanguageInPath($path, $language);
        $this->log($path);
        $this->log($newPath);
        $this->assertEquals($path, $newPath);
    }

    /**
     * Reemplaza el lenguaje en la URI Actual.
     *
     * @return void
     */
    public function testRemplazaLenguajeEnUriActual(): void
    {
        $originalLanguage = 'es';
        $replaceLanguage = 'en';
        $originalRoute = route('home', ['locale' => $originalLanguage], false);
        $this->log($originalRoute);
        $response = $this->get($originalRoute);

        $locale = $this->app->make('locale');
        $newRoute = $locale->replaceLocaleInCurrentURI($replaceLanguage);
        $this->log($newRoute);
        $this->assertNotEquals($originalRoute, $newRoute);

        $lenguajeEnNuevaRuta = Utils::segments($newRoute)[0];
        $this->assertEquals($replaceLanguage, $lenguajeEnNuevaRuta);
    }

    /**
     * The validators are callable.
     *
     * @return void
     */
    public function testValidatorsAreCallable(): void
    {
        $locale = $this->app->make('locale');
        $this->assertTrue(is_callable([$locale, 'validateValidLanguage']));
        $this->assertTrue(is_callable([$locale, 'validateSupportedLanguage']));
    }

    /**
     * Redirige solicitudes a la raiz del host a lenguaje por defecto
     *
     * @return void
     */
    public function testRedirectsHostRootRequest(): void
    {

        $response = $this->withHeader('Accept-Language', '')
                        ->get('/');
        
        $response->assertRedirect('/' . config('locale.languages.default'));
    }

    public function testRedirigeHomeSinLocale(): void
    {
        $response = $this->get('/home');
        $route = route('home');
        $this->log($route);
        $response->assertRedirect($route);
        $this->logStatus($response, 302);
    }

    /**
     * Redireccionamiento de acuerdo a HTTP 'Accept-Language'
     *
     * @return void
     */
    public function testRedirectsToHttpPreference(): void
    {
        $language = 'en';

        $response = $this->withHeader('Accept-Language', $language)
                            ->get('/');
        
        $response->assertRedirect('/' . $language);
        $response->assertHeader('Content-Language', $language);
        $response->assertHeader('Vary', 'Accept-Language');
        
        $language = 'es';

        $response = $this->withHeader('Accept-Language', $language)
                            ->get('/');
        
        $response->assertRedirect('/' . $language);
    }

    /**
     * El invitado es redireccionado de acuerdo al lenguaje configurado en su
     * sesión.
     *
     * @return void
     */
    public function testRedirectsToSessionLanguage(): void
    {
        $language = self::$languageA;

        $response = $this->withSession(['language' => $language])
                            ->get('/');
        
        $response->assertRedirect();
        $response->assertLocation('/' . $language);
    }

    /**
     * El usuario es redireccionado de acuerdo a su lenguaje configurado.
     *
     * @return void
     */
    public function testRedirectsToUserLanguage(): void
    {
        $language = $this->user->language;

        $response = $this->actingAs($this->user)
                         ->get('/');

        $response->assertRedirect();
        $response->assertLocation('/' . $language);
    }
    /**
     * Actualiza el Lenguaje del usuario mediante una solicitud JSON.
     *
     * @return void
     */
    public function testUpdatesUserLanguageByJson(): void
    {
        $this->assertEquals(
            $this->user->language,
            self::$languageA
        );

        $response = $this->actingAs($this->user)
                         ->putJson("/locale", [
                             'language' => self::$languageB
                         ]);
        // $this->log($response->getData());

        $this->assertEquals(self::$languageB, App::getLocale());
        $response->assertStatus(200);
        $response->assertJsonStructure(['language', 'redirect']);
        $response->assertJson([
            'language' => self::$languageB,
        ]);
        // $response->assertSessionHas('language', self::$languageB);

        $this->assertEquals(
            $this->user->language,
            self::$languageB
        );
    }

    /**
     * Actualiza lenguaje en usuario no registrado
     *
     * @return void
     */
    public function testUpdatesGuestLanguageByJson(): void
    {
        $otherLanguage = !session()->has('language')
                            ? self::$languageA
                            : (session('language') === self::$languageA
                                ? self::$languageB
                                : self::$languageA
                            );

        
        $response = $this->putJson("/locale", [
            'language' => $otherLanguage
        ]);

        // $this->log($response->getData());

        $this->assertEquals($otherLanguage, App::getLocale());
        $response->assertStatus(200);
        $response->assertJsonStructure(['language', 'redirect']);
        $response->assertJson([
            'language' => $otherLanguage,
        ]);

        $response->assertSessionHas('language', $otherLanguage);
    }

    /**
     * Actualiza el Lenguaje del usuario mediante una solicitud HTTP Standard.
     *
     * @return void
     */
    public function testRedirectsOnLanguajeUpdate(): void
    {
        $otherLanguage = $this->user->language == self::$languageA
                         ? self::$languageB
                         : self::$languageA;
        
        $response = $this->actingAs($this->user)
                    ->get('/');
        $response->assertRedirect();
        $newLocation = $response->headers->all()['location'][0];
        $this->ok($newLocation);

        $response = $this->actingAs($this->user)
                         ->get($newLocation);

        $response->assertStatus(200);

        $response = $this->actingAs($this->user)
                         ->put("/locale", [
                             'language' => $otherLanguage
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('language');
        $newLocation = $response->headers->all()['location'][0];

        $this->ok($newLocation);
        $this->ok('ok');
    }

  
    /**
     * Valida que el lenguaje tenga un formato válido y/o sea soportado por
     * la aplicación.
     *
     * @return void
     */
    public function testValidatesLanguageFormatAndSupport(): void
    {
        $invalidLanguage = "a2";
        $unsupportedLanguage = "fr";

        // Invalid language
        $response = $this->actingAs($this->user)
                         ->putJson("/locale", [
                             'language' => $invalidLanguage
                         ]);
        $content = json_decode($response->content());
        // $this->log($content);
        $this->assertObjectHasAttribute('errors', $content);
        $this->assertObjectHasAttribute('language', $content->errors);
        $this->log($content->errors->language);
        $this->assertEquals($content->errors->language[0], __('validation.language_valid'));

        // Unsupported language
        $response = $this->actingAs($this->user)
                         ->putJson("/locale", [
                             'language' => $unsupportedLanguage
                         ]);
        $content = json_decode($response->content());
        // $this->log($content);
        $this->assertObjectHasAttribute('errors', $content);
        $this->assertObjectHasAttribute('language', $content->errors);
        $this->log($content->errors->language);
        $this->assertEquals($content->errors->language[0], __('validation.language_supported'));
    }


}
