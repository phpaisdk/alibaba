<?php

declare(strict_types=1);

namespace AiSdk\Alibaba\Models;

use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Capability;
use AiSdk\Contracts\BaseModel;
use AiSdk\Contracts\TextModelInterface;
use AiSdk\OpenAICompatible\ChatRequestBuilder;
use AiSdk\OpenAICompatible\ChatResponseParser;
use AiSdk\OpenAICompatible\ChatStreamParser;
use AiSdk\Requests\TextModelRequest;
use AiSdk\Responses\TextModelResponse;
use AiSdk\Utils\Support\Url;
use Generator;

final class AlibabaTextModel extends BaseModel implements TextModelInterface
{
    private const array CAPS = [Capability::TextGeneration, Capability::Streaming, Capability::ToolCalling, Capability::StructuredOutput, Capability::Reasoning, Capability::TextInput, Capability::ImageInput, Capability::AudioInput, Capability::FileInput];

    public function __construct(private readonly string $modelId, private readonly AlibabaOptions $options) {}

    public function provider(): string
    {
        return AlibabaOptions::PROVIDER_NAME;
    }

    public function modelId(): string
    {
        return $this->modelId;
    }

    public function generate(TextModelRequest $r): TextModelResponse
    {
        $this->ensureTextRequestSupported($r, self::CAPS);
        $b = ChatRequestBuilder::build($this->modelId, $this->provider(), $r, false);
        $p = $this->runner($this->options->sdk)->postJson(Url::joinPath($this->options->baseUrl, '/chat/completions'), $b, $this->options->authHeaders(), $this->provider());

        return ChatResponseParser::parse($p, $this->provider());
    }

    public function stream(TextModelRequest $r): Generator
    {
        $this->ensureTextRequestSupported($r, self::CAPS, streaming: true);
        $b = ChatRequestBuilder::build($this->modelId, $this->provider(), $r, true);
        yield from ChatStreamParser::parse($this->runner($this->options->sdk)->postStream(Url::joinPath($this->options->baseUrl, '/chat/completions'), $b, $this->options->authHeaders(), $this->provider()), $this->provider());
    }
}
