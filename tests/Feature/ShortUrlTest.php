<?php

namespace Tests\Feature;

use Tests\TestCase;

class ShortUrlTest extends TestCase
{

    public function test_encode_url(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/encode?url=https://www.thisisverylongdomain.com?withsome=parameter');

        $response->assertStatus(201)->assertJsonStructure(['short_url']);
    }

    public function test_decode_url()
    {
        $encodeResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/encode?url=https://www.thisisverylongdomain.com?withsome=parameter');
        $shortUrl = $encodeResponse->getData()->short_url;

        $decodeResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/decode?short_url=' . $shortUrl);

        $decodeResponse->assertStatus(201)
            ->assertJson(['url' => 'https://www.thisisverylongdomain.com?withsome=parameter']);
    }

    public function test_decode_not_found()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/decode?short_url=' . url(\Str::random(6)));

        $response->assertStatus(404)
            ->assertJson(['error' => 'Shorten url not found']);
    }
}
