<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Star;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @OA\OpenApi(
 *    @OA\Info(
 *        title="My API",
 *        version="1.0.0",
 *        description="API for managing stars",
 *        @OA\Contact(
 *            email="antoine@antoinegreuzard.fr"
 *        )
 *    ),
 *    @OA\Server(
 *        description="API Server",
 *        url=L5_SWAGGER_CONST_HOST
 *    ),
 *    @OA\Components(
 *        @OA\Schema(
 *            schema="Star",
 *            type="object",
 *            required={"name", "first_name"},
 *            @OA\Property(
 *                property="id",
 *                type="integer",
 *                format="int64",
 *                description="Unique identifier for the Star"
 *            ),
 *            @OA\Property(
 *                property="name",
 *                type="string",
 *                description="Name of the Star"
 *            ),
 *            @OA\Property(
 *                property="first_name",
 *                type="string",
 *                description="First name of the Star"
 *            ),
 *            @OA\Property(
 *                property="image",
 *                type="string",
 *                format="uri",
 *                description="URL of the Star's image"
 *            ),
 *            @OA\Property(
 *                property="description",
 *                type="string",
 *                description="Description of the Star"
 *            )
 *        ),
 *        @OA\SecurityScheme(
 *            securityScheme="sanctum",
 *            type="apiKey",
 *            name="Authorization",
 *            in="header",
 *            description="Enter token in format (Bearer <token>)"
 *        )
 *    )
 * )
 */
class StarController extends Controller
{
    public function __construct()
    {
        // Appliquez le middleware 'auth:sanctum' à seulement certaines méthodes
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

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

    /**
     * @OA\Post(
     *     path="/api/stars",
     *     tags={"Stars"},
     *     summary="Create a new star",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass star data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "first_name"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=255,
     *                 description="Name of the Star",
     *                 example="Sirius"
     *             ),
     *             @OA\Property(
     *                 property="first_name",
     *                 type="string",
     *                 maxLength=255,
     *                 description="First name of the Star",
     *                 example="Alpha"
     *             ),
     *             @OA\Property(
     *                 property="image",
     *                 type="string",
     *                 format="uri",
     *                 description="URL of the Star's image",
     *                 example="http://example.com/images/star.jpg"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Brief description of the Star",
     *                 example="A bright star in the night sky."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Star created",
     *         @OA\JsonContent(ref="#/components/schemas/Star")
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/stars/{star}",
     *     tags={"Stars"},
     *     summary="Update an existing star",
     *     description="Update a 'star' by its ID",
     *     operationId="updateStar",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="star",
     *         in="path",
     *         required=true,
     *         description="ID of the star to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Star data to update",
     *         @OA\JsonContent(ref="#/components/schemas/Star")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Star updated",
     *         @OA\JsonContent(ref="#/components/schemas/Star")
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/stars/{star}",
     *     tags={"Stars"},
     *     summary="Delete a star",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="star",
     *         in="path",
     *         required=true,
     *         description="ID of the star to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Star deleted"
     *     )
     * )
     */
    public function destroy(Star $star): JsonResponse
    {
        $star->delete();

        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
