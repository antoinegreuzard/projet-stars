<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Star;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @OA\Tag(
 *     name="Stars",
 *     description="Star endpoints"
 * )
 */
class StarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/stars",
     *     tags={"Stars"},
     *     summary="Get list of all stars",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Star")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $stars = Star::all();
        return response()->json($stars);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'image' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stars', 'public');
            $validated['image'] = asset('storage/' . $path);
        }

        $star = Star::create($validated);

        return response()->json($star, 201);
    }

    /**
     * Display the specified Star.
     *
     * @OA\Get(
     *     path="/api/stars/{id}",
     *     tags={"Stars"},
     *     summary="Get a single star by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the star",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Star")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Star not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $star = Star::find($id);

        if (!$star) {
            return response()->json(['message' => 'Star not found'], 404);
        }

        return response()->json($star);
    }

    public function update(Request $request, Star $star): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stars', 'public');
            $validated['image'] = asset('storage/' . $path);
        }

        $star->update($validated);

        return response()->json($star);
    }

    public function destroy(Star $star): JsonResponse
    {
        $star->delete();

        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
