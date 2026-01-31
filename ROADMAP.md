# FoodPlease Development Roadmap

**Last Updated**: January 2025
**Current Status**: 70-75% Feature Complete (Functional Beta)

## Project Overview

FoodPlease is a local-first recipe and meal planning system built with Laravel 11, HTMX, Alpine.js, and SQLite. The application focuses on intelligent ingredient aggregation, unit conversion, and offline-capable meal planning for personal use.

---

## Current State Summary

### ✅ Fully Implemented Features

- **Recipe Management**: Complete CRUD with HTMX integration, ingredient management, time tracking
- **Weekly Meal Planning**: JSON-based flexible meal assignment, multi-target HTMX updates, recipe removal
- **Shopping List Generation**: Automatic ingredient aggregation, smart unit conversion, storage location grouping
- **Unit Conversion System**: Hierarchical units (mass/volume/count), ingredient-specific overrides, canonical amounts
- **Inventory Management**: Pantry/fridge/freezer tracking, expiry warnings, low stock alerts
- **Categories System**: Database schema for multi-type categorization (recipes, inventory, meal plans)

### 🚧 In Progress

- None currently

### ✅ Recently Completed

- **Meal Plan Recipe Management v1**: Fixed dropdown placeholder selection, first recipe now selectable. UI polish (button sizes, spinners) deferred to v2

### ❌ Not Yet Implemented

- User authentication & multi-user support
- Recipe search & advanced filtering
- Meal plan templates & duplication
- Recipe import from URLs
- Photo uploads for recipes
- Shopping list export/sharing
- Dietary tags & restrictions
- Inventory-aware shopping lists
- Leftover tracking
- Test coverage

---

## Development Phases

## Phase 1: Daily Usability (Priority: HIGH)
**Goal**: Make the app genuinely useful for daily meal planning
**Timeline**: 1-2 weeks

### 1.1 Recipe Search & Filtering 🔍
**Why**: Unusable with 100+ recipes without search
**Effort**: Medium (2-3 days)

**Tasks**:
- [ ] Add search bar to `recipes/index.blade.php`
- [ ] Implement text search in `RecipeController::index()`
- [ ] Add filters: prep time (<30min, <60min), cook time
- [ ] Add ingredient-based search ("find recipes using chicken")
- [ ] HTMX integration for live filtering
- [ ] Add "quick meals" badge for recipes under 30min total time

**Technical Notes**:
- Use Laravel query scopes on Recipe model
- HTMX partial update for `recipes/partials/list.blade.php`
- Consider full-text search on recipe title + notes fields

---

### 1.2 Meal Plan Duplication & Templates 📋
**Why**: Huge time-saver for recurring weekly patterns
**Effort**: Medium (2-3 days)

**Tasks**:
- [ ] Add "Duplicate This Week" button to meal plan show page
- [ ] Implement `WeeklyMealPlanController::duplicate()` method
- [ ] Create meal plan template system (flag `is_template` on weekly_meal_plans)
- [ ] Add "Save as Template" button
- [ ] Add "Load from Template" modal on meal plan create page
- [ ] UI for template library

**Technical Notes**:
- Clone meals JSON and adjust week_start date
- Templates should have `is_template = true` and `week_start = null`
- Consider adding template_name field for better organization

---

### 1.3 Shopping List Export 📱
**Why**: Used weekly, current flow requires screenshots
**Effort**: Low (1 day)

**Tasks**:
- [ ] Add "Copy to Clipboard" button to shopping list partial
- [ ] JavaScript function to format list as plain text
- [ ] Optional: Generate shareable URL (store list in cache with UUID)
- [ ] Optional: Email/SMS integration
- [ ] Optional: Print-friendly CSS styles

**Technical Notes**:
- Use Clipboard API: `navigator.clipboard.writeText()`
- Format: "• 2 cups flour (Pantry)\n• 1 lb chicken (Fridge)"
- Consider markdown or plain text format

---

### 1.4 Recipe Quick Tags 🏷️
**Why**: Enables filtering for "quick vegetarian dinners"
**Effort**: Medium (2 days)

