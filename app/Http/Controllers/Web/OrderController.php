<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.pages.order.index');
    }

}
