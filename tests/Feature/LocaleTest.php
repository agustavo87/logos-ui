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

    /**
     * Crea un modelo de usuario para ser usado en los tests.
     *
     * @return void
     */
    protected static function beforeAll(): void
    {
        self::$userId = User::factory()->create([
            'language' => self::$languageA
        ])->id;
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
        $this->locale = $this->app->make('locale');
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
     * Actualiza el Lenguaje del usuario mediante una solicitud JSON.
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
}
