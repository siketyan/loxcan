<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

class GitHubComment
{
    public function __construct(
        private int $id,
        private string $body,
        private GitHubUser $author
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getAuthor(): GitHubUser
    {
        return $this->author;
    }
}
