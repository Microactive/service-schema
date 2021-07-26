<?php
require_once('./vendor/autoload.php');

use Samples\MessageBroker\MockBroker;
use Samples\TaskService\TaskApp;
use Samples\UserService\UserApp;

try {
    $broker = new MockBroker();

    $userApp = new UserApp($broker);
    $userApp->createUser('Ken', 'ken.ngo@gmail.com');

    $taskApp = new TaskApp($broker);
    $taskApp->listen();
}catch (Exception $e){
    echo $e->getMessage();
}
