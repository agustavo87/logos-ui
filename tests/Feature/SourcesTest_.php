<?php

namespace Tests\Feature;

use Tests\FixturableTestCase as TestCase;
use Arete\Logos\Application\Sources;
use App\Models\{
    Creator,
    Source,
    User
};
use Illuminate\Support\Str;

class SourcesTest extends TestCase
{
    public static bool $verbose = false;
    public static bool $debug = false;

    public const BOOK_RENDER = 'Perez, J. & Zamudio, P. (2018). La inefable levedad del ser. Sor Maria Turgencia Inc.: Málaga.';
    public const ARTICLE_RENDER = 'Perez, J. & Zamudio, P. (2019). Efectos del automonitoreo en la ansiedad social en la escuela. Perspectiva en Trastornos de Ansiedad, vol. 13(2), 110-122.';
    public const DEFAULT_BOOK_RENDER = 'Perez, J. & Zamudio, P.(2018). La inefable levedad del ser.';

    public static $articleName = "Artículo";    // son variables porque
    public static $bookName = "Libro";          // eventualmente luego
                                                // pueden cambiar según idioma.

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
            'type' => 'person',
            'schema' => '0.0.1',
            'data' => [
                'name' => 'Juan',
                'last_name' => 'Perez'
            ]
        ]);
        self::$creator1Id = $creator1->id;

        $creator2 = $user->creators()->create([
            'key'   => 'zamudiop',
            'type' => 'person',
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
        $article->creators()->attach([
            $creator1->id => ['type' => 'author', 'relevance' => 1],
            $creator2->id => ['type' => 'author', 'relevance' => 0]
            ]);
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
        $this->sourceManager = $this->app->make(Sources::class); /** @todo cambiar por interface */
    }

    /**
     * Verifica que la función render() del SourceManager identifique
     * adecuadametne el tipo de fuente y devuelva el tipo de representación
     * adecuado
     */
    public function testRenderDevuelveAdecuadamenteLaRepresentacionDeUnTipoDeFuente(): void
    {
        $articleRender = $this->sourceManager->render($this->article);
        $this->log($articleRender);
        $this->assertEquals(
            $articleRender,
            self::ARTICLE_RENDER
        );

        $bookRender = $this->sourceManager->render($this->book);
        $this->log($bookRender);
        $this->assertEquals(
            $bookRender,
            self::BOOK_RENDER
        );

        $defaultBookRender = $this->sourceManager->renderDefault($this->book);
        $this->log($defaultBookRender);
        $this->assertEquals(
            $defaultBookRender,
            self::DEFAULT_BOOK_RENDER
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
        $articleRender = $this->article->render();
        $this->log($articleRender);
        $this->assertEquals(
            $articleRender,
            self::ARTICLE_RENDER
        );

        $bookRender = $this->book->render();
        $this->log($bookRender);
        $this->assertEquals(
            $bookRender,
            self::BOOK_RENDER
        );
    }

    /**
     * Prueba si devuelve adecuadamente el nombre legible de la fuente
     */
    public function testDevuelveElNombreDeLaFuente(): void
    {
        $this->assertEquals(
            $this->sourceManager->name($this->article),
            self::$articleName
        );
        $this->assertEquals(
            $this->sourceManager->name($this->book),
            self::$bookName
        );

        $this->assertEquals(
            $this->article->name(),
            self::$articleName
        );
        $this->assertEquals(
            $this->book->name(),
            self::$bookName
        );
    }
}
