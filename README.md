# Alibaba Cloud Model Studio provider

<a href="https://github.com/phpaisdk/alibaba/actions"><img alt="GitHub Workflow Status" src="https://img.shields.io/github/actions/workflow/status/phpaisdk/alibaba/tests.yml?branch=main&label=Tests"></a>
<a href="https://packagist.org/packages/aisdk/alibaba"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/aisdk/alibaba"></a>
<a href="https://packagist.org/packages/aisdk/alibaba"><img alt="Latest Version" src="https://img.shields.io/packagist/v/aisdk/alibaba"></a>
<a href="https://packagist.org/packages/aisdk/alibaba"><img alt="License" src="https://img.shields.io/packagist/l/aisdk/alibaba"></a>
<a href="https://whyphp.dev"><img src="https://img.shields.io/badge/Why_PHP-in_2026-7A86E8?style=flat-square&labelColor=18181b" alt="Why PHP in 2026"></a>

------

Supports Qwen text generation and streaming, tools, structured output, reasoning, multimodal input, Wan image and video generation, Qwen speech synthesis, and text embeddings.

```php
$video = Generate::video('A cinematic city at night')
    ->model(Alibaba::model('wan2.7-t2v'))
    ->resolution('1920x1080')
    ->duration(5)
    ->run(timeout: 600);
```

Configure `ALIBABA_API_KEY`, plus region/workspace-specific `baseUrl` and `videoBaseUrl` when required by Model Studio.

## Documentation

- [PHP AI SDK documentation](https://phpaisdk.com/docs)
- [Alibaba documentation](https://phpaisdk.com/docs/alibaba)

## Community

- [Contributing](https://github.com/phpaisdk/.github/blob/main/CONTRIBUTING.md)
- [Support](https://github.com/phpaisdk/.github/blob/main/SUPPORT.md)
- For private security reports, email [security@phpaisdk.com](mailto:security@phpaisdk.com).
