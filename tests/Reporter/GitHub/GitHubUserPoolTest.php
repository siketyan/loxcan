<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class GitHubUserPoolTest extends TestCase
{
    use ProphecyTrait;

    private GitHubUserPool $pool;

    protected function setUp(): void
    {
        $this->pool = new GitHubUserPool();
    }

    public function test(): void
    {
        $id = 123;

        $user = $this->prophesize(GitHubUser::class);
        $user->getId()->willReturn($id);
        $user = $user->reveal();

        $this->pool->add($user);

        $this->assertSame($user, $this->pool->get($id));
        $this->assertNull($this->pool->get(456));
    }
}
