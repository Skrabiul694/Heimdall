# Heimdall
An open-source package or mini-service that standardizes how applications talk to AI models. Instead of writing custom API integration code for every provider, this package offers a unified interface.
[![Latest Version on Packagist](https://img.shields.io/packagist/v/vendor/llm-routing-gateway.svg?style=flat-square)](https://packagist.org/packages/vendor/llm-routing-gateway)
[![Total Downloads](https://img.shields.io/packagist/dt/vendor/llm-routing-gateway.svg?style=flat-square)](https://packagist.org/packages/vendor/llm-routing-gateway)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

A high-availability, multi-provider LLM routing engine and telemetry suite for **Laravel 11**. 

Designed using the **Manager Pattern**, this package unifies disparate AI provider payloads into strict Data Transfer Objects (DTOs) and executes automated, array-defined **failover pipelines**. It isolates your application from vendor outages, rate limits, and network dropouts while automatically recording token metrics, execution latencies, and raw payloads directly to your database.

---

## Core Architectural Features

- **Unified Payload Normalization:** Standardizes input and output payloads across competing API schemas (Google's nested content structure, Anthropic's system constraints, and standard OpenAI chat completion signatures).
- **Automated Failover Pipelines:** Attempts primary providers and seamlessly falls through backup providers upon HTTP errors or timeouts, logging failures transparently along the chain.
- **DRY Driver Integration:** Features an expandable `OpenAiCompatibleDriver` that powers OpenAI, DeepSeek, and Kimi dynamically without code duplication.
- **Database Telemetry & Auditing:** Automatically logs execution latencies ($ms$), token counts, request hashes, success rates, error exceptions, and raw JSON payloads via an included migration and Eloquent model.
- **Testing Capabilities:** Built with native `Http::fake()` isolation hooks to test multi-driver fallback chains without hitting live API endpoints.

---

## Supported Providers Matrix

| Provider Key | Primary Flagship Target | Driver Implementation | API Protocol |
| :--- | :--- | :--- | :--- |
| **`gemini`** | Gemini 1.5 Flash / Pro | Dedicated Native Driver | Google AI REST v1beta |
| **`anthropic`** | Claude 3.5 Haiku / Sonnet | Dedicated Native Driver | Anthropic Messages API |
| **`openrouter`** | Llama 3.1 & Multi-Model Proxy | Custom Proxy Driver | OpenRouter API v1 |
| **`openai`** | GPT-4o / GPT-4o-mini | OpenAI-Compatible Driver | OpenAI v1 Chat Completions |
| **`deepseek`** | DeepSeek V4 Series | OpenAI-Compatible Driver | DeepSeek v1 Chat Completions |
| **`kimi`** | Moonshot Kimi K3 | OpenAI-Compatible Driver | Moonshot v1 Chat Completions |

---

## Installation & Setup

### 1. Require Package via Composer
Install the package directly into your Laravel application:

```bash
composer require vendor/heimdall
```
### 2. Publish Configuration & Database Migrations
```bash
php artisan vendor:publish --provider="Vendor\Heimdall\HeimdallServiceProvider"
```
### 3. Run Package Migrations
```bash
php artisan migrate
```
### 4. Environment Configuration
```bash
HEIMDALL_DEFAULT_PROVIDER=gemini
HEIMDALL_REQUEST_TIMEOUT=30

GEMINI_API_KEY=your_gemini_api_key
ANTHROPIC_API_KEY=your_anthropic_api_key
OPENROUTER_API_KEY=your_openrouter_api_key

OPENAI_API_KEY=your_openai_api_key
DEEPSEEK_API_KEY=your_deepseek_api_key
KIMI_API_KEY=your_kimi_api_key
```
