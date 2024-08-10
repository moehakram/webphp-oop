<?php
declare(strict_types=1);

namespace MA\PHPQUICK;

use Closure;
use MA\PHPQUICK\Session\Session;
use MA\PHPQUICK\Database\Database;
use MA\PHPQUICK\Http\Responses\Response;

/**
 * Class Bootstrap
 * 
 * This class is responsible for bootstrapping the application by initializing
 * configurations, database connections, services, repositories, error views,
 * and setting up the HTTP exception handler.
 */
class Bootstrap
{
    public function __construct(
        private Closure $initializeErrorViews,
        private Closure $initializeServices,
        private Closure $initializeRepositories,
        private ?Closure $initializeDatabase = null,
        private ?Closure $initializeConfig = null,
        private ?Closure $setHttpExceptionHandler = null, // function (HttpExceptionInterface $httpException) : ?Response
        private ?Closure $customBoot = null
    ) {}

    /**
     * Bootstraps the application by executing the initialization methods.
     *
     * @param Container $container
     * @return Container
     */
    public function boot(Container $container): Container
    {
        $this->initializeConfig($container);
        $this->initializeDatabase($container);
        $this->initializeRepositories($container);
        $this->initializeServices($container);
        $this->initializeErrorViews($container);
        $this->registerCoreInstances($container);
        $this->setHttpExceptionHandler($container);
        $this->initializeSession($container);

        $this->customBootMethods($container);
        return $container;
    }

    /**
     * Initializes the configuration by loading the config file and injecting it into the container.
     *
     * @param Container $container
     * @return void
     */
    private function initializeConfig(Container $container): void
    {
        $config = new Config(require base_path('config/config.php'));
        if($this->initializeConfig) {
            ($this->initializeConfig)($config);
        }
        $container->instance('config', $config);
    }

    /**
     * Initializes the database connection and injects it into the container.
     *
     * @param Container $container
     * @return void
     */
    private function initializeDatabase(Container $container): void
    {
        $dbConnection = Database::getConnection();
        if($this->initializeDatabase){
            ($this->initializeDatabase)($dbConnection);
        }
        $container->instance(\PDO::class, $dbConnection);
    }

    /**
     * Initializes the repositories by invoking the provided closure.
     *
     * @param Container $container
     * @return void
     */
    private function initializeRepositories(Container $container): void
    {
        ($this->initializeRepositories)($container);
    }

    /**
     * Initializes the services by invoking the provided closure.
     *
     * @param Container $container
     * @return void
     */
    private function initializeServices(Container $container): void
    {
        ($this->initializeServices)($container);
    }

    /**
     * Initializes the error views by invoking the provided closure.
     *
     * @param Container $container
     * @return void
     */
    private function initializeErrorViews(Container $container): void
    {
        ($this->initializeErrorViews)($container);
    }

    /**
     * Sets the HTTP exception handler in the application.
     *
     * @param Container $container
     * @return void
     */
    private function setHttpExceptionHandler(Container $container): void
    {
        if ($this->setHttpExceptionHandler) {
            $container->instance('HttpExceptionHandler', $this->setHttpExceptionHandler);
        }
    }

    /**
     * Registers core instances such as Container, Response, and Application in the container.
     *
     * @param Container $container
     * @return void
     */
    private function registerCoreInstances(Container $container): void
    {
        $container->instance(Container::class, $container);
        $container->instance(Application::class, $container);
        $container->instance(Response::class, new Response());
    }

    private function initializeSession(Container $container): void
    {
        $session = new Session();
        $container->instance(Session::class, $session);
    }


    private function customBootMethods(Container $container): void
    {
        if ($this->customBoot) {
            ($this->customBoot)($container);
        }
    }

}
