<?php

namespace Tests\Unit\Links\Sites;

use App\Links\Sites\GoogleDrive;
use PHPUnit\Framework\TestCase;

class GoogleDriveTest extends TestCase
{
    /** @test */
    public function it_can_get_its_site_name()
    {
        $googleDrive = new GoogleDrive('https://drive.google.com/file/d/1qjHMlN0coKQUv0TWPL3nyaiiQ2gzZLfW/view');

        $this->assertEquals('google_drive', $googleDrive->name());
    }

    /** @test */
    public function it_can_extract_resource_id_from_given_url()
    {
        $googleDrive = new GoogleDrive('https://drive.google.com/file/d/1qjHMlN0coKQUv0TWPL3nyaiiQ2gzZLfW/preview');

        $this->assertEquals('1qjHMlN0coKQUv0TWPL3nyaiiQ2gzZLfW', $googleDrive->getResourceId());
    }

    /** @test */
    public function it_will_return_null_if_somehow_can_not_get_expected_resource_id()
    {
        $googleDrive = new GoogleDrive('https://drive.google.com/file/d/');

        $this->assertNull($googleDrive->getResourceId());
    }

    /** @test */
    public function it_can_generate_a_general_url_by_provide_a_resource_id()
    {
        $this->assertEquals(
            'https://drive.google.com/file/d/1qjHMlN0coKQUv0TWPL3nyaiiQ2gzZLfW/view',
            GoogleDrive::generalUrl('1qjHMlN0coKQUv0TWPL3nyaiiQ2gzZLfW')
        );
    }
}
