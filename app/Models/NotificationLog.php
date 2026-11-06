<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationEvent;
use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Records that a notification was generated, so that it is only sent once.
 */
#[Fillable(['user_id', 'event'])]
#[Table('notification_log')]
class NotificationLog extends Model
{
    use BelongsToUser;

    public const ?string UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event' => NotificationEvent::class,
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
