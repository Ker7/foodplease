# FoodPlease - Complete Development Documentation
*Comprehensive guide for AI-assisted development*

## 🎯 Application Overview

**FoodPlease** is a comprehensive food management system built with Laravel 11, focusing on local-first architecture and progressive enhancement. The application enables users to manage recipes, track inventory, and plan weekly meals with intelligent shopping list generation.

### Core Philosophy
- **Local-First**: SQLite database for offline capability
- **Progressive Enhancement**: Full functionality with/without JavaScript
- **Fragment-First**: HTMX-powered UI updates for responsive interactions
- **Intelligent Aggregation**: Smart ingredient consolidation across recipes

---

## 🚀 Quick Start Commands

```bash
# Start development server (UNLESS using something like Laragon)
php artisan serve

# Run Vite for assets
npm run dev

# Run migrations and seed database
php artisan migrate:fresh --seed

# Test CLI commands
php artisan recipe:search chicken --json
php artisan inventory:check --expiring-soon --json
```

## 📁 File Structure & Locations

- **Models**: `app/Models/` (Recipe.php, Ingredient.php, WeeklyMealPlan.php, etc.)
- **Controllers**: `app/Http/Controllers/`
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/`
- **CLI Commands**: `app/Console/Commands/`
- **Views**: `resources/views/`
- **Routes**: `routes/web.php`
- **JavaScript**: `resources/js/app.js`
- **CSS**: `resources/css/app.css`

---

## 🏗️ System Architecture

### Tech Stack
- **Backend**: Laravel 11 (PHP 8.2)
- **Database**: SQLite (local-first approach)
- **Frontend**: HTMX + Alpine.js + Tailwind CSS
- **Pattern**: Fragment-based UI with progressive enhancement

### Database Design

#### Core Entities

**1. Recipes**
```sql
- id (primary key)
- title (string)
- source_url (nullable)
- prep_time (integer, minutes)
- cook_time (integer, minutes)
- servings (integer, default: 4)
- instructions (JSON array)
- notes (text, nullable)
- category_id (foreign key, nullable)
- timestamps
```

**2. Ingredients** (Standalone Entities)
```sql
- id (primary key)
- name (string, unique)
- default_unit (string, nullable)
- timestamps
```

**3. Recipe-Ingredient Relationships** (Many-to-Many)
```sql
recipe_ingredients:
- id (primary key)
- recipe_id (foreign key)
- ingredient_id (foreign key)
- amount (decimal, nullable)
- unit (string, nullable)
- timestamps
- unique(recipe_id, ingredient_id)
```

**4. Inventory**
```sql
- id (primary key)
- name (string)
- category (enum: fridge/pantry/freezer)
- quantity (decimal)
- unit (string)
- location (string, nullable)
- expiry_date (date, nullable)
- low_stock_threshold (decimal)
- category_id (foreign key, nullable)
- timestamps
```

**5. Weekly Meal Plans**
```sql
- id (primary key)
- name (string)
- week_start (date)
- meals (JSON) - stores day->meal_type->recipe_ids mapping
- is_active (boolean)
- category_id (foreign key, nullable)
- timestamps
```

**6. Categories**
```sql
- id (primary key)
- name (string)
- type (enum: recipe/inventory/meal_plan)
- color (string, hex color)
- timestamps
```

#### Key Relationships
- **Recipe ↔ Ingredient**: Many-to-Many with pivot data (amount, unit)
- **Recipe → Category**: Many-to-One (optional)
- **Inventory → Category**: Many-to-One (optional)
- **MealPlan → Category**: Many-to-One (optional)
- **MealPlan → Recipes**: Many-to-Many via JSON storage

---

## 🔧 Feature Implementation Status

### ✅ Completed Features

#### Recipe Management
- **Full CRUD Operations**: Create, read, update, delete recipes
- **Rich Recipe Data**: Title, source URL, prep/cook times, servings, instructions
- **Expandable Ingredient Lists**: Click to expand/collapse ingredient details
- **Category Organization**: Categorize recipes (Breakfast, Lunch, Dinner, etc.)
- **Ingredient Autocomplete**: Datalist with existing ingredients for reuse

#### Smart Ingredient System
- **Reusable Ingredients**: Each ingredient is a standalone entity
- **Many-to-Many Relationships**: Ingredients can be used in multiple recipes
- **Pivot Data Storage**: Amount and unit stored per recipe-ingredient relationship
- **Intelligent Aggregation**: Shopping lists consolidate duplicate ingredients

#### Advanced Meal Planning
- **Multi-Recipe Support**: Add multiple recipes per meal slot
- **Weekly Grid View**: 7 days × 3 meal types (breakfast/lunch/dinner)
- **Dynamic UI Management**: Dropdown disappears after adding, X buttons for removal
- **Meal Plan Statistics**: Total recipes, prep time, cook time, unique ingredients

#### Shopping List Generation
- **Automatic Aggregation**: Combines ingredients from all meal plan recipes
- **Unit Consolidation**: Sums amounts for ingredients with same units
- **Multi-Unit Detection**: Flags ingredients with conflicting units
- **Interactive Checkboxes**: Mark items as purchased

#### Progressive Enhancement
- **HTMX Integration**: Fragment-based updates without full page reloads
- **Alpine.js Enhancements**: Client-side interactivity (dropdowns, expandable sections)
- **Graceful Degradation**: All features work without JavaScript
- **Fragment Detection**: Controllers detect `HX-Request` headers for appropriate responses

---

## 💻 Development Patterns & Code Guidelines

### HTMX Response Pattern
```php
// In controller
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $item = Model::create($validated);
    
    if ($request->header('HX-Request')) {
        return view('partials.item', compact('item'));
    }
    
    return redirect()->route('items.index');
}
```

### Alpine.js Pattern
```html
<div x-data="{ expanded: false }">
    <button @click="expanded = !expanded">Toggle</button>
    <div x-show="expanded" x-collapse>Content</div>
