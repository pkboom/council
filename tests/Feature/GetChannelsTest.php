<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;

class GetChannelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function channel_names_can_be_obtained()
    {
        create(Channel::class, [], 2);

        $channels = Channel::get()->toArray();

        $response = $this->get(route('channels.show'))->json();

        $this->assertEquals($channels, $response);
    }
}
