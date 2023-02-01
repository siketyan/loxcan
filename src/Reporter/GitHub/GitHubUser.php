<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

class GitHubUser
{
    public function __construct(
        private readonly int $id,
        private readonly string $login,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }
}
