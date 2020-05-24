<?php

namespace App\DataFixtures;

use App\Entity\Estado;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class EstadoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $estado = new Estado();
        $estado->setNombre('Esperando');
        $manager->persist($estado);
       
        $manager->flush();
    }
}
