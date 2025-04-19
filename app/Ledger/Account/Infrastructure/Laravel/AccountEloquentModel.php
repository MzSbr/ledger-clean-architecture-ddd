<?php

declare(strict_types=1);

namespace App\Ledger\Account\Infrastructure\Laravel;

use App\Ledger\Account\Domain\Account as AccountDomain;
use App\Ledger\Account\Domain\Enums\AccountStatus;
use App\Ledger\Account\Domain\Enums\AccountType;
use App\Ledger\Account\Domain\ValueObjects\AccountCode;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Shared\Domain\Traits\HasNames;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Shared\Domain\Traits\HasRevisions;

final class AccountEloquentModel extends Model
{
    use HasUuids;
    use HasRevisions;
    use HasNames;

    protected $table = 'ledger_accounts';

    protected $fillable = [
        'id',
        'code',
        'type',
        'status',
        'parent_id',
        'is_category',
        'tax_code',
        'extra'
    ];

    protected $casts = [
        'is_category' => 'boolean',
        'extra' => 'array'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function toDomain(): AccountDomain
    {
        $id = new AccountId($this->id);
        $code = new AccountCode($this->code);
        $type = AccountType::from($this->type);
        $status = AccountStatus::from($this->status);
        $parentId = $this->parent_id ? new AccountId($this->parent_id) : null;

        return AccountDomain::fromPrimitives(
            $id,
            $code,
            $type,
            $status,
            $parentId,
            $this->is_category,
            $this->tax_code,
            $this->extra
        );
    }

    public static function fromDomain(AccountDomain $account): self
    {
        $model = new self();
        $model->id = $account->id()->value();
        $model->code = $account->code()->value();
        $model->type = $account->type()->value;
        $model->status = $account->status()->value;
        $model->parent_id = $account->parentId() ? $account->parentId()->value() : null;
        $model->is_category = $account->isCategory();
        $model->tax_code = $account->taxCode();
        $model->extra = $account->extra();

        return $model;
    }
}
