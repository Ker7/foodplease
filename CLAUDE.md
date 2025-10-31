# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Application Overview

FoodPlease is a local-first recipe and meal planning system built with Laravel 11, HTMX, and Alpine.js. It uses SQLite for offline-capable data storage and focuses on intelligent ingredient aggregation across recipes.

**Core Philosophy**: Progressive enhancement with HTMX for dynamic UI, local-first with SQLite, and intelligent unit conversion for shopping list aggregation.

## Development Commands

### Starting Development
```bash
# Start all services concurrently (Laravel server, queue, logs, Vite)
composer dev

# OR start individually:
php artisan serve              # Development server (if not using Laragon)
npm run dev                    # Vite asset compilation
php artisan queue:listen       # Queue worker
php artisan pail              # Log viewer
```

### Database Operations
```bash
# Fresh migration with seed data
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name

# Run specific seeder
php artisan db:seed --class=RecipeSeeder
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Run tests
php artisan test
./vendor/bin/phpunit
```

### Building Assets
```bash
npm run dev    # Development mode with hot reload
npm run build  # Production build
```

## Architecture Overview

### Tech Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: SQLite (local-first)
- **Frontend**: HTMX 2.0 + Alpine.js 3 + Tailwind CSS
- **Build Tool**: Vite

### Key Architectural Patterns

#### 1. HTMX Fragment-First Pattern
Every controller action must support both HTMX requests and traditional full-page requests:

```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $model = Model::create($validated);

    // HTMX request returns partial view
    if ($request->header('HX-Request')) {
        return view('partials.item', compact('model'));
    }

    // Traditional request returns full redirect
    return redirect()->route('items.show', $model);
}
```

**Multi-Target Updates**: Use `hx-swap-oob` (out-of-band swaps) to update multiple page sections from a single request. See `WeeklyMealPlanController::updateMeal()` for example (lines 175-178).

#### 2. Unit Conversion System
The application has a sophisticated unit conversion system that enables intelligent ingredient aggregation:

- **Unit Model** (`app/Models/Unit.php`): Hierarchical unit system with base units (g, ml) and derived units (kg, cup, tbsp)
- **UnitConverter Service** (`app/Services/UnitConverter.php`): Handles conversions between units, including ingredient-specific overrides
- **Conversion Flow**:
  1. Convert ingredient amounts to canonical base units (g for mass, ml for volume)
  2. Aggregate amounts across recipes
  3. Convert back to preferred display units

**Key Methods**:
- `convertToCanonical()`: Convert to base unit (g, ml)
- `convertFromCanonical()`: Convert from base unit to display unit
- `getIngredientOverrideFactor()`: Handle ingredient-specific conversions (e.g., "1 cup flour ≠ 1 cup water")

#### 3. Recipe-Ingredient Relationships
Ingredients are **standalone entities** reused across recipes via a many-to-many pivot table:

- **Pivot table**: `recipe_ingredients` stores `amount`, `unit`, `unit_id`, `canonical_amount`
- **Benefits**:
  - Shopping lists automatically detect duplicate ingredients
  - Unit conversion aggregates "2 cups flour + 1 cup flour = 3 cups flour"
  - Ingredient database grows smarter over time

#### 4. Meal Plan JSON Storage
Weekly meal plans use a JSON column to store flexible meal assignments:

```php
// Structure: $meals[day][meal_type] = recipe_id
[
    'monday' => [
        'breakfast' => 1,
        'lunch' => 2,
        'dinner' => 3
    ],
    'tuesday' => [...]
]
```

Model methods handle legacy array formats for backward compatibility (see `WeeklyMealPlan::getAllRecipesWithDuplicates()`).

### Database Schema Highlights

**Core Tables**:
- `recipes`: Recipe metadata with JSON instructions
- `ingredients`: Standalone ingredient entities
- `recipe_ingredients`: Pivot with amount/unit data
- `units`: Hierarchical unit system (base_unit_id, factor)
- `ingredient_unit_overrides`: Ingredient-specific conversion factors
- `weekly_meal_plans`: JSON-based meal scheduling
- `inventory`: Pantry/fridge/freezer tracking
- `categories`: Multi-type categorization (recipe/inventory/meal_plan)

**Key Indexes**: Most foreign keys have indexes; ingredient names are unique.

## Critical Development Patterns

### Adding New HTMX Interactions

1. **Controller**: Check for `HX-Request` header
2. **View**: Create both full page view and `partials/` fragment
3. **HTMX Attributes**:
   - `hx-get/hx-post`: Endpoint
   - `hx-target`: Where to insert response
   - `hx-swap`: How to swap (innerHTML, outerHTML, afterbegin, etc.)
   - `hx-trigger`: Custom events (optional)

