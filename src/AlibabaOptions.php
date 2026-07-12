<?php

declare(strict_types=1);

namespace AiSdk\Alibaba;

use AiSdk\Support\Sdk;
use AiSdk\Utils\Support\Env;
use AiSdk\Utils\Support\Url;

final class AlibabaOptions
{
    public const string PROVIDER_NAME = 'alibaba';

    /** @param array<string, string> $headers */
    public function __construct(public readonly string $apiKey, public readonly string $baseUrl = 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1', public readonly string $videoBaseUrl = 'https://dashscope-intl.aliyuncs.com', public readonly array $headers = [], public readonly ?Sdk $sdk = null) {}

    /** @param array<string, mixed> $c */
    public static function fromArray(array $c = []): self
    {
        return new self(Env::loadApiKey(isset($c['apiKey']) ? (string) $c['apiKey'] : null, 'ALIBABA_API_KEY', self::PROVIDER_NAME), Url::withoutTrailingSlash((string) ($c['baseUrl'] ?? 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1')), Url::withoutTrailingSlash((string) ($c['videoBaseUrl'] ?? 'https://dashscope-intl.aliyuncs.com')), is_array($c['headers'] ?? null) ? $c['headers'] : [], ($c['sdk'] ?? null) instanceof Sdk ? $c['sdk'] : null);
    }

    /** @return array<string, string> */
    public function authHeaders(): array
    {
        return array_merge(['Authorization' => 'Bearer '.$this->apiKey], $this->headers);
    }
}
