<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;

class LodestoneFreeCompanyRepository extends EntityRepository
{
    // Returns free company
    public function get()
    {
        $company = $this->findOneBy(array());
        return $company ? $company : new LodestoneFreeCompany();
    }
}
