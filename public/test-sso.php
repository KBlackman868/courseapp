<?php
/**
 * Moodle SSO Test Script
 * Access this at: http://your-site/test-sso.php
 * DELETE THIS FILE AFTER TESTING!
 */

// Your Moodle settings (copy from .env)
$token = 'YOUR_MOODLE_TOKEN_HERE';  // Replace with your actual token
$domainname = 'http://learnabouthealth.hin.gov.tt';  // Your Moodle URL
$functionname = 'auth_userkey_request_login_url';

// Test user (use a real user that exists in Moodle)
$useremail = 'test@example.com';  // Replace with actual email
$firstname = 'Test';
$lastname = 'User';
$username = 'testuser';
$courseid = 2;  // Replace with actual Moodle course ID

// Build the parameters
$param = [
    'user' => [
        'email' => $useremail,
        // Uncomment these if you have "create user" or "update user" enabled:
        // 'firstname' => $firstname,
        // 'lastname' => $lastname,
        // 'username' => $username,
    ]
];

// Build the web service URL
$serverurl = $domainname . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $functionname . '&moodlewsrestformat=json';

echo "<h2>Moodle SSO Test</h2>";
echo "<p><strong>Server URL:</strong> " . htmlspecialchars($serverurl) . "</p>";
echo "<p><strong>Parameters:</strong></p>";
echo "<pre>" . print_r($param, true) . "</pre>";

// Make the request using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $serverurl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable for testing
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // Disable for testing

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>";

if ($curlError) {
    echo "<p style='color:red'><strong>cURL Error:</strong> $curlError</p>";
}

echo "<p><strong>Raw Response:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Parse the response
$resp = json_decode($response);
echo "<p><strong>Parsed Response:</strong></p>";
echo "<pre>" . print_r($resp, true) . "</pre>";

if ($resp && !empty($resp->loginurl)) {
    $loginurl = $resp->loginurl;

    // Add course redirect
    $finalUrl = $loginurl . '&wantsurl=' . urlencode("$domainname/course/view.php?id=$courseid");

    echo "<h3 style='color:green'>SUCCESS!</h3>";
    echo "<p><strong>Login URL:</strong></p>";
    echo "<p><a href='" . htmlspecialchars($finalUrl) . "'>" . htmlspecialchars($finalUrl) . "</a></p>";
    echo "<p><a href='" . htmlspecialchars($finalUrl) . "' style='padding:10px 20px; background:green; color:white; text-decoration:none; border-radius:5px;'>Click to Test SSO Login</a></p>";
} else {
    echo "<h3 style='color:red'>FAILED</h3>";
    if (isset($resp->exception)) {
        echo "<p><strong>Exception:</strong> " . htmlspecialchars($resp->exception) . "</p>";
        echo "<p><strong>Error Code:</strong> " . htmlspecialchars($resp->errorcode ?? 'N/A') . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($resp->message ?? 'N/A') . "</p>";
    }
}

echo "<hr>";
echo "<p style='color:red'><strong>⚠️ DELETE THIS FILE AFTER TESTING!</strong></p>";
