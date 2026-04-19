<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class StockMovementsDocs
{
    #[OA\Get(
        path: "/api/stock-movements",
        operationId: "getStockMovements",
        tags: ["Stock Movements"],
        summary: "Get all stock movements",
        description: "Retrieve list of all stock movements with filtering",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ingredient_id",
                in: "query",
                required: false,
                description: "Filter by ingredient ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "type",
                in: "query",
                required: false,
                description: "Filter by type (IN or OUT)",
                schema: new OA\Schema(type: "string", enum: ["IN", "OUT"], example: "OUT")
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                description: "Page number",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of stock movements",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "user_id", type: "integer", example: 1),
                            new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                            new OA\Property(property: "type", type: "string", example: "OUT"),
                            new OA\Property(property: "quantity", type: "number", example: 30),
                            new OA\Property(property: "reference", type: "string", example: "TRX-ABCD1234"),
                            new OA\Property(property: "description", type: "string", example: "Penggunaan bahan dari transaksi"),
                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-07T14:30:00Z"),
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
        path: "/api/stock-movements/{id}",
        operationId: "getStockMovementDetail",
        tags: ["Stock Movements"],
        summary: "Get stock movement detail",
        description: "Retrieve detailed information about a specific stock movement",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Stock Movement ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Stock movement detail",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", example: 1),
                        new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                        new OA\Property(property: "type", type: "string", example: "OUT"),
                        new OA\Property(property: "quantity", type: "number", example: 30),
                        new OA\Property(property: "reference", type: "string", example: "TRX-ABCD1234"),
                        new OA\Property(property: "description", type: "string", example: "Penggunaan bahan dari transaksi"),
                        new OA\Property(
                            property: "ingredient",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Kopi Arabika"),
                                new OA\Property(property: "unit", type: "string", example: "gram"),
                            ]
                        ),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-07T14:30:00Z"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Stock movement not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Stock movement not found"),
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
        path: "/api/stock-movements/adjust",
        operationId: "adjustStock",
        tags: ["Stock Movements"],
        summary: "Manual stock adjustment",
        description: "Melakukan adjustment stok manual (IN/OUT)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["ingredient_id", "type", "quantity", "description"],
                properties: [
                    new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                    new OA\Property(property: "type", type: "string", enum: ["IN", "OUT"], example: "OUT"),
                    new OA\Property(property: "quantity", type: "number", example: 50),
                    new OA\Property(property: "description", type: "string", example: "Stock correction - damaged goods"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Stock adjustment successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Adjustment berhasil"),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Insufficient stock",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Stok tidak cukup untuk pengurangan"),
                        new OA\Property(property: "message", type: "string", example: "Stok tidak cukup untuk pengurangan"),
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
                                    property: "quantity",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The quantity must be at least 1."]
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
    public function adjust() {}
}
