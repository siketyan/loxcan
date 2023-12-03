<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter\GitHub;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Reporter\MarkdownBuilder;

class GitHubReporterTest extends TestCase
{
    private MarkdownBuilder&MockObject $markdownBuilder;
    private GitHubClient&MockObject $client;
    private GitHubReporter $reporter;

    protected function setUp(): void
    {
        $this->markdownBuilder = $this->createMock(MarkdownBuilder::class);
        $this->client = $this->createMock(GitHubClient::class);

        $this->reporter = new GitHubReporter(
            $this->markdownBuilder,
            $this->client,
        );

        putenv('LOXCAN_REPORTER_GITHUB=1');
        putenv('LOXCAN_REPORTER_GITHUB_OWNER=foo');
        putenv('LOXCAN_REPORTER_GITHUB_REPO=bar');
        putenv('LOXCAN_REPORTER_GITHUB_ISSUE_NUMBER=123');
        putenv('LOXCAN_REPORTER_GITHUB_USERNAME=me');
    }

    public function test(): void
    {
        $filename = 'foo.lock';
        $markdown = '## Markdown';
        $diff = $this->createStub(DependencyCollectionDiff::class);
        $diffs = [$filename => $diff];

        $this->markdownBuilder->method('build')->with($diffs)->willReturn($markdown);

        $this->client->expects($this->once())->method('getComments')->with('foo', 'bar', 123)->willReturn([]);
        $this->client->expects($this->once())->method('createComment')->with('foo', 'bar', 123, $markdown);

        $this->reporter->report($diffs);
    }

    public function testUpdate(): void
    {
        $filename = 'foo.lock';
        $markdown = '## Markdown';
        $diff = $this->createStub(DependencyCollectionDiff::class);
        $diffs = [$filename => $diff];

        $me = $this->createStub(GitHubUser::class);
        $me->method('getLogin')->willReturn('me');

        $comment = $this->createStub(GitHubComment::class);
        $comment->method('getId')->willReturn(123);
        $comment->method('getAuthor')->willReturn($me);

        $this->markdownBuilder->method('build')->with($diffs)->willReturn($markdown);

        $this->client->expects($this->once())->method('getComments')->with('foo', 'bar', 123)->willReturn([$comment]);
        $this->client->expects($this->once())->method('updateComment')->with('foo', 'bar', $comment, $markdown);

        $this->reporter->report($diffs);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->reporter->supports());

        putenv('LOXCAN_REPORTER_GITHUB=');

        $this->assertFalse($this->reporter->supports());
    }
}
