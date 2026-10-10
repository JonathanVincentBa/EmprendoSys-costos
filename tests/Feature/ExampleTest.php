<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Tus decisiones, con números claros.')
        ->assertSee('Facturación electrónica')
        ->assertSee('wa.me/593995789977')
        ->assertDontSee('cdn.tailwindcss.com');
});
