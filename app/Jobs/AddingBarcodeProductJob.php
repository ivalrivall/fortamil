<?php

namespace App\Jobs;

use App\Models\Product;
use App\Repositories\CloudinaryRepository;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\Facades\DNS1DFacade;
use Milon\Barcode\Facades\DNS2DFacade;

class AddingBarcodeProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $productId = $this->product->id;
        $timestamp = Carbon::now('Asia/Jakarta')->timestamp;
        $filename = "$timestamp.png";
        Storage::disk('barcode')->put($filename, base64_decode(DNS1DFacade::getBarcodePNG($productId, "C39")));
        $pictureUrl = Cloudinary::upload(storage_path('app/public/barcode/'.$filename), ['folder' => 'barcode'])->getSecurePath();
        $this->product->barcode_url = $pictureUrl;
        $this->product->save();
        return true;
    }
}
