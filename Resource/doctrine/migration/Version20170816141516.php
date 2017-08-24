<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Application;
use Eccube\Entity\PageLayout;
use Eccube\Entity\Master\DeviceType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170816141516 extends AbstractMigration
{
    protected $entities = array(
        'Plugin\Lib\Entity\KeyValue',
        'Plugin\Lib\Entity\Queue',
    );

    public function __construct()
    {
        $this->app = Application::getInstance();
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $meta = $this->getMetadata($this->app['orm.em']);
        // テーブル作成
        $tool = new SchemaTool($this->app['orm.em']);
        $tool->createSchema($meta);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $meta = $this->getMetadata($this->app['orm.em']);
        $tool = new SchemaTool($this->app['orm.em']);
        $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);
        // テーブル削除
        foreach ($schemaFromMetadata->getTables() as $table) {
            if ($schema->hasTable($table->getName())) {
                $schema->dropTable($table->getName());
            }
        }
        // シーケンス削除
        foreach ($schemaFromMetadata->getSequences() as $sequence) {
            if ($schema->hasSequence($sequence->getName())) {
                $schema->dropSequence($sequence->getName());
            }
        }
    }

    protected function getMetadata(EntityManager $em)
    {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }
        return $meta;
    }
}