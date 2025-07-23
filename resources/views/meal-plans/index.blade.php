@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Weekly Meal Plans</h1>
            <p class="mt-2 text-sm text-gray-700">Plan your meals for the week and save to your recipe database.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button type="button" 
                    hx-get="{{ route('meal-plans.create') }}" 
                    hx-target="#meal-plan-modal" 
                    hx-indicator="#loading-indicator"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                New Meal Plan
            </button>
        </div>
    </div>

    <!-- Meal Plans List -->
    <div id="meal-plans-list" class="mt-8">
        @include('meal-plans.partials.list', ['mealPlans' => $mealPlans])
    </div>
</div>

<!-- Modal Container -->
<div id="meal-plan-modal"></div>
@endsection