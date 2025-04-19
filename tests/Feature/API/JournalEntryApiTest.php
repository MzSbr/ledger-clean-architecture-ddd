<?php

namespace Tests\Feature\API;

use App\Journal\Entry\Domain\Enums\EntryStatus;
use App\Journal\Entry\Domain\Enums\EntryType;
use App\Journal\Entry\Infrastructure\Laravel\JournalEntryEloquentModel;
use App\Journal\Detail\Infrastructure\Laravel\JournalDetailEloquentModel;
use App\Ledger\Account\Infrastructure\Laravel\AccountEloquentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class JournalEntryApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private string $accountId1;
    private string $accountId2;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test accounts
        $this->accountId1 = Uuid::uuid4()->toString();
        $this->accountId2 = Uuid::uuid4()->toString();
        
        AccountEloquentModel::create([
            'id' => $this->accountId1,
            'code' => 'ASSET-1000',
            'type' => 'asset',
            'status' => 'active',
            'is_category' => false
        ]);
        
        AccountEloquentModel::create([
            'id' => $this->accountId2,
            'code' => 'LIABILITY-2000',
            'type' => 'liability',
            'status' => 'active',
            'is_category' => false
        ]);
    }

    public function testCreateJournalEntry(): void
    {
        $response = $this->postJson('/api/journal-entries', [
            'type' => EntryType::REGULAR->value,
            'description' => 'Test Journal Entry',
            'date' => '2025-04-18',
            'currency' => 'USD',
            'extra' => ['reference' => 'INV-001']
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'message'
            ]);

        $journalEntryId = $response->json('id');

        $this->assertDatabaseHas('journal_entries', [
            'id' => $journalEntryId,
            'type' => EntryType::REGULAR->value,
            'description' => 'Test Journal Entry',
            'currency' => 'USD',
        ]);
    }

    public function testAddJournalDetail(): void
    {
        // Create a journal entry first
        $journalEntryId = Uuid::uuid4()->toString();
        
        JournalEntryEloquentModel::create([
            'id' => $journalEntryId,
            'type' => EntryType::REGULAR->value,
            'status' => EntryStatus::DRAFT->value,
            'description' => 'Test Journal Entry',
            'date' => '2025-04-18',
            'currency' => 'USD'
        ]);

        $response = $this->postJson("/api/journal-entries/{$journalEntryId}/details", [
            'account_id' => $this->accountId1,
            'amount' => 1000,
            'currency' => 'USD',
            'memo' => 'Debit entry'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'message'
            ]);

        $detailId = $response->json('id');

        $this->assertDatabaseHas('journal_details', [
            'id' => $detailId,
            'journal_entry_id' => $journalEntryId,
            'account_id' => $this->accountId1,
            'amount' => 1000,
            'currency' => 'USD',
            'memo' => 'Debit entry'
        ]);
    }

    public function testPostJournalEntry(): void
    {
        // Create a balanced journal entry with details
        $journalEntryId = Uuid::uuid4()->toString();
        $detailId1 = Uuid::uuid4()->toString();
        $detailId2 = Uuid::uuid4()->toString();
        
        JournalEntryEloquentModel::create([
            'id' => $journalEntryId,
            'type' => EntryType::REGULAR->value,
            'status' => EntryStatus::DRAFT->value,
            'description' => 'Test Journal Entry',
            'date' => '2025-04-18',
            'currency' => 'USD'
        ]);
        
        JournalDetailEloquentModel::create([
            'id' => $detailId1,
            'journal_entry_id' => $journalEntryId,
            'account_id' => $this->accountId1,
            'amount' => 1000,
            'currency' => 'USD',
            'memo' => 'Debit entry'
        ]);
        
        JournalDetailEloquentModel::create([
            'id' => $detailId2,
            'journal_entry_id' => $journalEntryId,
            'account_id' => $this->accountId2,
            'amount' => -1000,
            'currency' => 'USD',
            'memo' => 'Credit entry'
        ]);

        $response = $this->postJson("/api/journal-entries/{$journalEntryId}/post");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);

        $this->assertDatabaseHas('journal_entries', [
            'id' => $journalEntryId,
            'status' => EntryStatus::POSTED->value
        ]);
    }

    public function testReverseJournalEntry(): void
    {
        // Create a posted journal entry with details
        $journalEntryId = Uuid::uuid4()->toString();
        $detailId1 = Uuid::uuid4()->toString();
        $detailId2 = Uuid::uuid4()->toString();
        
        JournalEntryEloquentModel::create([
            'id' => $journalEntryId,
            'type' => EntryType::REGULAR->value,
            'status' => EntryStatus::POSTED->value,
            'description' => 'Test Journal Entry',
            'date' => '2025-04-18',
            'currency' => 'USD'
        ]);
        
        JournalDetailEloquentModel::create([
            'id' => $detailId1,
            'journal_entry_id' => $journalEntryId,
            'account_id' => $this->accountId1,
            'amount' => 1000,
            'currency' => 'USD',
            'memo' => 'Debit entry'
        ]);
        
        JournalDetailEloquentModel::create([
            'id' => $detailId2,
            'journal_entry_id' => $journalEntryId,
            'account_id' => $this->accountId2,
            'amount' => -1000,
            'currency' => 'USD',
            'memo' => 'Credit entry'
        ]);

        $response = $this->postJson("/api/journal-entries/{$journalEntryId}/reverse", [
            'description' => 'Reversal of Test Journal Entry',
            'date' => '2025-04-19'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'message'
            ]);

        $reversalId = $response->json('id');

        $this->assertDatabaseHas('journal_entries', [
            'id' => $journalEntryId,
            'status' => EntryStatus::REVERSED->value
        ]);
        
        $this->assertDatabaseHas('journal_entries', [
            'id' => $reversalId,
            'type' => EntryType::REVERSAL->value,
            'description' => 'Reversal of Test Journal Entry',
            'reversal_of' => $journalEntryId
        ]);
        
        // Check that reversal details were created with opposite amounts
        $this->assertDatabaseHas('journal_details', [
            'journal_entry_id' => $reversalId,
            'account_id' => $this->accountId1,
            'amount' => -1000,
        ]);
        
        $this->assertDatabaseHas('journal_details', [
            'journal_entry_id' => $reversalId,
            'account_id' => $this->accountId2,
            'amount' => 1000,
        ]);
    }

    public function testGetJournalEntry(): void
    {
        // Create a journal entry with details
        $journalEntryId = Uuid::uuid4()->toString();
        $detailId1 = Uuid::uuid4()->toString();
        $detailId2 = Uuid::uuid4()->toString();
        
        JournalEntryEloquentModel::create([
            'id' => $journalEntryId,
            'type' => EntryType::REGULAR->value,
            'status' => EntryStatus::DRAFT->value,
            'description' => 'Test Journal Entry',
            'date' => '2025-04-18',
            'currency' => 'USD'
        ]);
        
        JournalDetailEloquentModel::create([
            'id' => $detailId1,
            'journal_entry_id' => $journalEntryId,
            'account_id' => $this->accountId1,
            'amount' => 1000,
            'currency' => 'USD',
            'memo' => 'Debit entry'
        ]);
        
        JournalDetailEloquentModel::create([
            'id' => $detailId2,
            'journal_entry_id' => $journalEntryId,
            'account_id' => $this->accountId2,
            'amount' => -1000,
            'currency' => 'USD',
            'memo' => 'Credit entry'
        ]);

        $response = $this->getJson("/api/journal-entries/{$journalEntryId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'type',
                'status',
                'description',
                'date',
                'currency',
                'reversalOf',
                'extra',
                'details' => [
                    '*' => [
                        'id',
                        'accountId',
                        'amount',
                        'currency',
                        'memo',
                        'extra'
                    ]
                ]
            ])
            ->assertJson([
                'id' => $journalEntryId,
                'type' => EntryType::REGULAR->value,
                'status' => EntryStatus::DRAFT->value,
                'description' => 'Test Journal Entry',
                'currency' => 'USD'
            ]);
            
        $this->assertCount(2, $response->json('details'));
    }

    public function testListJournalEntries(): void
    {
        // Create multiple journal entries
        $journalEntryId1 = Uuid::uuid4()->toString();
        $journalEntryId2 = Uuid::uuid4()->toString();
        
        JournalEntryEloquentModel::create([
            'id' => $journalEntryId1,
            'type' => EntryType::REGULAR->value,
            'status' => EntryStatus::DRAFT->value,
            'description' => 'Test Journal Entry 1',
            'date' => '2025-04-18',
            'currency' => 'USD'
        ]);
        
        JournalEntryEloquentModel::create([
            'id' => $journalEntryId2,
            'type' => EntryType::ADJUSTMENT->value,
            'status' => EntryStatus::POSTED->value,
            'description' => 'Test Journal Entry 2',
            'date' => '2025-04-19',
            'currency' => 'USD'
        ]);

        $response = $this->getJson("/api/journal-entries");

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'type',
                    'status',
                    'description',
                    'date',
                    'currency',
                    'reversalOf'
                ]
            ]);
            
        // Test filtering
        $response = $this->getJson("/api/journal-entries?type=adjustment");
        
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                [
                    'id' => $journalEntryId2,
                    'type' => EntryType::ADJUSTMENT->value
                ]
            ]);
    }
}
