<?php


namespace App\Tests\src\Controller\EquipeController;


use App\Tests\src\Functionnal;

class recalculerTVTest extends Functionnal
{
    /**
     * @test
     */
    public function recalcul_toutes_les_tv()
    {
        $this->client->request('GET', '/recalculerTV');

        $this->assertResponseStatusCodeSame(302);
    }
}