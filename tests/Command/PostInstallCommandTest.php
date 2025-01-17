<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\MissingInputException;
use Symfony\Component\Console\Tester\CommandTester;

final class PostInstallCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:post-install');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['mysql://dev:dev@127.0.0.1:3306/dbsaver_test', 'smtp://localhost', 'me@user.com', 'en', 'dev']);

        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Parameters have been saved in .env.local file.', $output);
    }

    /**
     * @dataProvider provideInvalidCases
     */
    public function testExecuteWithInvalidInput(string $databaseUrl, string $mailerDsn, string $mailerSender, string $locale, string $environment): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:post-install');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([$databaseUrl, $mailerDsn, $mailerSender, $locale, $environment]);

        $this->expectException(MissingInputException::class);
        $commandTester->execute(['command' => $command->getName()]);
    }

    public function provideInvalidCases(): iterable
    {
        yield 'no_database_url' => ['', '', '', '', ''];
        yield 'no_mailer_dsn' => ['test', '', '', '', ''];
        yield 'no_mailer_sender' => ['test', 'test', '', '', ''];
        yield 'invalid_mailer_sender' => ['test', 'test', 'test', '', ''];
        yield 'no_locale' => ['test', 'test', 'test', '', ''];
        yield 'invalid_locale' => ['test', 'test', 'test', 'de', ''];
        yield 'no_environment' => ['test', 'test', 'test', 'de', ''];
        yield 'invalid_environment' => ['test', 'test', 'test', 'de', 'test'];
    }
}
