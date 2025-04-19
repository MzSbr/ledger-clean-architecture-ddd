<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Queries\GetAccount;

use App\Ledger\Account\Domain\AccountRepository;
use App\Ledger\Account\Domain\Exceptions\AccountNotFoundException;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Shared\Application\QueryHandler;

final class GetAccountHandler implements QueryHandler
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {
    }

    public function __invoke(GetAccountQuery $query): array
    {
        $id = new AccountId($query->id);

        $account = $this->accountRepository->findById($id);

        if ($account === null) {
            throw new AccountNotFoundException("Account with ID {$query->id} not found");
        }

        return [
            'id' => $account->id()->value(),
            'code' => $account->code()->value(),
            'type' => $account->type()->value,
            'status' => $account->status()->value,
            'parentId' => $account->parentId() ? $account->parentId()->value() : null,
            'isCategory' => $account->isCategory(),
            'taxCode' => $account->taxCode(),
            'extra' => $account->extra(),
        ];
    }
}
