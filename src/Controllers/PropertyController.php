<?php

namespace TallCms\RealEstate\Controllers;

use App\Http\Controllers\Controller;
use TallCms\RealEstate\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display a listing of published properties
     */
    public function index(Request $request): View
    {
        $query = Property::published()->orderBy('listing_date', 'desc');
        
        // Apply filters from request
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $search = $request->get('search');
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('property_type')) {
            $query->ofType($request->get('property_type'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        if ($request->filled('bedrooms')) {
            $query->withBedrooms($request->get('bedrooms'));
        }

        if ($request->filled('bathrooms')) {
            $query->withBathrooms($request->get('bathrooms'));
        }

        if ($request->filled('location')) {
            $query->inLocation($request->get('location'));
        }

        // Apply sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'bedrooms':
                $query->orderBy('bedrooms', 'desc');
                break;
            case 'square_footage':
                $query->orderBy('square_footage', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('listing_date', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        $properties = $query->paginate(12);

        return view('real-estate::pages.properties-index', compact('properties'));
    }

    /**
     * Display a specific property
     */
    public function show(string $slug): View
    {
        $property = Property::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related properties (same type, exclude current)
        $relatedProperties = Property::published()
            ->where('id', '!=', $property->id)
            ->where('property_type', $property->property_type)
            ->limit(3)
            ->get();

        return view('real-estate::pages.property-detail', compact('property', 'relatedProperties'));
    }
}