<?php

declare(strict_types=1);

namespace App\Journal\Entry\Infrastructure\Laravel;

use App\Journal\Detail\Infrastructure\Laravel\JournalDetailEloquentModel;
use App\Journal\Entry\Domain\Enums\EntryStatus;
use App\Journal\Entry\Domain\Enums\EntryType;
use App\Journal\Entry\Domain\JournalEntry as JournalEntryDomain;
use App\Journal\Entry\Domain\ValueObjects\Description;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Ledger\Currency\Domain\ValueObjects\Currency;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class JournalEntryEloquentModel extends Model
{
    use HasUuids;
    
    protected $table = 'journal_entries';
    
    protected $fillable = [
        'id',
        'type',
        'status',
        'description',
        'date',
        'currency',
        'reversal_of',
        'extra'
    ];
    
    protected $casts = [
        'date' => 'datetime',
        'extra' => 'array'
    ];
    
    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of');
    }
    
    public function reversals(): HasMany
    {
        return $this->hasMany(self::class, 'reversal_of');
    }
    
    public function details(): HasMany
    {
        return $this->hasMany(JournalDetailEloquentModel::class, 'journal_entry_id');
    }
    
    public function toDomain(): JournalEntryDomain
    {
        $id = new JournalEntryId($this->id);
        $type = EntryType::from($this->type);
        $status = EntryStatus::from($this->status);
        $description = new Description($this->description);
        $date = new DateTimeImmutable($this->date);
        $currency = new Currency($this->currency);
        $reversalOf = $this->reversal_of ? new JournalEntryId($this->reversal_of) : null;
        
        return JournalEntryDomain::fromPrimitives(
            $id,
            $type,
            $status,
            $description,
            $date,
            $currency,
            $reversalOf,
            $this->extra
        );
    }
    
    public static function fromDomain(JournalEntryDomain $journalEntry): self
    {
        $model = new self();
        $model->id = $journalEntry->id()->value();
        $model->type = $journalEntry->type()->value;
        $model->status = $journalEntry->status()->value;
        $model->description = $journalEntry->description()->value();
        $model->date = $journalEntry->date()->format('Y-m-d H:i:s');
        $model->currency = $journalEntry->currency()->value();
        $model->reversal_of = $journalEntry->reversalOf() ? $journalEntry->reversalOf()->value() : null;
        $model->extra = $journalEntry->extra();
        
        return $model;
    }
}
