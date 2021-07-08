<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Function to extract all the areas, suburbs and all the accomodation options.
     *
     * @return Page
     */
    public function home(Request $request) {
        // Atlas API to fetch area, suburb and accomodations
        $area_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/areas?key=123456789101112&st=NSW&out=json');
        $suburb_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/suburbs?key=123456789101112&st=NSW&out=json');
        $accom_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/products?key=123456789101112&st=NSW&cats=ACCOMM&pge=1&size=10&out=json');

        // Converting from UTF-16LE to UTF-8
        $accom_response = json_decode(mb_convert_encoding($accom_response , 'UTF-8' , 'UTF-16LE'), true);

        // All accomodations and the total count
        $accoms = $accom_response['products'];
        $count = $accom_response['numberOfResults'];

        // Converting from UTF-16LE to UTF-8
        $areas = array_values(array_unique(array_column(json_decode(mb_convert_encoding($area_response , 'UTF-8' , 'UTF-16LE'), true ), 'Name')));
        $suburbs = array_values(array_unique(array_column(json_decode(mb_convert_encoding($suburb_response , 'UTF-8' , 'UTF-16LE'), true)[0]["Suburbs"], 'Name')));

        // Adding default values
        array_unshift($areas, "All Areas");
        array_unshift($suburbs, "All Suburbs");

        // Render page with the data
        return Inertia::render('Dashboard', [
            'areas' => $areas,
            'suburbs' => $suburbs,
            'accoms' => $accoms,
            'count' => $count
        ]);
    }

    /**
     * Function to filter accomodation.
     *
     * @return Response
     */
    public function filter(Request $request) {
        // Validator to check if page_number is present
        $rules['page_number'] = ['required', 'integer'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

			return response()->json([
				'status' => 0,
				'message' => $validator->errors()->first()
			]);
		}

        // Check if area and/or suburb filter is added
        (isset($request->area) && $request->area != 'All Areas') ? $area = preg_replace('/\s+/', '%20', $request->area) : $area = '';
        (isset($request->suburb) && $request->suburb != 'All Suburbs') ? $suburb = preg_replace('/\s+/', '%20', $request->suburb) : $suburb = '';

        // Atlas API to fetch accomodation
        $accom_response = Http::get('https://atlas.atdw-online.com.au/api/atlas/products?key=123456789101112&cats=ACCOMM&pge='.$request->page_number.'&ar='.$area.'&ct='.$suburb.'&size=10&out=json');
        $accoms = json_decode(mb_convert_encoding($accom_response , 'UTF-8' , 'UTF-16LE'), true);

        // Return JSON response with accomodation data
        return response()->json([
            "status" => 1,
            "message" => "Accomodations loaded",
            "accoms" => $accoms
        ]);
    }
}
