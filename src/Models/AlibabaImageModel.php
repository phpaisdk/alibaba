<?php

declare(strict_types=1);

namespace AiSdk\Alibaba\Models;

use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Contracts\BaseModel;
use AiSdk\Contracts\ImageModelInterface;
use AiSdk\Exceptions\InvalidResponseException;
use AiSdk\Requests\ImageRequest;
use AiSdk\Responses\ImageResponse;
use AiSdk\Results\ImageData;
use AiSdk\Support\Usage;
use AiSdk\Utils\Support\Url;

final class AlibabaImageModel extends BaseModel implements ImageModelInterface
{
    public function __construct(private readonly string $modelId, private readonly AlibabaOptions $options) {}

    public function provider(): string
    {
        return AlibabaOptions::PROVIDER_NAME;
    }

    public function modelId(): string
    {
        return $this->modelId;
    }

    public function generate(ImageRequest $r): ImageResponse
    {
        $o = $r->providerOptionsFor($this->provider());
        $content = [['text' => $r->prompt]];
        foreach ((array) ($o['referenceImages'] ?? []) as $image) {
            $content[] = ['image' => $image];
        }$parameters = array_filter(['n' => $r->count, 'size' => $r->size, 'aspect_ratio' => $r->aspectRatio, 'seed' => $r->seed], fn($v) => $v !== null);
        $parameters = array_replace($parameters, array_diff_key($o, array_flip(['referenceImages'])));
        $p = $this->runner($this->options->sdk)->postJson(Url::joinPath($this->options->videoBaseUrl, '/api/v1/services/aigc/multimodal-generation/generation'), ['model' => $this->modelId, 'input' => ['messages' => [['role' => 'user', 'content' => $content]]], 'parameters' => $parameters], $this->options->authHeaders(), $this->provider());
        $images = [];
        $items = $p['output']['choices'][0]['message']['content'] ?? $p['output']['results'] ?? [];
        foreach ((array) $items as $item) {
            $url = is_array($item) ? ($item['image'] ?? $item['url'] ?? null) : null;
            if (is_string($url) && $url !== '') {
                $images[] = new ImageData(url: $url);
            }
        }if ($images === []) {
            throw InvalidResponseException::forProvider($this->provider(), 'Alibaba image generation returned no images.', ['body' => $p]);
        }

        return new ImageResponse($images, Usage::empty(), $p, [$this->provider() => ['model' => $this->modelId]]);
    }
}
