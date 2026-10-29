<?php

declare(strict_types=1);

namespace AiSdk\Alibaba\Models;

use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Contracts\BaseModel;
use AiSdk\Contracts\SpeechModelInterface;
use AiSdk\Exceptions\InvalidResponseException;
use AiSdk\Generate;
use AiSdk\Requests\SpeechRequest;
use AiSdk\Responses\SpeechResponse;
use AiSdk\Results\AudioData;
use AiSdk\Support\Usage;
use AiSdk\Utils\Http\RemoteFetch;
use AiSdk\Utils\Support\Url;
use Closure;

final class AlibabaSpeechModel extends BaseModel implements SpeechModelInterface
{
    /** @param (Closure(string): list<string>)|null $dnsResolver */
    public function __construct(private readonly string $modelId, private readonly AlibabaOptions $options, private readonly ?Closure $dnsResolver = null) {}

    public function provider(): string
    {
        return AlibabaOptions::PROVIDER_NAME;
    }

    public function modelId(): string
    {
        return $this->modelId;
    }

    public function generate(SpeechRequest $r): SpeechResponse
    {
        $o = $r->providerOptionsFor($this->provider());
        $input = array_replace(['text' => $r->input, 'voice' => $r->voice ?? 'Cherry', 'language_type' => $o['languageType'] ?? 'Auto'], array_diff_key($o, array_flip(['format'])));
        $p = $this->runner($this->options->sdk)->postJson(Url::joinPath($this->options->videoBaseUrl, '/api/v1/services/aigc/multimodal-generation/generation'), ['model' => $this->modelId, 'input' => $input], $this->options->authHeaders(), $this->provider());
        $url = $p['output']['audio']['url'] ?? null;
        $base64 = $p['output']['audio']['data'] ?? null;
        if (is_string($base64) && $base64 !== '') {
            $data = base64_decode($base64, true);
        } elseif (is_string($url) && $url !== '') {
            $sdk = $this->options->sdk ?? Generate::sdk();
            $res = RemoteFetch::bytes($sdk, $url, dnsResolver: $this->dnsResolver);
            $data = $res->body;
        } else {
            throw InvalidResponseException::forProvider($this->provider(), 'Alibaba TTS returned no audio.', ['body' => $p]);
        }if ($data === false || $data === '') {
            throw InvalidResponseException::forProvider($this->provider(), 'Alibaba TTS returned empty audio.', ['body' => $p]);
        }$format = $r->format ?? $o['format'] ?? 'mp3';

        return new SpeechResponse(new AudioData($data, $format === 'wav' ? 'audio/wav' : 'audio/mpeg'), Usage::empty(), $p, [$this->provider() => ['audioUrl' => $url]]);
    }
}
