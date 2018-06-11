<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_pay_a_debt()
    {
        // TODO
        // test creant un deute de 30 i pagant 28, ha de quedar deute de 2
        // test creant un deute de 30 i pagant 40, ha de crear un deute invertit de 10
        // test creant un deute de 30 i pagant 30, ha de quedar com a pagat
    }
}
