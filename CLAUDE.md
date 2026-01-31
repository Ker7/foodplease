# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

```bash
# Full dev environment (server + queue + logs + vite) - runs all concurrently
composer dev

# Individual commands
php artisan serve          # Start development server (not needed with Laragon)
npm run dev                # Run Vite for asset compilation
npm run build              # Build production assets

# Database
php artisan migrate:fresh --seed    # Reset and seed database

# Testing
php artisan test                    # Run all tests
php artisan test --filter=TestName  # Run specific test
php artisan test tests/Feature/ExampleTest.php  # Run specific file

# Code quality
./vendor/bin/pint          # Run Laravel Pint code formatter

# Debugging
php artisan pail           # Tail application logs
```

## Architecture Overview

**Stack**: Laravel 11 + SQLite + HTMX + Alpine.js + Tailwind CSS

This is a local-first application using SQLite (`database/database.sqlite`) for offline capability. The UI follows a fragment-first design using HTMX for partial page updates without full reloads.

### Core Entities & Relationships

- **Recipe** ↔ **Ingredient**: Many-to-Many via `recipe_ingredients` pivot (stores `amount`, `unit`, `unit_id`, `canonical_amount`)
- **Ingredient** → **Unit**: BelongsTo (default unit)
- **Ingredient** ↔ **Unit**: Many-to-Many via `ingredient_unit_overrides` (custom conversion factors)
- **WeeklyMealPlan**: Stores meals as JSON (`day → meal_type → recipe_ids[]`)

### HTMX Response Pattern

All controllers must detect HTMX requests and return appropriate responses:

```php
public function store(Request $request)
{
    $item = Model::create($request->validated());

    if ($request->header('HX-Request')) {
        return view('partials.item', compact('item'));  // Fragment
    }

    return redirect()->route('items.index');  // Full page
}
```

### Key Routes

- `GET /` → RecipeController@index (home)
- Resource routes: `/recipes`, `/inventory`, `/meal-plans`
- Nested ingredient management: `/recipes/{recipe}/ingredients/*`

## Critical Development Rules

1. **Always check `HX-Request` header** in controllers - return fragments for HTMX, redirects for standard requests
2. **Don't create separate API endpoints** - use HTMX partials for dynamic updates
3. **Don't use localStorage/sessionStorage** - not needed for local-first SQLite approach
4. **Include CSRF tokens** in all forms (`@csrf`)
5. **Ingredient reusability** - Ingredients are standalone entities; use the pivot table for per-recipe amounts/units
6. **JSON columns** - `instructions` in recipes and `meals` in meal plans are JSON arrays

## File Organization

- **Models**: `app/Models/` - Recipe, Ingredient, Inventory, WeeklyMealPlan, Category, Unit
- **Controllers**: `app/Http/Controllers/` - Resource controllers with HTMX support
- **CLI Commands**: `app/Console/Commands/` - Recipe search, inventory check, import, shopping list
- **Views**: `resources/views/` - Blade templates with HTMX attributes
- **Frontend**: `resources/js/app.js` (Alpine.js + HTMX), `resources/css/app.css` (Tailwind)
