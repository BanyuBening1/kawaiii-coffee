<?php
// Debug UniversalComposerResolver untuk DocBlock

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "===== DOCBLOCK DEBUG =====\n\n";

echo "Doctrine Annotations available: " . (class_exists('\Doctrine\Common\Annotations\Reader') ? "YES" : "NO") . "\n";
echo "DocBlockParser enabled: " . (\OpenApi\Analysers\DocBlockParser::isEnabled() ? "YES" : "NO") . "\n";

$factories = [
    new \OpenApi\Analysers\DocBlockAnnotationFactory(),
    new \OpenApi\Analysers\AttributeAnnotationFactory(),
];

foreach ($factories as $factory) {
    echo get_class($factory) . " supported: " . ($factory->isSupported() ? "YES" : "NO") . "\n";
}

echo "\n===== Testing annotation parse =====\n\n";

$testCode = <<<'PHP'
<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Kawaiii Coffee API",
 *     version="1.0.0"
 * )
 */
class TestAnnotation {}
?>
PHP;

echo "Test code:\n$testCode\n\n";

// Try to parse manually
try {
    $reflectionClass = new ReflectionClass('App\OpenApi\ApiInfo');
    echo "Reflection file: " . $reflectionClass->getFileName() . "\n";
    echo "File exists: " . (file_exists($reflectionClass->getFileName()) ? "YES" : "NO") . "\n";
    
    if (file_exists($reflectionClass->getFileName())) {
        $content = file_get_contents($reflectionClass->getFileName());
        echo "\nFirst 200 chars of file:\n";
        echo substr($content, 0, 200) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n===== END DEBUG =====\n";
