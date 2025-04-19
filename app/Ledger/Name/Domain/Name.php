<?php

declare(strict_types=1);

namespace App\Ledger\Name\Domain;

use App\Ledger\Name\Domain\ValueObjects\NameId;
use App\Shared\Domain\Entity;
use App\Shared\Domain\Traits\HasUuid;
use App\Shared\Domain\Traits\HasRevisions;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

final class Name extends Entity
{
    use HasUuids;
    use HasRevisions;

    private NameId $id;
    private string $name;
    private string $language;
    private string $ownerUuid;
    private string $uuid;
    private int $revision = 0;
    private ?DateTimeImmutable $updatedAt = null;

    private function __construct(
        NameId $id,
        string $name,
        string $language,
        string $ownerUuid
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->language = $language;
        $this->ownerUuid = $ownerUuid;
        $this->initializeUuid();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function create(
        NameId $id,
        string $name,
        string $language,
        string $ownerUuid
    ): self {
        return new self($id, $name, $language, $ownerUuid);
    }

    public function id(): NameId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function ownerUuid(): string
    {
        return $this->ownerUuid;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->incrementRevision();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateLanguage(string $language): void
    {
        $this->language = $language;
        $this->incrementRevision();
        $this->updatedAt = new DateTimeImmutable();
    }

    protected function identity(): string
    {
        return $this->id->value();
    }
}
