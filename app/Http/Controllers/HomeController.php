<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function home(Request $request) {
        $region_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/areas?key=123456789101112&out=json');
        $suburb_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/suburbs?key=123456789101112&st=NSW&out=json');

        $regions = array_values(array_unique(array_column(json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $region_response), true ), 'Name')));
        $suburbs = array_values(array_unique(array_column(json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $suburb_response), true)[0]["Suburbs"], 'Name')));

        return Inertia::render('Dashboard', [
            'regions' => $regions,
            'suburbs' => $suburbs
        ]);
    }
}
