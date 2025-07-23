<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <a href="{{ route('meal-plans.show', $mealPlan) }}" class="hover:text-blue-600">
                    {{ $mealPlan->name }}
                </a>
            </h3>
            <div class="flex space-x-2">
                @if($mealPlan->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active
                    </span>
                @endif
                <button type="button" 
                        hx-get="{{ route('meal-plans.edit', $mealPlan) }}" 
                        hx-target="#meal-plan-modal"
                        class="text-blue-600 hover:text-blue-900">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button type="button" 
                        hx-delete="{{ route('meal-plans.destroy', $mealPlan) }}" 
                        hx-target="closest .bg-white"
                        hx-swap="outerHTML"
                        hx-confirm="Are you sure you want to delete this meal plan?"
                        class="text-red-600 hover:text-red-900">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="mt-2 flex items-center text-sm text-gray-500">
            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4H8zM8 7h8v10a1 1 0 01-1 1H9a1 1 0 01-1-1V7z" />
            </svg>
            Week of {{ $mealPlan->week_start->format('M j, Y') }}
        </div>
        
        @if($mealPlan->meals && count($mealPlan->meals) > 0)
            <div class="mt-4">
                <div class="text-sm text-gray-600">
                    {{ count(collect($mealPlan->meals)->flatten()) }} meals planned
                </div>
                <div class="mt-2 grid grid-cols-7 gap-1">
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <div class="text-center">
                            <div class="text-xs font-medium text-gray-500 uppercase">{{ substr($day, 0, 3) }}</div>
                            <div class="mt-1">
                                @if(isset($mealPlan->meals[$day]) && count($mealPlan->meals[$day]) > 0)
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-blue-600">{{ count($mealPlan->meals[$day]) }}</span>
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-gray-100 rounded-full"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>