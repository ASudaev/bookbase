<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    DAMA\DoctrineTestBundle\DAMADoctrineTestBundle::class => ['test' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Knp\DoctrineBehaviors\DoctrineBehaviorsBundle::class => ['all' => true],
];
