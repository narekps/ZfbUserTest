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

    public function isTransactional()
    {
        return true;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $bctypt = new Bcrypt();
        $bctypt->setCost(14);
        $password = $bctypt->create('123456');
        $this->connection->insert('zfb_users', [
            'identity'           => 'admin@mail.ru',
            'credential'         => $password,
            'identity_confirmed' => true,
            'surname'            => '',
            'name'               => 'Администратор',
            'role'               => 'admin',
        ]);

        $this->connection->insert('clients', [
            'full_name'    => 'fake',
            'inn'          => '0000000000',
            'kpp'          => '000000000',
            'address'      => '',
            'date_created' => '2018-01-01 00:00:00',
        ]);

        $fakeClientId = $this->connection->lastInsertId();

        // Пароль для фейкового пользователя
        $fakePassword = $bctypt->create('SDFGHJYUTRGR%^YT$%^e54fEER');
        $this->connection->insert('zfb_users', [
            'client_id'          => $fakeClientId,
            'identity'           => 'fake',
            'credential'         => $fakePassword,
            'identity_confirmed' => true,
            'surname'            => '',
            'name'               => 'Гость',
            'role'               => 'client_user',
        ]);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function down(Schema $schema)
    {
        $this->connection->delete('zfb_users', ['identity' => 'admin@mail.ru']);
        $this->connection->delete('zfb_users', ['identity' => 'fake']);
        $this->connection->delete('clients', ['full_name' => 'fake']);
    }
}
