<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class CategoriesDocs
{
    #[OA\Get(
        path: "/api/categories",
        operationId: "getCategories",
        tags: ["Categories"],
        summary: "Get all categories",
        description: "Retrieve list of all product categories",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of categories",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Kopi"),
                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-01T10:30:00Z"),
                            new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-04-01T10:30:00Z"),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - Token invalid or missing",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated."),
                    ]
                )
            ),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: "/api/categories",
        operationId: "createCategory",
        tags: ["Categories"],
        summary: "Create new category",
        description: "Membuat kategori produk baru",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Snack"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Category created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 4),
                        new OA\Property(property: "name", type: "string", example: "Snack"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-07T09:00:00Z"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-04-07T09:00:00Z"),
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
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated."),
                    ]
                )
            ),
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: "/api/categories/{id}",
        operationId: "updateCategory",
        tags: ["Categories"],
        summary: "Update category",
        description: "Memperbarui kategori produk yang sudah ada",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Category ID",
                schema: new OA\Schema(type: "integer", example: 2)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Beverage"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Category updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 2),
                        new OA\Property(property: "name", type: "string", example: "Beverage"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-02T11:15:00Z"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-04-07T10:30:00Z"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Category not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Category not found"),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The name has already been taken."),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated."),
                    ]
                )
            ),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: "/api/categories/{id}",
        operationId: "deleteCategory",
        tags: ["Categories"],
        summary: "Delete category",
        description: "Menghapus kategori produk",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Category ID",
                schema: new OA\Schema(type: "integer", example: 4)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Category deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Deleted"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Category not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Category not found"),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated."),
                    ]
                )
            ),
        ]
    )]
    public function destroy() {}
}
