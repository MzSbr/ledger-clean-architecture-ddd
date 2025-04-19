<?php

declare(strict_types=1);

namespace App\Journal\Entry\Presentation\API;

use App\Journal\Entry\Application\Commands\AddJournalDetail\AddJournalDetailCommand;
use App\Journal\Entry\Application\Commands\CreateJournalEntry\CreateJournalEntryCommand;
use App\Journal\Entry\Application\Commands\PostJournalEntry\PostJournalEntryCommand;
use App\Journal\Entry\Application\Commands\ReverseJournalEntry\ReverseJournalEntryCommand;
use App\Journal\Entry\Application\Queries\GetJournalEntry\GetJournalEntryQuery;
use App\Journal\Entry\Application\Queries\ListJournalEntries\ListJournalEntriesQuery;
use App\Shared\Application\CommandBus;
use App\Shared\Application\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

final class JournalEntryController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus
    ) {
    }

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:regular,adjustment,closing,opening,reversal',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'currency' => 'required|string|size:3',
            'reversal_of' => 'nullable|uuid',
            'extra' => 'nullable|array'
        ]);

        $id = Uuid::uuid4()->toString();

        $this->commandBus->dispatch(new CreateJournalEntryCommand(
            $id,
            $request->input('type'),
            $request->input('description'),
            $request->input('date'),
            $request->input('currency'),
            $request->input('reversal_of'),
            $request->input('extra')
        ));

        return new JsonResponse([
            'id' => $id,
            'message' => 'Journal entry created successfully'
        ], Response::HTTP_CREATED);
    }

    public function addDetail(Request $request, string $journalEntryId): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|uuid',
            'amount' => 'required|integer',
            'currency' => 'required|string|size:3',
            'memo' => 'nullable|string',
            'extra' => 'nullable|array'
        ]);

        $id = Uuid::uuid4()->toString();

        $this->commandBus->dispatch(new AddJournalDetailCommand(
            $id,
            $journalEntryId,
            $request->input('account_id'),
            $request->input('amount'),
            $request->input('currency'),
            $request->input('memo'),
            $request->input('extra')
        ));

        return new JsonResponse([
            'id' => $id,
            'message' => 'Journal detail added successfully'
        ], Response::HTTP_CREATED);
    }

    public function post(string $id): JsonResponse
    {
        $this->commandBus->dispatch(new PostJournalEntryCommand($id));

        return new JsonResponse([
            'message' => 'Journal entry posted successfully'
        ]);
    }

    public function reverse(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'date' => 'required|date'
        ]);

        $reversalId = Uuid::uuid4()->toString();

        $this->commandBus->dispatch(new ReverseJournalEntryCommand(
            $id,
            $reversalId,
            $request->input('description'),
            $request->input('date')
        ));

        return new JsonResponse([
            'id' => $reversalId,
            'message' => 'Journal entry reversed successfully'
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $journalEntry = $this->queryBus->ask(new GetJournalEntryQuery($id));

        return new JsonResponse($journalEntry);
    }

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $status = $request->query('status');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        
        $journalEntries = $this->queryBus->ask(new ListJournalEntriesQuery(
            $type,
            $status,
            $fromDate,
            $toDate
        ));

        return new JsonResponse($journalEntries);
    }
}
