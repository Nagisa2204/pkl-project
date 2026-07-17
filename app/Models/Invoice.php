<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property string $invoice_no
 * @property string $file_path
 * @property Carbon|null $generated_at
 * @property Carbon|null $emailed_at
 * @property Carbon|null $email_sending_at
 * @property Carbon|null $admin_emailed_at
 * @property Carbon|null $admin_email_sending_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

#[Fillable(['order_id', 'invoice_no', 'file_path', 'generated_at', 'emailed_at', 'email_sending_at', 'admin_emailed_at', 'admin_email_sending_at'])]
#[Hidden(['file_path'])]
class Invoice extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'emailed_at' => 'datetime',
            'email_sending_at' => 'datetime',
            'admin_emailed_at' => 'datetime',
            'admin_email_sending_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
