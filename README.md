# Alibaba Cloud Model Studio provider

Supports Qwen text generation and streaming, tools, structured output, reasoning, multimodal input, Wan image and video generation, Qwen speech synthesis, and text embeddings.

```php
$video = Generate::video('A cinematic city at night')
    ->model(Alibaba::model('wan2.7-t2v'))
    ->resolution('1920x1080')
    ->duration(5)
    ->run(timeout: 600);
```

Configure `ALIBABA_API_KEY`, plus region/workspace-specific `baseUrl` and `videoBaseUrl` when required by Model Studio.
