<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function home(Request $request) {
        $area_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/areas?key=123456789101112&st=NSW&out=json');
        $suburb_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/suburbs?key=123456789101112&st=NSW&out=json');
        $accom_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/products?key=123456789101112&st=NSW&cats=ACCOMM&pge=1&size=10&out=json');

        $accom_response = json_decode(mb_convert_encoding($accom_response , 'UTF-8' , 'UTF-16LE'), true);
        $count = $accom_response['numberOfResults'];

        $accoms = $accom_response['products'];

        $areas = array_values(array_unique(array_column(json_decode(mb_convert_encoding($area_response , 'UTF-8' , 'UTF-16LE'), true ), 'Name')));
        $suburbs = array_values(array_unique(array_column(json_decode(mb_convert_encoding($suburb_response , 'UTF-8' , 'UTF-16LE'), true)[0]["Suburbs"], 'Name')));

        array_unshift($areas, "All Areas");
        array_unshift($suburbs, "All Suburbs");

        return Inertia::render('Dashboard', [
            'areas' => $areas,
            'suburbs' => $suburbs,
            'accoms' => $accoms,
            'count' => $count
        ]);
    }

    public function filter(Request $request) {
        $rules['page_number'] = ['required', 'integer'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

			return response()->json([
				'status' => 0,
				'message' => $validator->errors()->first()
			]);
		}

        (isset($request->area) && $request->area != 'All Areas') ? $area = preg_replace('/\s+/', '%20', $request->area) : $area = '';
        (isset($request->suburb) && $request->suburb != 'All Suburbs') ? $suburb = preg_replace('/\s+/', '%20', $request->suburb) : $suburb = '';

        $accom_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/products?key=123456789101112&cats=ACCOMM&pge='.$request->page_number.'&ar='.$area.'&ct='.$suburb.'&size=10&out=json');
        $accoms = json_decode(mb_convert_encoding($accom_response , 'UTF-8' , 'UTF-16LE'), true);

        return response()->json([
            "status" => 1,
            "message" => "Accomodations loaded",
            "accoms" => $accoms
        ]);
    }
}
