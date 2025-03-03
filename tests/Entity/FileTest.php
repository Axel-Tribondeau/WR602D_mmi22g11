<?php
// tests/Entity/FileTest.php
namespace App\Tests\Entity;

use App\Entity\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetterAndSetter()
    {
        $file = new File();

        $name = 'document.pdf';
        $createdAt = new \DateTimeImmutable();

        $file->setName($name);
        $file->setCreatedAt($createdAt);

        $this->assertEquals($name, $file->getName());
        $this->assertEquals($createdAt, $file->getCreatedAt());
    }
}
