<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Exceptions\FileNotReadableException;
use Illuminate\Support\Facades\App;

test('exception com mensagem default em inglês', function () {
    $exception = new FileNotReadableException();

    expect($exception->getMessage())->toBe('The file entered could not be read');
});

test('exception com mensagem default em português alterando o locale', function () {
    App::setLocale('pt-br');

    $exception = new FileNotReadableException();

    expect($exception->getMessage())->toBe('O arquivo informado não pôde ser lido');
});
