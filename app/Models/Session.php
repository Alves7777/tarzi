<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\UserAgent;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session as SessionFacade;

/**
 * A row of Laravel's database session driver.
 *
 * Read-only by nature: the framework owns the writes, the panel only lists the
 * rows and deletes them to sign a device out.
 *
 * @property string $id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property int $last_activity
 */
class Session extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function getTable(): string
    {
        return (string) config('session.table', 'sessions');
    }

    public function getConnectionName(): ?string
    {
        return config('session.connection') ?? parent::getConnectionName();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_activity' => 'integer',
            'user_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Whether this row is the session making the current request.
     */
    public function isCurrent(): bool
    {
        return $this->getKey() === SessionFacade::getId();
    }

    public function lastActiveAt(): CarbonInterface
    {
        return Carbon::createFromTimestamp($this->last_activity);
    }

    public function agent(): UserAgent
    {
        return UserAgent::parse($this->user_agent);
    }

    /**
     * Sessions that have been touched within the given number of minutes.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeActiveWithin(Builder $query, int $minutes): void
    {
        $query->where('last_activity', '>=', now()->subMinutes($minutes)->getTimestamp());
    }

    /**
     * @param  Builder<$this>  $query
     */
    public function scopeForUser(Builder $query, User $user): void
    {
        $query->where('user_id', $user->getKey());
    }
}
