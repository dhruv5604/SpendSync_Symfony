<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->createMany(20);

        UserFactory::new()->create([
            'roles' => ['ROLE_ADMIN'],
            'email' => 'dhruvsolanki5604admin@gmail.com',
            'username' => 'admindhruv',
        ]);
        
        UserFactory::new()->create([
            'roles' => ['ROLE_USER'],
            'email' => 'dhruvsolanki5604@gmail.com',
            'username' => 'dhruv',
        ]);

        $manager->flush();
    }
}
