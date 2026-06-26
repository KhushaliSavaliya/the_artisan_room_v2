<?php

// Test script to authenticate and access the Admin Dashboard
$urlLogin = 'http://127.0.0.1:8000/admin/login';
$urlDashboard = 'http://127.0.0.1:8000/admin/dashboard';

// 1. Fetch CSRF token from Login Page
$ch = curl_init($urlLogin);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies_admin.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_admin.txt');
$loginPageHtml = curl_exec($ch);
curl_close($ch);

// Parse CSRF token
preg_match('/name="_token" value="([^"]+)"/', $loginPageHtml, $matches);
$csrfToken = $matches[1] ?? '';
echo "Found CSRF Token: " . $csrfToken . "\n";

if (!$csrfToken) {
    echo "Error: CSRF Token not found in login page HTML.\n";
    exit(1);
}

// 2. Perform Login POST request
$loginPayload = http_build_query([
    '_token' => $csrfToken,
    'email' => 'admin@kdartisanroom.com',
    'password' => 'ChangeMe123!'
]);

$ch = curl_init($urlLogin);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies_admin.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_admin.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow the redirect to dashboard

$dashboardHtml = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: " . $httpCode . "\n\n";

if (strpos($dashboardHtml, 'Admin Dashboard') !== false) {
    echo "SUCCESS: Logged in and loaded Admin Dashboard.\n\n";
    
    // Extract and print Metric Cards Values
    preg_match_all('/<span class="text-[^"]* font-sans font-bold[^"]*">([^<]+)<\/span>/i', $dashboardHtml, $metricMatches);
    echo "Metric Card Values:\n";
    foreach ($metricMatches[1] as $index => $val) {
        echo "- Card " . ($index + 1) . ": " . trim($val) . "\n";
    }
    
    // Extract revenue separately if class differs
    if (preg_match('/text-2xl font-sans font-bold text-gold-accent">([^<]+)<\/span>/i', $dashboardHtml, $revMatch)) {
        echo "- Revenue Card: " . trim($revMatch[1]) . "\n";
    }

    echo "\nRecent Orders Table:\n";
    // Check if recent order is rendered
    if (preg_match_all('/<td class="[^"]*font-semibold text-gold-accent[^"]*">([^<]+)<\/td>\s*<td[^>]*>([^<]+)<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $dashboardHtml, $orderMatches)) {
        for ($i = 0; $i < count($orderMatches[1]); $i++) {
            echo "- Order: " . trim($orderMatches[1][$i]) . " | Customer: " . trim($orderMatches[2][$i]) . " | Total: " . trim($orderMatches[3][$i]) . "\n";
        }
    } else {
        echo "No orders rendered or unmatched pattern.\n";
    }
} else {
    echo "FAILED: Could not access dashboard.\n";
    echo substr($dashboardHtml, 0, 1000) . "\n";
}
