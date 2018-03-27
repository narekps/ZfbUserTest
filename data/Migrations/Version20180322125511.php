<?php declare(strict_types = 1);

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Zend\Crypt\Password\Bcrypt;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180322125511 extends AbstractMigration
{
    /**
     * @var array
     */
    private $cfg;

    public function __construct(Version $version)
    {
        parent::__construct($version);

        $this->cfg = include __DIR__ . '/../../config/autoload/local.php';
    }

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
        $password = $bctypt->create($this->cfg['admin_user']['credential']);
        $this->connection->insert('zfb_users', [
            'identity'           => $this->cfg['admin_user']['identity'],
            'credential'         => $password,
            'identity_confirmed' => true,
            'surname'            => '',
            'name'               => 'Администратор',
            'role'               => 'admin',
        ]);

        $this->connection->insert('clients', [
            'full_name'    => $this->cfg['fake_user']['identity'],
            'inn'          => '0000000000',
            'kpp'          => '000000000',
            'address'      => '',
            'date_created' => '2018-01-01 00:00:00',
        ]);

        $fakeClientId = $this->connection->lastInsertId();

        // Пароль для фейкового пользователя
        $fakePassword = $bctypt->create($this->cfg['fake_user']['credential']);
        $this->connection->insert('zfb_users', [
            'client_id'          => $fakeClientId,
            'identity'           => $this->cfg['fake_user']['identity'],
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
        $this->connection->delete('zfb_users', ['identity' => $this->cfg['admin_user']['identity']]);
        $this->connection->delete('zfb_users', ['identity' => $this->cfg['fake_user']['identity']]);
        $this->connection->delete('clients', ['full_name' => $this->cfg['fake_user']['identity']]);
    }
}
