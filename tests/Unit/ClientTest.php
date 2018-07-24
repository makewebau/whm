<?php

namespace Tests\Unit;

use MakeWeb\WHM\Client;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /** @test */
    public function the_client_class_can_be_instantiated()
    {
        $this->assertTrue((new Client) instanceof Client);
    }
}
