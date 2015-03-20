<?php
namespace Core\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Debug\ErrorHandler,
    Symfony\Component\Debug\ExceptionHandler;
use Whoops\Provider\Silex\WhoopsServiceProvider,
    Whoops\Handler\JsonResponseHandler;

class ErrorHandlerProvider implements ServiceProviderInterface
{
    /**
     * @var \Core\Application
     */
    protected $app;

    public function register(Application $app)
    {
        $this->app = $app;
        $debug = $app['debug'];

        ErrorHandler::register(null, $debug);
        ExceptionHandler::register($debug);

        // Basically, this behaviour can't be changed in bootstrap.php
        $app['not.found.error.handler'] = $app->share(function () use ($app) {
            return function (\Exception $e, $code) use ($app) {
                $this->sendToSentry($e);

                return new Response($app['twig']->render('error.twig', ['message' => $e->getMessage()]), '404');
            };
        });

        if ($debug) {
            $app->register(new WhoopsServiceProvider);

            // Use json for ajax requests.
            $jsonHandler = new JsonResponseHandler;
            $jsonHandler->onlyForAjaxRequests(true);
            $app['whoops']->pushHandler($jsonHandler);
        } else {
            $app->error($app['not.found.error.handler']);
        }
    }

    protected function sendToSentry(\Exception $e)
    {
        $this->app->sentry->captureException($e);
    }

    public function boot(Application $app)
    {
    }
}
