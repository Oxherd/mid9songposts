<?php

namespace Tests\Unit\Posts\Baha;

use App\Models\Poster;
use App\Posts\Baha\PosterData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosterDataTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_save_poster_data_into_database()
    {
        $posterData = new PosterData('foobar666', 'JohnDoe');

        $this->assertCount(0, Poster::all());

        $posterData->save();

        $this->assertCount(1, Poster::all());

        $this->assertDatabaseHas('posters', [
            'account' => 'foobar666',
            'name' => 'JohnDoe'
        ]);
    }

    /** @test */
    public function it_wont_create_same_two_row_into_database()
    {
        $posterData = new PosterData('foobar666', 'JohnDoe');

        $posterData->save();

        $this->assertCount(1, Poster::all());

        $posterData->save();

        $this->assertCount(1, Poster::all());
    }

    /** @test */
    public function it_will_update_poster_name_if_passing_a_different_name_compare_to_existed_one()
    {
        $foobar666 = (new PosterData('foobar666', 'JohnDoe'))->save();

        $this->assertEquals('JohnDoe', $foobar666->name);

        (new PosterData('foobar666', 'Peter'))->save();

        $this->assertEquals('Peter', $foobar666->fresh()->name);
    }
}
