<?php
// test_guest_add.php
$url = "http://localhost/CAPSTONE!%202026/BTC%20SYSTEM5/CUSTOMER%20PAGE/add_to_cart.php";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["name" => "Test Product", "price" => "100", "image" => "test.png"]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Raw Response:\n$response\n";

$json = json_decode($response, true);
if ($json) {
    echo "JSON Decode Success.\n";
    echo "debug_user_id: " . (isset($json['debug_user_id']) ? $json['debug_user_id'] : "MISSING") . "\n";
} else {
    echo "JSON Decode Failed: " . json_last_error_msg() . "\n";
}
?>
