<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use App\Models\Project;

class SearchController extends Controller
{
    public function searchProjects(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $query = $request->input('query');

        $projects = Project::where('visibility_id', 1)
            ->where('title', 'like', "%{$query}%")
            ->orWhereHas('projectCategory', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('tags', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with('projectCategory:id,name')
            ->with('tags:id,name')
            ->get();

        return ResponseHelper::jsonWithData(200, 'success', ProjectResource::collection($projects));
    }

    public function searchSuggest(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $query = $request->input('query');

        // Get title suggestions
        $titleSuggestions = Project::where('visibility_id', 1)
            ->where('title', 'like', "%{$query}%")
            ->limit(5)
            ->pluck('title')
            ->map(function($title) {
                return [
                    'type' => 'title',
                    'value' => strtolower($title)
                ];
            });

        // Get category suggestions
        $categorySuggestions = Project::where('visibility_id', 1)
            ->whereHas('projectCategory', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with('projectCategory:id,name')
            ->limit(5)
            ->get()
            ->map(function($project) {
                return [
                    'type' => 'category',
                    'value' => strtolower($project->projectCategory->name)
                ];
            })
            ->unique('value');

        // Get tag suggestions
        $tagSuggestions = Project::where('visibility_id', 1)
            ->whereHas('tags', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with('tags:id,name')
            ->limit(5)
            ->get()
            ->pluck('tags')
            ->flatten()
            ->map(function($tag) {
                return [
                    'type' => 'tag',
                    'value' => strtolower($tag->name)
                ];
            })
            ->unique('value');

        // Combine and limit to 5 total suggestions
        $suggestions = $titleSuggestions->concat($categorySuggestions)
                                      ->concat($tagSuggestions)
                                      ->take(10);

        return ResponseHelper::jsonWithData(200, 'success', $suggestions);
    }
}
