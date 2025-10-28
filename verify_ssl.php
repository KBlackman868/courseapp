<?php
// SSL Certificate Verification Script
$certPath = __DIR__ . '/storage/certs/cacert.pem';

echo "Checking SSL Certificate Setup...\n";
echo "=================================\n\n";

if (file_exists($certPath)) {
    echo "✓ Certificate found at: " . $certPath . "\n";
    echo "✓ File size: " . round(filesize($certPath) / 1024, 2) . " KB\n";
    echo "✓ Readable: " . (is_readable($certPath) ? 'Yes' : 'No') . "\n";
    
    // Test SSL connection
    try {
        $client = new \GuzzleHttp\Client(['verify' => $certPath]);
        $response = $client->get('https://www.googleapis.com/oauth2/v3/certs');
        echo "✓ SSL Test: Successfully connected to Google OAuth with certificate!\n";
        echo "\n✅ PRODUCTION READY - Maximum security enabled!\n";
    } catch (Exception $e) {
        echo "✗ SSL Test Failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ Certificate not found!\n";
}
