# FoodPlease

A comprehensive food management system built with Laravel 11, focusing on local-first architecture and progressive enhancement. Manage recipes, track inventory, and plan weekly meals with intelligent shopping list generation.

## Core Philosophy

- **Local-First**: SQLite database for offline capability - all data stays on your device
- **Progressive Enhancement**: Full functionality with or without JavaScript
- **Ingredient Intelligence**: Smart ingredient consolidation across recipes
- **Privacy-Focused**: No cloud dependency, no external API calls for core features

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup database
php artisan migrate:fresh --seed

# Run development environment (server + queue + logs + vite)
composer dev

# Or run individually (if using Laragon, skip php artisan serve)
php artisan serve
npm run dev
```

## Features

### Recipe Management
- Full CRUD operations with rich recipe data (prep/cook times, servings, instructions)
- Expandable ingredient lists with autocomplete for existing ingredients
- Category organization (Breakfast, Lunch, Dinner, etc.)

### Smart Ingredient System
- Ingredients are standalone reusable entities
- Same ingredient shared across multiple recipes
- Per-recipe amounts and units via pivot table
- Intelligent aggregation for shopping lists

### Weekly Meal Planning
- 7-day grid view with breakfast/lunch/dinner slots
- Multiple recipes per meal slot
- Statistics: total recipes, prep time, cook time, unique ingredients

### Shopping List Generation
- Automatic ingredient aggregation from meal plan recipes
- Unit consolidation (e.g., "2 cups flour + 1 cup flour = 3 cups flour")
- Multi-unit conflict detection
- Interactive checkboxes for purchased items

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.2)
- **Database**: SQLite
- **Frontend**: HTMX + Alpine.js + Tailwind CSS
- **Key Packages**: spatie/laravel-data, symfony/dom-crawler

## Target Users

### Home Cooks & Meal Preppers
- Plan weekly meals efficiently
- Generate smart shopping lists
- Reuse favorite recipes
- Track cooking times and servings

### Busy Families
- Organize family meal planning
- Avoid duplicate grocery purchases
- Quick recipe lookup and modification

### Diet-Conscious Individuals
- Categorize recipes by meal type
- Track ingredient usage patterns
- Inventory management for fresh ingredients

## Key Differentiators

### Ingredient Intelligence
Unlike basic meal planners, FoodPlease understands that "flour" in Recipe A is the same as "flour" in Recipe B. Shopping lists automatically consolidate duplicate ingredients, and the ingredient database grows smarter over time.

### Progressive Enhancement
Works perfectly without JavaScript for accessibility and older devices. Enhanced experience with modern browsers. No complex JavaScript frameworks - lightweight and fast.

### Local-First Design
SQLite database means no internet dependency. All data stays on user's device with fast performance and complete privacy.

## Roadmap

### Phase 1: Core Feature Completion
- Recipe photo upload
- Advanced search (filter by ingredients, cook time, category)
- Recipe scaling for different serving sizes
- Print-friendly views for meal plans and shopping lists

### Phase 2: Enhanced User Experience
- Recipe import from URLs (common recipe sites)
- Inventory expiration alerts
- Meal plan templates
- Recipe favorites/starring

### Phase 3: Advanced Features
- Nutritional integration (connect with nutrition APIs)
- Mobile PWA
- Multi-user/family support
- Export/import backup functionality

## Business Opportunities

### Potential Revenue Streams
- **Premium Features**: Advanced analytics, unlimited recipes, cloud sync
- **Integration Partnerships**: Grocery stores, meal kit services
- **Content Licensing**: Curated recipe collections, meal plan templates
- **Mobile Apps**: Paid mobile applications with enhanced features

### Market Expansion
- **B2B**: Restaurant menu planning, catering businesses
- **Education**: Nutrition courses, cooking schools
- **Healthcare**: Dietitian meal planning tools
- **Corporate**: Employee wellness meal planning programs

## Environment

- Laravel 11.x
- PHP 8.2+
- SQLite database at `database/database.sqlite`
- Development URL: http://localhost:8000 (or Laragon equivalent)

## License

[Add your license here]
