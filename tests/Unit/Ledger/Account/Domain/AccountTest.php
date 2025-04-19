<?php

namespace tests\Unit\Ledger\Account\Domain;

use App\Ledger\Account\Domain\Account;
use App\Ledger\Account\Domain\Enums\AccountStatus;
use App\Ledger\Account\Domain\Enums\AccountType;
use App\Ledger\Account\Domain\Exceptions\AccountClosedException;
use App\Ledger\Account\Domain\Exceptions\InvalidAccountCodeException;
use App\Ledger\Account\Domain\ValueObjects\AccountCode;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private AccountId $id;
    private AccountCode $code;
    private AccountType $type;
    private ?AccountId $parentId;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->id = new AccountId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
        $this->code = new AccountCode('1000');
        $this->type = AccountType::ASSET;
        $this->parentId = null;
    }

    public function testCreateAccount(): void
    {
        $account = Account::create(
            $this->id,
            $this->code,
            $this->type,
            $this->parentId,
            false,
            null,
            null
        );

        $this->assertEquals($this->id, $account->id());
        $this->assertEquals($this->code, $account->code());
        $this->assertEquals($this->type, $account->type());
        $this->assertEquals(AccountStatus::ACTIVE, $account->status());
        $this->assertEquals($this->parentId, $account->parentId());
        $this->assertFalse($account->isCategory());
        $this->assertNull($account->taxCode());
        $this->assertNull($account->extra());
    }

    public function testUpdateAccount(): void
    {
        $account = Account::create(
            $this->id,
            $this->code,
            $this->type,
            $this->parentId,
            false,
            null,
            null
        );

        $newCode = new AccountCode('1001');
        $newTaxCode = 'TAX001';
        $newExtra = ['key' => 'value'];

        $account->update($newCode, $newTaxCode, $newExtra);

        $this->assertEquals($newCode, $account->code());
        $this->assertEquals($newTaxCode, $account->taxCode());
        $this->assertEquals($newExtra, $account->extra());
    }

    public function testCloseAccount(): void
    {
        $account = Account::create(
            $this->id,
            $this->code,
            $this->type,
            $this->parentId,
            false,
            null,
            null
        );

        $account->close();

        $this->assertEquals(AccountStatus::CLOSED, $account->status());
    }

    public function testUpdateClosedAccountThrowsException(): void
    {
        $account = Account::create(
            $this->id,
            $this->code,
            $this->type,
            $this->parentId,
            false,
            null,
            null
        );

        $account->close();

        $this->expectException(AccountClosedException::class);
        
        $newCode = new AccountCode('1001');
        $account->update($newCode, null, null);
    }

    public function testAccountCodeValidation(): void
    {
        $this->expectException(InvalidAccountCodeException::class);
        
        new AccountCode('');
    }
}
