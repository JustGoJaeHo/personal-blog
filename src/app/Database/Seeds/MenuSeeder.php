<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $largeId  = $this->insertMenu(null, 1, '블로그', 1);
        $mediumId = $this->insertMenu($largeId, 2, '개발', 1);
        $this->insertMenu($mediumId, 3, 'PHP', 1);
    }

    private function insertMenu(?int $parentId, int $depth, string $name, int $sortOrder): int
    {
        $this->db->table('menus')->insert([
            'parent_id'  => $parentId,
            'depth'      => $depth,
            'name'       => $name,
            'sort_order' => $sortOrder,
            'is_visible' => 1,
        ]);

        return $this->db->insertID();
    }
}
