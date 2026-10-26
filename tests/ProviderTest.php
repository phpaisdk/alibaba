<?php

declare(strict_types=1);

use AiSdk\Alibaba;
use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Alibaba\AlibabaProvider;
use AiSdk\Contracts\EmbeddingProviderInterface;
use AiSdk\Contracts\ImageProviderInterface;
use AiSdk\Contracts\SpeechProviderInterface;
use AiSdk\Contracts\TextProviderInterface;
use AiSdk\Contracts\VideoProviderInterface;
use AiSdk\Generate;
use AiSdk\Support\Sdk;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class AlibabaFakeClient implements ClientInterface
{
    public ?RequestInterface $request = null;

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->request = $request;

        return new Response(200, [], json_encode(['output' => ['task_id' => 'job-1']], JSON_THROW_ON_ERROR));
    }
}
it('exposes every implemented Alibaba capability', function () {
    $p = new AlibabaProvider(new AlibabaOptions('key'));
    expect($p)->toBeInstanceOf(TextProviderInterface::class)->toBeInstanceOf(ImageProviderInterface::class)->toBeInstanceOf(SpeechProviderInterface::class)->toBeInstanceOf(EmbeddingProviderInterface::class)->toBeInstanceOf(VideoProviderInterface::class);
});
it('starts Alibaba video tasks', function () {
    $c = new AlibabaFakeClient();
    $f = new Psr17Factory();
    Alibaba::create([
        'apiKey' => 'key',
        'videoBaseUrl' => 'https://dashscope',
        'sdk' => new Sdk($c, $f, $f),
    ]);

    $j = Generate::video('A scene')
        ->model(Alibaba::model('wan2.7-t2v'))
        ->job();

    expect($j->id)->toBe('job-1')->and($c->request?->getUri()->getPath())->toBe('/api/v1/services/aigc/video-generation/video-synthesis');
});
