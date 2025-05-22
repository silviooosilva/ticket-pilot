<?php

use Fabledsolutions\GithubSdk\Assignees;

require_once __DIR__ . '/../vendor/autoload.php';

$owner = 'silviooosilva';
$repo = 'CacheerPHP';
$issueNumber = 9;
$assignees = ['silviooosilva'];


// $assignee = Assignees::getAssignees($owner, $repo);
// $assignee = Assignees::addAssignees($owner, $repo, $issueNumber, $assignees);
// $assignee = Assignees::removeAssignees($owner, $repo, $issueNumber, $removeAssignees);
// var_dump($assignee);