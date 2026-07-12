<?php

declare(strict_types=1);

namespace AiSdk\Alibaba;

use AiSdk\Alibaba\Models\AlibabaEmbeddingModel;
use AiSdk\Alibaba\Models\AlibabaImageModel;
use AiSdk\Alibaba\Models\AlibabaSpeechModel;
use AiSdk\Alibaba\Models\AlibabaTextModel;
use AiSdk\Alibaba\Models\AlibabaVideoModel;
use AiSdk\Contracts\BaseProvider;
use AiSdk\Contracts\EmbeddingModelInterface;
use AiSdk\Contracts\EmbeddingProviderInterface;
use AiSdk\Contracts\ImageModelInterface;
use AiSdk\Contracts\ImageProviderInterface;
use AiSdk\Contracts\SpeechModelInterface;
use AiSdk\Contracts\SpeechProviderInterface;
use AiSdk\Contracts\TextModelInterface;
use AiSdk\Contracts\TextProviderInterface;
use AiSdk\Contracts\VideoModelInterface;
use AiSdk\Contracts\VideoProviderInterface;

final class AlibabaProvider extends BaseProvider implements EmbeddingProviderInterface, ImageProviderInterface, SpeechProviderInterface, TextProviderInterface, VideoProviderInterface
{
    public function __construct(public readonly AlibabaOptions $options) {}

    public function name(): string
    {
        return AlibabaOptions::PROVIDER_NAME;
    }

    public function textModel(string $id): TextModelInterface
    {
        return new AlibabaTextModel($id, $this->options);
    }

    public function embeddingModel(string $id): EmbeddingModelInterface
    {
        return new AlibabaEmbeddingModel($id, $this->options);
    }

    public function imageModel(string $id): ImageModelInterface
    {
        return new AlibabaImageModel($id, $this->options);
    }

    public function speechModel(string $id): SpeechModelInterface
    {
        return new AlibabaSpeechModel($id, $this->options);
    }

    public function videoModel(string $id): VideoModelInterface
    {
        return new AlibabaVideoModel($id, $this->options);
    }
}