</div>
```

### Fragment-First Design Rules
- Every form and list update uses HTMX fragments
- `HX-Request` header detection in all controllers
- Graceful fallback to full page redirects
- Target specific divs with `hx-target`
- Use `hx-trigger` for custom events

### Code Style Guidelines
- Use Laravel resource controllers
- Validate all inputs with Form Requests
- Keep controllers thin, logic in models/services
- Follow existing naming conventions
- Return appropriate views for HTMX vs full requests

### UI/UX Guidelines

#### HTMX Patterns
- Use `hx-get`, `hx-post` for dynamic updates
- Target specific divs with `hx-target`
- Use `hx-trigger` for custom events
- Loading states with `hx-indicator`

#### Tailwind Classes
- Cards: `bg-white rounded-lg shadow p-6`
- Buttons: `bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded`
- Forms: `shadow appearance-none border rounded w-full py-2 px-3`

---

## 🔄 Development Workflow

### When Creating a New Feature
1. Start with the migration (if database changes needed)
2. Define the model with relationships
3. Create controller methods with HTMX support
4. Add routes to `routes/web.php`
5. Build views with HTMX fragments
6. Test with and without JavaScript
7. Update seeders if needed

### Key Decisions Made
1. Using SQLite for local-first approach
2. HTMX for dynamic updates without heavy JS
3. Alpine.js for lightweight reactivity
4. Tailwind CSS for styling
5. JSON columns for flexible data (instructions, etc.)
6. Many-to-many ingredient relationships for reusability

### What NOT to Do
- Don't use localStorage/sessionStorage (not needed for local-first)
- Don't over-complicate with too much JavaScript
- Don't create separate API endpoints (use HTMX partials)
- Don't forget CSRF tokens in forms
- Don't use search commands like `find` and `grep` in bash (use Grep/Glob tools)

---

## 🎯 Business Model & Value Proposition

### Primary Use Cases

**1. Home Cooks & Meal Preppers**
- Plan weekly meals efficiently
- Generate smart shopping lists
- Reuse favorite recipes
- Track cooking times and servings

**2. Busy Families**
- Organize family meal planning
- Avoid duplicate grocery purchases
- Quick recipe lookup and modification
- Meal variety tracking

**3. Diet-Conscious Individuals**
- Categorize recipes by meal type
- Track ingredient usage patterns
- Plan balanced weekly nutrition
- Inventory management for fresh ingredients

### Key Differentiators

**1. Ingredient Intelligence**
- Unlike basic meal planners, FoodPlease understands that "flour" in Recipe A is the same as "flour" in Recipe B
- Shopping lists automatically consolidate duplicate ingredients
- Ingredient database grows smarter over time

**2. Progressive Enhancement**
- Works perfectly without JavaScript (accessibility/older devices)
- Enhanced experience with modern browsers
- No complex JavaScript frameworks - lightweight and fast

**3. Local-First Design**
- SQLite database means no internet dependency
- All data stays on user's device
- Fast performance with no API calls
- Privacy-focused approach

---

## 🚀 Future Enhancement Roadmap

### Phase 1: Core Feature Completion (2-4 weeks)
1. **Recipe Photo Upload**: Visual recipe identification
2. **Advanced Search**: Filter recipes by ingredients, cook time, category
3. **Recipe Scaling**: Automatically adjust ingredient amounts for different serving sizes
4. **Print-Friendly Views**: Meal plans and shopping lists optimized for printing

### Phase 2: Enhanced User Experience (4-6 weeks)
1. **Recipe Import**: Parse recipes from URLs (common recipe sites)
2. **Inventory Alerts**: Email/browser notifications for expiring items
3. **Meal Plan Templates**: Save and reuse common meal planning patterns
4. **Recipe Favorites**: Star system for frequently used recipes

### Phase 3: Advanced Features (6-8 weeks)
1. **Nutritional Integration**: Connect with nutrition APIs
2. **Mobile PWA**: Progressive Web App for mobile devices
3. **Multi-User Support**: Family/household meal planning
4. **Export/Import**: Backup and restore functionality

### Immediate Business Needs (High Impact)

**1. Recipe Import/Export**
- Import from popular recipe websites
- Export meal plans as PDF/text
- Recipe sharing between users
- Bulk recipe import from files

**2. Advanced Inventory Management**
- Barcode scanning for grocery items
- Expiration date notifications
- Low stock alerts
- Integration with shopping list

**3. Nutritional Information**
- Calorie tracking per recipe
- Nutritional breakdown by ingredient
- Dietary restriction flags (gluten-free, vegan, etc.)
- Portion size calculations

---

## 📊 Technical Implementation Details

### Current Architecture Strengths

#### Data Model Advantages
1. **Ingredient Reusability**: "Flour" exists once, used in multiple recipes
2. **Flexible Relationships**: Recipe-ingredient pivot allows per-recipe amounts/units
3. **Shopping List Intelligence**: Automatically aggregates "2 cups flour + 1 cup flour = 3 cups flour"
4. **Category System**: Spans all entity types for organization

#### UI/UX Benefits
1. **No Page Reloads**: HTMX provides seamless interactions
2. **Instant Feedback**: Ingredient additions appear immediately
3. **Smart State Management**: UI shows/hides elements appropriately
4. **Mobile Responsive**: Tailwind CSS ensures mobile compatibility

### Performance Characteristics
- **Local Database**: SQLite for instant queries
- **Fragment Updates**: HTMX reduces bandwidth usage
- **Lightweight Frontend**: No complex JavaScript frameworks
- **Responsive Design**: Mobile-first Tailwind CSS

### Current Implementation Status
- **Database Tables**: 6 core entities + 1 pivot table
- **CRUD Controllers**: 4 main controllers with fragment support
- **View Templates**: ~20 Blade templates with HTMX integration
- **JavaScript Footprint**: Minimal (Alpine.js only for enhancements)
- **Progressive Enhancement**: 100% functional without JavaScript

---

## 🧪 Testing & Quality Assurance

### Testing Data Guidelines
When creating seeders or testing:
- Use realistic recipe names
- Include common ingredients (flour, sugar, eggs, etc.)
- Test edge cases (expired items, empty inventory)
- Ensure ingredient reusability across recipes

### Testing Approach
- Feature tests for each major component
- Use Pest PHP for cleaner test syntax
- Test CLI commands separately
- Mock external recipe parsing
- Test both HTMX and non-HTMX flows

---

## 🔗 Example Code Patterns

### HTMX Form Example
```blade
<form hx-post="/recipes" hx-target="#recipe-list" hx-swap="afterbegin">
    @csrf
    <input type="text" name="title" required>
    <select name="category_id">
        @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    <button type="submit">Add Recipe</button>
