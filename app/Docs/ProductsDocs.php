<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class ProductsDocs
{
    #[OA\Get(
        path: "/api/products",
        operationId: "getProducts",
        tags: ["Products"],
        summary: "Get all products",
        description: "Menampilkan daftar semua produk yang aktif beserta kategorinya",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of products",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "List of products"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Americano"),
                                    new OA\Property(property: "category_name", type: "string", example: "Coffee"),
                                    new OA\Property(property: "selling_price", type: "number", format: "float", example: 25000),
                                    new OA\Property(property: "cost_price", type: "number", format: "float", example: 15000),
                                    new OA\Property(property: "profit", type: "number", format: "float", example: 10000),
                                    new OA\Property(property: "image", type: "string", nullable: true, example: "http://localhost:8000/storage/products/americano.jpg"),
                                    new OA\Property(property: "created_at", type: "string", example: "2026-03-10 10:00:00"),
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
        path: "/api/products",
        operationId: "createProduct",
        tags: ["Products"],
        summary: "Create product",
        description: "Menambahkan produk baru. Gunakan multipart/form-data jika upload gambar.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["name", "category_id", "selling_price", "cost_price"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Americano"),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(property: "selling_price", type: "number", format: "float", example: 25000),
                        new OA\Property(property: "cost_price", type: "number", format: "float", example: 15000),
                        new OA\Property(property: "image", type: "string", format: "binary", description: "File gambar (jpeg, png, jpg, webp, max 2MB)"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Product created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Product created successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Americano"),
                                new OA\Property(property: "category_id", type: "integer", example: 1),
                                new OA\Property(property: "selling_price", type: "number", example: 25000),
                                new OA\Property(property: "cost_price", type: "number", example: 15000),
                                new OA\Property(property: "image", type: "string", nullable: true, example: "products/americano.jpg"),
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
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Validation errors"),
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
                                    property: "category_id",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The selected category id is invalid."]
                                ),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function store() {}


    #[OA\Get(
        path: "/api/products/{id}",
        operationId: "getProduct",
        tags: ["Products"],
        summary: "Get product by ID",
        description: "Menampilkan detail satu produk berdasarkan ID",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID produk",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Product details",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Product details"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Americano"),
                                new OA\Property(property: "category_id", type: "integer", example: 1),
                                new OA\Property(property: "category_name", type: "string", example: "Coffee"),
                                new OA\Property(property: "selling_price", type: "number", format: "float", example: 25000),
                                new OA\Property(property: "cost_price", type: "number", format: "float", example: 15000),
                                new OA\Property(property: "profit", type: "number", format: "float", example: 10000),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                                new OA\Property(property: "image", type: "string", nullable: true, example: "http://localhost:8000/storage/products/americano.jpg"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Product not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Product not found")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function show() {}


    #[OA\Put(
        path: "/api/products/{id}",
        operationId: "updateProduct",
        tags: ["Products"],
        summary: "Update product",
        description: "Mengubah data produk. Gunakan POST dengan _method=PUT karena ada upload file.",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID produk",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "_method", type: "string", example: "PUT", description: "Method spoofing untuk Laravel"),
                        new OA\Property(property: "name", type: "string", example: "Americano Large"),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(property: "selling_price", type: "number", format: "float", example: 30000),
                        new OA\Property(property: "cost_price", type: "number", format: "float", example: 18000),
                        new OA\Property(property: "is_active", type: "boolean", example: true),
                        new OA\Property(property: "image", type: "string", format: "binary", description: "File gambar baru (opsional)"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Product updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Product updated successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Americano Large"),
                                new OA\Property(property: "category_id", type: "integer", example: 1),
                                new OA\Property(property: "selling_price", type: "number", example: 30000),
                                new OA\Property(property: "cost_price", type: "number", example: 18000),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                                new OA\Property(property: "image", type: "string", nullable: true, example: "products/americano-large.jpg"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Product not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Product not found")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "selling_price",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: ["The selling price must be at least 0."]
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


    #[OA\Delete(
        path: "/api/products/{id}",
        operationId: "deleteProduct",
        tags: ["Products"],
        summary: "Delete product",
        description: "Menghapus produk beserta gambarnya dari storage",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID produk",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Product deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Product deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Product not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Product not found")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function destroy() {}
}