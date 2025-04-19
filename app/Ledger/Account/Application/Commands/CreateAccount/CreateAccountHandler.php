<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Commands\CreateAccount;

use App\Ledger\Account\Domain\Account;
use App\Ledger\Account\Domain\AccountRepository;
use App\Ledger\Account\Domain\Enums\AccountType;
use App\Ledger\Account\Domain\Exceptions\InvalidAccountCodeException;
use App\Ledger\Account\Domain\ValueObjects\AccountCode;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Shared\Application\Command;
use App\Shared\Application\CommandHandler;

final class CreateAccountHandler implements CommandHandler
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {
    }

    public function __invoke(CreateAccountCommand $command): void
    {
        $id = new AccountId($command->id);
        $code = new AccountCode($command->code);
        $type = AccountType::from($command->type);
        $parentId = $command->parentId ? new AccountId($command->parentId) : null;

        if ($this->accountRepository->codeExists($code)) {
            throw new InvalidAccountCodeException('Account code already exists');
        }

        $account = Account::create(
            $id,
            $code,
            $type,
            $parentId,
            $command->isCategory,
            $command->taxCode,
            $command->extra
        );

        $this->accountRepository->save($account);
    }
}
