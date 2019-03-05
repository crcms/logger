<?php

namespace CrCms\Log;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\ParsesLogConfiguration;
use Monolog\Handler\MongoDBHandler;
use Monolog\Logger as MongoLogger;

/**
 * Class MongoDBLogger.
 */
class MongoDBLogger
{
    use ParsesLogConfiguration;

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * MongoDBLogger constructor.
     *
     * @param Repository $config
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db, Repository $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * @param array $config
     *
     * @return MongoLogger
     */
    public function __invoke(array $config): MongoLogger
    {
        return new MongoLogger(
            $this->parseChannel($config),
            [$this->mongoHandler($config),]
        );
    }

    /**
     * mongoHandler
     *
     * @param array $config
     * @return MongoDBHandler
     */
    protected function mongoHandler(array $config): MongoDBHandler
    {
        return new MongoDBHandler(
            $this->db->connection($config['database']['driver'])->getMongoClient(),
            $this->parseDatabase($config),
            $config['database']['collection'],
            $this->level($config)
        );
    }

    /**
     * parseDatabase
     *
     * @param array $config
     * @return string
     */
    protected function parseDatabase(array $config): string
    {
        return empty($config['database']['database']) ?
            $this->config->get("database.connections.{$config['database']['driver']}.database") :
            $config['database']['database'];
    }

    /**
     * getFallbackChannelName
     *
     * @return string
     */
    protected function getFallbackChannelName()
    {
        return $this->config->get('app.env', 'production');
    }
}
