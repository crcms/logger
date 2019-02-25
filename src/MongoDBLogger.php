<?php

namespace CrCms\Log;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use Monolog\Handler\MongoDBHandler;
use Monolog\Logger as MongoLogger;

/**
 * Class MongoDBLogger.
 */
class MongoDBLogger
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * MongoDBLogger constructor.
     *
     * @param Repository      $config
     * @param Dispatcher      $events
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db, Repository $config, Dispatcher $events)
    {
        $this->db = $db;
        $this->config = $config;
        $this->events = $events;
    }

    /**
     * @param array $config
     *
     * @return Logger
     */
    public function __invoke(array $config)
    {
        return new Logger($this->mongoLogger($config), $this->events);
    }

    /**
     * @param array $config
     *
     * @return MongoLogger
     */
    protected function mongoLogger(array $config): MongoLogger
    {
        $driver = $config['database']['driver'];

        $channel = empty($config['name']) ?
            $this->config->get('app.env', 'production') :
            $config['name'];

        $database = empty($config['database']['database']) ?
            $this->config->get("database.connections.{$driver}.database") :
            $config['database']['database'];

        return new MongoLogger(
            $channel,
            [
                new MongoDBHandler(
                    $this->db->connection($driver)->getMongoClient(),
                    $database,
                    $config['database']['collection']
                ),
            ]
        );
    }
}
