# Vacation

Vacation is a generic, extensible library for implementing REST endpoints in PHP.

## Dependencies

- JMS Metadata
- JMS Serializer

## Installation

```bash
composer require emarref/vacation
```

## Usage

Vacation works out of the box with standard REST API requirements.

```yaml
# vacation.yml
services:

    # The request matcher is responsible for taking a request and determining if it matches a controller, generally
    # based on URL parameter.
    vacation.request_matcher:
        class:  Emarref\Vacation\Request\Matcher
        arguments:
            - @request
            
    # The response factory builds and returns the instance of the PSR outgoing response class based on the request and
    # content, or from an exception or form error.
    vacation.response_factory:
        class:  Emarref\Vacation\Response\Factory
        arguments:
            - @jms.serializer
    
    # Controllers are registered in this registry, and is then uses the request matcher to find the appropriate
    # controller for a given request.
    vacation.controller_registry:
        class:  Emarref\Vacation\Controller\Registry
        arguments:
            - @jms.metadata_factory
            - @vacation.request_matcher
    
    # The engine uses the above services to take a request and return a response. It resolves the controller for the
    # endpoint, validates the payload if necessary, determines which operation to perform on the controller, then
    # executes it, passing the result to the response factory.
    vacation:
        class:  Emarref\Vacation\Engine
        arguments:
            - @vacation.controller_registry
            - @jms.metadata_factory
            - @vacation.response_factory
            - @dispatcher
```

```php
<?php

namespace Foo\Bar;

use Emarref\Vacation\Annotation as Vacation;

/**
 * @Vacation\Resource("posts")
 */
class BlogPostController
{
    /**
     * @Vacation\Operation("get", parameters={"sort", "direction"})
     * @param array $parameters
     * @return Entity\BlogPost[]
     */
    public function list(array $parameters = [])
    {
        // Retrieve blog posts from storage
        // Return an object that can be passed to the JMS serializer instance to serialize for the response.
    }
}
```

```php
<?php

class MyApiController
{
    public function restAction(Psr\Http\Message\IncomingRequestInterface $request)
    {
        $vacation = $container->get('vacation');
        /*
         * Register a controller for every endpoint you wish to control with this engine.
         * Ideally you would register your controllers by container configuration.
         */
        $vacation->registerController(new Foo\Bar\BlogPostController());
        $response = $vacation->execute($request);
        return $response;
    }
}
```

## Components

- Engine
- Request Matcher
- Response factory
- Controller Registry
