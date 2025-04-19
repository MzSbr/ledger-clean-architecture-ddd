<?php

declare(strict_types=1);

namespace App\Ledger\Account\Presentation\API;

use App\Ledger\Account\Application\Commands\CloseAccount\CloseAccountCommand;
use App\Ledger\Account\Application\Commands\CreateAccount\CreateAccountCommand;
use App\Ledger\Account\Application\Commands\UpdateAccount\UpdateAccountCommand;
use App\Ledger\Account\Application\Queries\GetAccount\GetAccountQuery;
use App\Ledger\Account\Application\Queries\ListAccounts\ListAccountsQuery;
use App\Shared\Application\CommandBus;
use App\Shared\Application\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

final class AccountController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'type' => 'required|string|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|uuid',
            'is_category' => 'boolean',
            'tax_code' => 'nullable|string|max:50',
            'extra' => 'nullable|array'
        ]);

        $id = Uuid::uuid4()->toString();

        $this->commandBus->dispatch(new CreateAccountCommand(
            $id,
            $request->input('code'),
            $request->input('type'),
            $request->input('parent_id'),
            $request->input('is_category', false),
            $request->input('tax_code'),
            $request->input('extra')
        ));

        return new JsonResponse([
            'id' => $id,
            'message' => 'Account created successfully'
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'tax_code' => 'nullable|string|max:50',
            'extra' => 'nullable|array'
        ]);

        $this->commandBus->dispatch(new UpdateAccountCommand(
            $id,
            $request->input('code'),
            $request->input('tax_code'),
            $request->input('extra')
        ));

        return new JsonResponse([
            'message' => 'Account updated successfully'
        ]);
    }

    public function close(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new CloseAccountCommand($id));

        return new JsonResponse([
            'message' => 'Account closed successfully'
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $account = $this->queryBus->ask(new GetAccountQuery($id));

        return new JsonResponse($account);
    }

    public function index(Request $request): JsonResponse
    {
        $parentId = $request->query('parent_id');
        
        $accounts = $this->queryBus->ask(new ListAccountsQuery($parentId));

        return new JsonResponse($accounts);
    }
}
