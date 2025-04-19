<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel\Providers;

use App\Ledger\Account\Domain\AccountRepository;
use App\Ledger\Account\Infrastructure\Laravel\EloquentAccountRepository;
use App\Journal\Detail\Domain\JournalDetailRepository;
use App\Journal\Detail\Infrastructure\Laravel\EloquentJournalDetailRepository;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Journal\Entry\Infrastructure\Laravel\EloquentJournalEntryRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AccountRepository::class,
            EloquentAccountRepository::class
        );
        
        $this->app->bind(
            JournalEntryRepository::class,
            EloquentJournalEntryRepository::class
        );
        
        $this->app->bind(
            JournalDetailRepository::class,
            EloquentJournalDetailRepository::class
        );
    }
}
