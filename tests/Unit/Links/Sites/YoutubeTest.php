<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\Youtube;
use Tests\TestCase;

class YoutubeTest extends TestCase
{
    /** @test */
    public function it_can_get_its_nick_name()
    {
        $youtube = new Youtube('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertEquals('youtube', $youtube->name());
    }

    /** @test */
    public function it_can_extract_resource_id_from_youtube_url()
    {
        $youtube = new Youtube('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertEquals('dQw4w9WgXcQ', $youtube->getResourceId());
    }

    /** @test */
    public function it_can_also_extract_resource_id_from_short_url()
    {
        $youtube = new Youtube('https://youtu.be/dQw4w9WgXcQ');

        $this->assertEquals('dQw4w9WgXcQ', $youtube->getResourceId());
    }

    /** @test */
    public function it_can_also_extract_resource_id_from_embed_url()
    {
        $youtube = new Youtube('https://www.youtube.com/embed/dQw4w9WgXcQ');

        $this->assertEquals('dQw4w9WgXcQ', $youtube->getResourceId());
    }

    /** @test */
    public function it_will_not_get_resource_id_if_given_url_is_not_provide_video()
    {
        $youtube = new Youtube('https://www.youtube.com/channel/UCuAXFkgsw1L7xaCfnd5JJOw');

        $this->assertNull($youtube->getResourceId());
    }

    /** @test */
    public function it_will_return_null_if_somehow_can_not_get_resource_id()
    {
        $generalUrl = new Youtube('https://www.youtube.com/watch?v=');
        $shortUrl = new Youtube('https://youtu.be/');
        $embedUrl = new Youtube('https://www.youtube.com/embed/');

        $this->assertNull($generalUrl->getResourceId());
        $this->assertNull($shortUrl->getResourceId());
        $this->assertNull($embedUrl->getResourceId());
    }

    /** @test */
    public function it_can_generate_a_general_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            Youtube::generalUrl('dQw4w9WgXcQ')
        );
    }
}
