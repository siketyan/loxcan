<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Git;

use Eloquent\Pathogen\PathInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Siketyan\Loxcan\Model\Repository;
use Symfony\Component\Process\Process;

class GitProcessFactoryTest extends TestCase
{
    use ProphecyTrait;

    private const GIT_PATH = '/usr/bin/git';

    private GitProcessFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new GitProcessFactory(
            self::GIT_PATH,
        );
    }

    public function test(): void
    {
        $path = $this->prophesize(PathInterface::class);
        $repository = $this->prophesize(Repository::class);

        $path->string()->willReturn(__DIR__);
        $repository->getPath()->willReturn($path->reveal());

        $process = $this->factory->create($repository->reveal(), ['foo', 'bar']);

        $this->assertInstanceOf(Process::class, $process);
        $this->assertSame("'/usr/bin/git' 'foo' 'bar'", $process->getCommandLine());
        $this->assertSame(__DIR__, $process->getWorkingDirectory());
    }
}
