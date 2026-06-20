<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="Authentication", description="Login and logout endpoints")
 */
class LoginDocumentaion
{

    /**
     * @OA\Post(
     *     path="/api/sign-in",
     *     summary="Sign in",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_id","password"},
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="password", type="string", example="password")
     *         ) 
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully authenticate user.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully authenticate user."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     )
     * )
     */
    public function store() {}
}
