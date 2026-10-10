<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Tus decisiones, con números claros.')
        ->assertSee('Facturación electrónica')
        ->assertDontSee('cdn.tailwindcss.com');
});
