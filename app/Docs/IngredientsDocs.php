<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class IngredientsDocs
{
    #[OA\Get(
        path: "/api/ingredients",
        operationId: "getIngredients",
        tags: ["Ingredients"],
        summary: "Get all ingredients",
        description: "Retrieve list of all ingredients with stock information",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of ingredients",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Kopi Arabika"),
                            new OA\Property(property: "stock", type: "number", example: 100),
                            new OA\Property(property: "unit", type: "string", example: "gram"),
                            new OA\Property(property: "min_stock", type: "number", example: 20),
                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-01T10:30:00Z"),
                            new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-04-07T09:00:00Z"),
                        ]
                    )
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
    public function index() {}

    #[OA\Get(
        path: "/api/ingredients/{id}",
        operationId: "getIngredientDetail",
        tags: ["Ingredients"],
        summary: "Get ingredient detail",
        description: "Retrieve detailed information about a specific ingredient",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Ingredient ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Ingredient detail",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Kopi Arabika"),
                        new OA\Property(property: "stock", type: "number", example: 100),
                        new OA\Property(property: "unit", type: "string", example: "gram"),
                        new OA\Property(property: "min_stock", type: "number", example: 20),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-01T10:30:00Z"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-04-07T09:00:00Z"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Ingredient not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ingredient not found"),
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
    public function show() {}

    #[OA\Post(
        path: "/api/ingredients",
        operationId: "createIngredient",
        tags: ["Ingredients"],
        summary: "Create new ingredient",
        description: "Membuat bahan baru di inventory",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "stock", "unit"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Karamel"),
                    new OA\Property(property: "stock", type: "number", example: 150),
                    new OA\Property(property: "unit", type: "string", example: "gram"),
                    new OA\Property(property: "min_stock", type: "number", example: 30),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Ingredient created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 4),
                        new OA\Property(property: "name", type: "string", example: "Karamel"),
                        new OA\Property(property: "stock", type: "number", example: 150),
                        new OA\Property(property: "unit", type: "string", example: "gram"),
                        new OA\Property(property: "min_stock", type: "number", example: 30),
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
                                    example: ["The name has already been taken."]
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
        path: "/api/ingredients/{id}",
        operationId: "updateIngredient",
        tags: ["Ingredients"],
        summary: "Update ingredient",
        description: "Memperbarui data bahan",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Ingredient ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Karamel Sirop"),
                    new OA\Property(property: "stock", type: "number", example: 200),
                    new OA\Property(property: "min_stock", type: "number", example: 50),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Ingredient updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Karamel Sirop"),
                        new OA\Property(property: "stock", type: "number", example: 200),
                        new OA\Property(property: "unit", type: "string", example: "gram"),
                        new OA\Property(property: "min_stock", type: "number", example: 50),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-04-07T10:30:00Z"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Ingredient not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ingredient not found"),
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
        path: "/api/ingredients/{id}",
        operationId: "deleteIngredient",
        tags: ["Ingredients"],
        summary: "Delete ingredient",
        description: "Menghapus bahan dari inventory",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Ingredient ID",
                schema: new OA\Schema(type: "integer", example: 4)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Ingredient deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Deleted"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Ingredient not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ingredient not found"),
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

    #[OA\Post(
        path: "/api/ingredients/{id}/restock",
        operationId: "restockIngredient",
        tags: ["Ingredients"],
        summary: "Restock ingredient",
        description: "Menambah stok bahan ke inventory",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Ingredient ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["quantity"],
                properties: [
                    new OA\Property(property: "quantity", type: "number", example: 250),
                    new OA\Property(property: "description", type: "string", example: "Restock dari supplier ABC"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Restock successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Restock berhasil"),
                        new OA\Property(property: "warning", type: "string", example: null),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Kopi Arabika"),
                                new OA\Property(property: "stock", type: "number", example: 350),
                                new OA\Property(property: "unit", type: "string", example: "gram"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The quantity must be at least 1."),
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
    public function restock() {}
}
