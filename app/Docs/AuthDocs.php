<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class AuthDocs
{
    #[OA\Post(
        path: "/api/auth/login",
        operationId: "loginUser",
        tags: ["Auth"],
        summary: "Login user",
        description: "Autentikasi user dan mendapatkan Bearer token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "admin"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "123456"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login berhasil"),
                        new OA\Property(property: "token", type: "string", example: "1|abc123..."),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(
                            property: "user",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "admin"),
                                new OA\Property(property: "role", type: "string", example: "kasir"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Kredensial salah",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Nama atau password salah"),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validasi gagal (field tidak diisi)",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The name field is required."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "name",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The name field is required."]
                                ),
                                new OA\Property(
                                    property: "password",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The password field is required."]
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function login() {}


    #[OA\Post(
        path: "/api/auth/logout",
        operationId: "logoutUser",
        tags: ["Auth"],
        summary: "Logout user",
        description: "Menghapus token autentikasi user yang sedang login",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Logout berhasil")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized / token tidak valid",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function logout() {}
}