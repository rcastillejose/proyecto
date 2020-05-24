<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $grupo = new Group();
        $grupo->setName('General');
        $manager->persist($grupo);

        $grupo2 = new Group();
        $grupo->setName('Sistemas');
        $manager->persist($grupo2);
        
        $grupo3 = new Group();
        $grupo->setName('Programadores');
        $manager->persist($grupo3);

        
       
        
        $manager->flush();
    }
}
