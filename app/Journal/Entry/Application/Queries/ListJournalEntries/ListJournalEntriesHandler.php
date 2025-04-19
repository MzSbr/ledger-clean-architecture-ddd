<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Queries\ListJournalEntries;

use App\Journal\Entry\Domain\Enums\EntryStatus;
use App\Journal\Entry\Domain\Enums\EntryType;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Shared\Application\QueryHandler;
use DateTimeImmutable;

final class ListJournalEntriesHandler implements QueryHandler
{
    public function __construct(
        private readonly JournalEntryRepository $journalEntryRepository
    ) {
    }

    public function __invoke(ListJournalEntriesQuery $query): array
    {
        $entries = $this->journalEntryRepository->findAll();
        
        // Apply filters
        if ($query->type !== null) {
            $type = EntryType::from($query->type);
            $entries = array_filter($entries, fn($entry) => $entry->type() === $type);
        }
        
        if ($query->status !== null) {
            $status = EntryStatus::from($query->status);
            $entries = array_filter($entries, fn($entry) => $entry->status() === $status);
        }
        
        if ($query->fromDate !== null) {
            $fromDate = new DateTimeImmutable($query->fromDate);
            $entries = array_filter($entries, fn($entry) => $entry->date() >= $fromDate);
        }
        
        if ($query->toDate !== null) {
            $toDate = new DateTimeImmutable($query->toDate);
            $entries = array_filter($entries, fn($entry) => $entry->date() <= $toDate);
        }

        return array_map(function ($entry) {
            return [
                'id' => $entry->id()->value(),
                'type' => $entry->type()->value,
                'status' => $entry->status()->value,
                'description' => $entry->description()->value(),
                'date' => $entry->date()->format('Y-m-d H:i:s'),
                'currency' => $entry->currency()->value(),
                'reversalOf' => $entry->reversalOf() ? $entry->reversalOf()->value() : null,
            ];
        }, $entries);
    }
}
