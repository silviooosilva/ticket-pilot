<?php

use Fabledsolutions\GithubSdk\Assignees;
use Fabledsolutions\GithubSdk\Client;

require_once __DIR__ . '/../vendor/autoload.php';

// var_dump(Assignees::getAssignees('silviooosilva', 'CacheerPHP'));

//var_dump(Client::setup());

// var_dump(Assignees::addAssignees('silviooosilva', 'CacheerPHP', 9, ['silviooosilva'])); 

 var_dump(Assignees::removeAssignees('silviooosilva', 'CacheerPHP', 9, ['silviooosilva']));

/*
use Fabledsolutions\GithubSdk\Client;

$class = new Client();
try {
    $user = $class->getUser('Igor-Ponso');
    echo "User: " . $user['login'] . "\n";
    echo "Name: " . $user['name'] . "\n";
    echo "Bio: " . $user['bio'] . "\n";
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
    */