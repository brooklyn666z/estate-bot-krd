<?php

namespace App\Telegram\Broadcast;

use Illuminate\Support\Facades\Cache;

class BroadcastDraft
{
    public function __construct(protected int|string $userId) {}

    protected function key(): string { return "broadcast:draft:{$this->userId}"; }

    public function get(): array {
        return Cache::get($this->key(), [
            'step'   => 'idle',   // idle|wait_key|menu|wait_text|ask_media|collect_media|confirm
            'text'   => null,
            'photos' => [],       // массив file_id
        ]);
    }

    public function set(array $data): void { Cache::put($this->key(), $data, now()->addHours(2)); }

    public function update(array $patch): array {
        $data = array_merge($this->get(), $patch);
        $this->set($data);
        return $data;
    }

    public function reset(): void { Cache::forget($this->key()); }
}
