<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime; // Import ajouté ici

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $subscriptions = [
            [
                'name' => 'Gratuit',
                'description' => 'Accès limité avec quelques fonctionnalités gratuites.',
                'max_pdf' => 3,
                'price' => 0,
                'special_price' => null,
                'special_price_from' => null,
                'special_price_to' => null,
            ],
            [
                'name' => 'Moyen',
                'description' => 'Un accès standard avec plus de fonctionnalités.',
                'max_pdf' => 10,
                'price' => 9.99,
                'special_price' => 7.99,
                'special_price_from' => new DateTime('2025-05-01'),
                'special_price_to' => new DateTime('2025-05-31'),
            ],
            [
                'name' => 'Premium',
                'description' => 'Accès complet avec toutes les fonctionnalités.',
                'max_pdf' => 150,
                'price' => 15.99,
                'special_price' => 12.99,
                'special_price_from' => new DateTime('2025-06-01'),
                'special_price_to' => new DateTime('2025-06-30'),
            ],
        ];

        foreach ($subscriptions as $data) {
            $subscription = new Subscription();
            $subscription->setName($data['name']);
            $subscription->setDescription($data['description']);
            $subscription->setMaxPdf($data['max_pdf']);
            $subscription->setPrice($data['price']);
            $subscription->setSpecialPrice($data['special_price']);
            $subscription->setSpecialPriceFrom($data['special_price_from']);
            $subscription->setSpecialPriceTo($data['special_price_to']);

            $manager->persist($subscription);
        }

        $manager->flush();
    }
}
