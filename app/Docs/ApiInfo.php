<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Kawaiii Coffee API",
    version: "1.0.0",
    description: "API untuk sistem POS Kawaiii Coffee"
)]
#[OA\Server(
    url: "http://202.10.48.252",
    description: "Production API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class ApiInfo
{
    // Menggunakan PHP 8.1+ Attributes
}