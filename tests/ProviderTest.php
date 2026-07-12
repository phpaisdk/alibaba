<?php

declare(strict_types=1);
use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Alibaba\AlibabaProvider;
use AiSdk\Alibaba\Models\AlibabaVideoModel;
use AiSdk\Contracts\EmbeddingProviderInterface;
use AiSdk\Contracts\ImageProviderInterface;
use AiSdk\Contracts\SpeechProviderInterface;
use AiSdk\Contracts\TextProviderInterface;
use AiSdk\Contracts\VideoProviderInterface;
use AiSdk\Requests\VideoRequest;
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
    $c = new AlibabaFakeClient;
    $f = new Psr17Factory;
    $m = new AlibabaVideoModel('wan2.7-t2v', new AlibabaOptions('key', videoBaseUrl: 'https://dashscope', sdk: new Sdk($c, $f, $f)));
    $j = $m->generate(new VideoRequest('A scene'));
    expect($j->id)->toBe('job-1')->and($c->request?->getUri()->getPath())->toBe('/api/v1/services/aigc/video-generation/video-synthesis');
});
