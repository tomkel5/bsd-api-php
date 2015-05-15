<?php
namespace Blue\Framework\Api\Command;

use App;
use Blue\Framework\Api\Client\ClientFactory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class ApiCommand extends Command {

    protected function getClient() {

        $clientSlug = $this->option('client');
        $user = $this->option('user');
        $secret = $this->option('secret');

        /** @var ClientFactory $clientFactory */
        $clientFactory = App::make('blue.framework-api.client-factory');

        $client = ($user && $secret)
            ? $clientFactory->createClient($user, $secret, "http://$clientSlug.bsd.net")
            : $clientFactory->createInternalClient($clientSlug);

        return $client;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['path', InputArgument::REQUIRED, 'The API path'],
            ['parameters', InputArgument::REQUIRED, 'The parameters for this API request in JSON format']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'client',
                null,
                InputOption::VALUE_REQUIRED,
                'The client'
            ],
            [
                'user',
                null,
                InputOption::VALUE_REQUIRED,
                'The API User ID'
            ],
            [
                'secret',
                null,
                InputOption::VALUE_REQUIRED,
                'The API secret'
            ]
        ];
    }


}