<?php

/**
 * Codeburner Router API example
 *
 * You could test the routes requesting this file with a query string containing path variable with the
 * needed path representation. e.g. /index.php?path=/v1/category
 */

include "vendor/autoload.php";

use Codeburner\Router\Collector;
use Codeburner\Router\Matcher;
use Codeburner\Router\Exceptions\Http\NotFoundException;
use Codeburner\Router\Exceptions\Http\MethodNotAllowedException;
use Codeburner\Container\Container;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

/**
 * Our API will use three packages, the codeburner router, the codeburner container
 * and the zend diactoros as implementation of psr7.
 */

$request = ServerRequestFactory::fromGlobals();
$response = new Response();
$collector = new Collector();
$matcher = new Matcher($collector);
$container = new Container();

/**
 * Let the codeburner container know what exactly request and response give to our strategy.
 * We will use the codeburner container to create the strategy and the resources.
 */

$container->bindTo('Codeburner\Router\Strategies\RequestJsonStrategy', 'Psr\Http\Message\RequestInterface', $request);
$container->bindTo('Codeburner\Router\Strategies\RequestJsonStrategy', 'Psr\Http\Message\ResponseInterface', $response);

/**
 * Our API return only json objects, so we don't need the routes designed to return forms.
 * let's just define in a separate variable these routes for later use.
 */

$removedRoutesFromApi = ["make", "edit"];

/**
 * Let's group because of our default test route "/" and if in the future more routes
 * must be registered, you just need to add to these routes to group.
 */

$collector->group([

    /**
     * Bellow we map 10 routes for manipulating ours Categories and Articles, as in database, one article needs to have
     * at least one category, so let's make the collector know this by using the nest method. Doing this, all actions of
     * Article resource will receive one Category id.
     */

    $collector->resource('Category')->except($removedRoutesFromApi)->nest(
        $collector->resource('Article')->except($removedRoutesFromApi)
    ),

    /**
     * Setting a default route for test if API respond.
     */

    $collector->get('/', function () {
        return ['status' => 'Congratulations! Our API is working, now test some restful requests.'];
    })

])

    /**
     * Our API are on version 1, so prefix all patterns with this identifier.
     */

    ->setPrefix('/v1')

    /**
     * To finish the route definition lets define the RequestJsonStrategy as our strategy, this strategy will give
     * our actions the request object, and will use ours returning arrays to build a valid json response.
     */

    ->setStrategy('Codeburner\Router\Strategies\RequestJsonStrategy');

/**
 * For simple example we will not use the path to determine route patterns, as it will require some server
 * configuration, so simply get the path from query string "path". e.g. localhost/?path=/v1/category
 */

$query = $request->getQueryParams();
$path  = isset($query['path']) ? $query['path'] : '/v1';

/**
 * Now we are ready to find the requested route using our simulated path and the given request http method.
 * Here two exceptions can be thrown, we will not treat then now.
 */

try {
    $route = $matcher->match($request->getMethod(), $path);
    $response = $route->call([$container, 'make']); // passing a container wrapper to create objects like strategy and controller

    /**
     * Sending our response headers from PSR7 response object implemented in Zend\Diactoros.
     * In our case no headers are defined on resources, but with RequestJsonStrategy the content-type: application/json
     * is defined by default, and it is the necessary.
     */

    if (!headers_sent()) {
        header(
            sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            )
        );

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
    }

    /**
     * And finally send the response body, here we simply send all together,
     * but with the PSR7 support we can easily make a stream, sending fragments of response.
     */

    echo (string) $response->getBody();
} catch (NotFoundException $e) {
    // route not found
} catch (MethodNotAllowedException $e) {
    // route found but in another http method
}
