<?php
namespace Blue\Framework\Api\Command;

use App;
use Blue\Framework\Api\Client\ClientFactory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ApiPostCommand extends ApiGetCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'api:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute a POST request against the BSD API';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $client = $this->getClient();

        $path = $this->argument('path');
        $parameters = $this->argument('parameters');
        $body = ($this->argument('body')) ?: '';

        $parameters = json_decode($parameters, true);
        if (!$parameters) {
            $parameters = [];
        }

        $response = $client->post($path, $parameters, $body);

        $contents = $response->getContent();

        $this->output->writeln('');

        foreach (explode("\n", $contents) as $line) {
            $this->output->writeln($line);
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(
            parent::getArguments(),
            [
                ['body', InputArgument::OPTIONAL, 'The body for this API request']
            ]
        );
    }
}