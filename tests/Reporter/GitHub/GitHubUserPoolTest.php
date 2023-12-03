<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use PHPUnit\Framework\TestCase;

class GitHubUserPoolTest extends TestCase
{
    private GitHubUserPool $pool;

    protected function setUp(): void
    {
        $this->pool = new GitHubUserPool();
    }

    public function test(): void
    {
        $id = 123;

        $user = $this->createStub(GitHubUser::class);
        $user->method('getId')->willReturn($id);

        $this->pool->add($user);

        $this->assertSame($user, $this->pool->get($id));
        $this->assertNull($this->pool->get(456));
    }
}
