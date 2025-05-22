<?php

use Fabledsolutions\GithubSdk\Issues;

require_once __DIR__ . '/../vendor/autoload.php';

$owner = 'silviooosilva';
$repo = 'CacheerPHP';
$issueData = [
    'title' => 'CacheerPHP Issue Title',
    'body' => 'CacheerPHP Issue Body',
    'assignees' => ['silviooosilva'],
    'labels' => ['feature', 'bug'],
];

// $issue = Issues::listIssues($owner, $repo);
// $issue = Issues::createIssue($owner, $repo, $issueData);
// $issue = Issues::getIssue($owner, $repo, 11);
// $issue = Issues::updateIssue($owner, $repo, 11, $issueData);
 $issue = Issues::closeIssue($owner, $repo, 12);

var_dump($issue);