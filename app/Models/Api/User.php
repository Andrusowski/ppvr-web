<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Models\Api;

class User
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string[]|null
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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string[]|null
     */
    public function getPreviousUsernames(): ?array
    {
        return $this->previousUsernames;
    }

    /**
     * @param string[]|null $previousUsernames
     */
    public function setPreviousUsernames(?array $previousUsernames): void
    {
        $this->previousUsernames = $previousUsernames;
    }
}
