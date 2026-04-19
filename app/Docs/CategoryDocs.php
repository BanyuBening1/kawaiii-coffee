<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class CategoryDocs
{
    #[OA\Get(
        path: "/api/categories",
        operationId: "getCategories",
        tags: ["Categories"],
        summary: "Get all categories",
        description: "Menampilkan daftar semua kategori",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of categories",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "list of categories"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Coffee"),
                                    new OA\Property(property: "created_at", type: "string", example: "2026-03-10 10:00:00"),
                                    new OA\Property(property: "updated_at", type: "string", example: "2026-03-10 10:00:00"),
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function index() {}


    #[OA\Post(
        path: "/api/categories",
        operationId: "createCategory",
        tags: ["Categories"],
        summary: "Create category",
        description: "Menambahkan kategori baru",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Coffee")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Category created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "category created successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Coffee"),
                                new OA\Property(property: "created_at", type: "string", example: "2026-03-10 10:00:00"),
                                new OA\Property(property: "updated_at", type: "string", example: "2026-03-10 10:00:00"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
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
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function store() {}


    #[OA\Put(
        path: "/api/categories/{id}",
        operationId: "updateCategory",
        tags: ["Categories"],
        summary: "Update category",
        description: "Mengubah data kategori",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID kategori",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Non Coffee")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Category updated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Category updated"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Non Coffee"),
                                new OA\Property(property: "created_at", type: "string", example: "2026-03-10 10:00:00"),
                                new OA\Property(property: "updated_at", type: "string", example: "2026-03-10 10:00:00"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Category not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "No query results for model [Category]")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
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
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function update() {}
}