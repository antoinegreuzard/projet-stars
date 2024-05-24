<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
 *    )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
