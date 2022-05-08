<?php

use FruiVita\Corporate\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class)->in('Feature', 'Unit');

uses()
->beforeEach(function () {
    $template = require __DIR__ . '/template/Corporate.php';
    $xml = (new \SimpleXMLElement($template))->asXML();

    $this->file_system = Storage::fake('corporate', [
        'driver' => 'local',
    ]);
    $this->file_system->put('dumb_corporate_file.xml', $xml);

    // full path of the corporate file that will be created
    $this->file_path = $this->file_system->path('dumb_corporate_file.xml');
})->in('Feature/Importer');
