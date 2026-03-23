<?php

namespace App\Http\Controllers;

class DocumateHandbookController extends Controller
{
    public function index()
    {
        return view('backend.handbook.index', [
            'handbook' => config('documate.handbook'),
            'officeLocations' => config('documate.office_locations'),
        ]);
    }
}
