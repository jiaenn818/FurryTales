<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherTestController extends Controller
{
    public function index()
    {
        return view('admin.weatherTest');
    }

    public function byCoords(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!$lat || !$lng) {
            return response()->json(['error' => 'Missing coordinates'], 400);
        }

        $response = Http::get('http://api.weatherstack.com/current', [
            'access_key' => env('WEATHERSTACK_API_KEY'),
            'query' => $lat . ',' . $lng,
            'units' => 'm'
        ]);

        return response()->json($response->json());
    }

    public function byAddress(Request $request)
    {
        $address = $request->query('address');

        if (!$address) {
            return response()->json(['error' => 'Missing address'], 400);
        }

        $response = Http::get('http://api.weatherstack.com/current', [
            'access_key' => env('WEATHERSTACK_API_KEY'),
            'query' => $address,
            'units' => 'm'
        ]);

        return response()->json($response->json());
    }
}
