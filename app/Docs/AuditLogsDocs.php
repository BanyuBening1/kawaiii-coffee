<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class AuditLogsDocs
{
    #[OA\Get(
        path: "/api/audit-logs",
        operationId: "getAuditLogs",
        tags: ["Audit Logs"],
        summary: "Get audit logs",
        description: "Retrieve audit logs dengan filtering berdasarkan action, table, user, dan date range",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "action",
                in: "query",
                required: false,
                description: "Filter by action (CREATE, UPDATE, DELETE)",
                schema: new OA\Schema(type: "string", enum: ["CREATE", "UPDATE", "DELETE"], example: "CREATE")
            ),
            new OA\Parameter(
                name: "table_name",
                in: "query",
                required: false,
                description: "Filter by table name",
                schema: new OA\Schema(type: "string", example: "ingredients")
            ),
            new OA\Parameter(
                name: "user_id",
                in: "query",
                required: false,
                description: "Filter by user ID",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "record_id",
                in: "query",
                required: false,
                description: "Filter by record ID",
                schema: new OA\Schema(type: "integer", example: 1)
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
                description: "List of audit logs",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "user_id", type: "integer", example: 1),
                            new OA\Property(property: "action", type: "string", example: "CREATE"),
                            new OA\Property(property: "table_name", type: "string", example: "transactions"),
                            new OA\Property(property: "record_id", type: "integer", example: 1),
                            new OA\Property(property: "description", type: "string", example: "Membuat transaksi TRX-ABCD1234"),
                            new OA\Property(property: "old_data", type: "string", example: null),
                            new OA\Property(property: "new_data", type: "string", example: "{\"id\":1,\"transaction_code\":\"TRX-ABCD1234\",\"total\":65000}"),
                            new OA\Property(
                                property: "user",
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                                    new OA\Property(property: "email", type: "string", example: "cashier@kawaiii.com"),
                                ]
                            ),
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
}
