<?php

declare(strict_types=1);

namespace Fabledsolutions\GithubSdk;

require_once __DIR__ . '/../vendor/autoload.php';

class Issues
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
     * Get the list of issues for a repository.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @return array The list of issues.
     */
    public static function listIssues(string $owner, string $repo): array
    {
        $client = self::getClient();
        $response = $client->get("repos/{$owner}/{$repo}/issues");
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * Create a new issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array $data The data for the new issue.
     * @return array The created issue.
     */
    public static function createIssue(string $owner, string $repo, array $data): array
    {
        $client = self::getClient();
        $response = $client->post("repos/{$owner}/{$repo}/issues", [
            'json' => [
                'title' => $data['title'] ?? '',
                'body' => $data['body'] ?? '',
                'assignees' => $data['assignees'] ?? [],
                'labels' => $data['labels'] ?? [],
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Update an existing issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @param array $data The data for the updated issue.
     * @return array The updated issue.
     */
    public static function updateIssue(string $owner, string $repo, int $issueNumber, array $data): array
    {
        $client = self::getClient();
        $response = $client->patch("repos/{$owner}/{$repo}/issues/{$issueNumber}", [
            'json' => [
                'title' => $data['title'] ?? '',
                'body' => $data['body'] ?? '',
                'assignees' => $data['assignees'] ?? [],
                'labels' => $data['labels'] ?? [],
                'milestone' => $data['milestone'] ?? [],
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get a specific issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @return array The issue details.
     */
    public static function getIssue(string $owner, string $repo, int $issueNumber): array
    {
        $client = self::getClient();
        $response = $client->get("repos/{$owner}/{$repo}/issues/{$issueNumber}");
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * Close an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @return array The response from GitHub API.
     */
    public static function closeIssue(string $owner, string $repo, int $issueNumber)
    {
        $client = self::getClient();
        $response = $client->patch("repos/{$owner}/{$repo}/issues/{$issueNumber}", [
            'json' => [
                'state' => 'closed',
            ],
        ]);
        
        if ($response->getStatusCode() === 200) {
            return [
                'message' => 'Issue closed successfully',
                'status_code' => $response->getStatusCode(),
            ];
        }
    }



    /**
     * Assign users to an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @param array $assignees List of usernames to assign.
     * @return array The response from GitHub API.
     */
    public static function assignUsersToIssue(string $owner, string $repo, int $issueNumber, array $assignees): array
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
    public static function removeUsersFromIssue(string $owner, string $repo, int $issueNumber, array $assignees): array
    {
        $client = self::getClient();
        $response = $client->delete("repos/{$owner}/{$repo}/issues/{$issueNumber}/assignees", [
            'json' => [
                'assignees' => $assignees,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Add labels to an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @param array $labels List of labels to add.
     * @return array The response from GitHub API.
     */
    public static function addLabelsToIssue(string $owner, string $repo, int $issueNumber, array $labels): array
    {
        $client = self::getClient();
        $response = $client->post("repos/{$owner}/{$repo}/issues/{$issueNumber}/labels", [
            'json' => [
                'labels' => $labels,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Remove labels from an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @param array $labels List of labels to remove.
     * @return array The response from GitHub API.
     */
    public static function removeLabelsFromIssue(string $owner, string $repo, int $issueNumber, array $labels): array
    {
        $client = self::getClient();
        $response = $client->delete("repos/{$owner}/{$repo}/issues/{$issueNumber}/labels", [
            'json' => [
                'labels' => $labels,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * List labels for an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @return array The list of labels for the issue.
     */
    public static function listLabelsForIssue(string $owner, string $repo, int $issueNumber): array
    {
        $client = self::getClient();
        $response = $client->get("repos/{$owner}/{$repo}/issues/{$issueNumber}/labels");
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * Add a milestone to an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @param int $milestoneNumber The milestone number.
     * @return array The response from GitHub API.
     */

    public static function addMalestoneToIssue(string $owner, string $repo, int $issueNumber, int $milestoneNumber): array
    {
        $client = self::getClient();
        $response = $client->post("repos/{$owner}/{$repo}/issues/{$issueNumber}/milestone", [
            'json' => [
                'milestone' => $milestoneNumber,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Remove a milestone from an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @return array The response from GitHub API.
     */
    public static function removeMalestoneFromIssue(string $owner, string $repo, int $issueNumber): array
    {
        $client = self::getClient();
        $response = $client->delete("repos/{$owner}/{$repo}/issues/{$issueNumber}/milestone");
        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * List milestones for an issue.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $issueNumber The issue number.
     * @return array The list of milestones for the issue.
     */
    public static function listMilestonesForIssue(string $owner, string $repo, int $issueNumber): array
    {
        $client = self::getClient();
        $response = $client->get("repos/{$owner}/{$repo}/issues/{$issueNumber}/milestone");
        return json_decode($response->getBody()->getContents(), true);
    }

}