**Tasks**:
- [ ] Add `tags` JSON column to recipes table (migration)
- [ ] Add tag input to recipe create/edit forms (multi-select or autocomplete)
- [ ] Predefined tags: quick (<30min), vegetarian, vegan, gluten-free, dairy-free, kid-friendly, one-pot, batch-cook
- [ ] Add tag filter to recipe index
- [ ] Display tags as badges on recipe cards
- [ ] Tag-based recipe scopes on Recipe model

**Technical Notes**:
- JSON column: `['vegetarian', 'quick', 'one-pot']`
- Use Alpine.js for tag input UI (similar to ingredient management)
- Consider using Select2 or Choices.js for better UX

---

## Phase 2: Recipe Collection Building (Priority: HIGH)
**Goal**: Make it easy to build a large recipe database
**Timeline**: 1-2 weeks

### 2.1 Recipe Import from URL 🌐
**Why**: Manual entry is tedious; this is the bottleneck
**Effort**: High (3-5 days)

**Tasks**:
- [ ] Research recipe scraping libraries (consider `recipe-scrapers` Python package or build PHP scraper)
- [ ] Add URL input field to recipe create form
- [ ] Implement `RecipeImportController` (currently stub)
- [ ] Parse common recipe sites: AllRecipes, NYT Cooking, Food Network, etc.
- [ ] Extract: title, ingredients, instructions, prep/cook time, servings, images
- [ ] Map ingredients to existing database or create new ones
- [ ] Handle unit conversion during import
- [ ] Error handling for unsupported sites

**Technical Notes**:
- Consider using Guzzle for HTTP requests
- Use DOMDocument/DOMXPath for HTML parsing
- Many sites use JSON-LD schema.org/Recipe markup (easy to parse!)
- Fallback to OpenGraph tags for images
- Queue job for import to prevent timeout

---

### 2.2 Recipe Photos 📸
**Why**: Visual browsing is more inspiring than text lists
**Effort**: Low-Medium (1-2 days)

