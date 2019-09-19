# ReactPHP functions

Set of simple PHP functions turned non-blocking on too of
[ReactPHP](https://reactphp.org/)

[![CircleCI](https://circleci.com/gh/mmoreram/reactphp-functions.svg?style=svg)](https://circleci.com/gh/mmoreram/reactphp-functions)

**Table of Contents**
- [Quickstart example](#quickstart-example)
- [Usage](#usage)
    - [sleep()](#sleep)
    - [usleep()](#usleep)
    - [mime_content_type()](#mime_content_type)
- [Install](#install)
- [Tests](#tests)
- [License](#license)
    
## Quickstart example

You can easily add a sleep in your non-blocking code by using these functions.
In this code you can wait 1 second before continuing with the execution of your
promise, while the loop is actively switching between active Promises.

```php
    // use a unique event loop instance for all parallel operations
    $loop = React\EventLoop\Factory::create();

    $promise = $this
        ->doWhateverNonBlocking($loop)
        ->then(function() use ($loop) {

            // This will be executed on time t=0
            return React\sleep(1, $loop);
        })
        ->then(function() {
            
            // This will be executed on time t=1
        });
```

## Usage

This lightweight library has some small methods with the exact behavior than
their sibling methods in regular and blocking PHP.

```php
use Mmoreram\React;

React\sleep(...);
```

## EventLoop

Each function is responsible for orchestrating the [EventLoop](https://github.com/reactphp/event-loop#usage)
in order to make it run (block) until your conditions are fulfilled.

```php
$loop = React\EventLoop\Factory::create();
```

### sleep

The `sleep($seconds, LoopInterface $loop)` method can be used to sleep for
$time seconds.

```php
React\sleep(1.5, $loop)
    ->then(function() {
        // Do whatever you need after 1.5 seconds
    });
```

It is important to understand all the possible sleep implementations you can use
under this reactive programming environment.

- `\sleep($time)` - Block the PHP thread n seconds, and after this time is
elapsed, continue from the same point
- `\Clue\React\Block\sleep($time, $loop` - Don't block the PHP thread, but let
the loop continue doing cycles. Block the program execution after n seconds, and
after this time is elapsed, continue from the same point. This is a blocking
feature.
- `\Mmoreram\React\sleep($time, $loop)` - Don't block neither the PHP thread nor
the program execution. This method returns a Promise that will be resolved after
n seconds. This is a non-blocking feature.

### usleep

The `sleep($seconds, LoopInterface $loop)` method can be used to sleep for
$time microseconds.

```php
React\usleep(3000, $loop)
    ->then(function() {
        // Do whatever you need after 3000 microseconds
    });
```

The same rationale than the [`React\sleep`](#sleep) method. This is a
non-blocking action.

### mime_content_type

The `mime_content_type("/tmp/file.png", LoopInterface $loop)` method can be used
to guess the mime content type of a file. If failure, then rejects with a
RuntimeException.

```php
React\mime_content_type("/tmp/file.png", $loop)
    ->then(function(string $type) {
        // Do whatever you need with the found mime type
    });
```

This is a non-blocking action and equals the regular PHP function
[mime_content_type](https://www.php.net/manual/en/function.mime-content-type.php).

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This project follows [SemVer](https://semver.org/).
This will install the latest supported version:

```bash
$ composer require mmoreram/react-functions:dev-master
```

This library requires PHP7.

## Tests

To run the test suite, you first need to clone this repo and then install all
dependencies [through Composer](https://getcomposer.org):

```bash
$ composer install
```

To run the test suite, go to the project root and run:

```bash
$ php vendor/bin/phpunit
```

## License

This project is released under the permissive [MIT license](LICENSE).