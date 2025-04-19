# Ledger App - Laravel Clean Architecture

A Laravel-based accounting ledger library built with Clean Architecture principles, Domain-Driven Design (DDD), Command Query Responsibility Segregation (CQRS), and Command Bus patterns.

## Overview

This library is a rebuild of the original [Abivia Ledger](https://github.com/abivia/ledger) using modern architectural patterns and Laravel framework. It provides a solid foundation for accounting operations with a clean, maintainable, and testable codebase.

## Features

- **Account Management**: Create, update, and close accounts with support for hierarchical structures
- **Journal Entries**: Create, post, and reverse journal entries with multiple details
- **Multi-currency Support**: Handle transactions in different currencies
- **Clean Architecture**: Clear separation of concerns with domain, application, infrastructure, and presentation layers
- **DDD Implementation**: Bounded contexts, aggregates, value objects, and domain events
- **CQRS Pattern**: Separate command and query models for better scalability and maintainability
- **Command Bus**: Decoupled command handling for better testability and separation of concerns

## Requirements

- PHP 8.1 or higher
- Laravel 10.x
- Composer


After installing the package, publish the migrations:

```bash
php artisan vendor:publish --provider="App\Shared\Infrastructure\Laravel\Providers\LedgerServiceProvider" --tag="migrations"
```

Then run the migrations:

```bash
php artisan migrate
```

## Usage

### Account Management

#### Creating an Account

```php
use App\Ledger\Account\Application\Commands\CreateAccount\CreateAccountCommand;
use App\Shared\Application\CommandBus;

public function createAccount(CommandBus $commandBus)
{
    $accountId = Uuid::uuid4()->toString();
    
    $commandBus->dispatch(new CreateAccountCommand(
        $accountId,
        'ASSET-1000',
        'asset',
        null, // parent_id
        false, // is_category
        'TAX001', // tax_code
        ['department' => 'sales'] // extra
    ));
    
    return $accountId;
}
```

#### Retrieving an Account

```php
use App\Ledger\Account\Application\Queries\GetAccount\GetAccountQuery;
use App\Shared\Application\QueryBus;

public function getAccount(QueryBus $queryBus, string $accountId)
{
    return $queryBus->ask(new GetAccountQuery($accountId));
}
```

### Journal Entries

#### Creating a Journal Entry

```php
use App\Journal\Entry\Application\Commands\CreateJournalEntry\CreateJournalEntryCommand;
use App\Shared\Application\CommandBus;

public function createJournalEntry(CommandBus $commandBus)
{
    $journalEntryId = Uuid::uuid4()->toString();
    
    $commandBus->dispatch(new CreateJournalEntryCommand(
        $journalEntryId,
        'regular',
        'Purchase of office supplies',
        '2025-04-18',
        'USD',
        null, // reversal_of
        ['reference' => 'INV-001'] // extra
    ));
    
    return $journalEntryId;
}
```

#### Adding Journal Details

```php
use App\Journal\Entry\Application\Commands\AddJournalDetail\AddJournalDetailCommand;
use App\Shared\Application\CommandBus;

public function addJournalDetail(CommandBus $commandBus, string $journalEntryId, string $accountId)
{
    $detailId = Uuid::uuid4()->toString();
    
    $commandBus->dispatch(new AddJournalDetailCommand(
        $detailId,
        $journalEntryId,
        $accountId,
        1000, // amount (positive for debit, negative for credit)
        'USD',
        'Office supplies', // memo
        null // extra
    ));
    
    return $detailId;
}
```

#### Posting a Journal Entry

```php
use App\Journal\Entry\Application\Commands\PostJournalEntry\PostJournalEntryCommand;
use App\Shared\Application\CommandBus;

public function postJournalEntry(CommandBus $commandBus, string $journalEntryId)
{
    $commandBus->dispatch(new PostJournalEntryCommand($journalEntryId));
}
```

#### Reversing a Journal Entry

```php
use App\Journal\Entry\Application\Commands\ReverseJournalEntry\ReverseJournalEntryCommand;
use App\Shared\Application\CommandBus;

public function reverseJournalEntry(CommandBus $commandBus, string $journalEntryId)
{
    $reversalId = Uuid::uuid4()->toString();
    
    $commandBus->dispatch(new ReverseJournalEntryCommand(
        $journalEntryId,
        $reversalId,
        'Reversal of journal entry',
        '2025-04-19'
    ));
    
    return $reversalId;
}
```

## API Endpoints

The library provides a set of RESTful API endpoints for interacting with the ledger:

### Account Endpoints

- `GET /api/accounts` - List all accounts
- `POST /api/accounts` - Create a new account
- `GET /api/accounts/{id}` - Get account details
- `PUT /api/accounts/{id}` - Update an account
- `POST /api/accounts/{id}/close` - Close an account

### Journal Entry Endpoints

- `GET /api/journal-entries` - List journal entries
- `POST /api/journal-entries` - Create a new journal entry
- `GET /api/journal-entries/{id}` - Get journal entry details
- `POST /api/journal-entries/{id}/details` - Add a detail to a journal entry
- `POST /api/journal-entries/{id}/post` - Post a journal entry
- `POST /api/journal-entries/{id}/reverse` - Reverse a journal entry

## Architecture

The library follows Clean Architecture principles with the following layers:

### Domain Layer

Contains the core business logic, entities, value objects, and repository interfaces.

- `Domain/Account.php` - Account aggregate root
- `Domain/ValueObjects/AccountId.php` - Value object for account ID
- `Domain/Enums/AccountType.php` - Enumeration of account types
- `Domain/AccountRepository.php` - Repository interface for accounts

### Application Layer

Contains application-specific business rules, commands, queries, and handlers.

- `Application/Commands/CreateAccount/CreateAccountCommand.php` - Command for creating an account
- `Application/Commands/CreateAccount/CreateAccountHandler.php` - Handler for the create account command
- `Application/Queries/GetAccount/GetAccountQuery.php` - Query for retrieving an account
- `Application/Queries/GetAccount/GetAccountHandler.php` - Handler for the get account query

### Infrastructure Layer

Contains implementations of the interfaces defined in the domain layer.

- `Infrastructure/Laravel/AccountEloquentModel.php` - Eloquent model for accounts
- `Infrastructure/Laravel/EloquentAccountRepository.php` - Eloquent implementation of the account repository

### Presentation Layer

Contains controllers and routes for exposing the functionality to the outside world.

- `Presentation/API/AccountController.php` - Controller for account-related endpoints

## Testing

The library includes comprehensive tests for all components:

- Unit tests for domain entities and value objects
- Integration tests for repositories
- Feature tests for API endpoints

To run the tests:

```bash
php artisan test
```

## License

This library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
