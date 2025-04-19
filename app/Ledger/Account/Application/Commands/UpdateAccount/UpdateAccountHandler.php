<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Commands\UpdateAccount;

use App\Ledger\Account\Domain\AccountRepository;
use App\Ledger\Account\Domain\Exceptions\AccountNotFoundException;
use App\Ledger\Account\Domain\ValueObjects\AccountCode;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Shared\Application\CommandHandler;

final class UpdateAccountHandler implements CommandHandler
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {
    }

    public function __invoke(UpdateAccountCommand $command): void
    {
        $id = new AccountId($command->id);
        $code = new AccountCode($command->code);

        $account = $this->accountRepository->findById($id);

        if ($account === null) {
            throw new AccountNotFoundException("Account with ID {$command->id} not found");
        }

        $account->update(
            $code,
            $command->taxCode,
            $command->extra
        );

        $this->accountRepository->save($account);
    }
}
