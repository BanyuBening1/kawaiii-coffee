<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class ProductIngredientsDocs
{
    #[OA\Get(
        path: "/api/products/{id}/ingredients",
        operationId: "getProductIngredients",
        tags: ["Product Ingredients"],
        summary: "Get product ingredients",
        description: "Retrieve all ingredients for a specific product",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Product ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of product ingredients",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "product_id", type: "integer", example: 1),
                            new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                            new OA\Property(property: "quantity", type: "number", example: 15),
                            new OA\Property(
                                property: "ingredient",
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Kopi Arabika"),
                                    new OA\Property(property: "unit", type: "string", example: "gram"),
                                ]
                            ),
                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-01T10:30:00Z"),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Product not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Product not found"),
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
    public function getProductIngredients() {}

    #[OA\Post(
        path: "/api/products/{id}/ingredients/bulk",
        operationId: "bulkAddProductIngredients",
        tags: ["Product Ingredients"],
        summary: "Bulk add ingredients to product",
        description: "Menambahkan multiple ingredients ke product sekaligus",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Product ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["ingredients"],
                properties: [
                    new OA\Property(
                        property: "ingredients",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            properties: [
                                new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                                new OA\Property(property: "quantity", type: "number", example: 150),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Ingredients added successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Berhasil menambahkan ingredients"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "product_id", type: "integer", example: 1),
                                    new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                                    new OA\Property(property: "quantity", type: "number", example: 150),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Validation error"),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "ingredients",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The ingredients field is required."]
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
    public function bulkAddProductIngredients() {}

    #[OA\Post(
        path: "/api/product-ingredients",
        operationId: "createProductIngredient",
        tags: ["Product Ingredients"],
        summary: "Create product ingredient",
        description: "Menambahkan single ingredient ke product",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["product_id", "ingredient_id", "quantity"],
                properties: [
                    new OA\Property(property: "product_id", type: "integer", example: 1),
                    new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                    new OA\Property(property: "quantity", type: "number", example: 20),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Product ingredient created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "product_id", type: "integer", example: 1),
                        new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                        new OA\Property(property: "quantity", type: "number", example: 20),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-07T09:30:00Z"),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Validation error"),
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
    public function createProductIngredient() {}

    #[OA\Put(
        path: "/api/product-ingredients/{id}",
        operationId: "updateProductIngredient",
        tags: ["Product Ingredients"],
        summary: "Update product ingredient",
        description: "Memperbarui quantity ingredient di product",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Product Ingredient ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["quantity"],
                properties: [
                    new OA\Property(property: "quantity", type: "number", example: 25),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Product ingredient updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "product_id", type: "integer", example: 1),
                        new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                        new OA\Property(property: "quantity", type: "number", example: 25),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-04-07T10:15:00Z"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Product ingredient not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Product ingredient not found"),
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
    public function updateProductIngredient() {}

    #[OA\Delete(
        path: "/api/product-ingredients/{id}",
        operationId: "deleteProductIngredient",
        tags: ["Product Ingredients"],
        summary: "Delete product ingredient",
        description: "Menghapus ingredient dari product",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Product Ingredient ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Product ingredient deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Deleted"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Product ingredient not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Product ingredient not found"),
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
    public function deleteProductIngredient() {}
}
