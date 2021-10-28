<?php

namespace App\Tests;

use App\Service\FbGraph\GetAllPublications;
use PHPUnit\Framework\TestCase;

class GetAllPublicationsTest extends TestCase
{
    private $getAllPublications;

    protected function setUp(): void
    {
        $this->getAllPublications = new getAllPublications();
    }


    public function testSomething(): void
    {
        dd($this->getAllPublications);
        $this->assertTrue(true);
    }
}