### Working with Units

When adding ingredients to recipes or processing shopping lists:

1. Always store `unit_id` (preferred) or `unit` slug in pivot table
2. Use `UnitConverter` service for all conversions
3. Shopping list aggregation happens in blade view (`meal-plans/partials/shopping-list.blade.php:8-169`)
4. Wrap conversions in try-catch for graceful fallback

**Unit Seeder**: `database/seeders/UnitSeeder.php` defines all available units with conversion factors

### Recipe Ingredient Attachment

```php
// Proper way to attach ingredients to recipes
$ingredient = Ingredient::where('name', 'flour')->first();
$recipe->ingredients()->attach($ingredient->id, [
    'amount' => 2,
    'unit_id' => $cupUnit->id,  // Preferred
    'unit' => 'cup'              // Fallback
]);
```

### Multi-Target HTMX Updates

When one action needs to update multiple page sections:

```php
$response = view('partials.main-content', $data)->render();
$response .= '<div hx-swap-oob="innerHTML:#sidebar">' . view('partials.sidebar', $data)->render() . '</div>';
$response .= '<div hx-swap-oob="innerHTML:#stats">' . view('partials.stats', $data)->render() . '</div>';

return response($response)->header('Content-Type', 'text/html');
```

See `WeeklyMealPlanController::updateMeal()` for production example.

## File Structure

```
app/
├── Console/Commands/       # CLI commands (if any)
├── Http/Controllers/       # HTMX-aware controllers
├── Models/                 # Eloquent models
└── Services/               # Business logic (UnitConverter, etc.)

database/
├── migrations/            # Database schema
└── seeders/              # Test data (recipes, units, ingredients)

resources/
├── views/
│   ├── *.blade.php       # Full page views
│   └── */partials/       # HTMX fragments
├── js/app.js             # Alpine.js + HTMX initialization
└── css/app.css           # Tailwind CSS

routes/
└── web.php               # All routes (resource + custom)
```

## Common Tasks

### Adding a New Feature

1. **Migration**: Define database changes
2. **Model**: Add relationships and casts
3. **Controller**: Implement CRUD with HTMX support
4. **Routes**: Add to `routes/web.php` (prefer `Route::resource()`)
5. **Views**: Create full page + partials
6. **Seeder**: Update seeders for test data

### Modifying Shopping List Aggregation

The shopping list logic is in `resources/views/meal-plans/partials/shopping-list.blade.php`:
- Lines 8-130: Ingredient aggregation with unit conversion
- Lines 135-169: Grouping by storage location (fridge/pantry/freezer)
- Uses `UnitConverter` service for canonical amount calculations

### Testing HTMX Interactions

Always test both request types:
1. **HTMX request**: Include header `HX-Request: true` - should return partial
2. **Traditional request**: No special header - should return full page or redirect

## Important Notes

- **SQLite Location**: `database/database.sqlite`
- **Asset Compilation**: Vite must be running (`npm run dev`) for changes to JS/CSS
- **CSRF Tokens**: Always include `@csrf` in forms
- **Unit Conversions**: Never manually calculate conversions; always use `UnitConverter` service
- **Ingredient Reusability**: Always check if ingredient exists before creating new one
- **JSON Columns**: `instructions` (Recipe) and `meals` (WeeklyMealPlan) are JSON-cast arrays

## Known Patterns

### Alpine.js Usage
```html
<div x-data="{ expanded: false }">
    <button @click="expanded = !expanded">Toggle</button>
    <div x-show="expanded" x-collapse>Content</div>
</div>
```

### HTMX Loading States
```html
<button hx-post="/endpoint"
        hx-indicator="#spinner"
        hx-disabled-elt="this">
    Submit
</button>
<div id="spinner" class="htmx-indicator">Loading...</div>
```

## Debugging

- **Laravel Debugbar**: Available in development (`barryvdh/laravel-debugbar`)
- **Telescope**: Advanced debugging (`laravel/telescope`)
- **Pail**: Real-time log viewing (`php artisan pail`)
- **Debug Mode**: Shopping list shows debug info when `APP_DEBUG=true` (line 173-184 of shopping-list.blade.php)

## Recent Improvements

Based on recent commits:
- Unit aggregation system with conversion factors
- Multi-recipe meal planning with proper duplicate handling
- Shopping list groups ingredients by storage location (fridge/pantry/freezer)
- Meal plan overview with total prep/cook times and ingredient counts
- Remove recipe functionality from meal plans with UI cleanup
