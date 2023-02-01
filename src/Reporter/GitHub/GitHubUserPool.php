<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

class GitHubUserPool
{
    /**
     * @param GitHubUser[] $users
     */
    public function __construct(
        private array $users = [],
    ) {
    }

    public function get(int $id): ?GitHubUser
    {
        return $this->users[$id] ?? null;
    }

    public function add(GitHubUser $user): void
    {
        $this->users[$user->getId()] = $user;
    }
}
