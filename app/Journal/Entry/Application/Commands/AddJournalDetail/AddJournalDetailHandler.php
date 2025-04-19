<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\AddJournalDetail;

use App\Journal\Detail\Domain\JournalDetail;
use App\Journal\Detail\Domain\JournalDetailRepository;
use App\Journal\Detail\Domain\ValueObjects\JournalDetailId;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Ledger\Currency\Domain\ValueObjects\Currency;
use App\Ledger\Currency\Domain\ValueObjects\Money;
use App\Shared\Application\CommandHandler;

final class AddJournalDetailHandler implements CommandHandler
{
    public function __construct(
        private readonly JournalEntryRepository $journalEntryRepository,
        private readonly JournalDetailRepository $journalDetailRepository
    ) {
    }

    public function __invoke(AddJournalDetailCommand $command): void
    {
        $id = new JournalDetailId($command->id);
        $journalEntryId = new JournalEntryId($command->journalEntryId);
        $accountId = new AccountId($command->accountId);
        $currency = new Currency($command->currency);
        $money = new Money($command->amount, $currency);

        $journalEntry = $this->journalEntryRepository->findById($journalEntryId);

        if ($journalEntry === null) {
            throw new \InvalidArgumentException("Journal entry with ID {$command->journalEntryId} not found");
        }

        $journalDetail = JournalDetail::create(
            $id,
            $journalEntryId,
            $accountId,
            $money,
            $command->memo,
            $command->extra
        );

        $journalEntry->addDetail($journalDetail);

        $this->journalDetailRepository->save($journalDetail);
        $this->journalEntryRepository->save($journalEntry);
    }
}
