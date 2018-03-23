<?php declare(strict_types = 1);

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Zend\Crypt\Password\Bcrypt;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180322125511 extends AbstractMigration
{

    const LOGIN = 'admin@mail.ru';
    const PASSWORD = '123456';
    const ROLE = 'admin';

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
        $password = $bctypt->create(self::PASSWORD);

        $sql = "INSERT INTO zfb_users (identity, credential, identity_confirmed, surname, name, role) VALUES ('" . self::LOGIN . "', '{$password}', 1, 'Администратор', 'Админ', '" . self::ROLE . "');";
        $this->addSql($sql);
    }

    public function down(Schema $schema)
    {
        $identity = 'admin@mail.ru';
        $sql = "DELETE FROM zfb_users WHERE identity='{$identity}'";
        $this->addSql($sql);
    }
}
