<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Account;
use App\Rest\Resources\AccountResource;
use Illuminate\Http\Request;

class AccountController extends RestController
{
    public function index()
    {
        $accounts = Account::all();
        return AccountResource::collection($accounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'holder' => 'required|string|max:255',
            'type' => 'required|string|max:50',
        ]);

        $account = Account::create($validated);
        return new AccountResource($account);
    }

    public function show(Account $account)
    {
        return new AccountResource($account);
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'holder' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:50',
        ]);

        $account->update($validated);
        return new AccountResource($account);
    }

    public function destroy( $account)
    {
        $account->delete();
        return response()->json(null, 204);
    }
}
