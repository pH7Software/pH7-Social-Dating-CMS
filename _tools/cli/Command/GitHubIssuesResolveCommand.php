<?php
/**
 * Copyright (c) Pierre-Henry Soria <hi@ph7.me>
 * MIT License - https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace PH7\Cli\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class GitHubIssuesResolveCommand extends Command
{
    private const API_BASE_URI = 'https://api.github.com';
    private const DEFAULT_REPOSITORY = 'pH7Software/pH7-Social-Dating-CMS';
    private const TOKEN_ENV_NAMES = ['GITHUB_TOKEN', 'GH_TOKEN'];
    private const USER_AGENT = 'pH7Builder-Issue-Resolver';
    private const HTTP_TIMEOUT = 30;

    protected function configure(): void
    {
        $this
            ->setName('github:issues:resolve')
            ->setDescription('Inspect GitHub issues and optionally post a resolution comment and close them.')
            ->addArgument(
                'issues',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'One or more issue numbers to inspect or update.'
            )
            ->addOption(
                'repo',
                null,
                InputOption::VALUE_REQUIRED,
                'Repository slug in the form owner/name.',
                self::DEFAULT_REPOSITORY
            )
            ->addOption(
                'comment-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Markdown file to post as the same comment on every issue.'
            )
            ->addOption(
                'comment-dir',
                null,
                InputOption::VALUE_REQUIRED,
                'Directory containing one Markdown file per issue number, for example 1196.md.'
            )
            ->addOption(
                'close',
                null,
                InputOption::VALUE_NONE,
                'Close the issue after posting the comment or after inspection if no comment is supplied.'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Show the actions that would be performed without posting comments or closing issues.'
            )
            ->addOption(
                'token',
                null,
                InputOption::VALUE_REQUIRED,
                'GitHub token. Defaults to GITHUB_TOKEN or GH_TOKEN from the environment.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $repository = $this->validateRepository((string)$input->getOption('repo'));
            $issues = $this->normalizeIssueNumbers($input->getArgument('issues'));

            $commentFile = $this->normalizeOptionalString($input->getOption('comment-file'));
            $commentDir = $this->normalizeOptionalString($input->getOption('comment-dir'));
            $shouldClose = (bool)$input->getOption('close');
            $isDryRun = (bool)$input->getOption('dry-run');

            if ($commentFile !== null && $commentDir !== null) {
                throw new InvalidArgumentException('Use either --comment-file or --comment-dir, not both.');
            }

            $requestsWrite = $commentFile !== null || $commentDir !== null || $shouldClose;
            $token = $this->resolveToken($this->normalizeOptionalString($input->getOption('token')));
            if ($requestsWrite && !$isDryRun && $token === null) {
                throw new InvalidArgumentException('A GitHub token is required for commenting or closing issues. Set GITHUB_TOKEN or pass --token.');
            }

            $client = $this->createClient($token);
            $failures = [];

            foreach ($issues as $issueNumber) {
                try {
                    $this->processIssue(
                        $client,
                        $io,
                        $repository,
                        $issueNumber,
                        $commentFile,
                        $commentDir,
                        $shouldClose,
                        $isDryRun
                    );
                } catch (Throwable $exception) {
                    $failures[] = sprintf('#%d: %s', $issueNumber, $exception->getMessage());
                    $io->error(sprintf('Issue #%d failed: %s', $issueNumber, $exception->getMessage()));
                }
            }

            if ($failures !== []) {
                $io->listing($failures);

                return Command::FAILURE;
            }

            if (!$requestsWrite) {
                $io->success('Issue inspection completed. No write actions were requested.');
            }

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }

    private function createClient(?string $token): Client
    {
        $headers = [
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => self::USER_AGENT
        ];

        if ($token !== null) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return new Client([
            'base_uri' => self::API_BASE_URI,
            'headers' => $headers,
            'http_errors' => false,
            'timeout' => self::HTTP_TIMEOUT,
        ]);
    }

    private function processIssue(
        Client $client,
        SymfonyStyle $io,
        string $repository,
        int $issueNumber,
        ?string $commentFile,
        ?string $commentDir,
        bool $shouldClose,
        bool $isDryRun
    ): void {
        $issue = $this->requestJson($client, 'GET', $this->buildIssueUri($repository, $issueNumber));
        $title = (string)($issue['title'] ?? '');
        $state = (string)($issue['state'] ?? 'unknown');
        $htmlUrl = (string)($issue['html_url'] ?? '');

        $this->displayIssueHeader($io, $issueNumber, $title, $state, $htmlUrl);

        $commentBody = $this->loadCommentBody($issueNumber, $commentFile, $commentDir);
        $this->postCommentIfRequested($client, $io, $repository, $issueNumber, $commentBody, $isDryRun);
        $this->closeIssueIfRequested($client, $io, $repository, $issueNumber, $state, $shouldClose, $isDryRun);
    }

    private function displayIssueHeader(
        SymfonyStyle $io,
        int $issueNumber,
        string $title,
        string $state,
        string $htmlUrl
    ): void {
        $io->section(sprintf('#%d [%s] %s', $issueNumber, strtoupper($state), $title));
        if ($htmlUrl !== '') {
            $io->text($htmlUrl);
        }
    }

    private function postCommentIfRequested(
        Client $client,
        SymfonyStyle $io,
        string $repository,
        int $issueNumber,
        ?string $commentBody,
        bool $isDryRun
    ): void {
        if ($commentBody === null) {
            $io->text('No comment file supplied for this issue.');

            return;
        }

        if ($isDryRun) {
            $io->text(sprintf('Dry run: would post %d characters of comment text.', mb_strlen($commentBody)));

            return;
        }

        $this->requestJson(
            $client,
            'POST',
            $this->buildIssueUri($repository, $issueNumber) . '/comments',
            ['body' => $commentBody]
        );
        $io->success(sprintf('Posted comment to issue #%d.', $issueNumber));
    }

    private function closeIssueIfRequested(
        Client $client,
        SymfonyStyle $io,
        string $repository,
        int $issueNumber,
        string $state,
        bool $shouldClose,
        bool $isDryRun
    ): void {
        if (!$shouldClose) {
            return;
        }

        if ($state === 'closed') {
            $io->text(sprintf('Issue #%d is already closed.', $issueNumber));

            return;
        }

        if ($isDryRun) {
            $io->text(sprintf('Dry run: would close issue #%d.', $issueNumber));

            return;
        }

        $this->requestJson(
            $client,
            'PATCH',
            $this->buildIssueUri($repository, $issueNumber),
            ['state' => 'closed']
        );
        $io->success(sprintf('Closed issue #%d.', $issueNumber));
    }

    /**
     * @return array<string, mixed>
     */
    private function requestJson(Client $client, string $method, string $uri, array $payload = []): array
    {
        try {
            $options = [];
            if ($payload !== []) {
                $options['json'] = $payload;
            }

            $response = $client->request($method, $uri, $options);
        } catch (GuzzleException $exception) {
            throw new RuntimeException($exception->getMessage(), 0, $exception);
        }

        $contents = (string)$response->getBody();

        try {
            $data = $contents === '' ? [] : json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('GitHub API returned invalid JSON.', 0, $exception);
        }

        if ($response->getStatusCode() >= 400) {
            $message = is_array($data) && isset($data['message']) ? (string)$data['message'] : 'GitHub API request failed.';
            throw new RuntimeException(sprintf('%s (%d)', $message, $response->getStatusCode()));
        }

        return is_array($data) ? $data : [];
    }

    /**
     * @param array<int, mixed> $issues
     *
     * @return array<int, int>
     */
    private function normalizeIssueNumbers(array $issues): array
    {
        $normalized = [];

        foreach ($issues as $issue) {
            $issueNumber = trim((string)$issue);
            if ($issueNumber === '' || !ctype_digit($issueNumber) || (int)$issueNumber <= 0) {
                throw new InvalidArgumentException(sprintf('Invalid issue number "%s".', (string)$issue));
            }

            $normalized[] = (int)$issueNumber;
        }

        return array_values(array_unique($normalized));
    }

    private function validateRepository(string $repository): string
    {
        $repository = trim($repository);
        if ($repository === '' || !preg_match('#^[^/]+/[^/]+$#', $repository)) {
            throw new InvalidArgumentException('The repository must be in the form owner/name.');
        }

        return $repository;
    }

    private function resolveToken(?string $token): ?string
    {
        if ($token !== null && trim($token) !== '') {
            return trim($token);
        }

        foreach (self::TOKEN_ENV_NAMES as $envName) {
            $envValue = getenv($envName);
            if (is_string($envValue) && trim($envValue) !== '') {
                return trim($envValue);
            }
        }

        return null;
    }

    private function normalizeOptionalString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function loadCommentBody(int $issueNumber, ?string $commentFile, ?string $commentDir): ?string
    {
        if ($commentFile === null && $commentDir === null) {
            return null;
        }

        $path = $this->resolveCommentFilePath($issueNumber, $commentFile, $commentDir);

        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('Comment file not found or not readable for issue #%d: %s', $issueNumber, (string)$path));
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to read comment file for issue #%d.', $issueNumber));
        }

        $contents = trim($contents);
        if ($contents === '') {
            throw new InvalidArgumentException(sprintf('Comment file is empty for issue #%d.', $issueNumber));
        }

        return $contents;
    }

    private function resolveCommentFilePath(int $issueNumber, ?string $commentFile, ?string $commentDir): string
    {
        if ($commentDir !== null) {
            return rtrim($commentDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $issueNumber . '.md';
        }

        return (string)$commentFile;
    }

    private function buildIssueUri(string $repository, int $issueNumber): string
    {
        return sprintf('/repos/%s/issues/%d', $repository, $issueNumber);
    }
}
