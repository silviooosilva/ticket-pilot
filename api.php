<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/src/Issues.php';
require_once __DIR__ . '/src/Milestones.php';
require_once __DIR__ . '/src/Assignees.php';
require_once __DIR__ . '/vendor/autoload.php';

use Fabledsolutions\GithubSdk\Client;
use Fabledsolutions\GithubSdk\Issues;
use Fabledsolutions\GithubSdk\Assignees;
use Fabledsolutions\GithubSdk\Milestones;

header('Content-Type: application/json');

$input = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? json_decode(file_get_contents('php://input'), true)
    : $_GET;

$action = $input['action'] ?? '';
$owner = $input['owner'] ?? '';
$repo = $input['repo'] ?? '';

try {
    switch ($action) {
        case 'listIssues':
            if (isset($input['milestone']) && $input['milestone'] !== '') {
                echo json_encode(Issues::listIssues($owner, $repo, ['milestone' => $input['milestone']]));
            } else {
                echo json_encode(Issues::listIssues($owner, $repo));
            }
            break;
        case 'getIssue':
            echo json_encode(Issues::getIssue($owner, $repo, (int)$input['issueNumber']));
            break;
        case 'createIssue':
            echo json_encode(Issues::createIssue($owner, $repo, $input['data']));
            break;
        case 'updateIssue':
            echo json_encode(Issues::updateIssue($owner, $repo, (int)$input['issueNumber'], $input['data']));
            break;
        case 'deleteIssue':
        case 'closeIssue':
            echo json_encode(Issues::closeIssue($owner, $repo, (int)$input['issueNumber']));
            break;
        case 'listMilestones':
            echo json_encode(Milestones::listMilestones($owner, $repo));
            break;
        case 'createMilestone':
            echo json_encode(Milestones::createMilestone($owner, $repo, $input['data']));
            break;
        case 'updateMilestone':
            echo json_encode(Milestones::updateMilestone($owner, $repo, (int)$input['milestoneNumber'], $input['data']));
            break;
        case 'getMilestone':
            echo json_encode(Milestones::getMilestone($owner, $repo, (int)$input['milestoneNumber']));
            break;
        case 'getAssignees':
            echo json_encode(Assignees::getAssignees($owner, $repo));
            break;
        case 'deleteMilestone':
            echo json_encode(Milestones::deleteMilestone($owner, $repo, (int)$input['milestoneNumber']));
            break;
        case 'listPublicRepositories':
            echo json_encode(Client::listPublicRepositoriesByUser($owner));
            break;
        default:
            echo json_encode(['error' => 'Invalid action specified.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
