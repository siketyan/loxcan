<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

class GitHubComment
{
    private int $id;
    private string $body;
    private GitHubUser $author;

    public function __construct(
        int $id,
        string $body,
        GitHubUser $author
    ) {
        $this->id = $id;
        $this->body = $body;
        $this->author = $author;
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
