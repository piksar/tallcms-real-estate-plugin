{{--
    Property Search Block Template
    
    This template renders the property search and listings functionality.
    It includes search filters, property grid, and pagination.
--}}

@php
    $blockData = $data ?? [];
    $title = $blockData['title'] ?? 'Find Your Dream Property';
    $subtitle = $blockData['subtitle'] ?? 'Search through our extensive collection of properties';
    $showSearchForm = $blockData['show_search_form'] ?? true;
    $showFilters = $blockData['show_filters'] ?? true;
    $propertiesPerPage = $blockData['properties_per_page'] ?? 9;
@endphp

<div class="property-search-block py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $title }}</h2>
            @if($subtitle)
                <p class="text-lg text-gray-600">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Property Search Component --}}
        <div class="property-search-component">
            @livewire('real-estate.property-search-component', [
                'showSearchForm' => $showSearchForm,
                'showFilters' => $showFilters,
                'perPage' => $propertiesPerPage,
                'blockData' => $blockData,
            ])
        </div>
    </div>
</div>

@push('styles')
<style>
    .property-search-block {
        min-height: 400px;
    }
    
    .property-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .property-image {
        aspect-ratio: 16/12;
        object-fit: cover;
    }
</style>
@endpush