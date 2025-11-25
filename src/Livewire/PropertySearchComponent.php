<?php

namespace TallCms\RealEstate\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use TallCms\RealEstate\Models\Property;
use TallCms\RealEstate\Models\PropertyType;
use TallCms\RealEstate\Models\District;

class PropertySearchComponent extends Component implements HasForms
{
    use WithPagination;
    use InteractsWithForms;

    protected $paginationView = 'real-estate::pagination.livewire-tailwind';

    // Search parameters
    public $search = '';
    public $keywords = '';
    public $propertyTypeId = '';
    public $districtId = '';
    public $districts = []; // Array of district codes
    public $minPrice = '';
    public $maxPrice = '';
    public $minBedrooms = '';
    public $maxBedrooms = '';
    public $bedrooms = '';
    public $minBathrooms = '';
    public $bathrooms = '';
    public $tenure = []; // Array of tenure types
    public $sortBy = 'latest';
    public $viewMode = 'grid'; // grid or list
    
    // UI state
    public $showSearchForm = true;
    public $showFilters = true;
    public $isLoading = false;
    public $perPage = 9;
    
    // Configuration
    public $searchPlaceholder = 'Search properties...';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'keywords' => ['except' => ''],
        'propertyTypeId' => ['except' => '', 'as' => 'type'],
        'districtId' => ['except' => '', 'as' => 'district'],
        'districts' => ['except' => []],
        'minPrice' => ['except' => '', 'as' => 'min_price'],
        'maxPrice' => ['except' => '', 'as' => 'max_price'],
        'minBedrooms' => ['except' => '', 'as' => 'min_bedrooms'],
        'maxBedrooms' => ['except' => '', 'as' => 'max_bedrooms'],
        'bedrooms' => ['except' => ''],
        'minBathrooms' => ['except' => '', 'as' => 'min_bathrooms'],
        'bathrooms' => ['except' => ''],
        'tenure' => ['except' => []],
        'sortBy' => ['except' => 'latest', 'as' => 'sort'],
        'page' => ['except' => 1],
    ];

    public function mount($perPage = 9, $viewMode = 'grid', $searchPlaceholder = 'Search properties...', $showSearchForm = true, $showFilters = true, $blockData = [])
    {
        $this->perPage = $perPage;
        $this->viewMode = $viewMode;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->showSearchForm = $showSearchForm;
        $this->showFilters = $showFilters;
        
        // Initialize from URL parameters
        $this->search = request('search', '');
        $this->keywords = request('keywords', '');
        $this->propertyTypeId = request('type', '');
        $this->districtId = request('district', '');
        $this->districts = request('districts', []);
        $this->minPrice = request('min_price', '');
        $this->maxPrice = request('max_price', '');
        $this->minBedrooms = request('min_bedrooms', '');
        $this->maxBedrooms = request('max_bedrooms', '');
        $this->bedrooms = request('bedrooms', '');
        $this->minBathrooms = request('min_bathrooms', '');
        $this->bathrooms = request('bathrooms', '');
        $this->tenure = request('tenure', []);
        $this->sortBy = request('sort', 'latest');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedKeywords()
    {
        $this->resetPage();
    }

    public function updatedPropertyTypeId()
    {
        $this->resetPage();
    }

    public function updatedDistrictId()
    {
        $this->resetPage();
    }

    public function updatedDistricts()
    {
        $this->resetPage();
    }

    public function updatedMinPrice()
    {
        $this->resetPage();
    }

    public function updatedMaxPrice()
    {
        $this->resetPage();
    }

    public function updatedMinBedrooms()
    {
        $this->resetPage();
    }

    public function updatedMaxBedrooms()
    {
        $this->resetPage();
    }

    public function updatedBedrooms()
    {
        $this->resetPage();
    }

    public function updatedMinBathrooms()
    {
        $this->resetPage();
    }

    public function updatedBathrooms()
    {
        $this->resetPage();
    }

    public function updatedTenure()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->reset([
            'search', 'keywords', 'propertyTypeId', 'districtId', 'districts',
            'minPrice', 'maxPrice', 'minBedrooms', 'maxBedrooms', 'bedrooms', 
            'minBathrooms', 'bathrooms', 'tenure'
        ]);
        $this->resetPage();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function getPropertiesProperty()
    {
        $query = Property::published()->active()->with(['propertyType', 'district']);

        // Apply search filter (legacy support)
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('address', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%");
            });
        }

        // Apply keywords filter (enhanced search)
        if ($this->keywords) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->keywords}%")
                  ->orWhere('description', 'like', "%{$this->keywords}%")
                  ->orWhere('address', 'like', "%{$this->keywords}%")
                  ->orWhere('city', 'like', "%{$this->keywords}%")
                  ->orWhere('state', 'like', "%{$this->keywords}%")
                  ->orWhere('zip_code', 'like', "%{$this->keywords}%")
                  ->orWhere('agent_name', 'like', "%{$this->keywords}%")
                  ->orWhere('meta_title', 'like', "%{$this->keywords}%")
                  ->orWhere('meta_description', 'like', "%{$this->keywords}%");
            });
        }

        // Apply property type filter
        if ($this->propertyTypeId) {
            $query->where('property_type_id', $this->propertyTypeId);
        }

        // Apply single district filter (legacy support)
        if ($this->districtId) {
            $query->where('district_id', $this->districtId);
        }

        // Apply multiple districts filter
        if (!empty($this->districts)) {
            $query->whereIn('district_id', $this->districts);
        }

        // Apply price filters
        if ($this->minPrice && is_numeric($this->minPrice)) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice && is_numeric($this->maxPrice)) {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Apply bedroom filters
        if ($this->minBedrooms && is_numeric($this->minBedrooms)) {
            $query->where('bedrooms', '>=', $this->minBedrooms);
        }

        if ($this->maxBedrooms && is_numeric($this->maxBedrooms)) {
            $query->where('bedrooms', '<=', $this->maxBedrooms);
        }

        // Apply bedroom filter (legacy support)
        if ($this->bedrooms) {
            if ($this->bedrooms === '5+') {
                $query->where('bedrooms', '>=', 5);
            } else {
                $query->where('bedrooms', $this->bedrooms);
            }
        }

        // Apply bathroom filters
        if ($this->minBathrooms && is_numeric($this->minBathrooms)) {
            $query->where('bathrooms', '>=', $this->minBathrooms);
        }

        // Apply bathroom filter (legacy support)
        if ($this->bathrooms) {
            if ($this->bathrooms === '4+') {
                $query->where('bathrooms', '>=', 4);
            } else {
                $query->where('bathrooms', $this->bathrooms);
            }
        }

        // Apply tenure filter
        if (!empty($this->tenure)) {
            $query->whereIn('tenure', $this->tenure);
        }

        // Apply sorting
        switch ($this->sortBy) {
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

        return $query->paginate($this->perPage);
    }

    public function getPropertyTypesProperty()
    {
        return PropertyType::getSelectOptions();
    }

    public function getDistrictsProperty()
    {
        return District::getSelectOptions();
    }

    public function getAllDistrictsProperty()
    {
        // Return array suitable for multi-select
        return District::active()->ordered()->pluck('name', 'id')->toArray();
    }

    public function getTenureOptionsProperty()
    {
        return [
            'Freehold' => 'Freehold',
            '99-year' => '99-year Leasehold',
            '999-year' => '999-year Leasehold',
            '103-year' => '103-year Leasehold',
            'Leasehold' => 'Other Leasehold',
        ];
    }

    public function getPriceRangesProperty()
    {
        return [
            '100000' => '$100K',
            '200000' => '$200K',
            '500000' => '$500K',
            '1000000' => '$1M',
            '2000000' => '$2M',
            '5000000' => '$5M',
            '10000000' => '$10M+',
        ];
    }

    public function getBedroomOptionsProperty()
    {
        return [
            '1' => '1',
            '2' => '2', 
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6+',
        ];
    }

    public function getBathroomOptionsProperty()
    {
        return [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5+',
        ];
    }

    public function hasActiveFilters()
    {
        return !empty($this->search) ||
               !empty($this->keywords) ||
               !empty($this->propertyTypeId) ||
               !empty($this->districtId) ||
               !empty($this->districts) ||
               !empty($this->minPrice) ||
               !empty($this->maxPrice) ||
               !empty($this->minBedrooms) ||
               !empty($this->maxBedrooms) ||
               !empty($this->bedrooms) ||
               !empty($this->minBathrooms) ||
               !empty($this->bathrooms) ||
               !empty($this->tenure);
    }

    public function render()
    {
        return view('real-estate::livewire.property-search-component', [
            'properties' => $this->properties,
            'propertyTypes' => $this->propertyTypes,
            'districts' => $this->districts,
            'allDistricts' => $this->allDistricts,
            'tenureOptions' => $this->tenureOptions,
            'priceRanges' => $this->priceRanges,
            'bedroomOptions' => $this->bedroomOptions,
            'bathroomOptions' => $this->bathroomOptions,
        ]);
    }
}