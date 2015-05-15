<?php
namespace Blue\Framework\Api;

use Blue\Framework\Api\Client\ClientFactory;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Log\Writer;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class FrameworkApiServiceProvider extends ServiceProvider {

    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('blue/framework-api', 'blue-framework-api', __DIR__ . '/../../../');

        $this->app->bindShared(
            'blue.framework-api.client-factory',
            function(Application $app) {

                /** @var Writer $log */
                $log = $app['log'];

                $monolog = $log->getMonolog();

                $clientFactory = new ClientFactory($monolog);

                return $clientFactory;

            }
        );


        $this->app->bindShared(
            'blue.framework-api.client',
            function(Application $app) {

                /** @var Repository $config */
                $config = $app['config'];

                $user = $config->get('blue-framework-api::user');
                $secret = $config->get('blue-framework-api::secret');
                $clientSlug = $config->get('blue-framework-api::client_slug');

                if ($user && $secret && $clientSlug) {

                    /** @var ClientFactory $clientFactory */
                    $clientFactory = $app->make('blue.framework-api.client-factory');

                    $url = "http://$clientSlug.bsd.net";

                    $client = $clientFactory->createClient($user, $secret, $url);

                    return $client;
                }

                throw new RuntimeException("Framework API credentials were not properly configured");
            }
        );

        $this->commands(
            [
                'Blue\Framework\Api\Command\ApiGetCommand',
                'Blue\Framework\Api\Command\ApiPostCommand',
            ]
        );

    }

    public function provides()
    {
        return [
            'blue.framework-api.client',
            'blue.framework-api.client-factory',
        ];
    }


}