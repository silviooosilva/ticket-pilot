<?php

declare(strict_types=1);

namespace Fabledsolutions\GithubSdk;

require_once __DIR__ . '/../vendor/autoload.php';

class Milestones
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
     * Get the list of milestones for a repository.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @return array The list of milestones.
     */
    public static function listMilestones(string $owner, string $repo): array
    {
        $client = self::getClient();
        $response = $client->get("repos/{$owner}/{$repo}/milestones");
        return json_decode($response->getBody()->getContents(), true);
    }
    /**
     * Create a new milestone.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array $data The data for the new milestone.
     * @return array The created milestone.
     */
    public static function createMilestone(string $owner, string $repo, array $data): array
    {
        $client = self::getClient();
        $response = $client->post("repos/{$owner}/{$repo}/milestones", [
            'json' => [
            'title' => $data['title'] ?? '',
            'state' => 'open',
            'description' => $data['description'] ?? '',
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get a specific milestone.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $milestoneNumber The milestone number.
     * @return array The milestone details.
     */
    public static function getMilestone(string $owner, string $repo, int $milestoneNumber): array
    {
        $client = self::getClient();
        $response = $client->get("repos/{$owner}/{$repo}/milestones/{$milestoneNumber}");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Update an existing milestone.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $milestoneNumber The milestone number.
     * @param array $data The data to update the milestone.
     * @return array The updated milestone.
     */
    public static function updateMilestone(string $owner, string $repo, int $milestoneNumber, array $data): array
    {
        $client = self::getClient();
        $response = $client->patch("repos/{$owner}/{$repo}/milestones/{$milestoneNumber}", [
            'json' => [
            'title' => $data['title'],
            'state' => 'open',
            'description' => $data['description'] ?? '',
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Delete a milestone.
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param int $milestoneNumber The milestone number.
     * @return array The response from GitHub API.
     */
    public static function deleteMilestone(string $owner, string $repo, int $milestoneNumber)
    {
        $client = self::getClient();
        $response = $client->delete("repos/{$owner}/{$repo}/milestones/{$milestoneNumber}");

        if ($response->getStatusCode() === 204) {
            return [
                'message' => 'Milestone deleted successfully',
                'status_code' => $response->getStatusCode(),
            ];
        }
       
    }
   
}