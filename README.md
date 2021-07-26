# Service Schema
[![Software license][ico-license]](README.md)
[![Version][ico-version-stable]][link-packagist]
[![Build status][ico-travis]][link-travis]
[![Coverage][ico-codecov]][link-codecov]


[ico-license]: https://img.shields.io/github/license/nrk/predis.svg?style=flat-square
[ico-version-stable]: https://img.shields.io/packagist/v/micronative/service-schema.svg
[ico-travis]: https://travis-ci.com/micronative/service-schema.svg?branch=master
[ico-codecov]: https://codecov.io/gh/micronative/service-schema/branch/master/graph/badge.svg

[link-packagist]: https://packagist.org/packages/micronative/service-schema
[link-codecov]: https://codecov.io/gh/micronative/service-schema
[link-travis]: https://travis-ci.com/github/micronative/service-schema

Service-schema was created as a tool to process messages from a broker or between microservices.
Based on the concept of "event schema first", service-schema improves things a step further by introducing  schema for each service 
in order to reuse services and schemas in different events through configuration:

+ Each event might has one, or many services that are listening to it
+ Each service has one schema which will be used to validate the input json

## Configuration
<pre>
"require": {
        "micronative/service-schema": "^3.0.0"
},
"repositories": [
    { "type": "vcs", "url": "https://github.com/micronative/service-schema" }
],
</pre>

Run
<pre>
composer require micronative/service-schema:3.0.0
</pre>

## Sample code
The codes under [samples](./samples) is a mock microservice architecture:
- a [MessageBroker](./samples/MessageBroker)
- two microservices: [UserService](./samples/UserService) and [TaskService](./samples/TaskService)

When a User created or updated on UserService, it will use ServiceSchema to validate the event then publish it to MessageBroker. TaskService is listening to these events and use ServiceSchema to process the incoming events
```php
try {
    $broker = new MockBroker();
    $userApp = new UserApp($broker);
    $taskApp = new TaskApp($broker);

    $userApp->createUser('Ken', 'ken.ngo@gmail.com');
    $taskApp->listen();
}catch (Exception $e){
    echo $e->getMessage();
}
```
@see: [create_user.php](./samples/create_user.php)

```php
try {
    $broker = new MockBroker();
    $userApp = new UserApp($broker);
    $taskApp = new TaskApp($broker);

    $user = new User('Ken', 'ken.ngo@gmail.com');
    $userApp->updateUser($user);
    $taskApp->listen();
}catch (Exception $e){
    echo $e->getMessage();
}
```
@see: [update_user.php](./samples/update_user.php)
