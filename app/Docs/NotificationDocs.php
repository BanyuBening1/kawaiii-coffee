<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class NotificationDocs
{
    #[OA\Get(
        path: "/api/notifications",
        operationId: "getNotifications",
        tags: ["Notifications"],
        summary: "Get user notifications",
        description: "Mengambil daftar notifikasi milik user yang login (pagination)",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Daftar notifikasi berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(
                                    property: "data",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "title", type: "string", example: "Stok Rendah"),
                                            new OA\Property(property: "message", type: "string", example: "Stok Kopi Arabica sudah mencapai level minimum"),
                                            new OA\Property(property: "type", type: "string", example: "low_stock"),
                                            new OA\Property(property: "is_read", type: "boolean", example: false),
                                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-04-11T10:30:00Z"),
                                        ]
                                    )
                                ),
                                new OA\Property(property: "total", type: "integer", example: 25),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "last_page", type: "integer", example: 2),
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
            )
        ]
    )]
    public function index() {}


    #[OA\Post(
        path: "/api/notifications/save-token",
        operationId: "saveFcmToken",
        tags: ["Notifications"],
        summary: "Save FCM token",
        description: "Menyimpan token Firebase Cloud Messaging dari device user",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["fcm_token"],
                properties: [
                    new OA\Property(property: "fcm_token", type: "string", example: "eJftJ6k9z..."),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token berhasil disimpan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Token berhasil disimpan"),
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
    public function saveToken() {}


    #[OA\Patch(
        path: "/api/notifications/{id}/read",
        operationId: "markNotificationRead",
        tags: ["Notifications"],
        summary: "Mark notification as read",
        description: "Menandai satu notifikasi sebagai sudah dibaca",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID notifikasi",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Notifikasi telah dibaca",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Notifikasi telah dibaca"),
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
    public function markAsRead() {}


    #[OA\Patch(
        path: "/api/notifications/read-all",
        operationId: "markAllNotificationsRead",
        tags: ["Notifications"],
        summary: "Mark all notifications as read",
        description: "Menandai semua notifikasi user sebagai sudah dibaca",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Semua notifikasi telah dibaca",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Semua notifikasi telah dibaca"),
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
    public function markAllAsRead() {}
}
