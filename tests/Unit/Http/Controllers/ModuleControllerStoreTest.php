<?php

namespace Tests\Unit\Http\Controllers;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class ModuleControllerStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanStoreModuleAndReturnSuccessResponse()
    {
        $data = [
            'width' => '100px',
            'height' => '100px',
            'color' => '#ff5733',
            'link' => 'https://example.com',
        ];

        $response = $this->postJson('/api/modules', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'message' => 'Module created successfully!',
            'data' => true,
        ]);

        $this->assertDatabaseHas('modules', [
            'width' => '100px',
            'height' => '100px',
            'color' => '#ff5733',
            'link' => 'https://example.com',
        ]);
    }

    /** @test */
    public function itShouldFailValidationIfInvalidDataIsProvided()
    {
        $data = [
            'width' => 'invalid_value',
            'height' => '100px',
            'color' => '#ff5733',
            'link' => 'https://example.com',
        ];

        $response = $this->postJson('/api/modules', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['width']);
    }

}
