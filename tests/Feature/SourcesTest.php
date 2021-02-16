<?php

namespace Tests\Feature;

use Tests\FixturableTestCase as TestCase;
use App\Logos\Sources;
use App\Models\{
    Creator,
    Source,
    User
};

use Illuminate\Support\Str;

class SourcesTest extends TestCase
{
    public static bool $verbose = true;
    public static bool $debug = true;

    public const bookRender = 'Perez, J. & Zamudio, P. (2018). La inefable levedad del ser. Sor Maria Turgencia Inc.: Málaga.';
    public const articleRender = 'Perez, J. & Zamudio, P. (2019). Efectos del automonitoreo en la ansiedad social en la escuela. Perspectiva en Trastornos de Ansiedad, vol. 13(2), 110-122.';

    public static $userId;
    public static $creator1Id;
    public static $creator2Id;
    public static $bookId;
    public static $articleId;

    public User $user;
    public Creator $creator1;
    public Creator $creator2;
    public Source $book;
    public Source $article;
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
        
        $creator1 = $user->creators()->create([
            'key'   => 'perezj',
            'type' => 'author',
            'schema' => '0.0.1',
            'data' => [
                'name' => 'Juan',
                'last_name' => 'Perez'
            ]
        ]);
        self::$creator1Id = $creator1->id;
        
        $creator2 = $user->creators()->create([
            'key'   => 'zamudiop',
            'type' => 'author',
            'schema' => '0.0.1',
            'data' => [
                'name' => 'Pablo',
                'last_name' => 'Zamudio'
            ]
        ]);
        self::$creator2Id = $creator2->id;

        $book = $user->sources()->create([
            'key' => 'perez2018',
            'type' => 'citation.book',
            'schema' => '0.0.1',
            'data' => [
                'year' => 2018,
                'title' => "La inefable levedad del ser",
                'editorial' => 'Sor Maria Turgencia Inc.',
                'city' => 'Málaga'
            ]
        ]);
        $book->creators()->attach([$creator1->id, $creator2->id]);
        
        self::$bookId = $book->id;

        $article = $user->sources()->create([
            'key' => 'perez2019',
            'type' => 'citation.article',
            'schema' => '0.0.1',
            'data' => [
                'year' => 2019,
                'title' => "Efectos del automonitoreo en la ansiedad social en la escuela",
                'journal' => 'Perspectiva en Trastornos de Ansiedad',
                'volume' => 13,
                'issue' => 2,
                'firstPage' => 110,
                'lastPage' => 122
            ]
        ]);
        $article->creators()->attach([$creator2->id, $creator1->id]);
        self::$articleId = $article->id;

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
        $this->creator1 = Creator::find(self::$creator1Id);
        $this->creator2 = Creator::find(self::$creator2Id);
        $this->book =  Source::find(self::$bookId);
        $this->article =  Source::find(self::$articleId);
        $this->sourceManager = new Sources();
    }

    /**
     * Verifica que la función render() del SourceManager identifique
     * adecuadametne el tipo de fuente y devuelva el tipo de representación
     * adecuado
     */
    public function testRenderDevuelveAdecuadamenteLaRepresentacionDeUnTipoDeFuente():void
    {
        $this->assertEquals(
            $this->sourceManager->render($this->article),
            self::articleRender
        );

        $this->assertEquals(
            $this->sourceManager->render($this->book),
            self::bookRender
        );
    }

    /**
     * Chequea que el modelo Source tenga un método render()
     * ej. $source->render
     * 
     * @return void
     */
    public function testSourceTieneMetodoRenderQueFunciona(): void
    {
        $this->assertEquals(
            $this->article->render(),
            self::articleRender
        );

        $this->assertEquals(
            $this->book->render(),
            self::bookRender
        );
    }
}
