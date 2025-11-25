# TALL CMS Real Estate Plugin

A comprehensive Real Estate plugin for TALL CMS featuring advanced property search, management, and display capabilities.

![Real Estate Plugin](https://via.placeholder.com/800x400/3B82F6/FFFFFF?text=TALL+CMS+Real+Estate+Plugin)

## Features

### 🏠 **Property Management**
- Comprehensive property listing system
- Property types, districts, amenities, and features management
- Tenure support (Freehold, Leasehold, etc.)
- Agent contact information
- SEO optimization fields

### 🔍 **Advanced Search & Filtering**
- Real-time property search powered by Livewire
- Keyword search across multiple fields
- Price range filtering (min/max)
- Bedroom and bathroom range filtering
- Multiple district selection
- Tenure type filtering
- Sorting options (latest, price, bedrooms, size)

### 🎨 **TALL CMS Integration**
- Native Filament admin panel integration
- Custom blocks for page builder
- PropertySearchBlock with advanced filtering
- PropertyListingsBlock for curated displays
- FeaturedPropertyBlock for highlighting properties

### ⚡ **Modern Tech Stack**
- **Livewire 3** for real-time interactions
- **Filament 3** for admin interface
- **Tailwind CSS** for responsive design
- **Alpine.js** for enhanced UX

### 🔧 **Plugin Management**
- Safe-by-default uninstall process
- Data preservation options
- Complete lifecycle management
- Automated table cleanup with foreign key respect

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher
- TALL CMS 1.0 or higher
- Livewire 3.0 or higher
- Filament 3.0 or higher

## Installation

### Via Composer

```bash
composer require tallcms/real-estate-plugin:dev-main

```

### Run Installation Command

```bash
php artisan real-estate:install
```

#### Installation Options

```bash
# Install with demo data (50 sample properties)
php artisan real-estate:install --demo

# Safe reset: ONLY plugin tables (preserves users, pages, etc.)
php artisan real-estate:install --fresh

# Install and seed reference data only
php artisan real-estate:install --seed

# Publish configuration and views for customization
php artisan real-estate:install --publish
```

#### ⚠️ Important: Safe `--fresh` Option

The `--fresh` option is **SAFE** and only affects Real Estate plugin tables:

**What it does:**
- ✅ Drops and recreates ONLY plugin tables
- ✅ Preserves users, pages, and all other data  
- ✅ Re-runs plugin migrations cleanly
- ✅ Requires confirmation before proceeding

**What it does NOT do:**
- ❌ Does NOT affect users table
- ❌ Does NOT affect pages or other CMS data
- ❌ Does NOT run Laravel's `migrate:fresh` command

### Manual Installation

If you prefer manual installation:

1. **Publish migrations:**
```bash
php artisan vendor:publish --provider="TallCms\RealEstate\RealEstateServiceProvider" --tag="real-estate-migrations"
```

2. **Run migrations:**
```bash
php artisan migrate
```

3. **Publish configuration (optional):**
```bash
php artisan vendor:publish --provider="TallCms\RealEstate\RealEstateServiceProvider" --tag="real-estate-config"
```

4. **Seed reference data:**
```bash
php artisan db:seed --class="TallCms\RealEstate\Database\Seeders\RealEstateReferenceDataSeeder"
```

## Uninstall

The plugin provides safe and comprehensive uninstall options to protect your valuable property data.

### 🛡️ Safe Uninstall (Recommended)

**Removes code only, preserves all data:**

```bash
# Check current plugin state and data summary
php artisan real-estate:uninstall

# Remove plugin code (data preserved)
composer remove tallcms/real-estate-plugin
```

**What this does:**
- ✅ Removes plugin code and dependencies
- ✅ Preserves all property data and tables
- ✅ Allows reinstallation without data loss
- ✅ Safe for production environments

### 🗑️ Full Uninstall (Destructive)

**Removes code AND all plugin data:**

```bash
# Full uninstall with data removal (requires confirmation)
php artisan real-estate:uninstall --remove-data

# Automated uninstall (skip confirmations)
php artisan real-estate:uninstall --remove-data --force
```

**⚠️ Warning: This will permanently delete:**
- All property listings and data
- Property types, districts, amenities, features
- All plugin tables and relationships
- Migration records

**Safety features:**
- Shows data summary before deletion
- Requires explicit confirmation ("DELETE ALL DATA")
- Double confirmation for data removal
- Respects foreign key constraints during table drops

### Convenience Scripts

```bash
# Quick safe uninstall check
composer uninstall

# Quick full uninstall
composer uninstall-full
```

### Uninstall Options

- `--remove-data` - Remove all plugin tables and data (destructive)
- `--force` - Skip confirmation prompts (for automation)
- `--keep-migrations` - Keep migration records in database

## Configuration

The plugin configuration file is located at `config/real-estate.php`. Key configuration options:

```php
return [
    // Database configuration
    'database' => [
        'table_prefix' => 'real_estate_',
        'properties_table' => 'real_estate_properties',
        'use_soft_deletes' => true,
    ],

    // Search settings
    'search' => [
        'default_per_page' => 12,
        'enable_keywords_search' => true,
        'searchable_fields' => ['title', 'description', 'address', 'city'],
    ],

    // Currency settings
    'currency' => [
        'default' => 'USD',
        'supported' => ['USD', 'SGD', 'EUR', 'GBP'],
    ],

    // Admin panel configuration
    'admin' => [
        'navigation_group' => 'Real Estate',
        'navigation_sort' => 100,
    ],
];
```

## Usage

### Admin Panel

After installation, you'll have access to:

- **Properties** - Manage property listings
- **Property Types** - Manage types (Condo, HDB, Landed, etc.)
- **Districts** - Manage location districts
- **Amenities** - Manage property amenities
- **Features** - Manage property features

### Adding Property Search to Pages

1. **Via Page Builder:**
   - Edit any page in the TALL CMS admin
   - Add the "Property Search & Listings" block
   - Configure search options and display settings

2. **Via Dedicated Properties Page:**
   - The plugin automatically creates a `/properties` page
   - Features comprehensive search and filtering
   - Responsive design for all devices

### Blocks Available

#### PropertySearchBlock
Comprehensive property search interface with:
- Real-time search and filtering
- Advanced filter options
- Grid and list view modes
- Pagination with loading states

```php
// Configure in page builder or programmatically
$block = [
    'type' => 'property-search-block',
    'data' => [
        'title' => 'Find Your Dream Property',
        'properties_per_page' => 12,
        'enable_keywords_search' => true,
        'show_filters' => true,
    ]
];
```

#### PropertyListingsBlock
Display curated property listings:
- Filter by property type, price, etc.
- Customizable display options
- Featured property highlighting

#### FeaturedPropertyBlock
Showcase a single featured property:
- Large image display
- Property highlights
- Call-to-action integration

## Customization

### Views

Publish views for customization:

```bash
php artisan vendor:publish --provider="TallCms\RealEstate\RealEstateServiceProvider" --tag="real-estate-views"
```

Views will be available in `resources/views/vendor/real-estate/`

### Extending Models

All plugin models support standard Laravel relationships:

```php
use TallCms\RealEstate\Models\Property;

// Add custom relationships
class Property extends \TallCms\RealEstate\Models\Property
{
    public function customField()
    {
        return $this->hasOne(CustomField::class);
    }
}
```

### Custom Blocks

Create your own real estate blocks:

```php
use TallCms\RealEstate\Blocks\AbstractRealEstateBlock;

class CustomPropertyBlock extends AbstractRealEstateBlock
{
    public function getDisplayName(): string
    {
        return 'Custom Property Display';
    }

    // Implement required methods...
}
```

## API Integration

The plugin provides clean APIs for external integration:

```php
use TallCms\RealEstate\Models\Property;

// Search properties
$properties = Property::published()
    ->search('Marina Bay')
    ->priceRange(500000, 2000000)
    ->withBedrooms(2, 4)
    ->withTenure(['Freehold', '99-year'])
    ->paginate(12);

// Get property types
$types = PropertyType::getSelectOptions();
```

## Database Schema

The plugin creates the following tables:

- `real_estate_properties` - Main property listings
- `real_estate_property_types` - Property type categories
- `real_estate_districts` - Location districts
- `real_estate_amenities` - Available amenities
- `real_estate_features` - Property features
- Pivot tables for many-to-many relationships

## SEO Features

- **Meta tags** - Automatic meta title and description generation
- **Structured data** - JSON-LD markup for properties
- **URL structure** - Clean, SEO-friendly URLs
- **Sitemap integration** - Automatic sitemap generation

## Performance

The plugin includes several performance optimizations:

- **Query optimization** - Efficient database queries with proper indexing
- **Caching** - Configurable search result caching
- **Lazy loading** - Optimized image loading
- **Pagination** - Server-side pagination for large datasets

## Testing

Run the plugin test suite:

```bash
vendor/bin/phpunit
```

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## License

This plugin is licensed under the [MIT License](LICENSE.md).

## Support

- **Documentation:** [https://docs.tallcms.com/plugins/real-estate](https://docs.tallcms.com/plugins/real-estate)
- **GitHub Issues:** [https://github.com/tallcms/real-estate-plugin/issues](https://github.com/tallcms/real-estate-plugin/issues)
- **Community Forum:** [https://community.tallcms.com](https://community.tallcms.com)

## Changelog

### v1.0.2 (2025-11-25)
- **Current Release**
- Fixed table naming consistency (all tables now use `real_estate_` prefix)
- Added comprehensive uninstall command with safety features
- Enhanced plugin lifecycle management
- Improved data preservation during uninstalls
- Added convenience composer scripts for uninstall operations

### v1.0.1 (2024-11-25)
- Plugin registration improvements
- Table existence checks for better error handling
- Enhanced migration stability

### v1.0.0 (2024-11-25)
- Initial release
- Property management system
- Advanced search and filtering
- TALL CMS block integration
- Filament admin panel
- Real-time Livewire components
- SEO optimization
- Multi-currency support

---

**Made with ❤️ for the TALL CMS community**
