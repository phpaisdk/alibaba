<?php

declare(strict_types=1);

namespace AiSdk\Alibaba\Models;

use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Content;
use AiSdk\ContentSource;
use AiSdk\Contracts\BaseModel;
use AiSdk\Contracts\VideoModelInterface;
use AiSdk\Exceptions\InvalidResponseException;
use AiSdk\Requests\VideoRequest;
use AiSdk\Responses\VideoJob;
use AiSdk\Responses\VideoJobStatus;
use AiSdk\Results\VideoData;
use AiSdk\Support\Usage;
use AiSdk\Utils\Support\Url;

final class AlibabaVideoModel extends BaseModel implements VideoModelInterface
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

    public function generate(VideoRequest $r): VideoJob
    {
        $o = $r->providerOptionsFor($this->provider());
        $mode = str_contains($this->modelId, '-i2v') ? 'i2v' : (str_contains($this->modelId, '-r2v') ? 'r2v' : 't2v');
        $input = ['prompt' => $r->prompt];
        if (isset($o['negativePrompt'])) {
            $input['negative_prompt'] = $o['negativePrompt'];
        }if (isset($o['audioUrl'])) {
            $input['audio_url'] = $o['audioUrl'];
        }if ($r->video !== null) {
            $input['video_url'] = $this->media($r->video);
        }if ($mode === 'i2v' && $r->image) {
            $input['img_url'] = $r->image->source() === ContentSource::Url ? $r->image->url() : 'data:' . $r->image->mimeType() . ';base64,' . $r->image->base64Data();
        }if ($mode === 'r2v' && is_array($o['referenceUrls'] ?? null)) {
            $input['reference_urls'] = $o['referenceUrls'];
        }$params = array_filter(['duration' => $r->output?->duration, 'seed' => $r->output?->seed], fn($v) => $v !== null);
        if ($r->output?->resolution) {
            $params[$mode === 'i2v' ? 'resolution' : 'size'] = $mode === 'i2v' ? $this->resolution($r->output->resolution) : str_replace('x', '*', $r->output->resolution);
        }foreach (['promptExtend' => 'prompt_extend', 'shotType' => 'shot_type', 'watermark' => 'watermark', 'audio' => 'audio'] as $k => $n) {
            if (array_key_exists($k, $o)) {
                $params[$n] = $o[$k];
            }
        }$p = $this->runner($this->options->sdk)->postJson(Url::joinPath($this->options->videoBaseUrl, '/api/v1/services/aigc/video-generation/video-synthesis'), ['model' => $this->modelId, 'input' => $input, 'parameters' => $params], array_merge($this->options->authHeaders(), ['X-DashScope-Async' => 'enable']), $this->provider());
        $id = $p['output']['task_id'] ?? null;
        if (! is_string($id) || $id === '') {
            throw InvalidResponseException::forProvider($this->provider(), 'Alibaba returned no video task id.', ['body' => $p]);
        }

        return new VideoJob($id, $this->provider(), $this->modelId, rawResponse: $p, providerMetadata: [$this->provider() => ['taskId' => $id, 'pollIntervalMs' => (int) ($o['pollIntervalMs'] ?? 5000), 'pollTimeoutMs' => (int) ($o['pollTimeoutMs'] ?? 600000)]]);
    }

    public function poll(VideoJob $j): VideoJob
    {
        $p = $this->runner($this->options->sdk)->getJson(Url::joinPath($this->options->videoBaseUrl, '/api/v1/tasks/' . rawurlencode($j->id)), $this->options->authHeaders(), $this->provider());
        $s = (string) ($p['output']['task_status'] ?? '');
        if ($s === 'SUCCEEDED') {
            $url = $p['output']['video_url'] ?? null;

            return is_string($url) && $url !== '' ? new VideoJob($j->id, $j->provider, $j->modelId, VideoJobStatus::Succeeded, new VideoData(url: $url, duration: isset($p['usage']['output_video_duration']) ? (float) $p['usage']['output_video_duration'] : null), usage: Usage::empty(), rawResponse: $p, providerMetadata: $j->providerMetadata) : new VideoJob($j->id, $j->provider, $j->modelId, VideoJobStatus::Failed, errorMessage: 'Alibaba completed without a video URL.', rawResponse: $p, providerMetadata: $j->providerMetadata);
        }

        return new VideoJob($j->id, $j->provider, $j->modelId, in_array($s, ['FAILED', 'CANCELED'], true) ? ($s === 'CANCELED' ? VideoJobStatus::Canceled : VideoJobStatus::Failed) : VideoJobStatus::Running, errorMessage: in_array($s, ['FAILED', 'CANCELED'], true) ? (string) ($p['output']['message'] ?? 'Alibaba video generation failed.') : null, rawResponse: $p, providerMetadata: $j->providerMetadata);
    }

    private function resolution(string $r): string
    {
        return match ($r) {
            '1280x720','720x1280','960x960' => '720P','1920x1080','1080x1920','1440x1440' => '1080P','832x480','480x832','624x624' => '480P',default => $r,
        };
    }

    private function media(Content $content): string
    {
        return $content->source() === ContentSource::Url
            ? (string) $content->url()
            : 'data:' . $content->mimeType() . ';base64,' . $content->base64Data();
    }
}
