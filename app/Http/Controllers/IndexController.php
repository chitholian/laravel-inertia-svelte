<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class IndexController extends Controller
{
    public function home()
    {
        return Inertia::render('Home', []);
    }
}
