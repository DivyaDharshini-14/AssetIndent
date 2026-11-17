<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Brand;
use App\Http\Requests\Vendor\VendorRequest;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $vendors = Vendor::query()
    ->when($request->name, fn($q) => $q->where('name', 'like', '%'.$request->name.'%'))
    ->when($request->phone_number, fn($q) => $q->where('phone_number', 'like', '%'.$request->phone_number.'%'))
    ->when($request->email, fn($q) => $q->where('email', 'like', '%'.$request->email.'%'))
    ->when($request->status, fn($q) => $q->where('status', $request->status))
    ->when($request->brands, fn($q) => $q->whereJsonContains('brands', $request->brands)) // single brand
    ->paginate(50);

         $brands=Brand::active()->get();   

         return view('vendors.index', compact('vendors', 'brands'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
          $brands=Brand::active()->get();  

        return view('vendors.create',compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VendorRequest $request)
    {

       $validated = $request->validated();

        Vendor::create($validated);

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
         $brands=Brand::active()->get();  

        return view('vendors.create',compact('vendor','brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendorRequest $request, Vendor $vendor)
    {

        $vendor->update($request->validated());

        return redirect()
            ->route('vendor.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()
            ->route('vendor.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Minimal JSON create for asset form "+" (same flow as quick asset name).
     */
    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $name = trim($validated['name']);
        $normalized = mb_strtolower($name);

        $existing = Vendor::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'data' => $existing->only(['id', 'name']),
                'message' => 'Supplier already exists.',
            ]);
        }

        $vendor = Vendor::query()->create([
            'name' => $name,
            'phone_number' => $validated['phone_number'] ?? null,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'data' => $vendor->only(['id', 'name']),
            'message' => 'Supplier created successfully.',
        ], 201);
    }
}
