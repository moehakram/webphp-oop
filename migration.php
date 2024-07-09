<?php

define('CONFIG', __DIR__ . '/config');
require_once __DIR__ . '/vendor/autoload.php';

use MA\PHPMVC\Database\Database;

interface Migration
{
    public function version(): int;
    public function migrate(\PDO $db): void;
}

class MigrationRunner
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function runMigration(Migration $migration, int $existingVersion): void
    {
        if ($migration->version() > $existingVersion) {
            $migration->migrate($this->db);
            $this->db->exec("INSERT INTO `version` (`id`) VALUES ({$migration->version()})");
            echo 'migration - '.$migration->version() . ' sukses'. PHP_EOL;
        }
    }

    public function getExistingVersion(): int
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS version (
            id INT NOT NULL
        ) ENGINE=InnoDB");

        $result = $this->db->query("SELECT MAX(id) AS version FROM `version`")->fetch();
        return $result['version'] ?? 0;
    }
}

function execute(array $migrations): void
{
    $db = Database::getConnection();
    $runner = new MigrationRunner($db);
    try {
        $db->beginTransaction();
        $existingVersion = $runner->getExistingVersion();
        foreach ($migrations as $migration) {
            $runner->runMigration(new $migration($db), $existingVersion);
        }
        $db->commit();
    } catch (\Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
            echo "Migration failed: " . $e->getMessage();
        }
    }
}

class Migration01 implements Migration
{
    public function version(): int
    {
        return 1;
    }

    public function migrate(\PDO $db): void
    {
        $db->exec("CREATE TABLE users (
            id VARCHAR(255) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role INT NOT NULL
        ) ENGINE=InnoDB");
    }
}

class Migration02 implements Migration
{
    public function version(): int
    {
        return 2;
    }

    public function migrate(\PDO $db): void
    {
        $db->exec("CREATE TABLE sessions (
            id VARCHAR(255) PRIMARY KEY,
            user_id VARCHAR(255) NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB");
    }
}

execute([
    Migration01::class,
    Migration02::class
]);
