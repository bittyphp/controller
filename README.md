# Bitty Controller

[![Build Status](https://travis-ci.org/bittyphp/bitty-controller.svg?branch=master)](https://travis-ci.org/bittyphp/bitty-controller)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/dc4b54b867cc44a5882dfb8c9fdc4ff5)](https://www.codacy.com/app/bittyphp/bitty-controller)
[![Total Downloads](https://poser.pugx.org/bittyphp/bitty-controller/downloads)](https://packagist.org/packages/bittyphp/bitty-controller)
[![License](https://poser.pugx.org/bittyphp/bitty-controller/license)](https://packagist.org/packages/bittyphp/bitty-controller)

Abstract controller for Bitty.

This package is not required. It merely provides an abstract controller that can be extended to provide commonly useful functionality.

## Installation

It's best to install using [Composer](https://getcomposer.org/).

```sh
$ composer require bittyphp/bitty-controller
```

## Container Access

A simple wrapper exists around the container `get()` method purely to access services using less code.

```php
<?php

namespace Acme\Controller;

use Bitty\Controller\AbstractController;

class ExampleController extends AbstractController
{
    public function test()
    {
        // Short method
        $myService = $this->get('some.service');

        // Normal, longer method
        $myOtherService = $this->container->get('some.other.serivce');

        // ...
    }
}
```

## Route Redirects

It is fairly common to need to redirect a user to a different route for various reasons. For that, the `redirectToRoute()` method exists to generate the route URI and return a redirect response.

```php
<?php

namespace Acme\Controller;

use Bitty\Controller\AbstractController;

class ExampleController extends AbstractController
{
    public function test()
    {
        // Redirect to another route with optional parameters.
        return $this->redirectToRoute('some.route', ['foo' => 'bar']);
    }
}
```

## Rendering Templates

You can render templates as a response using the `render()` method. Note: This method requires a `view` service to be defined that implements `Bitty\View\ViewInterface`. See the [View Layer](https://github.com/bittyphp/bitty-view) for what template engines are available or how to build your own.

```php
<?php

namespace Acme\Controller;

use Bitty\Controller\AbstractController;

class ExampleController extends AbstractController
{
    public function test()
    {
        // Render a template with the given data
        return $this->render('some.template.html', ['foo' => 'bar']);
    }
}
```
