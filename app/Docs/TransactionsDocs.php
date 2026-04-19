<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class TransactionsDocs
{
    #[OA\Post(
        path: "/api/transactions",
        operationId: "storeTransaction",
        tags: ["Transactions"],
        summary: "Create transaction",
        description: "Membuat transaksi baru beserta detail item",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["payment_method", "paid_amount", "items"],
                properties: [
                    new OA\Property(property: "payment_method", type: "string", enum: ["cash", "qris", "transfer"], example: "cash"),
                    new OA\Property(property: "paid_amount", type: "number", example: 50000),
                    new OA\Property(
                        property: "items",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            properties: [
                                new OA\Property(property: "product_id", type: "integer", example: 1),
                                new OA\Property(property: "quantity", type: "integer", example: 2),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Transaksi berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Transaksi berhasil"),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Uang tidak cukup",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Uang tidak cukup"),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validasi gagal",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            ),
        ]
    )]
    public function store() {}


    #[OA\Get(
        path: "/api/transactions",
        operationId: "getTransactions",
        tags: ["Transactions"],
        summary: "Get transaction history",
        description: "Menampilkan daftar transaksi (khusus owner)",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "start_date", in: "query", required: false, description: "Filter tanggal awal", schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "end_date", in: "query", required: false, description: "Filter tanggal akhir", schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "cashier_id", in: "query", required: false, description: "Filter kasir", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "payment_method", in: "query", required: false, description: "Filter metode pembayaran", schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mendapatkan data",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "current_page", type: "integer", example: 1),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object")),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden (bukan owner)",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            ),
        ]
    )]
    public function index() {}


    #[OA\Get(
        path: "/api/transactions/{id}",
        operationId: "getTransactionDetail",
        tags: ["Transactions"],
        summary: "Get transaction detail",
        description: "Menampilkan detail 1 transaksi",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID transaksi",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mendapatkan detail transaksi",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "transaction_code", type: "string", example: "TRX-ABC123"),
                        new OA\Property(property: "total", type: "number", example: 42000),
                        new OA\Property(property: "cashier", type: "object"),
                        new OA\Property(property: "details", type: "array", items: new OA\Items(type: "object")),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Data tidak ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Not Found")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            ),
        ]
    )]
    public function show() {}
}