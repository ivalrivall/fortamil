<?php

namespace App\Console\Commands;

use App\Jobs\AddingBarcodeProductJob;
use App\Models\Product;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\Facades\DNS1DFacade;

class GenerateBarcodeProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barcode:generate {productId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate barcode for product';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $timestamp = Carbon::now('Asia/Jakarta')->timestamp;
        $filename = "$timestamp.png";
        Storage::disk('barcode')->put($filename, base64_decode(DNS1DFacade::getBarcodePNG($this->argument('productId'), "C39")));

        if ($this->argument('productId')) {
            $pictureUrl = Cloudinary::upload(storage_path('app/public/barcode/'.$filename), ['folder' => 'barcode'])->getSecurePath();
            Product::where('id', $this->argument('productId'))->update(['barcode_url' => $pictureUrl]);
        } else {
            $products = Product::where('created_at', '<', Carbon::now('Asia/Jakarta')->toDateTimeString())
                ->where('barcode_url', null)
                ->get();
            if (count($products) > 0) {
                foreach ($products as $key => $value) {
                    dispatch(new AddingBarcodeProductJob($value));
                }
            }
        }
        return 0;
    }
}
