<?php

namespace App\Http\Controllers\SGU;

use Illuminate\View\View;

class DashboardController
{
    public function index(): View
    {
        return view('sgu.admin.dashboard');
    }
}
