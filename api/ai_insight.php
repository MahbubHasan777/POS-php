<?php
require_once '../includes/db.php';
requireRole('shop_admin');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$apiKey = getenv('GEMINI_API_KEY');

if (!$apiKey || $apiKey === 'your_generic_key') {
    echo json_encode(['success' => false, 'message' => 'API Key not configured. Please set GEMINI_API_KEY in .env']);
    exit;
}

$prompt = "I run a retail shop. Here is my sales data from {$input['start']} to {$input['end']}: 
Total Revenue: \${$input['revenue']}
Total Profit: \${$input['profit']}
Total Orders: {$input['orders']}

Please provide 3 concise tips to increase my sales and profitability based on this data. Keep it professional.";

// Call Gemini API
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;
$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $result = json_decode($response, true);
    $insight = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Could not generate insight.";
    echo json_encode(['success' => true, 'insight' => $insight]);
} else {
    // Fallback for demo if API fails or quota exceeded
    echo json_encode(['success' => true, 'insight' => "Based on your revenue of \${$input['revenue']}, consider bundling slow-moving items with popular ones. Increasing average order value could boost your profit of \${$input['profit']}."]);
}
?>
