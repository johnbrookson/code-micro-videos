<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $this->assertCount(1, $genres);
        $genreKey = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'created_at',
                'updated_at',
                'deleted_at',
                'is_active'
            ],
            $genreKey
        );
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'Genre 1'
        ]);
        $genre->refresh();

        $this->assertTrue($this->isValidUuid($genre->id), 'UUID was not generated');
        $this->assertEquals('Genre 1', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'Genre 1',
            'is_active' => false
        ]);
        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'Genre 1',
            'is_active' => true
        ]);
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create([
            'is_active' => true
        ])->first();

        $data = [
            'name' => 'test_name_update',
            'is_active' => true
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create([
            'is_active' => true
        ])->first();
        $this->assertNull($genre->deleted_at);

        $genre->delete();
        $this->assertNotNull($genre->deleted_at);
    }

     private function isValidUuid( $uuid )
     {
        if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
            return false;
        }
        return true;
    }
}
