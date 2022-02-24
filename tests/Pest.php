<?php

use FruiVita\Corporate\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class)->in('Feature', 'Unit');

uses()
->beforeEach(function () {
    $template = require __DIR__ . '/template/Corporate.php';
    $xml = (new \SimpleXMLElement($template))->asXML();

    $this->file_system = Storage::fake('corporativo', [
        'driver' => 'local',
    ]);
    $this->file_system->put('corporativo.xml', $xml);

    // full path do arquivo corporativo que será criado
    $this->file_path = $this->file_system->path('corporativo.xml');
})->in('Feature/Importer');
