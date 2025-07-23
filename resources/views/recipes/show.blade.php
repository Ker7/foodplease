@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ $recipe->title }}
            </h2>
            @if($recipe->source_url)
                <p class="mt-1 text-sm text-gray-500">
                    <a href="{{ $recipe->source_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        View Original Recipe
                    </a>
                </p>
            @endif
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <button type="button" 
                    hx-get="{{ route('recipes.edit', $recipe) }}" 
                    hx-target="#recipe-modal"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Edit Recipe
            </button>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Recipe Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recipe Details</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        @if($recipe->prep_time || $recipe->cook_time)
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Cooking Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    @if($recipe->prep_time)
                                        Prep: {{ $recipe->prep_time }} minutes
                                    @endif
                                    @if($recipe->prep_time && $recipe->cook_time) • @endif
                                    @if($recipe->cook_time)
                                        Cook: {{ $recipe->cook_time }} minutes
                                    @endif
                                    @if($recipe->total_time)
                                        • Total: {{ $recipe->total_time }} minutes
                                    @endif
                                </dd>
                            </div>
                        @endif
                        
                        @if($recipe->servings)
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Servings</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $recipe->servings }}</dd>
                            </div>
                        @endif
                        
                        @if($recipe->instructions)
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Instructions</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <ol class="list-decimal list-inside space-y-2">
                                        @foreach($recipe->instructions as $instruction)
                                            <li>{{ $instruction }}</li>
                                        @endforeach
                                    </ol>
                                </dd>
                            </div>
                        @endif
                        
                        @if($recipe->notes)
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $recipe->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Ingredients -->
        <div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Ingredients</h3>
                    <button type="button" 
                            hx-get="{{ route('recipes.ingredients.create', $recipe) }}" 
                            hx-target="#ingredient-form"
                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add Ingredient
                    </button>
                </div>
                <div class="border-t border-gray-200">
                    <div id="ingredient-form"></div>
                    <div id="ingredients-list">
                        @forelse($recipe->ingredients as $ingredient)
                            @include('recipes.partials.ingredient', ['ingredient' => $ingredient])
                        @empty
                            <div class="px-4 py-5 text-center text-gray-500">
                                No ingredients added yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="recipe-modal"></div>
@endsection