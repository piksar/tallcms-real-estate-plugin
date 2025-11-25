{{--
    Property Listings Block Template
    
    This template displays a curated list of properties without search functionality.
--}}

@php
    $blockData = $data ?? [];
    $title = $blockData['title'] ?? 'Featured Properties';
    $subtitle = $blockData['subtitle'] ?? 'Discover our handpicked selection of premium properties';
    $limit = $blockData['limit'] ?? 6;
    $showFeaturedOnly = $blockData['show_featured_only'] ?? true;
    $propertyTypes = $blockData['property_types'] ?? [];
@endphp

<div class="property-listings-block py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title }}</h2>
            @if($subtitle)
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Property Grid --}}
        @php
            $query = \TallCms\RealEstate\Models\Property::published()->active();
            
            if ($showFeaturedOnly) {
                $query->featured();
            }
            
            if (!empty($propertyTypes)) {
                $query->whereIn('property_type_id', $propertyTypes);
            }
            
            $properties = $query
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($properties as $property)
                <div class="property-card bg-white rounded-lg shadow-lg overflow-hidden">
                    {{-- Property Image --}}
                    <div class="relative">
                        @if($property->primary_image)
                            <img src="{{ Storage::url($property->primary_image) }}" 
                                 alt="{{ $property->title }}" 
                                 class="property-image w-full h-48 object-cover">
                        @else
                            <div class="property-image w-full h-48 bg-gray-300 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0v-6a2 2 0 012-2h4a2 2 0 012 2v6m2 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v12"></path>
                                </svg>
                            </div>
                        @endif
                        
                        @if($property->is_featured)
                            <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                Featured
                            </span>
                        @endif
                        
                        <span class="absolute top-2 right-2 bg-white text-gray-800 text-sm px-2 py-1 rounded-full font-semibold">
                            ${{ number_format($property->price) }}
                        </span>
                    </div>
                    
                    {{-- Property Details --}}
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $property->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $property->address }}, {{ $property->city }}</p>
                        
                        @if($property->description)
                            <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                                {{ Str::limit($property->description, 120) }}
                            </p>
                        @endif
                        
                        {{-- Property Stats --}}
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h6"></path>
                                </svg>
                                {{ $property->bedrooms ?? 0 }} beds
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                </svg>
                                {{ $property->bathrooms ?? 0 }} baths
                            </span>
                            @if($property->square_footage)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                    {{ number_format($property->square_footage) }} sq ft
                                </span>
                            @endif
                        </div>
                        
                        {{-- View Property Button --}}
                        <a href="{{ route('property.show', $property->slug) }}" 
                           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            View Property
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0v-6a2 2 0 012-2h4a2 2 0 012 2v6m2 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v12"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                    <p class="text-gray-600">Check back later for new listings.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>