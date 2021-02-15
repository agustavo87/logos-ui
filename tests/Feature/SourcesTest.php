<?php

namespace Tests\Feature;

use Tests\FixturableTestCase as TestCase;
use App\Logos\Sources;
use App\Models\Creator;
use App\Models\Source;
use App\Models\User;

use Illuminate\Support\Str;

class SourcesTest extends TestCase
{
    public static bool $verbose = true;
    public static bool $debug = true;

    public static $userId;
    public static $creatorId;
    public static $sourceId;

    public User $user;
    public Creator $creator;
    public Source $source;
    public Sources $sourceManager;

    

    /**
     * Inicializa el entorno de todos los tests.
     * 
     * - Crea una fuente de prueba
     *
     * @return void
     */
    protected static function beforeAll(): void
    {
        $user = User::create([
            'name' => 'Jhony Bravo',
            'email' => 'bravoj@example.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'language' => config('locale.languages.default'),
            'country' => 'AR'
        ]);
        self::$userId = $user->id;
        
        self::$creatorId = $user->creators()->create([
            'key'   => 'perezj',
            'type' => 'author',
            'schema' => '0.0.1',
            'data' => [
                'name' => 'Juan',
                'last_name' => 'Perez'
            ]
        ])->id;

        self::$sourceId = $user->sources()->create([
            'key' => 'perez2018',
            'type' => 'citation.book',
            'schema' => '0.0.1',
            'data' => [
                'year' => 2018,
                'title' => "La inefable levedad del ser",
                'editorial' => 'Sor Maria Turgencia Inc.',
                'city' => 'Málaga'
            ]
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
        $this->creator = Creator::find(self::$creatorId);
        $this->source =  Source::find(self::$sourceId);
        $this->sourceManager = new Sources();
    }




    public function testElTestFunciona():void
    {
        $this->log($this->sourceManager->render($this->source));
        $this->assertTrue(true);
    }
    

}
