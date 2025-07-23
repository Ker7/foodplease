<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMealPlan;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WeeklyMealPlanController extends Controller
{
    public function index(Request $request)
    {
        $mealPlans = WeeklyMealPlan::latest()->get();
        
        if ($request->header('HX-Request')) {
            return view('meal-plans.partials.list', compact('mealPlans'));
        }
        
        return view('meal-plans.index', compact('mealPlans'));
    }

    public function create(Request $request)
    {
        $weekStart = Carbon::now()->startOfWeek();
        
        if ($request->header('HX-Request')) {
            return view('meal-plans.partials.create-form', compact('weekStart'));
        }
        
        return view('meal-plans.create', compact('weekStart'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'week_start' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $mealPlan = WeeklyMealPlan::create($validated);

        if ($request->header('HX-Request')) {
            return view('meal-plans.partials.card', compact('mealPlan'));
        }

        return redirect()->route('meal-plans.show', $mealPlan)->with('success', 'Meal plan created successfully!');
    }

    public function show(WeeklyMealPlan $mealPlan, Request $request)
    {
        $recipes = Recipe::all();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
        
        if ($request->header('HX-Request')) {
            return view('meal-plans.partials.show', compact('mealPlan', 'recipes', 'days', 'mealTypes'));
        }
        
        return view('meal-plans.show', compact('mealPlan', 'recipes', 'days', 'mealTypes'));
    }

    public function edit(WeeklyMealPlan $mealPlan, Request $request)
    {
        if ($request->header('HX-Request')) {
            return view('meal-plans.partials.edit-form', compact('mealPlan'));
        }
        
        return view('meal-plans.edit', compact('mealPlan'));
    }

    public function update(Request $request, WeeklyMealPlan $mealPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'week_start' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $mealPlan->update($validated);

        if ($request->header('HX-Request')) {
            return view('meal-plans.partials.card', compact('mealPlan'));
        }

        return redirect()->route('meal-plans.show', $mealPlan)->with('success', 'Meal plan updated successfully!');
    }

    public function destroy(WeeklyMealPlan $mealPlan, Request $request)
    {
        $mealPlan->delete();

        if ($request->header('HX-Request')) {
            return response('', 200);
        }

        return redirect()->route('meal-plans.index')->with('success', 'Meal plan deleted successfully!');
    }

    public function updateMeal(Request $request, WeeklyMealPlan $mealPlan)
    {
        $validated = $request->validate([
            'day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner',
            'recipe_id' => 'nullable|exists:recipes,id',
            'action' => 'string|in:add,remove',
            'remove_recipe_id' => 'nullable|exists:recipes,id'
        ]);

        $action = $validated['action'] ?? 'add';

        if ($action === 'remove' && $validated['remove_recipe_id']) {
            $mealPlan->removeMealForDay(
                $validated['day'],
                $validated['meal_type'],
                $validated['remove_recipe_id']
            );
        } elseif ($action === 'add' && $validated['recipe_id']) {
            $mealPlan->addMealForDay(
                $validated['day'],
                $validated['meal_type'],
                $validated['recipe_id']
            );
        }
        
        $mealPlan->save();

        if ($request->header('HX-Request')) {
            return view('meal-plans.partials.meal-slot', [
                'mealPlan' => $mealPlan,
                'day' => $validated['day'],
                'mealType' => $validated['meal_type'],
                'recipes' => Recipe::all()
            ]);
        }

        return back()->with('success', 'Meal updated successfully!');
    }
}
