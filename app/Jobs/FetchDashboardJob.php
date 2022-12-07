<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Dashboard;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchDashboardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('type jobs => '.$this->type);
        if ($this->type == 'statistic') {
            $this->runStatistic();
            return true;
        }

        $this->runStatistic();
        return true;
    }

    private function runStatistic()
    {
        $statistic = Dashboard::where('type', 'statistic')->first();
        if (!$statistic) {
            $this->createDataStatistic();
        } else {
            $this->updateDataStatistic($statistic);
        }
    }

    private function getDataStatistic()
    {
        $product = Product::select('id')->get()->count();
        $customer = Customer::select('id')->get()->count();
        $order = Order::select('id')->get()->count();
        $dropshipper = User::where('role_id', 3)->select('id')->get()->count();
        $data = [
            'product' => $product,
            'customer' => $customer,
            'order' => $order,
            'dropshipper' => $dropshipper
        ];
        return $data;
    }

    private function createDataStatistic()
    {
        $data = $this->getDataStatistic();
        Dashboard::create([
            'data' => $data,
            'type' => 'statistic'
        ]);
    }

    private function updateDataStatistic(Model $model)
    {
        Log::info('data mdodel => '. $model->id);
        $data = $this->getDataStatistic();
        $model->data = $data;
        $model->updated_at = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $model->save();
    }
}
