@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ $mealPlan->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Week of {{ $mealPlan->week_start->format('M j, Y') }} - {{ $mealPlan->week_end->format('M j, Y') }}
                @if($mealPlan->is_active)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active Plan
                    </span>
                @endif
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <button type="button" 
                    hx-get="{{ route('meal-plans.edit', $mealPlan) }}" 
                    hx-target="#meal-plan-modal"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Edit Plan
            </button>
        </div>
    </div>

    <!-- Meal Plan Statistics -->
    <div id="meal-plan-overview">
        @include('meal-plans.partials.overview', ['mealPlan' => $mealPlan])
    </div>

    <!-- Weekly Grid -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Weekly Schedule</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Plan your meals for each day of the week</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-7 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                @foreach($days as $day)
                    <div class="p-4">
                        <h4 class="text-sm font-medium text-gray-900 capitalize mb-3">{{ $day }}</h4>
                        <div class="space-y-2">
                            @foreach($mealTypes as $mealType)
                                @include('meal-plans.partials.meal-slot', [
                                    'day' => $day,
                                    'mealType' => $mealType,
                                    'recipes' => $recipes,
                                    'mealPlan' => $mealPlan
                                ])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Shopping List Section -->
    <div id="shopping-list">
        @include('meal-plans.partials.shopping-list', ['mealPlan' => $mealPlan])
    </div>
</div>

<!-- Modal Container -->
<div id="meal-plan-modal"></div>
@endsection