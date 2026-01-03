<?php
// Load Env for Key
$envPath = __DIR__ . '/../../.env';
$apiKey = '';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        if (trim($name) === 'GEMINI_API_KEY') {
            $apiKey = trim($value);
            break;
        }
    }
}

if (!$apiKey) {
    die("API Key not found in .env");
}

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode !== 200) {
    echo "Error Fetching Models (HTTP $httpCode): " . $response;
} else {
    $data = json_decode($response, true);
    echo "Available Models:\n";
    foreach ($data['models'] as $model) {
        if (strpos($model['name'], 'generateContent') !== false || strpos($model['supportedGenerationMethods'][0] ?? '', 'generateContent') !== false) {
             echo "- " . $model['name'] . "\n";
        }
    }
    // Dump full raw for analysis if filter fails
    // print_r($data); 
}
?>