**Tasks**:
- [ ] Add `image_path` column to recipes table (migration)
- [ ] Add image upload field to recipe create/edit forms
- [ ] Store images in `storage/app/public/recipes/`
- [ ] Create symbolic link: `php artisan storage:link`
- [ ] Display thumbnails on recipe cards
- [ ] Display full image on recipe show page
- [ ] Optional: Support image URL (don't require upload)
- [ ] Optional: Image optimization (resize, compress)

**Technical Notes**:
- Use Laravel's `Storage` facade
- Validate file types: jpg, png, webp
- Consider using Intervention Image for thumbnails
- Default placeholder image if none provided

---

### 2.3 Recipe Notes & Source Tracking
**Why**: Remember where recipes came from, add personal tweaks
**Effort**: Low (already partially implemented)

**Tasks**:
- [x] `source_url` field exists in database
- [x] `notes` field exists in database
- [ ] Improve notes UI (larger textarea, markdown support?)
- [ ] Add "Source" link display on recipe show page
- [ ] Add "My Notes" section with prominent display

---

## Phase 3: Smart Features (Priority: MEDIUM)
**Goal**: Move from planning tool to decision assistant
**Timeline**: 2-3 weeks

### 3.1 Inventory-Aware Shopping Lists 🔄
**Why**: Prevents buying what you already own
**Effort**: Medium-High (3-4 days)

**Tasks**:
- [ ] Add checkbox to shopping list: "Check inventory"
- [ ] Cross-reference shopping list with inventory table
- [ ] Highlight/strike-through items already in stock
- [ ] Show inventory quantity vs. needed quantity
- [ ] "Add to inventory" quick button from shopping list
- [ ] Subtract from inventory when meal marked "cooked"

**Technical Notes**:
- Match by ingredient name (case-insensitive)
- Unit conversion needed: inventory in "lb", recipe needs "oz"
- Consider fuzzy matching for ingredient names

---

### 3.2 Leftover Tracking ♻️
**Why**: Real-world usage creates partial ingredients
**Effort**: Medium (2-3 days)

**Tasks**:
- [ ] Add `cooked_at` timestamp to meal plan recipe assignments
- [ ] "Mark as Cooked" button on meal plan
- [ ] Automatically add leftovers to inventory (scaled by servings)
- [ ] Track ingredient depletion over time
- [ ] "Leftover suggestions" on dashboard

**Technical Notes**:
- Calculate leftover amounts: recipe amount - (servings made / servings in recipe)
- Trigger inventory updates via queue job

---

### 3.3 Smart Meal Suggestions 💡
**Why**: Reduces decision fatigue
**Effort**: Medium (2-3 days)

**Tasks**:
- [ ] Dashboard widget: "Recipes using expiring ingredients"
- [ ] "Quick meals for tonight" (under 30min total time)
- [ ] "What can I make with current inventory?"
- [ ] "Complete the week" suggestions (fill empty meal slots)
- [ ] Consider dietary preferences/restrictions in suggestions

**Technical Notes**:
- Query recipes where ingredients overlap with expiring inventory
- Use recipe scopes: `quickMeals()`, `underXMinutes($minutes)`
- Cache suggestions for performance

---

### 3.4 Batch Cooking & Scaling
**Why**: Make larger quantities, freeze portions
**Effort**: Low-Medium (1-2 days)

**Tasks**:
- [ ] Servings multiplier on recipe show page (Alpine.js)
- [ ] Dynamically scale ingredient amounts
- [ ] "Make 2x" or "Make 3x" quick buttons
- [ ] Track batch-cooked portions in inventory

---

## Phase 4: Polish & Sharing (Priority: LOW)
**Goal**: Multi-user support and mobile experience
**Timeline**: 2-4 weeks (optional)

### 4.1 Multi-User Support 👥
**Why**: Share with family/household
**Effort**: High (4-5 days)

**Tasks**:
- [ ] Implement Laravel Breeze or Jetstream
- [ ] Add `user_id` to recipes, meal_plans, inventory tables
- [ ] Migration to assign existing data to default user
- [ ] Add authentication middleware to routes
- [ ] Household/team concept (shared recipes, individual inventories)
- [ ] Permission system (owner, editor, viewer)

**Technical Notes**:
- Skip if solo use only
- Consider multi-tenancy package (Spatie Multitenancy?)
- Separate inventories per user, shared recipes

---

### 4.2 Mobile PWA 📱
**Why**: Access shopping lists on phone without browser
**Effort**: Medium (2-3 days)

**Tasks**:
- [ ] Create `manifest.json` for PWA
- [ ] Add service worker for offline caching
- [ ] Install prompt on mobile browsers
- [ ] Optimize UI for mobile (responsive improvements)
- [ ] Offline shopping list access

**Technical Notes**:
- Use Workbox or Laravel PWA package
- Cache API responses for offline
- Sync data when connection restored

---

### 4.3 Nutrition Tracking 📊
**Why**: Health-conscious meal planning
**Effort**: High (4-5 days)

**Tasks**:
- [ ] Add nutrition fields to ingredients (calories, protein, carbs, fat, fiber)
- [ ] Calculate per-recipe nutrition (sum ingredients)
- [ ] Display nutrition facts on recipe show page
- [ ] Weekly nutrition overview on meal plan
- [ ] Nutritional goals tracking

**Technical Notes**:
- Use USDA FoodData Central API for ingredient lookup
- Per-100g nutrition data
- Scale by recipe amounts

---

### 4.4 Social/Sharing Features
**Why**: Share recipes with friends/family
**Effort**: Medium-High (3-4 days)

**Tasks**:
- [ ] Public recipe URLs (optional auth bypass)
- [ ] Generate shareable recipe cards (image export)
- [ ] Export recipe as PDF
- [ ] "Share via email" button
- [ ] Recipe rating/favorites system

---

## Technical Debt & Maintenance

### High Priority

- [ ] **Write tests for UnitConverter** - Most complex business logic; bugs break shopping lists
- [ ] **Remove old Livewire components** - Migrated to HTMX; delete unused files
- [ ] **Add error handling for HTMX requests** - Graceful fallback for network errors
- [ ] **Database indexes** - Add indexes on frequently queried fields (recipe.title, ingredient.name)
- [ ] **RecipeImportController stub** - Remove or implement

### Medium Priority

- [ ] **API documentation** - Document HTMX endpoints for future reference
- [ ] **Code comments** - Add docblocks to complex methods (UnitConverter especially)
- [ ] **Logging** - Add structured logging for debugging (Monolog channels)
- [ ] **Security audit** - CSRF tokens, SQL injection prevention, XSS protection

### Low Priority

- [ ] **Performance optimization** - Query optimization, eager loading relationships
- [ ] **Code linting** - Set up Pint configuration for consistent formatting
- [ ] **Dependency updates** - Keep Laravel, HTMX, Alpine.js up to date

---

## Testing Strategy

### Unit Tests (Priority: HIGH)
- [ ] `UnitConverter::convertToCanonical()` with various units
- [ ] `UnitConverter::convertFromCanonical()` edge cases
- [ ] `UnitConverter::convert()` cross-unit conversions
- [ ] Ingredient-specific override factor tests
- [ ] Recipe model methods (getAllRecipesWithDuplicates, etc.)

### Feature Tests (Priority: MEDIUM)
- [ ] Recipe CRUD operations (create, read, update, delete)
- [ ] Meal plan CRUD with JSON meal storage
- [ ] Shopping list aggregation accuracy
- [ ] HTMX partial responses vs. full page requests
- [ ] Inventory expiry date calculations

### Browser Tests (Priority: LOW)
- [ ] HTMX interactions (meal slot updates, ingredient forms)
- [ ] Alpine.js component behavior
- [ ] Multi-target HTMX updates

---

## Metrics & Success Criteria

**For Daily Personal Use:**
- [ ] Can add recipe in <2 minutes (with import)
- [ ] Can plan full week in <5 minutes (with templates)
- [ ] Shopping list generation is accurate (no missing ingredients)
- [ ] Unit conversions are correct (tested with real recipes)
- [ ] Can find any recipe in <10 seconds (search works)

**For Public Release:**
- [ ] Test coverage >70%
- [ ] No critical security vulnerabilities
- [ ] Multi-user support with data isolation
- [ ] Mobile-responsive on all pages
- [ ] Documentation for setup and usage

---

## Known Issues & Bugs

**Current Issues:**
- None logged (add as discovered)

**Future Considerations:**
- Meal plan date range validation (prevent overlapping weeks)
- Ingredient name normalization (handle "flour" vs "all-purpose flour")
- Unit compatibility checks (prevent "2 cups of chicken")
- Recipe versioning (track changes over time)

---

## Resources & References

**Documentation:**
- Laravel 11: https://laravel.com/docs/11.x
- HTMX 2.0: https://htmx.org/docs/
- Alpine.js 3: https://alpinejs.dev/
- Tailwind CSS: https://tailwindcss.com/

**Inspiration & Similar Apps:**
- Paprika Recipe Manager
- Mealime
- Plan to Eat
- Cooklist

**Recipe Scraping:**
- schema.org/Recipe specification
- Open Recipe Format
- Python recipe-scrapers library

---

## Decision Log

**Why HTMX over Livewire?**
- Simpler mental model, less server state
- Better for local-first architecture
- Smaller payload sizes

**Why SQLite over MySQL/PostgreSQL?**
- Local-first philosophy
- No server setup required
- Fast for single-user workloads
- Easy backups (single file)

**Why standalone Ingredients table?**
- Enables intelligent aggregation
- Shopping list automatically detects duplicates
- Ingredient database grows smarter over time

**Why JSON for meal plans?**
- Flexible schema (easy to add meal types)
- No complex join queries needed
- Backward compatible with simple updates

---

## Getting Started for Contributors

1. Review `CLAUDE.md` for architecture overview
2. Run `php artisan migrate:fresh --seed` for test data
3. Start development: `composer dev`
4. Check this roadmap for current priorities
5. Write tests before implementing features
6. Follow HTMX fragment-first pattern for all new features

---

**Last Updated**: January 2025
**Maintained By**: FoodPlease Development Team
