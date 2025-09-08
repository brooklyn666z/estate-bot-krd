<?php

namespace App\Telegram\Survey;

use Illuminate\Support\Facades\Cache;

class SurveyStorage
{
    protected string $key;

    public function __construct(int|string $userId)
    {
        $this->key = "survey:{$userId}";
    }

    public function get(): array
    {
        return Cache::get($this->key, [
            'step'    => 1,
            'type'    => null,
            'credit'  => null,
            'contact' => null,
        ]);
    }

    public function set(array $data): void
    {
        // TTL на 2 часа, чтобы не висело бесконечно
        Cache::put($this->key, $data, now()->addHours(2));
    }

    public function update(array $patch): array
    {
        $data = array_merge($this->get(), $patch);
        $this->set($data);
        return $data;
    }

    public function reset(): void
    {
        Cache::forget($this->key);
    }
}
