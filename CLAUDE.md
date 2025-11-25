# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the **TALL CMS Real Estate Plugin** - a comprehensive Laravel package that provides real estate property management and search functionality for the TALL CMS platform. The plugin integrates with Filament for admin management and Livewire for real-time frontend components.

### Key Technologies
- **Laravel 10/11/12** - Core framework
- **Livewire 3** - Real-time frontend components
- **Filament 3** - Admin panel resources and forms
- **TALL CMS** - Content management system integration
- **Spatie Laravel Package Tools** - Package structure and service provider

## Development Commands

### Testing
```bash
# Run the test suite
composer test
# OR
vendor/bin/phpunit

# Run tests with coverage report
composer test-coverage
# OR
vendor/bin/phpunit --coverage-html coverage
```

### Plugin Installation & Management
```bash
# Install the plugin (run after composer install in host project)
php artisan real-estate:install

# Install with demo data
php artisan real-estate:install --demo

# Safe reset of plugin tables only
php artisan real-estate:install --fresh

# Verify plugin installation
php artisan real-estate:verify
```

### Database Management
```bash
# Run migrations
php artisan migrate

# Seed reference data only
php artisan db:seed --class="TallCms\RealEstate\Database\Seeders\RealEstateReferenceDataSeeder"

# Seed demo properties
php artisan db:seed --class="TallCms\RealEstate\Database\Seeders\PropertySeeder"
```

### Plugin Uninstall
```bash
# Safe uninstall (recommended) - removes code, preserves data
composer remove tallcms/real-estate-plugin

# Check what would be removed before uninstalling
php artisan real-estate:uninstall

# Full uninstall with data removal (DESTRUCTIVE)
php artisan real-estate:uninstall --remove-data

# Automated uninstall (skip confirmations)
php artisan real-estate:uninstall --remove-data --force
```

## Architecture Overview

### Core Architecture
This plugin follows Laravel package development patterns with these key architectural components:

1. **Service Provider Registration** (`src/RealEstateServiceProvider.php`):
   - Registers Livewire components
   - Registers Filament admin resources
   - Registers custom blocks with TALL CMS
   - Includes safety checks for table existence before registration

2. **Plugin Class** (`src/RealEstatePlugin.php`):
   - Implements Filament's Plugin interface
   - Manages admin resource registration
   - Provides fluent configuration API

3. **Model Relationships**:
   - `Property` model is central with relationships to PropertyType, District, Amenities, Features
   - Uses pivot tables for many-to-many relationships (property_amenities, property_features)
   - Supports both legacy fields and new normalized relationships

4. **Admin Resources** (Filament):
   - Complete CRUD for Properties, PropertyTypes, Districts, Amenities, Features
   - Located in `src/Resources/` with separate page classes

5. **Frontend Components**:
   - `PropertySearchComponent` (Livewire) - Real-time property search and filtering
   - Custom blocks for TALL CMS page builder integration

### Database Schema Architecture
- **Properties table**: Main entity with backward compatibility for legacy fields
- **Reference tables**: `real_estate_property_types`, `real_estate_districts`, `real_estate_amenities`, `real_estate_features`
- **Pivot tables**: Many-to-many relationships between properties and amenities/features
- **Migration strategy**: Progressive enhancement supporting both legacy and new normalized data

### Block System Integration
The plugin provides three blocks for TALL CMS:
- `PropertySearchBlock` - Advanced search interface
- `PropertyListingsBlock` - Curated property displays  
- `FeaturedPropertyBlock` - Single property showcase

## Configuration

### Main Config File
Configuration is managed in `config/real-estate.php` with these key sections:
- **Database**: Table naming and soft deletes
- **Search**: Default pagination, searchable fields, sorting options
- **Currency**: Multi-currency support with formatting
- **Admin**: Filament resource configuration
- **Blocks**: TALL CMS block registration
- **SEO**: Meta fields and URL structure
- **Performance**: Caching and optimization settings

### Key Configuration Points
- Default properties per page: 12
- Supported currencies: USD, SGD, EUR, GBP, AUD, CAD
- Property images stored in `public/properties` by default
- Admin navigation grouped under "Real Estate"

## Important Implementation Details

### Safety Mechanisms
The plugin includes table existence checks (`pluginTablesExist()`) to prevent errors during registration before migrations are run. This is critical for proper plugin initialization.

### Backward Compatibility
The Property model maintains both legacy fields (`amenities`, `features` as JSON) and new normalized relationships to ensure smooth transitions for existing installations.

### Livewire Integration
Real-time search functionality is powered by `PropertySearchComponent` with advanced filtering capabilities including price ranges, bedroom counts, districts, and tenure types.

### Package Structure
- `src/` - Main PHP classes
- `database/` - Migrations and seeders  
- `resources/views/` - Blade templates for blocks and Livewire components
- `routes/web.php` - Frontend routes
- `config/real-estate.php` - Plugin configuration

## Release Management

### **CRITICAL: Version Tagging Workflow**
This plugin is distributed via Packagist/Composer. **ALWAYS** follow this workflow when pushing changes to main:

1. **Update composer.json version** (currently at `1.0.2`)
2. **Create and push git tag** matching the composer.json version
3. **Push to main branch**

```bash
# Example release workflow for version 1.0.3:
# 1. Update version in composer.json
# 2. Commit changes
git add composer.json
git commit -m "Bump version to 1.0.3"

# 3. Create and push tag
git tag v1.0.3
git push origin main --tags

# 4. Packagist will automatically detect the new tag
```

### Version Numbering
- **Patch** (1.0.x): Bug fixes, small improvements
- **Minor** (1.x.0): New features, backward compatible
- **Major** (x.0.0): Breaking changes

## Development Notes

### When Adding New Features
1. Consider backward compatibility with existing Property data
2. Update both the model fillable arrays and admin resources
3. Add appropriate validation rules in Filament resources
4. Update search functionality if new searchable fields are added
5. **Don't forget to bump version and tag before pushing to main**

### Database Changes
- Always create new migrations rather than editing existing ones
- Use the established table prefix pattern (`real_estate_`)
- Maintain the migration naming convention established in the service provider

### Block Development
New blocks should extend the AbstractBlock class and be registered in the service provider's `registerPropertyBlocks()` method.

### Plugin Uninstall Workflow
The plugin provides safe uninstall options:

1. **Safe Uninstall (Default)**: Only removes code, preserves all data
   ```bash
   composer remove tallcms/real-estate-plugin
   ```

2. **Full Uninstall**: Removes code AND data (requires explicit confirmation)
   ```bash
   php artisan real-estate:uninstall --remove-data
   ```

3. **Uninstall Features**:
   - Shows current data summary before uninstall
   - Requires explicit confirmation for data removal
   - Drops tables in correct dependency order
   - Cleans migration records
   - Provides `--force` flag for automation

### Pre-Push Checklist
- [ ] Update `composer.json` version number
- [ ] Test the plugin thoroughly
- [ ] Update CHANGELOG.md if it exists
- [ ] Commit version bump
- [ ] Create and push git tag
- [ ] Push to main branch