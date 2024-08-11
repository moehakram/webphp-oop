<?php
declare(strict_types=1);

namespace MA\PHPQUICK;

use Closure;
use MA\PHPQUICK\Database\Database;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Contracts\ExtendedContainerInterface as App;

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
        private Closure $initializeSession,
        private Closure $middlewareAliases,
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
    public function boot(App $app): Container
    {
        $this->registerCoreInstances($app);
        $this->initializeConfig($app);
        $this->initializeDatabase($app);
        $this->initializeRepositories($app);
        $this->initializeServices($app);
        $this->initializeErrorViews($app);
        $this->setHttpExceptionHandler($app);
        $this->initializeSession($app);
        $this->middlewareAliases($app);
        $this->customBootMethods($app);
        return $app;
    }

    /**
     * Initializes the configuration by loading the config file and injecting it into the container.
     *
     * @param Container $container
     * @return void
     */
    private function initializeConfig(App $app): void
    {
        $config = new Config(require base_path('config/config.php'));
        if($this->initializeConfig) {
            ($this->initializeConfig)($config);
        }
        $app->instance('config', $config);
    }

    /**
     * Initializes the database connection and injects it into the container.
     *
     * @param Container $container
     * @return void
     */
    private function initializeDatabase(App $app): void
    {
        $dbConnection = Database::getConnection();
        if($this->initializeDatabase){
            ($this->initializeDatabase)($dbConnection);
        }
        $app->instance(\PDO::class, $dbConnection);
    }

    /**
     * Initializes the repositories by invoking the provided closure.
     *
     * @param Container $container
     * @return void
     */
    private function initializeRepositories(App $app): void
    {
        ($this->initializeRepositories)($app);
    }

    /**
     * Initializes the services by invoking the provided closure.
     *
     * @param Container $container
     * @return void
     */
    private function initializeServices(App $app): void
    {
        ($this->initializeServices)($app);
    }

    /**
     * Initializes the error views by invoking the provided closure.
     *
     * @param Container $container
     * @return void
     */
    private function initializeErrorViews(App $app): void
    {
       $app->bindMany(($this->initializeErrorViews)($app));
    }

    /**
     * Sets the HTTP exception handler in the application.
     *
     * @param Container $container
     * @return void
     */
    private function setHttpExceptionHandler(App $app): void
    {
        $app->instance('http.exception.handler', $this->setHttpExceptionHandler ?? '');
    }

    /**
     * Registers core instances such as Container, Application, and Response in the container.
     *
     * @param Container $container
     * @return void
     */
    private function registerCoreInstances(App $app): void
    {
        $app->instance(Container::class, $app);
        // $app->instance(Application::class, $app);
        $app->instance(Response::class, new Response());
    }

    private function initializeSession(App $app): void
    {
        if ($this->initializeSession) {
            ($this->initializeSession)($app);
        }
    }

    private function customBootMethods(App $app): void
    {
        if ($this->customBoot) {
            ($this->customBoot)($app);
        }
    }

    private function middlewareAliases(App $app){
        $app->instance('middleware.aliases', ($this->middlewareAliases)());
    }
}
