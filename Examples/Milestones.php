<?php

use Fabledsolutions\GithubSdk\Milestones;

require_once __DIR__ . '/../vendor/autoload.php';

$owner = 'silviooosilva';
$repo = 'CacheerPHP';

$milestoneData = [
    'title' => 'Milestone Title123',
    'description' => 'Description of the milestone for fabledsolutions',
];

//  $milestone = Milestones::createMilestone($owner, $repo, $milestoneData);
//  $milestone = Milestones::listMilestones($owner, $repo);
//  $milestone = Milestones::getMilestone($owner, $repo, 5);
//  $milestone = Milestones::updateMilestone($owner, $repo, 7, $milestoneData);
//  $milestone = Milestones::deleteMilestone($owner, $repo, 7);

var_dump($milestone);