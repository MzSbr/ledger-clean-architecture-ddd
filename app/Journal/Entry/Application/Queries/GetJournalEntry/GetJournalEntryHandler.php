<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Queries\GetJournalEntry;

use App\Journal\Detail\Domain\JournalDetailRepository;
use App\Journal\Entry\Domain\Exceptions\JournalEntryNotFoundException;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Shared\Application\QueryHandler;

final class GetJournalEntryHandler implements QueryHandler
{
    public function __construct(
        private readonly JournalEntryRepository $journalEntryRepository,
        private readonly JournalDetailRepository $journalDetailRepository
    ) {
    }

    public function __invoke(GetJournalEntryQuery $query): array
    {
        $id = new JournalEntryId($query->id);

        $journalEntry = $this->journalEntryRepository->findById($id);

        if ($journalEntry === null) {
            throw new JournalEntryNotFoundException("Journal entry with ID {$query->id} not found");
        }

        $details = $this->journalDetailRepository->findByJournalEntryId($id);

        $detailsData = array_map(function ($detail) {
            return [
                'id' => $detail->id()->value(),
                'accountId' => $detail->accountId()->value(),
                'amount' => $detail->amount(),
                'currency' => $detail->money()->currency()->value(),
                'memo' => $detail->memo(),
                'extra' => $detail->extra(),
            ];
        }, $details);

        return [
            'id' => $journalEntry->id()->value(),
            'type' => $journalEntry->type()->value,
            'status' => $journalEntry->status()->value,
            'description' => $journalEntry->description()->value(),
            'date' => $journalEntry->date()->format('Y-m-d H:i:s'),
            'currency' => $journalEntry->currency()->value(),
            'reversalOf' => $journalEntry->reversalOf() ? $journalEntry->reversalOf()->value() : null,
            'extra' => $journalEntry->extra(),
            'details' => $detailsData,
        ];
    }
}
