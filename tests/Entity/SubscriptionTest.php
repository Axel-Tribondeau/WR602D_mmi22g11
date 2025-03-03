<?php
// tests/Entity/SubscriptionTest.php
namespace App\Tests\Entity;

use App\Entity\Subscription;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function testGetterAndSetter()
    {
        $subscription = new Subscription();

        $name = 'Premium';
        $description = 'Premium subscription';
        $maxPdf = 10;
        $price = 19.99;
        $specialPrice = 9.99;
        $specialPriceFrom = new \DateTime('2024-03-01');
        $specialPriceTo = new \DateTime('2024-04-01');

        $subscription->setName($name);
        $subscription->setDescription($description);
        $subscription->setMaxPdf($maxPdf);
        $subscription->setPrice($price);
        $subscription->setSpecialPrice($specialPrice);
        $subscription->setSpecialPriceFrom($specialPriceFrom);
        $subscription->setSpecialPriceTo($specialPriceTo);

        $this->assertEquals($name, $subscription->getName());
        $this->assertEquals($description, $subscription->getDescription());
        $this->assertEquals($maxPdf, $subscription->getMaxPdf());
        $this->assertEquals($price, $subscription->getPrice());
        $this->assertEquals($specialPrice, $subscription->getSpecialPrice());
        $this->assertEquals($specialPriceFrom, $subscription->getSpecialPriceFrom());
        $this->assertEquals($specialPriceTo, $subscription->getSpecialPriceTo());
    }
}
