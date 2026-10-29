<?php

declare(strict_types=1);

use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Alibaba\Models\AlibabaVideoModel;
use AiSdk\Alibaba\Tests\Fakes\SequenceHttpClient;
use AiSdk\Responses\VideoJob;
use AiSdk\Responses\VideoJobStatus;
use AiSdk\Support\Sdk;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;

require_once __DIR__ . '/Fakes/SequenceHttpClient.php';

it('polls only the configured task endpoint with an encoded task id', function () {
    $client = new SequenceHttpClient([new Response(200, [], '{"output":{"task_status":"SUCCEEDED","video_url":"https://media.example/video.mp4"}}')]);
    $factory = new Psr17Factory();
    $model = new AlibabaVideoModel('wan', new AlibabaOptions(apiKey: 'secret', videoBaseUrl: 'https://custom.example:8443', sdk: new Sdk($client, $factory, $factory)));
    $job = new VideoJob('../https://evil.test', 'alibaba', 'wan', providerMetadata: ['alibaba' => ['pollingUrl' => 'https://evil.test']]);
    $result = $model->poll($job);
    expect($result->status)->toBe(VideoJobStatus::Succeeded);
    expect($result->result?->url)->toBe('https://media.example/video.mp4');
    expect($client->requests)->toHaveCount(1);
    expect((string) $client->requests[0]->getUri())->toBe('https://custom.example:8443/api/v1/tasks/..%2Fhttps%3A%2F%2Fevil.test');
    expect($client->requests[0]->getHeaderLine('Authorization'))->toBe('Bearer secret');
});
