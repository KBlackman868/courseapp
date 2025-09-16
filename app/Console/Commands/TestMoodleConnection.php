<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MoodleClient;

class TestMoodleConnection extends Command
{
    protected $signature = 'moodle:test-connection';
    protected $description = 'Test Moodle API connection';

    public function handle()
    {
        $this->info('Testing Moodle connection...');
        
        try {
            $client = new MoodleClient();
            
            // Test getting site info
            $result = $client->call('core_webservice_get_site_info');
            
            $this->info('✓ Connection successful!');
            $this->info('Site name: ' . ($result['sitename'] ?? 'Unknown'));
            $this->info('Moodle version: ' . ($result['release'] ?? 'Unknown'));
            
        } catch (\Exception $e) {
            $this->error('✗ Connection failed: ' . $e->getMessage());
        }
    }
}