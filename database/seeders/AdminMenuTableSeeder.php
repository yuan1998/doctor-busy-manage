<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminMenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_menu')->delete();
        
        \DB::table('admin_menu')->insert(array (
            0 => 
            array (
                'created_at' => '2025-08-13 09:52:40',
                'extension' => '',
                'icon' => 'feather icon-bar-chart-2',
                'id' => 1,
                'order' => 1,
                'parent_id' => 0,
                'show' => 1,
                'title' => 'Index',
                'updated_at' => NULL,
                'uri' => '/',
            ),
            1 => 
            array (
                'created_at' => '2025-08-13 09:52:40',
                'extension' => '',
                'icon' => 'feather icon-settings',
                'id' => 2,
                'order' => 2,
                'parent_id' => 0,
                'show' => 1,
                'title' => 'Admin',
                'updated_at' => NULL,
                'uri' => '',
            ),
            2 => 
            array (
                'created_at' => '2025-08-13 09:52:40',
                'extension' => '',
                'icon' => '',
                'id' => 3,
                'order' => 3,
                'parent_id' => 2,
                'show' => 1,
                'title' => 'Users',
                'updated_at' => NULL,
                'uri' => 'auth/users',
            ),
            3 => 
            array (
                'created_at' => '2025-08-13 09:52:40',
                'extension' => '',
                'icon' => '',
                'id' => 4,
                'order' => 4,
                'parent_id' => 2,
                'show' => 1,
                'title' => 'Roles',
                'updated_at' => NULL,
                'uri' => 'auth/roles',
            ),
            4 => 
            array (
                'created_at' => '2025-08-13 09:52:40',
                'extension' => '',
                'icon' => '',
                'id' => 5,
                'order' => 5,
                'parent_id' => 2,
                'show' => 1,
                'title' => 'Permission',
                'updated_at' => NULL,
                'uri' => 'auth/permissions',
            ),
            5 => 
            array (
                'created_at' => '2025-08-13 09:52:40',
                'extension' => '',
                'icon' => '',
                'id' => 6,
                'order' => 6,
                'parent_id' => 2,
                'show' => 1,
                'title' => 'Menu',
                'updated_at' => NULL,
                'uri' => 'auth/menu',
            ),
            6 => 
            array (
                'created_at' => '2025-08-13 09:52:40',
                'extension' => '',
                'icon' => '',
                'id' => 7,
                'order' => 7,
                'parent_id' => 2,
                'show' => 1,
                'title' => 'Extensions',
                'updated_at' => NULL,
                'uri' => 'auth/extensions',
            ),
            7 => 
            array (
                'created_at' => '2025-08-13 09:54:40',
                'extension' => '',
                'icon' => NULL,
                'id' => 8,
                'order' => 8,
                'parent_id' => 0,
                'show' => 1,
                'title' => '医生管理',
                'updated_at' => '2025-08-13 09:54:40',
                'uri' => '/doctors',
            ),
            8 => 
            array (
                'created_at' => '2025-08-13 10:00:01',
                'extension' => '',
                'icon' => NULL,
                'id' => 9,
                'order' => 9,
                'parent_id' => 0,
                'show' => 1,
                'title' => '科室管理',
                'updated_at' => '2025-08-13 10:00:01',
                'uri' => '/departments',
            ),
            9 => 
            array (
                'created_at' => '2025-08-13 10:32:59',
                'extension' => '',
                'icon' => NULL,
                'id' => 10,
                'order' => 10,
                'parent_id' => 0,
                'show' => 1,
                'title' => '科室治疗项目',
                'updated_at' => '2025-08-13 10:32:59',
                'uri' => '/surgeries',
            ),
        ));
        
        
    }
}