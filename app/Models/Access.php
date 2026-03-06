<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Access extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['description', 'role'];
}
