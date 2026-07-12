<?php

declare(strict_types=1);

namespace AiSdk;

use AiSdk\Alibaba\AlibabaOptions;
use AiSdk\Alibaba\AlibabaProvider;
use AiSdk\Contracts\EmbeddingModelInterface;
use AiSdk\Contracts\ImageModelInterface;
use AiSdk\Contracts\SpeechModelInterface;
use AiSdk\Contracts\TextModelInterface;
use AiSdk\Contracts\VideoModelInterface;

final class Alibaba
{
    private static ?AlibabaProvider $default = null;

    /** @param array<string, mixed> $c */
    public static function create(array $c = []): AlibabaProvider
    {
        return self::$default = new AlibabaProvider(AlibabaOptions::fromArray($c));
    }

    public static function default(): AlibabaProvider
    {
        return self::$default ??= self::create();
    }

    public static function reset(): void
    {
        self::$default = null;
    }

    public static function model(string $id): TextModelInterface
    {
        return self::default()->textModel($id);
    }

    public static function embedding(string $id): EmbeddingModelInterface
    {
        return self::default()->embeddingModel($id);
    }

    public static function image(string $id): ImageModelInterface
    {
        return self::default()->imageModel($id);
    }

    public static function speech(string $id): SpeechModelInterface
    {
        return self::default()->speechModel($id);
    }

    public static function video(string $id): VideoModelInterface
    {
        return self::default()->videoModel($id);
    }
}
