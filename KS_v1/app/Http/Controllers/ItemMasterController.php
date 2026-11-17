<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\AssetType;
use App\Models\ItemMaster;
use App\Services\Procurement\DocumentNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ItemMasterController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:item.view', only: ['index', 'show', 'getAssetTypes']),
            new Middleware('permission:item.create', only: ['create', 'store']),
            new Middleware('permission:item.edit', only: ['edit', 'update']),
            new Middleware('permission:item.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = ItemMaster::with(['category', 'assetType']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('asset_type_id')) {
            $query->where('asset_type_id', $request->asset_type_id);
        }

        $items = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $categories = AssetCategory::active()->whereNull('parent_id')->orderBy('category_name')->get();
        // Since we are filtering, we also want to pass selected asset types if category is selected
        $assetTypes = collect();
        if ($request->filled('category_id')) {
            $assetTypes = AssetType::active()->where('category_id', $request->category_id)->orderBy('asset_type_name')->get();
        }

        return view('item-masters.index', compact('items', 'categories', 'assetTypes'));
    }

    public function create()
    {
        $categories = AssetCategory::active()->whereNull('parent_id')->orderBy('category_name')->get();
        return view('item-masters.create', compact('categories'));
    }

    public function store(Request $request, DocumentNumberGenerator $numberGenerator)
    {
        $request->validate([
            'category_id' => 'required|exists:asset_categories,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'item_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('item_masters')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id)
                                 ->where('asset_type_id', $request->asset_type_id);
                }),
            ],
            'description' => 'nullable|string',
            'uom' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ], [
            'item_name.unique' => 'An item with this name already exists in the selected Category and Asset Type.',
        ]);

        $itemCode = $numberGenerator->next('ITM', ItemMaster::class, 'item_code');

        ItemMaster::create([
            'category_id' => $request->category_id,
            'asset_type_id' => $request->asset_type_id,
            'item_code' => $itemCode,
            'item_name' => $request->item_name,
            'description' => $request->description,
            'uom' => $request->uom,
            'status' => $request->status,
        ]);

        return redirect()->route('item-masters.index')->with('success', 'Item created successfully.');
    }

    public function edit(ItemMaster $itemMaster)
    {
        $categories = AssetCategory::active()->whereNull('parent_id')->orderBy('category_name')->get();
        $assetTypes = AssetType::active()->where('category_id', $itemMaster->category_id)->orderBy('asset_type_name')->get();
        
        return view('item-masters.edit', compact('itemMaster', 'categories', 'assetTypes'));
    }

    public function update(Request $request, ItemMaster $itemMaster)
    {
        $request->validate([
            'category_id' => 'required|exists:asset_categories,id',
            'asset_type_id' => 'required|exists:asset_types,id',
            'item_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('item_masters')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id)
                                 ->where('asset_type_id', $request->asset_type_id);
                })->ignore($itemMaster->id),
            ],
            'description' => 'nullable|string',
            'uom' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ], [
            'item_name.unique' => 'An item with this name already exists in the selected Category and Asset Type.',
        ]);

        $itemMaster->update($request->only([
            'category_id',
            'asset_type_id',
            'item_name',
            'description',
            'uom',
            'status'
        ]));

        return redirect()->route('item-masters.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(ItemMaster $itemMaster)
    {
        $itemMaster->delete();
        return redirect()->route('item-masters.index')->with('success', 'Item deleted successfully.');
    }

    public function getAssetTypes(Request $request)
    {
        $categoryId = $request->query('category_id');
        if (!$categoryId) {
            return response()->json([]);
        }

        $assetTypes = AssetType::active()
            ->where('category_id', $categoryId)
            ->orderBy('asset_type_name')
            ->get(['id', 'asset_type_name']);

        return response()->json($assetTypes);
    }
}
