<?php

namespace Tests\Feature\API;

use App\Ledger\Account\Domain\Enums\AccountType;
use App\Ledger\Account\Infrastructure\Laravel\AccountEloquentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class AccountApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testCreateAccount(): void
    {
        $response = $this->postJson('/api/accounts', [
            'code' => 'ASSET-1000',
            'type' => AccountType::CREDIT,
            'is_category' => false,
            'tax_code' => 'TAX001',
            'extra' => ['department' => 'sales']
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'message'
            ]);

        $this->assertDatabaseHas('ledger_accounts', [
            'code' => 'ASSET-1000',
            'type' => AccountType::CREDIT,
            'is_category' => false,
            'tax_code' => 'TAX001',
        ]);
    }

    public function testGetAccount(): void
    {
        $accountId = Uuid::uuid4()->toString();
        
        AccountEloquentModel::create([
            'id' => $accountId,
            'code' => 'ASSET-1000',
            'type' => AccountType::CREDIT,
            'status' => 'active',
            'is_category' => false,
            'tax_code' => 'TAX001',
            'extra' => json_encode(['department' => 'sales'])
        ]);

        $response = $this->getJson("/api/accounts/{$accountId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'code',
                'type',
                'status',
                'parentId',
                'isCategory',
                'taxCode',
                'extra'
            ])
            ->assertJson([
                'id' => $accountId,
                'code' => 'ASSET-1000',
                'type' => AccountType::CREDIT,
                'status' => 'active',
                'isCategory' => false,
                'taxCode' => 'TAX001',
                'extra' => ['department' => 'sales']
            ]);
    }

    public function testUpdateAccount(): void
    {
        $accountId = Uuid::uuid4()->toString();
        
        AccountEloquentModel::create([
            'id' => $accountId,
            'code' => 'ASSET-1000',
            'type' => AccountType::CREDIT,
            'status' => 'active',
            'is_category' => false,
            'tax_code' => 'TAX001',
            'extra' => json_encode(['department' => 'sales'])
        ]);

        $response = $this->putJson("/api/accounts/{$accountId}", [
            'code' => 'ASSET-1001',
            'tax_code' => 'TAX002',
            'extra' => ['department' => 'marketing']
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);

        $this->assertDatabaseHas('ledger_accounts', [
            'id' => $accountId,
            'code' => 'ASSET-1001',
            'tax_code' => 'TAX002',
        ]);
    }

    public function testCloseAccount(): void
    {
        $accountId = Uuid::uuid4()->toString();
        
        AccountEloquentModel::create([
            'id' => $accountId,
            'code' => 'ASSET-1000',
            'type' => AccountType::CREDIT,
            'status' => 'active',
            'is_category' => false,
            'tax_code' => 'TAX001',
            'extra' => json_encode(['department' => 'sales'])
        ]);

        $response = $this->postJson("/api/accounts/{$accountId}/close");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);

        $this->assertDatabaseHas('ledger_accounts', [
            'id' => $accountId,
            'status' => 'closed',
        ]);
    }

    public function testListAccounts(): void
    {
        $accountId1 = Uuid::uuid4()->toString();
        $accountId2 = Uuid::uuid4()->toString();
        
        AccountEloquentModel::create([
            'id' => $accountId1,
            'code' => 'ASSET-1000',
            'type' => AccountType::CREDIT,
            'status' => 'active',
            'is_category' => false,
            'tax_code' => 'TAX001',
            'extra' => json_encode(['department' => 'sales'])
        ]);
        
        AccountEloquentModel::create([
            'id' => $accountId2,
            'code' => 'ASSET-2000',
            'type' => AccountType::CREDIT,
            'status' => 'active',
            'is_category' => false,
            'tax_code' => 'TAX002',
            'extra' => json_encode(['department' => 'marketing'])
        ]);

        $response = $this->getJson("/api/accounts");

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'code',
                    'type',
                    'status',
                    'parentId',
                    'isCategory',
                    'taxCode',
                    'extra'
                ]
            ]);
    }
}
