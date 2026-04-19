<?php
// Debug script untuk menganalisis Swagger scanning

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use OpenApi\Generator as OpenApiGenerator;
use OpenApi\SourceFinder;

$config = $app['config']['l5-swagger'];

$documentationConfig = $config['documentations']['default'];
$paths = $documentationConfig['paths'];
$scanOptions = $config['defaults']['scanOptions'];

// Paths to scan
$annotationsPaths = $paths['annotations'];
$excludeDirs = $paths['excludes'] ?? [];
$pattern = $scanOptions['pattern'] ?? '*.php';

echo "===== SWAGGER DEBUG INFO =====\n\n";

echo "📁 Paths konfigurasi:\n";
foreach ($annotationsPaths as $path) {
    $realPath = $path;
    echo "  - {$realPath}\n";
    echo "    Exists: " . (is_dir($realPath) ? "✓" : "✗") . "\n";
    
    if (is_dir($realPath)) {
        // Gunakan iterator daripada glob
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($realPath));
        $phpFiles = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && substr($file->getFilename(), -4) === '.php') {
                $phpFiles[] = $file->getPathname();
            }
        }
        echo "    Files: " . count($phpFiles) . " PHP files\n";
        foreach (array_slice($phpFiles, 0, 5) as $file) {
            echo "      - " . basename($file) . "\n";
        }
        if (count($phpFiles) > 5) {
            echo "      ... dan " . (count($phpFiles) - 5) . " file lainnya\n";
        }
    }
}

echo "\nPattern: $pattern\n";
echo "Exclude: " . json_encode($excludeDirs) . "\n";

echo "\n📊 Scanning dengan SourceFinder...\n";

try {
    // Create finder
    $finder = new SourceFinder($annotationsPaths, $excludeDirs, $pattern);
    
    // Create generator
    $generator = new OpenApiGenerator();
    
    echo "Analyser: " . get_class($generator->getAnalyser()) . "\n";
    
    // Scan
    $openapi = $generator->generate($finder);
    
    echo "✓ Scan berhasil!\n";
    
    // Check type
    echo "Info type: " . gettype($openapi->info) . "\n";
    echo "Paths type: " . gettype($openapi->paths) . "\n";
    
    // Check if undefined
    $undefMarker = \OpenApi\Generator::UNDEFINED;
    echo "Info is UNDEFINED: " . ($openapi->info === $undefMarker ? "YES" : "NO") . "\n";
    echo "Paths is UNDEFINED: " . ($openapi->paths === $undefMarker ? "YES" : "NO") . "\n";
    
    echo "Info: " . ($openapi->info ? json_encode((array) $openapi->info) : "No info") . "\n";
    
    if (is_array($openapi->paths) && count($openapi->paths) > 0) {
        echo "Paths: " . count($openapi->paths) . " endpoint\n";
        echo "\nEndpoints yang ditemukan:\n";
        foreach ($openapi->paths as $path => $pathItem) {
            echo "  - $path\n";
        }
    } else {
        echo "Paths: 0 endpoint (or UNDEFINED)\n";
        echo "\n⚠️  Tidak ada endpoint atau info yang ditemukan!\n";
        echo "\n💡 ANALISIS:\n";
        echo "- Analyzer tidak menemukan annotation dalam file\n";
        echo "- Kemungkinan: Format annotation salah atau analyzer tidak kompatibel\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStacktrace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n===== END DEBUG =====\n";
