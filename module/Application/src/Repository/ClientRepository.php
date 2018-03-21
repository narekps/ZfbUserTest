<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Client as ClientEntity;

/**
 * Class ClientRepository
 *
 * @package Application\Repository
 */
class ClientRepository extends EntityRepository
{
    /**
     * @param string $inn
     * @param string $kpp
     *
     * @return \Application\Entity\Client|null
     */
    public function getByInnKpp(string $inn, string $kpp): ?ClientEntity
    {
        /** @var ClientEntity $client */
        $client = $this->findOneBy([
            'inn' => $inn,
            'kpp' => $kpp,
        ]);

        return $client;
    }
}
