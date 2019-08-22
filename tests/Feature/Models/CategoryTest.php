<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
                'deleted_at',
                'is_active'
            ],
            $categoryKey
        );
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'Category 1'
        ]);
        $category->refresh();

        $this->assertTrue($this->isValidUuid($category->id), 'UUID was not generated');
        $this->assertEquals('Category 1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'Category 1',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Category 1',
            'description' => 'test_description'
        ]);
        $this->assertEquals('test_description', $category->description);

        $category = Category::create([
            'name' => 'Category 1',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'Category 1',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create([
            'description' => 'Test Description',
            'is_active' => true
        ])->first();

        $data = [
            'name' => 'test_name_update',
            'description' => 'test_description_update',
            'is_active' => true
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create([
            'description' => 'Test Description',
            'is_active' => true
        ])->first();
        $this->assertNull($category->deleted_at);

        $category->delete();
        $this->assertNotNull($category->deleted_at);
    }

    /**  Joel-James/verify-uuid.php */
    private function isValidUuid( $uuid )
    {
        if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
            return false;
        }
        return true;
    }
}
