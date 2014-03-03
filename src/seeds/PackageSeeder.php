<?php namespace Quasimodal\Uploadyoda\seeds;

use Illuminate\Database\Seeder,
    Carbon\Carbon,
    DB,
    Quasimodal\Uploadyoda\Facades\Uploadyoda;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $uploads = $this->makeRecords();
        DB::table('uploads')->insert($uploads); 
    }

    public function makeRecords()
    {
        $filePrefixes = ['foo', 'bar', 'baz', 'qux'];
        $fileExtensions = ['jpeg', 'png', 'gif', 'wmv', 'ogv', 'mp4', 'pdf'];

        $records = [];

        foreach( $filePrefixes as $filePrefix )
            foreach( $fileExtensions as $fileExtension )
            {
                $record = [];
                $record['name'] = $filePrefix . '.' . $fileExtension;
                $record['mime_type'] = Uploadyoda::guessMimeFromExtension($fileExtension);   
                $record['size'] = rand(1, 999) . 'kB';
                $record['path'] = 'path/to/file';
                $record['created_at'] = Carbon::now();
                $record['updated_at'] = Carbon::now();
                array_push( $records, $record );
            }
        return $records;
    } 
}
