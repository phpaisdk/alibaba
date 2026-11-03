# PHP AI SDK — Alibaba Cloud Model Studio

Text, image, speech, embedding, and video generation with Qwen and Wan models. Part of the framework-agnostic [PHP AI SDK](https://phpaisdk.com).

## Installation

```bash
composer require aisdk/alibaba
```

Set `ALIBABA_API_KEY` in your environment.

## Usage

```php
use AiSdk\Alibaba;
use AiSdk\Generate;

$result = Generate::text('Explain closures in PHP in one sentence.')
    ->model(Alibaba::model('qwen3.8-max'))
    ->run();

echo $result->text;
```

## Documentation

[Read the documentation](https://phpaisdk.com/docs/0.x/providers/alibaba) for configuration, supported features, and more examples.

[Contributing](https://github.com/phpaisdk/.github/blob/main/CONTRIBUTING.md) · [Support](https://github.com/phpaisdk/.github/blob/main/SUPPORT.md)

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.
