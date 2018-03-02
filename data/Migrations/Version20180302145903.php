<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Zend\Crypt\Password\Bcrypt;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180302145903 extends AbstractMigration
{
    public function isTransactional()
    {
        return true;
    }

    public function getDescription()
    {
        return 'Add admin user';
    }

    public function up(Schema $schema)
    {
        $bctypt = new Bcrypt();
        $bctypt->setCost(14);
        $password = $bctypt->create('123456');

        $sql = "INSERT INTO zfb_users (identity, credential, identity_confirmed, surname, name) VALUES ('admin@mail.ru', '{$password}', 1, 'Админов', 'Админ');";
        $this->addSql($sql);
    }

    public function down(Schema $schema)
    {
        $identity = 'admin@mail.ru';
        $sql = "DELETE FROM zfb_users WHERE identity='{$identity}'";
        $this->addSql($sql);
    }
}
