<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper\ResponseHelper;
use App\Http\Resources\MaterialCategoryResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\UserResource;
use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function deleteProjectMaterials(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'materials' => 'required|array',
            'materials.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        $project->materials()->detach($request->materials);

        return ResponseHelper::jsonWithData(200, 'Materials deleted from project successfully', MaterialResource::collection($project->materials));
    }

    public function deleteProjectUsedMaterials(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'materials' => 'required|array',
            'materials.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        try {
            DB::beginTransaction();

            // Get the materials that will be detached to access their pivot data
            $materialsToDetach = $project->usedMaterials()
                ->whereIn('material_id', $request->materials)
                ->get();

            // For each material, restore its quantity
            foreach ($materialsToDetach as $material) {
                $quantityToRestore = $material->pivot->material_quantity;
                $material->increment('quantity', $quantityToRestore);
            }

            // Now detach the materials
            $project->usedMaterials()->detach($request->materials);

            DB::commit();
            return ResponseHelper::jsonWithData(200, 'Materials deleted from project and quantities restored successfully',
                MaterialResource::collection($project->usedMaterials));

        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::json(500, $e->getMessage());
        }
    }

    public function addMaterialsToProject(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'materials' => 'required|array',
            'materials.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        $project->materials()->attach($request->materials);

        return ResponseHelper::jsonWithData(200, 'Materials added to project successfully', MaterialResource::collection($project->materials));
    }

    public function addUsedMaterialsToProject(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'materials' => 'required|array',
            'materials.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        $materialsWithQuantities = [];

        for ($i = 0; $i < count($request->materials); $i++) {
            $id = $request->materials[$i];
            $material = Material::findOrFail($id);
            $materialsWithQuantities[$id] = ['material_quantity' => $material->quantity];
        }

        $project->usedMaterials()->attach($materialsWithQuantities);

        return ResponseHelper::jsonWithData(200, 'Materials added to project successfully', MaterialResource::collection($project->usedMaterials));
    }

    public function saveMaterialsToProject(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'materials' => 'array',
            'materials.*' => 'integer',
            'quantities' => 'array',
            'quantities.*' => 'integer',
        ]);

        $materialsWithQuantities = [];

        for ($i = 0; $i < count( $request->materials); $i++) {
            $materialsWithQuantities[$request->materials[$i]] = ['material_quantity' => $request->quantities[$i]];
        }

        try {
            DB::beginTransaction();
            $project->materials()->detach();
            $project->materials()->attach($materialsWithQuantities);
            DB::commit();

            return ResponseHelper::jsonWithData(200, 'Materials added to project successfully', MaterialResource::collection($project->materials));
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::json(500, $e->getMessage());
        }
    }

    public function saveUsedMaterialsToProject(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'materials' => 'array',
            'materials.*' => 'integer',
            'quantities' => 'array',
            'quantities.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        try {
            DB::beginTransaction();

            // Get current used materials before detaching
            $currentUsedMaterials = $project->usedMaterials()->get();

            // Restore quantities for materials that will be removed
            foreach ($currentUsedMaterials as $material) {
                if (!in_array($material->id, $request->materials)) {
                    $quantityToRestore = $material->pivot->material_quantity;
                    $material->increment('quantity', $quantityToRestore);
                }
            }

            // // First validate all quantities for new materials
            // for ($i = 0; $i < count($request->materials); $i++) {
            //     $material = Material::findOrFail($request->materials[$i]);
            //     if ($material->quantity < $request->quantities[$i]) {
            //         throw new \Exception("Insufficient quantity for material: {$material->name}");
            //     }
            // }

            // Then process the materials
            $materialsWithQuantities = [];
            for ($i = 0; $i < count($request->materials); $i++) {
                $materialId = $request->materials[$i];
                $quantity = $request->quantities[$i];

                // Decrease the material quantity
                $material = Material::findOrFail($materialId);
                $material->decrement('quantity', $quantity);

                $materialsWithQuantities[$materialId] = ['material_quantity' => $quantity];
            }

            $project->usedMaterials()->detach();
            $project->usedMaterials()->attach($materialsWithQuantities);

            DB::commit();

            return ResponseHelper::jsonWithData(200, 'Materials added and quantities updated successfully',
                MaterialResource::collection($project->usedMaterials));

        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseHelper::json(500, $e->getMessage());
        }
    }


    public function getProjectMaterials($project)
    {
        $project = Project::find($project);

        if (!$project) {
            return ResponseHelper::json(404, 'Project not found');
        }

        $materials = $project->materials;

        return ResponseHelper::jsonWithData(
            200,
            'Materials fetched successfully',
            $materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'user' => UserResource::make($material->user),
                    'name' => $material->name,
                    'description' => $material->description,
                    'materialCategory' => MaterialCategoryResource::make($material->category),
                    'quantity' => $material->quantity,
                    'imageUrl' => $material->image,
                    'createdAt' => $material->created_at,
                    'updatedAt' => $material->updated_at,

                    'materialQuantity' => $material->pivot->material_quantity ?? 0

                ];
            }),
        );
    }

    public function getProjectUsedMaterials($project)
    {
        $project = Project::find($project);

        if (!$project) {
            return ResponseHelper::json(404, 'Project not found');
        }

        $materials = $project->usedMaterials;

        return ResponseHelper::jsonWithData(
            200,
            'Materials fetched successfully',
            $materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'user' => UserResource::make($material->user),
                    'name' => $material->name,
                    'description' => $material->description,
                    'materialCategory' => MaterialCategoryResource::make($material->category),
                    'quantity' => $material->quantity,
                    'imageUrl' => $material->image,
                    'createdAt' => $material->created_at,
                    'updatedAt' => $material->updated_at,

                    'materialQuantity' => $material->pivot->material_quantity ?? 0

                ];
            }),
        );
    }

    public function searchMaterials(Request $request) {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        $query = $request->q;
        $user = auth()->user();

        $materials = Material::where('user_id', $user->id)
            ->where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->get();

        return ResponseHelper::jsonWithData(
            200,
            'Materials fetched successfully',
            MaterialResource::collection($materials),
        );
    }

    public function deleteMaterials()
    {
        $validator = Validator::make(request()->all(), [
            'materials' => 'required|array',
            'materials.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        try {
            $ids = request()->materials;
            Material::whereIn('id', $ids)->delete();

            $materials = auth()->user()->materials;

            return ResponseHelper::jsonWithData(200, 'Materials deleted successfully', MaterialResource::collection($materials));
        } catch (\Exception $e) {
            return ResponseHelper::json(500, $e->getMessage());
        }
    }

    public function deleteMaterial(Material $material)
    {
        $material->delete();
        return ResponseHelper::json(200, 'Material deleted successfully');
    }

    public function editMaterial(Material $material)
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'string',
            'description' => 'string',
            'category_id' => 'integer',
            'quantity' => 'integer',
            'image_url' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        $material->update(request()->all());
        return ResponseHelper::json(200, 'Material updated successfully');
    }

    public function getMaterial(Material $material) {
        return ResponseHelper::jsonWithData(
            200,
            'Material fetched successfully',
            MaterialResource::make($material),
        );
    }

    public function getMaterials(Request $request)
    {
        $user = auth()->user();
        $query = Material::where('user_id', $user->id);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('sort_by') && $request->has('order_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->order_by;

            if (in_array($sortBy, ['created_at', 'updated_at', 'name', 'quantity']) && in_array($sortOrder, ['asc', 'desc'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $materials = $query->get();

        return ResponseHelper::jsonWithData(
            200,
            'Materials fetched successfully',
            MaterialResource::collection($materials),
        );
    }

    public function getMaterialCategory()
    {
        $categories = MaterialCategory::all();
        return ResponseHelper::jsonWithData(
            200,
            'Material categories fetched successfully',
            MaterialCategoryResource::collection($categories),
        );
    }

    public function createMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|integer',
            'quantity' => 'integer|nullable',
            'image_url' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::errInput();
        }

        $quantity = $request->quantity ?? 1;
        $user = auth()->user();


        $category = Material::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category,
            'quantity' => $quantity,
            'image' => $request->image_url,
            'user_id' => $user->id,
        ]);

        return ResponseHelper::jsonWithData(201, 'Material created successfully', ['id' => $category->id]);
    }
}
