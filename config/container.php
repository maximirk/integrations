<?php

use App\Domain\Product\ProductRepositoryInterface;
use App\Domain\Synchronization\SynchronizationRepositoryInterface;
use App\Domain\ControlSystem\ControlSystemRepositoryInterface;
use App\Factory\LoggerFactory;
use App\Handler\DefaultErrorHandler;
use App\Repository\RedisProductRepository;
use App\Service\ControlSystem\MoySklad\ApiMoySklad;
use App\Service\ControlSystem\MoySklad\Product\GetProductMoySklad;
use App\Service\ControlSystem\MoySklad\Stock\GetStockMoySklad;
use App\Service\ControlSystem\Wildberries\ApiWildberries;
use App\Service\ControlSystem\Wildberries\Stock\SetStockWildberries;
use App\Service\Stack\RedisStack;
use App\Service\Stack\StackInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Predis\Client;
use Predis\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Middleware\ErrorMiddleware;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use App\Repository\JsonSynchronizationRepository;
use App\Repository\JsonControlSystemRepository;
use Monolog\Processor\IntrospectionProcessor;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    ClientInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');

        return new Client([
            'scheme' => 'tcp',
            'host' => $settings['redis']['host'],
            'port' => $settings['redis']['port']
        ]);
    },

    App::class => function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        (require __DIR__ . '/routes.php')($app);
        (require __DIR__ . '/middleware.php')($app);

        return $app;
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    StreamFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UploadedFileFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UriFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    // The Slim RouterParser
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    // The logger factory
    LoggerFactory::class => function (ContainerInterface $container) {
        return new LoggerFactory($container->get('settings')['logger']);
    },

    // The logger in file error.log
    LoggerInterface::class => function (ContainerInterface $container) {
        $logger = $container->get(LoggerFactory::class)
            ->addFileHandler($container->get('settings')['logger_file'])
            ->createLogger();

        !is_object($logger)
            ?: !method_exists($logger, "pushProcessor")
            ?: $logger->pushProcessor(new IntrospectionProcessor());

        return $logger;
    },

    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['error'];
        $app = $container->get(App::class);

        $logger = $container->get(LoggerInterface::class);

        $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details'],
            $logger
        );

        $errorMiddleware->setDefaultErrorHandler($container->get(DefaultErrorHandler::class));

        return $errorMiddleware;
    },

    Application::class => function (ContainerInterface $container) {
        $application = new Application();

        $application->getDefinition()->addOption(
            new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev')
        );

        foreach ($container->get('settings')['commands'] as $class) {
            $application->add($container->get($class));
        }

        return $application;
    },

    SynchronizationRepositoryInterface::class => function (ContainerInterface $container) {
        return $container->get(JsonSynchronizationRepository::class);
    },

    ControlSystemRepositoryInterface::class => function (ContainerInterface $container) {
        return $container->get(JsonControlSystemRepository::class);
    },

    ProductRepositoryInterface::class => function (ContainerInterface $container) {
        return $container->get(RedisProductRepository::class);
    },

    StackInterface::class => function (ContainerInterface $container) {
        return new RedisStack($container->get(ClientInterface::class));
    },

    GetProductMoySklad::class => DI\autowire()
        ->method('boot', DI\get(ControlSystemRepositoryInterface::class), DI\get(LoggerInterface::class))
        ->method('bootMoySklad', DI\get(ApiMoySklad::class)),

    GetStockMoySklad::class => DI\autowire()
        ->method('boot', DI\get(ControlSystemRepositoryInterface::class), DI\get(LoggerInterface::class))
        ->method('bootMoySklad', DI\get(ApiMoySklad::class)),

    SetStockWildberries::class => DI\autowire()
        ->method('boot', DI\get(ControlSystemRepositoryInterface::class), DI\get(LoggerInterface::class))
        ->method('bootWildberries', DI\get(ApiWildberries::class)),
];
