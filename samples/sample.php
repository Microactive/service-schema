<?php
require_once('./vendor/autoload.php');

use Samples\MockBroker\MessageBroker;
use Samples\TaskService\TaskApp;
use Samples\UserService\UserApp;

$broker = new MessageBroker();

$userApp = new UserApp($broker);
$userApp->createUser('Ken', 'ken.ngo@gmail.com');

$taskApp = new TaskApp($broker);
$taskApp->listen();
