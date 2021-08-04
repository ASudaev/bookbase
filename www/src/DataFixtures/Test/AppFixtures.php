<?php

namespace App\DataFixtures\Test;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $sql = file_get_contents(__DIR__ . '/test_data.sql');
        $manager->getConnection()->exec($sql);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
