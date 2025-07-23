<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <a href="{{ route('recipes.show', $recipe) }}" class="hover:text-blue-600">
                    {{ $recipe->title }}
                </a>
            </h3>
            <div class="flex space-x-2">
                <button type="button" 
                        hx-get="{{ route('recipes.edit', $recipe) }}" 
                        hx-target="#recipe-modal"
                        class="text-blue-600 hover:text-blue-900">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button type="button" 
                        hx-delete="{{ route('recipes.destroy', $recipe) }}" 
                        hx-target="closest .bg-white"
                        hx-swap="outerHTML"
                        hx-confirm="Are you sure you want to delete this recipe?"
                        class="text-red-600 hover:text-red-900">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
        
        @if($recipe->source_url)
            <p class="mt-1 text-sm text-gray-500">
                <a href="{{ $recipe->source_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                    View Source
                </a>
            </p>
        @endif
        
        <div class="mt-4 flex items-center text-sm text-gray-500 space-x-4">
            @if($recipe->prep_time)
                <span class="flex items-center">
                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Prep: {{ $recipe->prep_time }}min
                </span>
            @endif
            
            @if($recipe->cook_time)
                <span class="flex items-center">
                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    </svg>
                    Cook: {{ $recipe->cook_time }}min
                </span>
            @endif
            
            @if($recipe->servings)
                <span class="flex items-center">
                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Serves {{ $recipe->servings }}
                </span>
            @endif
        </div>
        
        @if($recipe->ingredients && $recipe->ingredients->count() > 0)
            <div class="mt-4" x-data="{ expanded: false }">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full text-left">
                    <h4 class="text-sm font-medium text-gray-900">Ingredients ({{ $recipe->ingredients->count() }})</h4>
                    <svg class="h-4 w-4 text-gray-500 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="!expanded" class="mt-2">
                    <div class="flex flex-wrap gap-1">
                        @foreach($recipe->ingredients->take(3) as $ingredient)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $ingredient->name }}
                            </span>
                        @endforeach
                        @if($recipe->ingredients->count() > 3)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600">
                                +{{ $recipe->ingredients->count() - 3 }} more
                            </span>
                        @endif
                    </div>
                </div>
                
                <div x-show="expanded" x-collapse class="mt-2">
                    <div class="space-y-1">
                        @foreach($recipe->ingredients as $ingredient)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-900">{{ $ingredient->name }}</span>
                                @if($ingredient->pivot && ($ingredient->pivot->amount || $ingredient->pivot->unit))
                                    @php
                                        $amount = $ingredient->pivot->amount ? number_format($ingredient->pivot->amount, 2) : '';
                                        $unit = $ingredient->pivot->unit ?: '';
                                        $formattedAmount = trim($amount . ($unit ? ' ' . $unit : ''));
                                    @endphp
                                    @if($formattedAmount)
                                        <span class="text-gray-500">{{ $formattedAmount }}</span>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>