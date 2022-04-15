<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Models\Api;

class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $previousUsernames;

    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->setId($data['id'] ?? null);
            $this->setName($data['username'] ?? null);
            $this->setPreviousUsernames($data['previous_usernames'] ?? null);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string[]
     */
    public function getPreviousUsernames(): array
    {
        return $this->previousUsernames;
    }

    /**
     * @param string[] $previousUsernames
     */
    public function setPreviousUsernames(array $previousUsernames): void
    {
        $this->previousUsernames = $previousUsernames;
    }
}
