<?php
namespace Tests\Feature\Pages\Admin;

use Tests\Psr4\Concerns\AuthConcern;
use Tests\Psr4\TestCases\AdminTestCase;

class PaymentWalletTest extends AdminTestCase
{
    use AuthConcern;

    /** @test */
    public function it_loads()
    {
        // given
        $user = $this->factory->user();
        $this->actingAs($user);

        // when
        $response = $this->get('/', ['pid' => 'payment_wallet']);

        // then
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Panel Admina', $response->getContent());
        $this->assertContains('<div class="title">Płatności z portfela', $response->getContent());
    }
}
