<?php

namespace App\Console\Commands;

use App\Models\Access;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccessCreate extends Command
{
    protected $signature = 'access:create
        {--r|role= : Role: create|create-mirror|mirror}
        {--d|description= : Unique description (max 70 chars)}';

    protected $description = 'Create an Access row and mint a Sanctum token for it';

    public function handle(): int
    {
        $data = [
            'role' => $this->option('role'),
            'description' => $this->option('description'),
        ];

        $v = Validator::make($data, [
            'role' => ['required', Rule::in(['create', 'create-mirror', 'mirror'])],
            'description' => ['required', 'string', 'max:70', 'unique:accesses,description'],
        ]);

        if ($v->fails()) {
            foreach ($v->errors()->all() as $err) {
                $this->error($err);
            }
            return self::FAILURE;
        }

        $access = Access::create([
            'role' => $data['role'],
            'description' => $data['description'],
        ]);

        // Sanctum token (stored in personal_access_tokens)
        $plainTextToken = $access->createToken($access->description)->plainTextToken;

        $payload = [
            'id' => $access->id,
            'description' => $access->description,
            'role' => $access->role,
            'token' => $plainTextToken,
        ];

        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return self::SUCCESS;
    }
}