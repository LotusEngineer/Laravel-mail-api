<?php
namespace App\Logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Logger;


class CloudWatchLoggerFactory {
    
    
    public function __invoke(array $config)
    {
        $sdkParams = $config["sdk"];
        $tags = $config["tags"] ?? [ ];
        $name = $config["name"] ?? 'cloudwatch';
        
        // Instantiate AWS SDK CloudWatch Logs Client
        $client = new CloudWatchLogsClient($sdkParams);
        
        // Log group name, will be created if none
        $groupName = '/app/' . config('app.name') . '-' . config('app.env');
        
        // Log stream name, will be created if none
        $streamName = $sdkParams['logstream'];
        
        // Days to keep logs, 14 by default. Set to `null` to allow indefinite retention.
        $retentionDays = $sdkParams["retention"];
        
        // Instantiate handler (tags are optional)
        $handler = new CloudWatch($client, $groupName, $streamName, $retentionDays, 10000, $tags);
        
        // Create a log channel
        $logger = new Logger($name);
        // Set handler
        $logger->pushHandler($handler);
        
        return $logger;
    }
}
    