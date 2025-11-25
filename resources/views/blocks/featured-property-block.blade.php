{{--
    Featured Property Block Template
    
    This template displays a single featured property with detailed information.
--}}

@php
    $blockData = $data ?? [];
    $title = $blockData['title'] ?? 'Featured Property';
    $subtitle = $blockData['subtitle'] ?? 'Discover our property of the month';
    $propertyId = $blockData['property_id'] ?? null;
    $showDescription = $blockData['show_description'] ?? true;
    $showFeatures = $blockData['show_features'] ?? true;
    $showAgent = $blockData['show_agent'] ?? true;
@endphp

<div class="featured-property-block py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title }}</h2>
            @if($subtitle)
                <p class="text-lg text-gray-600">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Featured Property --}}
        @php
            if ($propertyId) {
                $property = \TallCms\RealEstate\Models\Property::find($propertyId);
            } else {
                $property = \TallCms\RealEstate\Models\Property::published()->active()->featured()->first();
            }
        @endphp

        @if($property)
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="md:flex">
                    {{-- Property Image --}}
                    <div class="md:w-1/2">
                        @if($property->primary_image)
                            <img src="{{ Storage::url($property->primary_image) }}" 
                                 alt="{{ $property->title }}" 
                                 class="w-full h-64 md:h-full object-cover">
                        @else
                            <div class="w-full h-64 md:h-full bg-gray-300 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0v-6a2 2 0 012-2h4a2 2 0 012 2v6m2 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v12"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Property Details --}}
                    <div class="md:w-1/2 p-8">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-semibold">
                                Featured Property
                            </span>
                            <span class="text-3xl font-bold text-blue-600">
                                ${{ number_format($property->price) }}
                            </span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $property->title }}</h3>
                        <p class="text-gray-600 mb-4">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $property->address }}, {{ $property->city }}, {{ $property->state }} {{ $property->zip_code }}
                        </p>
                        
                        @if($showDescription && $property->description)
                            <p class="text-gray-700 mb-6 leading-relaxed">
                                {{ Str::limit($property->description, 300) }}
                            </p>
                        @endif
                        
                        {{-- Property Stats --}}
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $property->bedrooms ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Bedrooms</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $property->bathrooms ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Bathrooms</div>
                            </div>
                            @if($property->square_footage)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ number_format($property->square_footage) }}</div>
                                    <div class="text-sm text-gray-600">Sq Ft</div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Features --}}
                        @if($showFeatures && $property->features->count() > 0)
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Key Features</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($property->features->take(6) as $feature)
                                        <span class="bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded-full">
                                            {{ $feature->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        {{-- Agent Info --}}
                        @if($showAgent && ($property->agent_name || $property->agent_email || $property->agent_phone))
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Contact Agent</h4>
                                @if($property->agent_name)
                                    <p class="font-medium text-gray-900">{{ $property->agent_name }}</p>
                                @endif
                                <div class="flex flex-col space-y-1 text-sm text-gray-600">
                                    @if($property->agent_email)
                                        <a href="mailto:{{ $property->agent_email }}" class="hover:text-blue-600">
                                            {{ $property->agent_email }}
                                        </a>
                                    @endif
                                    @if($property->agent_phone)
                                        <a href="tel:{{ $property->agent_phone }}" class="hover:text-blue-600">
                                            {{ $property->agent_phone }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        {{-- Action Buttons --}}
                        <div class="flex space-x-4 pt-6">
                            <a href="{{ route('property.show', $property->slug) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition duration-200">
                                View Details
                            </a>
                            @if($property->agent_email)
                                <a href="mailto:{{ $property->agent_email }}?subject=Inquiry about {{ $property->title }}" 
                                   class="flex-1 border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-3 px-6 rounded-lg text-center transition duration-200">
                                    Contact Agent
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0v-6a2 2 0 012-2h4a2 2 0 012 2v6m2 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v12"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No featured property found</h3>
                <p class="text-gray-600">Please select a property to feature or mark a property as featured.</p>
            </div>
        @endif
    </div>
</div>