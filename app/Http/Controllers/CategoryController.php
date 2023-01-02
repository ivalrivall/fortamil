<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiHelpers;

    public function getAllCategory()
    {
        $categories = Category::all();
        return $this->onSuccess($categories);
    }
}
