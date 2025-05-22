<?php

declare(strict_types=1);

namespace Fabledsolutions\GithubSdk;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class Assignees
 */
class Assignees
{
    /**
     * @var \GuzzleHttp\Client|null
     */
    private static $client = null;

    /**
     * Initializes and returns the HTTP client.
     * @return \GuzzleHttp\Client
     */
    private static function getClient()
    {
        if (self::$client === null) {
            self::$client = Client::setup();
        }
        return self::$client;
    }

    /**
     * Get the list of assignees for a repository.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @return array The list of assignees.
     */
    public static function getAssignees(string $owner, string $repo): array
    {
        $client = self::getClient();
        $response = $client->get("repos/{$owner}/{$repo}/assignees");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Assign users to an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @param array $assignees List of usernames to assign.
     * @return array The response from GitHub API.
     */
    public static function addAssignees(string $owner, string $repo, int $issueNumber, array $assignees): array
    {
        $client = self::getClient();
        $response = $client->post("repos/{$owner}/{$repo}/issues/{$issueNumber}/assignees", [
            'json' => [
                'assignees' => $assignees,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Remove users from an issue's assignees.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @param array $assignees List of usernames to remove.
     * @return array The response from GitHub API.
     */
    public static function removeAssignees(string $owner, string $repo, int $issueNumber, array $assignees): array
    {
        $client = self::getClient();
        $response = $client->delete("repos/{$owner}/{$repo}/issues/{$issueNumber}/assignees", [
            'json' => [
                'assignees' => $assignees,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
