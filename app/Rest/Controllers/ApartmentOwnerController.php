<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\ApartmentOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApartmentOwnerController extends RestController
{
    /**
     * List apartment owners with pagination.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = ApartmentOwner::query();

        // Optionally filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Optionally eager-load apartments relation if requested, e.g., ?with=apartments
        if ($request->input('with') === 'apartments') {
            $query->with('apartments');
        }

        $owners = $query->paginate($perPage);

        return response()->json($owners, 200);
    }

    /**
     * Return all apartment owner names (id + name).
     */
    public function getAllOwnerNames()
    {
        $owners = ApartmentOwner::select('id', 'name')->get();
        return response()->json($owners, 200);
    }

    /**
     * Store a new apartment owner.
     * Accepts optional file upload for 'contract' => stores file and sets contract_path.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'address'  => ['required', 'string', 'max:500'],
            // If you want contract file upload:
            'contract' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // max 5MB
            'status'   => ['nullable', 'string', 'max:255'],
            // We will set created_by/updated_by automatically:
            // If you prefer to accept client-provided IDs (not recommended), uncomment:
            // 'created_by' => ['nullable','integer','exists:users,id'],
            // 'updated_by' => ['nullable','integer','exists:users,id'],
        ]);

        // Prepare data:
        $data = [
            'name'    => $validated['name'],
            'address' => $validated['address'],
            'status'  => $validated['status'] ?? 'active',
        ];

        // Handle contract file upload if present
        if ($request->hasFile('contract')) {
            // Store in a folder, e.g., 'contracts/apartment_owners'
            $path = $request->file('contract')->store('contracts/apartment_owners', 'public');
            $data['contract_path'] = $path;
        }

        // Set created_by / updated_by to authenticated user, if available
        if (Auth::check()) {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
        }

        $owner = ApartmentOwner::create($data);

        return response()->json($owner, 201);
    }

    /**
     * Show a single apartment owner.
     */
    public function show(ApartmentOwner $apartmentOwner)
    {
        // Optionally eager-load apartments if query param ?with=apartments
        if (request()->input('with') === 'apartments') {
            $apartmentOwner->load('apartments');
        }
        return response()->json($apartmentOwner, 200);
    }

    /**
     * Update an apartment owner.
     * Can replace contract file if new file uploaded.
     */
    public function update(Request $request, ApartmentOwner $apartmentOwner)
    {
        $validated = $request->validate([
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'address'  => ['sometimes', 'required', 'string', 'max:500'],
            'contract' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'status'   => ['nullable', 'string', 'max:255'],
            // 'updated_by' if from client, but we'll set automatically
            // 'updated_by' => ['nullable','integer','exists:users,id'],
        ]);

        $data = [];
        if (array_key_exists('name', $validated)) {
            $data['name'] = $validated['name'];
        }
        if (array_key_exists('address', $validated)) {
            $data['address'] = $validated['address'];
        }
        if (array_key_exists('status', $validated)) {
            $data['status'] = $validated['status'];
        }

        // Handle contract replacement
        if ($request->hasFile('contract')) {
            // Delete old file if exists
            if ($apartmentOwner->contract_path
                && Storage::disk('public')->exists($apartmentOwner->contract_path)) {
                Storage::disk('public')->delete($apartmentOwner->contract_path);
            }
            // Store new contract
            $newPath = $request->file('contract')->store('contracts/apartment_owners', 'public');
            $data['contract_path'] = $newPath;
        }

        // Set updated_by
        if (Auth::check()) {
            $data['updated_by'] = Auth::id();
        }

        $apartmentOwner->update($data);

        // Optionally load apartments
        if ($request->input('with') === 'apartments') {
            $apartmentOwner->load('apartments');
        }

        return response()->json($apartmentOwner, 200);
    }

    /**
     * Delete an apartment owner.
     * Optionally delete stored contract file.
     */
    public function destroy($id)
    {
        $apartmentOwner = ApartmentOwner::find($id);
        if (!$apartmentOwner) {
            return response()->json(['message' => 'ApartmentOwner not found'], 404);
        }

        // Delete contract file if exists
        if ($apartmentOwner->contract_path
            && Storage::disk('public')->exists($apartmentOwner->contract_path)) {
            Storage::disk('public')->delete($apartmentOwner->contract_path);
        }

        $apartmentOwner->delete();

        return response()->json(['message' => 'ApartmentOwner deleted successfully'], 200);
    }
}
