<?php

namespace App\Rest\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Rest\Controller as RestController;

class ExchangeRateController extends RestController
{
    /**
     * List all exchange rates
     */
    public function index()
    {
        return response()->json(ExchangeRate::all());
    }

    /**
     * Store or update a rate manually from frontend
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3',
            'rate_to_usd' => 'required|numeric|min:0.000001',
        ]);

        $rate = ExchangeRate::updateOrCreate(
            ['currency_code' => strtoupper($request->currency_code)],
            ['rate_to_usd' => $request->rate_to_usd]
        );

        return response()->json([
            'message' => 'Exchange rate saved successfully.',
            'rate' => $rate,
        ]);
    }

    /**
     * Delete an exchange rate (optional)
     */
    public function destroy($code)
    {
        $deleted = ExchangeRate::where('currency_code', strtoupper($code))->delete();

        return response()->json([
            'message' => $deleted ? 'Rate deleted.' : 'Currency not found.',
        ]);
    }
}
