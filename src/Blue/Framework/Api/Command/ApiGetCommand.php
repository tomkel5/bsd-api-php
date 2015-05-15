<?php
namespace Blue\Framework\Api\Command;

class ApiGetCommand extends ApiCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'api:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute a GET request against the BSD API';


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

        $parameters = json_decode($parameters, true);
        if (!$parameters) {
            $parameters = [];
        }

        $response = $client->get($path, $parameters);

        $contents = $response->getContent();

        $this->output->writeln('');

        foreach (explode("\n", $contents) as $line) {
            $this->output->writeln($line);
        }

    }
}