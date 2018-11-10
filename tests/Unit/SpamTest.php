<?php

namespace Tests\Feature;

use App\Inspections\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpamTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_checks_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException(\Exception::class);

        $spam->detect('yahoo customer support');
    }
    
    /** @test */
    public function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();

        $this->expectException(\Exception::class);

        $spam->detect('Hello world aaaaaaaa');
    }
}
