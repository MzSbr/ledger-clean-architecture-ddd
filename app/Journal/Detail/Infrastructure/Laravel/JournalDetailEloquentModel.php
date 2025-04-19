<?php

declare(strict_types=1);

namespace App\Journal\Detail\Infrastructure\Laravel;

use App\Journal\Detail\Domain\JournalDetail as JournalDetailDomain;
use App\Journal\Detail\Domain\ValueObjects\JournalDetailId;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Journal\Entry\Infrastructure\Laravel\JournalEntryEloquentModel;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Ledger\Account\Infrastructure\Laravel\AccountEloquentModel;
use App\Ledger\Currency\Domain\ValueObjects\Currency;
use App\Ledger\Currency\Domain\ValueObjects\Money;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class JournalDetailEloquentModel extends Model
{
    use HasUuids;
    
    protected $table = 'journal_details';
    
    protected $fillable = [
        'id',
        'journal_entry_id',
        'account_id',
        'amount',
        'currency',
        'memo',
        'extra'
    ];
    
    protected $casts = [
        'amount' => 'integer',
        'extra' => 'array'
    ];
    
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntryEloquentModel::class, 'journal_entry_id');
    }
    
    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountEloquentModel::class, 'account_id');
    }
    
    public function toDomain(): JournalDetailDomain
    {
        $id = new JournalDetailId($this->id);
        $journalEntryId = new JournalEntryId($this->journal_entry_id);
        $accountId = new AccountId($this->account_id);
        $currency = new Currency($this->currency);
        $money = new Money($this->amount, $currency);
        
        return JournalDetailDomain::create(
            $id,
            $journalEntryId,
            $accountId,
            $money,
            $this->memo,
            $this->extra
        );
    }
    
    public static function fromDomain(JournalDetailDomain $journalDetail): self
    {
        $model = new self();
        $model->id = $journalDetail->id()->value();
        $model->journal_entry_id = $journalDetail->journalEntryId()->value();
        $model->account_id = $journalDetail->accountId()->value();
        $model->amount = $journalDetail->amount();
        $model->currency = $journalDetail->money()->currency()->value();
        $model->memo = $journalDetail->memo();
        $model->extra = $journalDetail->extra();
        
        return $model;
    }
}
