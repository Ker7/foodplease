<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($mealPlans as $mealPlan)
        @include('meal-plans.partials.card', ['mealPlan' => $mealPlan])
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4H8zM8 7h8v10a1 1 0 01-1 1H9a1 1 0 01-1-1V7z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No meal plans</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first weekly meal plan.</p>
            <div class="mt-6">
                <button type="button" 
                        hx-get="{{ route('meal-plans.create') }}" 
                        hx-target="#meal-plan-modal"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Meal Plan
                </button>
            </div>
        </div>
    @endforelse
</div>