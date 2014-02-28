<?php namespace Quasimodal\Uploadyoda\seeds;

use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $upload = array(
            'name' => 'test.jpg',
            'path' => 'test',
            'mime_type' => 'jpg',
            'size' => '100kb',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        );
        \DB::table('uploads')->insert($upload); 
    }
}
