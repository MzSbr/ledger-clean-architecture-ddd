<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Commands\CloseAccount;

use App\Ledger\Account\Domain\AccountRepository;
use App\Ledger\Account\Domain\Exceptions\AccountNotFoundException;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Shared\Application\CommandHandler;

final class CloseAccountHandler implements CommandHandler
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {
    }

    public function __invoke(CloseAccountCommand $command): void
    {
        $id = new AccountId($command->id);

        $account = $this->accountRepository->findById($id);

        if ($account === null) {
            throw new AccountNotFoundException("Account with ID {$command->id} not found");
        }

        $account->close();

        $this->accountRepository->save($account);
    }
}
