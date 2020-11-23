<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;

class GitHubReporterTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $markdownBuilder;
    private ObjectProphecy $client;
    private GitHubReporter $reporter;

    protected function setUp(): void
    {
        $this->markdownBuilder = $this->prophesize(GitHubMarkdownBuilder::class);
        $this->client = $this->prophesize(GitHubClient::class);

        $this->reporter = new GitHubReporter(
            $this->markdownBuilder->reveal(),
            $this->client->reveal(),
        );

        putenv('LOXCAN_REPORTER_GITHUB=1');
        putenv('LOXCAN_REPORTER_GITHUB_OWNER=foo');
        putenv('LOXCAN_REPORTER_GITHUB_REPO=bar');
        putenv('LOXCAN_REPORTER_GITHUB_ISSUE_NUMBER=123');
    }

    public function test(): void
    {
        $filename = 'foo.lock';
        $markdown = '## Markdown';
        $diff = $this->prophesize(DependencyCollectionDiff::class)->reveal();

        $this->markdownBuilder->build($diff, $filename)->willReturn($markdown);
        $this->client->comment('foo', 'bar', 123, $markdown)->shouldBeCalledOnce();

        $this->reporter->report($diff, $filename);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->reporter->supports());

        putenv('LOXCAN_REPORTER_GITHUB=');

        $this->assertFalse($this->reporter->supports());
    }
}
