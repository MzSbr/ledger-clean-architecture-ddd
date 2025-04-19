<?php

namespace App\Shared\Domain\Traits;

use DateTime;
use App\Shared\Domain\ValueObject\Revision;

trait HasRevisions
{
    /**
     * @var int The revision number
     */
    protected int $revision = 0;
    
    /**
     * @var DateTime|null The last update timestamp
     */
    protected ?DateTime $updatedAt = null;
    
    /**
     * @var string|null Cached revision hash
     */
    private ?string $revisionHashCached = null;
    
    /**
     * Get the current revision number.
     *
     * @return int
     */
    public function getRevision(): int
    {
        return $this->revision;
    }
    
    /**
     * Set the revision number.
     *
     * @param int $revision
     * @return self
     */
    public function setRevision(int $revision): self
    {
        $this->revision = $revision;
        $this->clearRevisionCache();
        return $this;
    }
    
    /**
     * Increment the revision number.
     *
     * @return self
     */
    public function incrementRevision(): self
    {
        $this->revision++;
        $this->clearRevisionCache();
        return $this;
    }
    
    /**
     * Get the last update timestamp.
     *
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
    
    /**
     * Set the last update timestamp.
     *
     * @param DateTime $updatedAt
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        $this->clearRevisionCache();
        return $this;
    }
    
    /**
     * Get the revision hash.
     *
     * @return string
     */
    public function getRevisionHash(): string
    {
        if (!isset($this->revisionHashCached)) {
            $this->revisionHashCached = $this->createRevisionHash();
        }
        
        return $this->revisionHashCached;
    }
    
    /**
     * Clear the revision hash cache.
     */
    public function clearRevisionCache(): void
    {
        $this->revisionHashCached = null;
    }
    
    /**
     * Create a revision hash from the revision number and update timestamp.
     *
     * @return string
     */
    protected function createRevisionHash(): string
    {
        $timestamp = $this->updatedAt ? $this->updatedAt->getTimestamp() : time();
        return hash('md5', $this->revision . ':' . $timestamp);
    }
    
    /**
     * Check if the provided revision hash matches the current revision.
     *
     * @param string|null $revisionHash
     * @return bool
     */
    public function checkRevision(?string $revisionHash): bool
    {
        if ($revisionHash === null) {
            return true;
        }
        
        return $revisionHash === $this->getRevisionHash();
    }
}
