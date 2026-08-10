<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key', 'store_name', 'email', 'phone', 'whatsapp', 'address', 'province', 'city',
    'postal_code', 'logo_path', 'favicon_path', 'description', 'operating_hours',
    'website_url', 'social_links', 'additional_contacts', 'metadata',
])]
class StoreSetting extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'social_links' => AsArrayObject::class,
            'additional_contacts' => AsArrayObject::class,
            'metadata' => AsArrayObject::class,
        ];
    }
}
