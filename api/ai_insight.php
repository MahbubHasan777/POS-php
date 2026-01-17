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

$topItemsStr = "";
foreach ($input['top_items'] as $item) {
    $topItemsStr .= "- {$item['name']}: {$item['total_qty']} sold (\${$item['total_revenue']})\n";
}


$trendStr = "";
foreach ($input['daily_sales'] as $day) {
    $trendStr .= "- {$day['date']}: \${$day['revenue']}\n";
}

$prompt = "I run a retail shop. Analyze my sales data from {$input['start']} to {$input['end']}:

**Performance Overview:**
- Total Revenue: \${$input['revenue']}
- Total Profit: \${$input['profit']}
- Total Orders: {$input['orders']}

**Top Selling Products:**
{$topItemsStr}

**Recent Daily Sales Trend:**
{$trendStr}

**Task:**
Please act as a Senior Retail Analyst. Provide exactly 3 specific, data-driven strategies to improve my business.
Format your response as a clean **HTML list** (use `<ul>` and `<li>` tags) with `<strong>` for key points. Do not include `<html>` or `<body>` tags.

Example structure:
<ul>
  <li><strong>Insight Title:</strong> Explanation...</li>
</ul>

1. **Sales Driver Analysis**: Why are these specific items selling well? (Analyze the top products).
2. **Promotional Strategy**: Suggest a concrete 'Bundle Offer' or 'Discount Campaign' based on my top items to cross-sell slower moving goods.
3. **Profitability & Growth**: Suggest one operational change or up-sell technique to increase the average order value.

Keep the tone professional, motivating, and concise. HTML only.";

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;
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
    // Return actual error for debugging
    $error = curl_error($ch);
    $responseBody = strip_tags($response); // basic security
    echo json_encode([
        'success' => false, 
        'message' => "Gemini API Failed (HTTP $httpCode). Error: $error. Response: $responseBody"
    ]);
}
?>
