<?php
include 'vendor/autoload.php';

$imageName = __DIR__  . '/tests/images/i4.jpg';
$maskName = __DIR__ . '/tests/templates/template.json';

Tarsius\Tarsius::config([
    'minArea' => 200,
]);

$obj = new Tarsius\Form($imageName,$maskName);
$results = $obj->evaluate();

print_r($results);
echo "\n";
print_r($results['configuration']);
echo "\n";
print_r($results['scale']);
echo "\n";