</form>
```

### Ingredient Attachment Pattern
```php
// In RecipeSeeder or similar
private function attachIngredients(Recipe $recipe, array $ingredients): void
{
    $attachData = [];
    
    foreach ($ingredients as $ingredientData) {
        $ingredient = Ingredient::where('name', $ingredientData['name'])->first();
        
        if ($ingredient) {
            $attachData[$ingredient->id] = [
                'amount' => $ingredientData['amount'],
                'unit' => $ingredientData['unit']
            ];
        }
    }
    
    $recipe->ingredients()->attach($attachData);
}
```

---

## 💡 Business Model Opportunities

### Potential Revenue Streams
1. **Premium Features**: Advanced analytics, unlimited recipes, cloud sync
2. **Integration Partnerships**: Grocery stores, meal kit services
3. **Content Licensing**: Curated recipe collections, meal plan templates
4. **Mobile Apps**: Paid mobile applications with enhanced features

### Target Market Expansion
1. **B2B Opportunities**: Restaurant menu planning, catering businesses
2. **Educational Market**: Nutrition courses, cooking schools
3. **Healthcare Integration**: Dietitian meal planning tools
4. **Corporate Wellness**: Employee meal planning programs

---

## 🌟 Environment & Deployment

### Environment Details
- Laravel 11.x
- PHP 8.2
- SQLite database at `database/database.sqlite`
- Development URL: http://localhost:8000 (or Laragon equivalent)

### Key Dependencies
- **spatie/laravel-data**: DTOs for type safety
- **symfony/dom-crawler**: Recipe parsing capabilities
- **livewire/livewire**: Optional reactive components

---

*This comprehensive documentation serves as the single source of truth for FoodPlease development, combining technical implementation details, business strategy, and development guidelines for AI-assisted coding sessions.*