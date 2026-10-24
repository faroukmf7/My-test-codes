<?php
/**
 * MTN SMS V2 - quick test script
 * WARNING: SSL verification is DISABLED below. Use for local testing only.
 * Run with:  php mtn_sms_test.php
 */

// ====== FILL THESE IN ======
$clientId     = 'W6EY4s7ygW9hLbcdsxNRquGFnGoSdUZqLWalFIz6ead80txE';
$clientSecret = '87qRuDOOwyAQspyUsJni7nhHHGfVGAqihf5FehR5wGCtGutaPim56Q1PQYrKsLpV';
$senderAddress = 'Flash';      // sandbox value from MTN docs/app
$receiver      = '233241234567';        // recipient, international format
$messageText   = 'Hello from my SMS API!';
// ===========================

$tokenUrl = 'https://api.mtn.com/v1/oauth/access_token?grant_type=client_credentials';
$smsUrl   = 'https://api.mtn.com/v1/messages/sms/outbound';

/**
 * Small cURL helper (SSL verification off for testing).
 */
function request($url, $method, $headers, $body = null)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,  // no SSL verification (testing only)
        CURLOPT_SSL_VERIFYHOST => 0,      // no host verification (testing only)
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    return [$status, $response, $error];
}

// ---------- Step 1: get access token ----------
echo "Step 1: Requesting access token...\n";

[$status, $response, $error] = request(
    $tokenUrl,
    'POST',
    ['Content-Type: application/x-www-form-urlencoded'],
    http_build_query([
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
    ])
);

if ($error) {
    die("cURL error: $error\n");
}
echo "HTTP $status\n$response\n\n";

$tokenData   = json_decode($response, true);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    die("No access_token found. Check your key/secret and that your app is subscribed to the SMS product.\n");
}

// ---------- Step 2: send the SMS ----------
echo "Step 2: Sending SMS...\n";

$payload = json_encode([
    'senderAddress'    => $senderAddress,
    'receiverAddress'  => [$receiver],
    'message'          => $messageText,
    'clientCorrelator' => uniqid('msg-', true),   // unique per message
]);

[$status, $response, $error] = request(
    $smsUrl,
    'POST',
    [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'transactionId: ' . uniqid('txn-', true),  // unique per request
    ],
    $payload
);

if ($error) {
    die("cURL error: $error\n");
}

echo "HTTP $status\n$response\n";