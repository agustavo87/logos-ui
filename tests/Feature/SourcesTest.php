<?php

namespace Tests\Feature;

use Tests\FixturableTestCase as TestCase;

use App\Models\{
    User,
    Source
};

class SourcesTest extends TestCase
{

    protected static $userId;
    public static bool $verbose = true;
    public static bool $debug = true;

    protected User $user;
    protected array $sources;

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
        // self::$userId = User::factory()->create()->id;
        self::$userId  = Source::factory()
            ->count(10)
            ->for(User::factory())
            ->create()[0]->user->id;
        // $user = $sources[0]->user;
        // self::$userId = $user->id;
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
        $this->user = User::where('id', self::$userId)->first();
        // $this->locale = $this->app->make('locale');
    }

    /**
     * Logs Status.
     * 
     * Depends on
     * - getStatusText macro
     * - LogsInformation trait
     * @param \Illuminate\Testing\TestResponse $response
     * @param int $expected 
     *
     * @return string
     */
    public function logStatus($response, int $expected = 200): void {

        $statusCode = $response->getStatusCode();
        $this->log("$statusCode: {$response->getStatusText()}", $statusCode == $expected);
    }

    /**
     * Error on un-auntheticated attemp.
     *
     * @return void
     */
    public function test_return_error_on_post_a_source_unauthenticated() 
    {
        $key = 'gus2020';
        $data = 'Gustavo, A. (2020). El ocaso del menemismo amarillo. Viñeta2: Buenos Aires.';
        $response = $this
            // ->actingAs($this->user)
            ->postJson(route('users.sources.store', ['user' => $this->user->id]), [
                'key' => $key,
                'data' => $data
        ]);

        
        $this->logStatus($response, 401);

        $response
            ->assertStatus(401); // UnAuthorized.

    }


    /**
     * Post a source.
     *
     * @return string
     */
    public function test_posts_a_source(): string
    {
        $key = 'gus2020';
        $data = 'Gustavo, A. (2020). El ocaso del menemismo amarillo. Viñeta2: Buenos Aires.';
        $response = $this
            ->actingAs($this->user)
            // ->postJson("/users/{$this->user->id}/sources", [
            ->postJson(route('users.sources.store', ['user' => $this->user->id]), [
                'key' => $key,
                'data' => $data
        ]);

        
        $this->logStatus($response, 201);

        $response
            ->assertStatus(201) // created.
            ->assertJson([
                'key' => $key,
                'data' => $data,
                'user_id' => $this->user->id
            ]);

        return $key;

    }

    /**
     * Get a source data.
     * @depends test_posts_a_source
     * @param string $key
     * @return string
     */
    public function test_gets_a_source(string $key): string
    {
        // $key = 'gus2020';
        $response = $this
            ->actingAs($this->user)
            // ->getJson("/users/{$this->user->id}/sources/$key");
            ->getJson(route('users.sources.show', ['user' => $this->user->id, 'source' => $key]));

        
        $this->logStatus($response, 200);

        $response
            ->assertStatus(200) // ok
            ->assertJson([
                'key' => $key,
                'owner_id' => $this->user->id
            ]);

        return $key;

    }

    /**
     * Updates a source data.
     * @depends test_gets_a_source
     * @param string $key
     * @return string
     */
    public function test_updates_a_source(string $key): string
    {
        $newData = "Gustavo R., A. (2020). El resultado siniestro. Arkadia: Buenos Aires.";
        $response = $this
            ->actingAs($this->user)
            // ->putJson("/users/{$this->user->id}/sources/$key", [
            ->putJson(route('users.sources.update', ['user' => $this->user->id, 'source' => $key]), [
                'data' => $newData
            ]);
        $this->logStatus($response, 200);

        $response
        ->assertStatus(200) // ok
        ->assertJson([
            'key' => $key,
            'owner_id' => $this->user->id,
            'data' => $newData
        ]);

        $this->assertSame($this->user->sources->where('key', $key)->first()->data, $newData);

        return $key;
    }

    /**
     * Deletes a source data.
     * @depends test_updates_a_source
     * @param string $key
     * @return string
     */
    public function test_deletes_a_source(string $key): string
    {
        $this->assertTrue($this->user->sources->contains('key', $key));
        $response = $this
            ->actingAs($this->user)
            // ->deleteJson("/users/{$this->user->id}/sources/$key");
            ->deleteJson(route('users.sources.destroy', ['user' => $this->user->id, 'source' => $key]));

        $this->logStatus($response, 200);
        $response
        ->assertStatus(200); // ok
        $this->user->refresh();
        $this->assertFalse($this->user->sources->contains('key', $key));

        return $key;
    }

    /**
     * Get a source data.
     * @depends test_updates_a_source
     * 
     * @todo clean database 
     * 
     * @param string $key
     * @return string
     */
    public function test_index_sources(string $key): string
    {
        // $sources = Source::factory()
        //     ->count(3)
        //     ->for(User::factory())
        //     ->create();
        // $user = $sources[0]->user;

        $perPage = 3;
        $response = $this
            ->actingAs($this->user)
            // ->getJson("users/{$this->user->id}/sources?perpage={$perPage}");
            // ->getJson("users/{$this->user->id}/sources?perpage={$perPage}");
            ->getJson(route('users.sources.index', ['user' => $this->user->id, 'perpage' => $perPage]));
        $this->logStatus($response, 200);
        $response->dump();
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links',
                'path',
                'per_page',
                'to',
                'total',
                'user' => [
                    'id',
                    'name'
                ]
            ],
        ]);
        $this->user->sources->take($perPage)->each(function ($item) use ($response) {
            $response->assertJsonFragment(['key' => $item->key]);
        });



        // $this->user->sources->each(function ($source) use ($response) {
        //     $response->assertJsonFragment([
        //         'key' => $source->key
        //     ]); 
        // });
        return $key;
    }

}
