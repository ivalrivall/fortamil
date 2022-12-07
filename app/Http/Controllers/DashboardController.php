<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\DashboardRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Dashboard;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiHelpers;

    private ProductRepositoryInterface $product;
    private CustomerRepositoryInterface $customer;
    private OrderRepositoryInterface $order;
    private DashboardRepositoryInterface $dashboard;

    public function __construct(
        ProductRepositoryInterface $product,
        CustomerRepositoryInterface $customer,
        OrderRepositoryInterface $order,
        DashboardRepositoryInterface $dashboard
    )
    {
        $this->product = $product;
        $this->customer = $customer;
        $this->order = $order;
        $this->dashboard = $dashboard;
    }

    public function getStatisticData()
    {
        $dashboard = Dashboard::where('type', 'statistic')->first();
        $data = $dashboard ? $dashboard->data : null;
        $result = [
            [
                'color' => 'light-primary',
                'customClass' => 'mb-2 mb-xl-0',
                'icon' => 'TrendingUpIcon',
                'subtitle' => 'Order',
                'title' => $data ? $data->order : 0
            ],
            [
                'color' => 'light-info',
                'customClass' => 'mb-2 mb-xl-0',
                'icon' => 'UserIcon',
                'subtitle' => 'Customer',
                'title' => $data ? $data->customer : 0
            ],
            [
                'color' => 'light-danger',
                'customClass' => 'mb-2 mb-xl-0',
                'icon' => 'BoxIcon',
                'subtitle' => 'Produk',
                'title' => $data ? $data->product : 0
            ],
            [
                'color' => 'light-success',
                'customClass' => 'mb-2 mb-xl-0',
                'icon' => 'UserIcon',
                'subtitle' => 'Dropshipper',
                'title' => $data ? $data->dropshipper : 0
            ]
        ];
        return $this->onSuccess([
            'result' => $result,
            'last_update' => $dashboard ? $dashboard->updated_at : Carbon::now('Asia/Jakarta')->toDateTimeString()
        ]);
    }
}
