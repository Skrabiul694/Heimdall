<?php

return [

    'default' => env('HEIMDALL_DEFAULT_PROVIDER', 'gemini'),
    'timeout' => env('HEIMDALL_REQUEST_TIMEOUT', 30),

    'providers' => [

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'default_model' => env('GEMINI_DEFAULT_MODEL', 'gemini-1.5-flash'),
        ],

        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'default_model' => env('OPENROUTER_DEFAULT_MODEL', 'meta-llama/llama-3.1-8b-instruct:free'),
            'site_url' => env('APP_URL', 'http://localhost'),
            'site_name' => env('APP_NAME', 'Heimdall Gateway'),
        ],

        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'organization' => env('OPENAI_ORGANIZATION'),
            'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4o-mini'),
            'base_url' => 'https://api.openai.com/v1/chat/completions',
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'default_model' => env('ANTHROPIC_DEFAULT_MODEL', 'claude-3-5-haiku-latest'),
        ],

        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'default_model' => env('DEEPSEEK_DEFAULT_MODEL', 'deepseek-v4-flash'),
            'base_url' => 'https://api.deepseek.com/chat/completions',
        ],

        'kimi' => [
            'api_key' => env('KIMI_API_KEY'),
            'default_model' => env('KIMI_DEFAULT_MODEL', 'kimi-k3'),
            'base_url' => 'https://api.moonshot.ai/v1/chat/completions',
        ],
    ],
];
