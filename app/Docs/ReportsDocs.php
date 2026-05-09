<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class ReportsDocs
{
    #[OA\Get(
        path: "/api/reports/sales-summary",
        operationId: "getSalesSummary",
        tags: ["Reports"],
        summary: "Get sales summary",
        description: "Mendapatkan ringkasan penjualan berdasarkan periode tertentu",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "period",
                in: "query",
                required: true,
                description: "Periode laporan penjualan",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["daily", "weekly", "monthly", "yearly"]
                )
            ),
            new OA\Parameter(
                name: "date",
                in: "query",
                required: false,
                description: "Tanggal untuk filter (format YYYY-MM-DD)",
                schema: new OA\Schema(type: "string", format: "date")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sales summary berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Sales summary berhasil diambil."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "period", type: "string", example: "weekly"),
                                new OA\Property(
                                    property: "date_range",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "start", type: "string", example: "2026-05-04 00:00:00"),
                                        new OA\Property(property: "end", type: "string", example: "2026-05-10 23:59:59"),
                                    ]
                                ),
                                new OA\Property(property: "total_revenue", type: "integer", example: 250000),
                                new OA\Property(property: "total_subtotal", type: "integer", example: 240000),
                                new OA\Property(property: "total_transactions", type: "integer", example: 12),
                                new OA\Property(property: "average_transaction", type: "number", format: "float", example: 20833.33),
                            ]
                        ),
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
            ),
            new OA\Response(
                response: 422,
                description: "Validasi gagal",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The period field is required."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "period",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The period field is required."]
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function salesSummary() {}


    #[OA\Get(
        path: "/api/reports/today-sales",
        operationId: "getTodaySales",
        tags: ["Reports"],
        summary: "Get today sales",
        description: "Mendapatkan data penjualan hari ini dengan breakdown metode pembayaran",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Today sales berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Today sales berhasil diambil."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "date", type: "string", format: "date", example: "2026-05-04"),
                                new OA\Property(property: "total_revenue", type: "integer", example: 500000),
                                new OA\Property(property: "total_subtotal", type: "integer", example: 480000),
                                new OA\Property(property: "total_transactions", type: "integer", example: 20),
                                new OA\Property(property: "total_items_sold", type: "integer", example: 55),
                                new OA\Property(
                                    property: "payment_breakdown",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "payment_method", type: "string", example: "cash"),
                                            new OA\Property(property: "total_transactions", type: "integer", example: 10),
                                            new OA\Property(property: "total_revenue", type: "integer", example: 250000),
                                        ]
                                    )
                                ),
                            ]
                        ),
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
            ),
        ]
    )]
    public function todaySales() {}


    #[OA\Get(
        path: "/api/reports/best-sellers",
        operationId: "getBestSellers",
        tags: ["Reports"],
        summary: "Get best sellers",
        description: "Mendapatkan daftar produk terlaris berdasarkan periode tertentu",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "period",
                in: "query",
                required: true,
                description: "Periode laporan best sellers",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["daily", "weekly", "monthly", "yearly"]
                )
            ),
            new OA\Parameter(
                name: "date",
                in: "query",
                required: false,
                description: "Tanggal untuk filter (format YYYY-MM-DD)",
                schema: new OA\Schema(type: "string", format: "date")
            ),
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                description: "Jumlah data yang ditampilkan",
                schema: new OA\Schema(type: "integer", example: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Best sellers berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Best sellers berhasil diambil."),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "rank", type: "integer", example: 1),
                                    new OA\Property(property: "product_id", type: "integer", example: 1),
                                    new OA\Property(property: "product_name", type: "string", example: "Americano"),
                                    new OA\Property(property: "selling_price", type: "integer", example: 18000),
                                    new OA\Property(property: "total_quantity", type: "integer", example: 120),
                                    new OA\Property(property: "total_revenue", type: "integer", example: 2160000),
                                    new OA\Property(property: "total_cost", type: "integer", example: 1000000),
                                    new OA\Property(property: "total_profit", type: "integer", example: 1160000),
                                ]
                            )
                        ),
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
            ),
            new OA\Response(
                response: 422,
                description: "Validasi gagal",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The period field is required."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "period",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The period field is required."]
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function bestSellers() {}


    #[OA\Get(
        path: "/api/reports/low-stock",
        operationId: "getLowStock",
        tags: ["Reports"],
        summary: "Get low stock ingredients",
        description: "Mendapatkan daftar bahan dengan stok di bawah minimum",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Low stock berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Low stock berhasil diambil."),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "ingredient_id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Kopi Arabica"),
                                    new OA\Property(property: "unit", type: "string", example: "gram"),
                                    new OA\Property(property: "current_stock", type: "integer", example: 200),
                                    new OA\Property(property: "min_stock", type: "integer", example: 500),
                                    new OA\Property(property: "deficit", type: "integer", example: 300),
                                    new OA\Property(property: "stock_percent", type: "integer", example: 40),
                                ]
                            )
                        ),
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
            ),
        ]
    )]
    public function lowStock() {}


    #[OA\Get(
        path: "/api/reports/sales-chart",
        operationId: "getSalesChart",
        tags: ["Reports"],
        summary: "Get sales chart data",
        description: "Mendapatkan data grafik penjualan berdasarkan periode tertentu",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "period",
                in: "query",
                required: true,
                description: "Periode laporan grafik penjualan",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["daily", "weekly", "monthly", "yearly"]
                )
            ),
            new OA\Parameter(
                name: "date",
                in: "query",
                required: false,
                description: "Tanggal untuk filter (format YYYY-MM-DD)",
                schema: new OA\Schema(type: "string", format: "date")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sales chart berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Sales chart berhasil diambil."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "period", type: "string", example: "weekly"),
                                new OA\Property(
                                    property: "date_range",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "start", type: "string", format: "date", example: "2026-05-04"),
                                        new OA\Property(property: "end", type: "string", format: "date", example: "2026-05-10"),
                                    ]
                                ),
                                new OA\Property(
                                    property: "chart_data",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "label", type: "string", example: "2026-05-04"),
                                            new OA\Property(property: "total_revenue", type: "integer", example: 500000),
                                            new OA\Property(property: "total_subtotal", type: "integer", example: 480000),
                                            new OA\Property(property: "total_transactions", type: "integer", example: 20),
                                        ]
                                    )
                                ),
                            ]
                        ),
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
            ),
            new OA\Response(
                response: 422,
                description: "Validasi gagal",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The period field is required."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "period",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The period field is required."]
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function salesChart() {}
}
