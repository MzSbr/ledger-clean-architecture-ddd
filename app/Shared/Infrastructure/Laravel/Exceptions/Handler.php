<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel\Exceptions;

use App\Journal\Entry\Domain\Exceptions\JournalEntryAlreadyPostedException;
use App\Journal\Entry\Domain\Exceptions\JournalEntryAlreadyReversedException;
use App\Journal\Entry\Domain\Exceptions\JournalEntryNotBalancedException;
use App\Journal\Entry\Domain\Exceptions\JournalEntryNotFoundException;
use App\Ledger\Account\Domain\Exceptions\AccountClosedException;
use App\Ledger\Account\Domain\Exceptions\AccountNotFoundException;
use App\Ledger\Account\Domain\Exceptions\InvalidAccountCodeException;
use App\Ledger\Currency\Domain\Exceptions\InvalidCurrencyException;
use App\Ledger\Currency\Domain\Exceptions\InvalidMoneyException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

final class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AccountNotFoundException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'account_not_found'
            ], Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (InvalidAccountCodeException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'invalid_account_code'
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (AccountClosedException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'account_closed'
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (JournalEntryNotFoundException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'journal_entry_not_found'
            ], Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (JournalEntryAlreadyPostedException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'journal_entry_already_posted'
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (JournalEntryAlreadyReversedException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'journal_entry_already_reversed'
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (JournalEntryNotBalancedException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'journal_entry_not_balanced'
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (InvalidCurrencyException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'invalid_currency'
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (InvalidMoneyException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'error' => 'invalid_money'
            ], Response::HTTP_BAD_REQUEST);
        });
    }
}
