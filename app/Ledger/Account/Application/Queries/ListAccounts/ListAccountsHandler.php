<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Queries\ListAccounts;

use App\Ledger\Account\Domain\AccountRepository;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Shared\Application\QueryHandler;

final class ListAccountsHandler implements QueryHandler
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {
    }

    public function __invoke(ListAccountsQuery $query): array
    {
        if ($query->parentId) {
            $parentId = new AccountId($query->parentId);
            $accounts = $this->accountRepository->findChildren($parentId);
        } else {
            $accounts = $this->accountRepository->findAll();
        }

        return array_map(function ($account) {
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
        }, $accounts);
    }
}
