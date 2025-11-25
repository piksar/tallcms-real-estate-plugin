{{--
    Property Search Livewire Component Template
    
    This component handles property search, filtering, and display with pagination.
--}}

<div class="property-search-component">
    {{-- Search Form --}}
    @if($showSearchForm ?? true)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form wire:submit.prevent="search">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Keywords Search --}}
                    <div>
                        <label for="keywords" class="block text-sm font-medium text-gray-700 mb-1">Keywords</label>
                        <input type="text" 
                               id="keywords"
                               wire:model.live="keywords" 
                               placeholder="Enter keywords..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    {{-- Property Type --}}
                    <div>
                        <label for="propertyTypeId" class="block text-sm font-medium text-gray-700 mb-1">Property Type</label>
                        <select wire:model.live="propertyTypeId" 
                                id="propertyTypeId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Types</option>
                            @foreach(\TallCms\RealEstate\Models\PropertyType::all() as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Min Price --}}
                    <div>
                        <label for="minPrice" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                        <input type="number" 
                               id="minPrice"
                               wire:model.live="minPrice" 
                               placeholder="Min price"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    {{-- Max Price --}}
                    <div>
                        <label for="maxPrice" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                        <input type="number" 
                               id="maxPrice"
                               wire:model.live="maxPrice" 
                               placeholder="Max price"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                @if($showFilters ?? true)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                        {{-- Bedrooms --}}
                        <div>
                            <label for="minBedrooms" class="block text-sm font-medium text-gray-700 mb-1">Min Bedrooms</label>
                            <select wire:model.live="minBedrooms" 
                                    id="minBedrooms"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}+</option>
                                @endfor
                            </select>
                        </div>
                        
                        {{-- Bathrooms --}}
                        <div>
                            <label for="minBathrooms" class="block text-sm font-medium text-gray-700 mb-1">Min Bathrooms</label>
                            <select wire:model.live="minBathrooms" 
                                    id="minBathrooms"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any</option>
                                @for($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}">{{ $i }}+</option>
                                @endfor
                            </select>
                        </div>
                        
                        {{-- Districts --}}
                        <div>
                            <label for="districts" class="block text-sm font-medium text-gray-700 mb-1">Districts</label>
                            <select wire:model.live="districts" 
                                    id="districts"
                                    multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach(\TallCms\RealEstate\Models\District::all() as $district)
                                    <option value="{{ $district->code }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Clear Filters --}}
                        <div class="flex items-end">
                            <button type="button" 
                                    wire:click="clearFilters"
                                    class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    @endif

    {{-- Loading Indicator --}}
    <div wire:loading class="flex justify-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    {{-- Results Summary --}}
    <div class="flex justify-between items-center mb-6" wire:loading.remove>
        <p class="text-gray-600">
            Showing {{ $properties->firstItem() ?? 0 }} to {{ $properties->lastItem() ?? 0 }} 
            of {{ $properties->total() }} properties
        </p>
        
        {{-- Sort Options --}}
        <div class="flex items-center space-x-2">
            <label for="sortBy" class="text-sm font-medium text-gray-700">Sort by:</label>
            <select wire:model.live="sortBy" 
                    id="sortBy"
                    class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="latest">Latest</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="bedrooms">Bedrooms</option>
                <option value="square_footage">Square Footage</option>
            </select>
        </div>
    </div>

    {{-- Property Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" wire:loading.remove>
        @forelse($properties as $property)
            <div class="property-card bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200">
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $property->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $property->address }}, {{ $property->city }}</p>
                    
                    @if($property->description)
                        <p class="text-gray-700 text-sm mb-4 line-clamp-2">
                            {{ Str::limit($property->description, 80) }}
                        </p>
                    @endif
                    
                    {{-- Property Stats --}}
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h6"></path>
                            </svg>
                            {{ $property->bedrooms ?? 0 }} bed{{ $property->bedrooms != 1 ? 's' : '' }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                            </svg>
                            {{ $property->bathrooms ?? 0 }} bath{{ $property->bathrooms != 1 ? 's' : '' }}
                        </span>
                        @if($property->square_footage)
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                                {{ number_format($property->square_footage) }} ft²
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                <p class="text-gray-600">Try adjusting your search criteria or clearing filters.</p>
                @if($this->hasActiveFilters())
                    <button wire:click="clearFilters" 
                            class="mt-4 text-blue-600 hover:text-blue-800 font-medium">
                        Clear all filters
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($properties->hasPages())
        <div class="flex justify-center" wire:loading.remove>
            {{ $properties->links('real-estate::pagination.livewire-tailwind') }}
        </div>
    @endif
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .property-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush