<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlushLocalBarcodeImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barcode:flushlocalimage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove local barcode image';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = glob(storage_path("app/public/barcode/*")); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
        return 0;
    }
}
