<?php

use Illuminate\Database\Seeder;

class ActionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
				DB::table('actions')->insert([
            ['name'	=>	'index', 'displayname'	=>	'Index'],
            ['name'	=>	'view', 'displayname'	=>	'View'],
            ['name'	=>	'create', 'displayname'	=>	'Add/Create'],
            ['name'	=>	'update', 'displayname'	=>	'Edit/Update'],
            ['name'	=>	'delete', 'displayname'	=>	'Delete'],
            ['name'	=>	'verify', 'displayname'	=>	'Verify'],
            ['name'	=>	'approve', 'displayname'	=>	'Approve'],
            ['name'	=>	'publish', 'displayname'	=>	'Publish'],
            ['name'	=>	'register', 'displayname'	=>	'Register'],
            ['name'	=>	'assign', 'displayname'	=>	'Assign']
        ]);
		}
}
