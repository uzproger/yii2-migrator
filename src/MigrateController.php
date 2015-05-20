<?php

namespace uzproger\migrator;

use Yii;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Yii2 migrator class
 */
class MigrateController extends BaseMigrateController
{
    /**
     * @var string Main migration name
    */
    public $baseMigrationName = 'Base';
  
    /**
     * @var array Migration pathes.
     */
    public $additionalPaths = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        echo "\n";
        $this->additionalPaths = ArrayHelper::merge([
            [
                'name' => $this->baseMigrationName,
                'path' => $this->migrationPath,
            ]  
        ], $this->additionalPaths);
        $this->selectModule();

        return parent::beforeAction($action);
    }
  
    /**
     * Console select module
     *
     * @return void
     */
    protected function selectModule()
    {
        $selectedModule = Console::select("please select module", $this->getMigrationPaths());
        $this->setMigrationPath($selectedModule);
    }

    /**
     * Set migration path of selected module
     *
     * @throws Exception
     * @return void
     */
    protected function setMigrationPath($module)
    {
        if (!isset($this->migrationPaths[$module]) || !isset($this->migrationPaths[$module]['path'])) {
            throw new \Exception("Undefinied module or wrong path format.");   
        }

        $this->migrationPath = $this->migrationPaths[$module]['path'];
    }

    /**
     * @inheritdoc
     */
    protected function getMigrationHistory($limit)
    {
        if ($this->db->schema->getTableSchema($this->migrationTable, true) === null) {
            $this->createMigrationHistoryTable();
        }
        $query = new Query;
        $rows = $query->select(['version', 'apply_time'])
            ->from($this->migrationTable)
            ->where('path_hash=:path_hash', [':path_hash' => $this->hashPath()])
            ->orderBy('version DESC')
            ->limit($limit)
            ->createCommand($this->db)
            ->queryAll();
        $history = ArrayHelper::map($rows, 'version', 'apply_time');
        unset($history[self::BASE_MIGRATION]);

        return $history;
    }

    /**
     * @inheritdoc
     */
    protected function createMigrationHistoryTable()
    {
        $tableName = $this->db->schema->getRawTableName($this->migrationTable);
        echo "Creating migration history table \"$tableName\"...";
        $this->db->createCommand()->createTable($this->migrationTable, [
            'version' => 'varchar(180) NOT NULL PRIMARY KEY',
            'path_hash' => 'varchar(32) NOT NULL',
            'apply_time' => 'integer',
        ])->execute();
        $this->db->createCommand()->insert($this->migrationTable, [
            'version' => self::BASE_MIGRATION,
            'path_hash' => $this->hashPath(),
            'apply_time' => time(),
        ])->execute();
        echo "done.\n";
    }

    /**
     * @inheritdoc
     */
    protected function addMigrationHistory($version)
    {
        $command = $this->db->createCommand();
        $command->insert($this->migrationTable, [
            'version' => $version,
            'path_hash' => $this->hashPath(),
            'apply_time' => time(),
        ])->execute();
    }

    /**
     * Generate hash for current migration path
     * @return string
     */
    protected function hashPath()
    {
        return md5($this->migrationPath);
    } 

    /**
     * Returns migration paths
     * @return array
     */
    protected function getMigrationPaths()
    {
        return $this->additionalPaths;
    }
}