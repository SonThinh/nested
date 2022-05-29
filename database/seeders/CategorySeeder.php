<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 5) as $item) {
            Category::query()->create([
                'parent_id' => $this->randParentId(),
                'name'      => 'category '.$item,
            ]);
        }

        Category::fixTree();
    }

    public function randParentId()
    {
        $id = optional(Category::query()->inRandomOrder()->first())->id;

        if ($id == rand(1, 5)) {
            $id = null;
        }

        return $id;
    }
}
