# Importador de Estrutura Corporativa para aplicações Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fruivita/corporate?logo=packagist)](https://packagist.org/packages/fruivita/corporate)
[![GitHub Release Date](https://img.shields.io/github/release-date/fruivita/corporate?logo=github)](/../../releases)
[![GitHub last commit (branch)](https://img.shields.io/github/last-commit/fruivita/corporate/main?logo=github)](/../../commits/main)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/fruivita/corporate/Unit%20and%20Feature%20tests/main?label=tests&logo=github)](/../../actions/workflows/tests.yml?query=branch%3Amain)
[![Test Coverage](https://api.codeclimate.com/v1/badges/98e8f7e0704865225131/test_coverage)](https://codeclimate.com/github/fruivita/corporate/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/98e8f7e0704865225131/maintainability)](https://codeclimate.com/github/fruivita/corporate/maintainability)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/fruivita/corporate/Static%20Analysis/main?label=code%20style&logo=github)](/../../actions/workflows/static.yml?query=branch%3Amain)
[![GitHub issues](https://img.shields.io/github/issues/fruivita/corporate?logo=github)](/../../issues)
![GitHub repo size](https://img.shields.io/github/repo-size/fruivita/corporate?logo=github)
[![Packagist Total Downloads](https://img.shields.io/packagist/dt/fruivita/corporate?logo=packagist)](https://packagist.org/packages/fruivita/corporate)
[![GitHub](https://img.shields.io/github/license/fruivita/corporate?logo=github)](LICENSE.md)

Importa a **Estrutura Corporativa** em formato **XML** para aplicações **[Laravel](https://laravel.com/docs)**.

Este package foi planejado de acordo com as necessidades da Justiça Federal do Espírito Santo. Contudo, ele pode ser utilizado em outros órgãos e projetos observados os termos previstos no [licenciamento](#license).

```php
use FruiVita\Corporate\Facades\Corporate;

Corporate::import($file_path);
```

&nbsp;

---

## Table of Contents

1. [Notes](#notes)

2. [Prerequisites](#prerequisites)

3. [Installation](#installation)

4. [How it works](#how-it-works)

5. [Testing and Continuous Integration](#testing-and-continuous-integration)

6. [Changelog](#changelog)

7. [Contributing](#contributing)

8. [Code of conduct](#code-of-conduct)

9. [Security Vulnerabilities](#security-vulnerabilities)

10. [Support and Updates](#support-and-updates)

11. [Roadmap](#roadmap)

12. [Credits](#credits)

13. [Thanks](#thanks)

14. [License](#license)

---

## Notes

⭐ **Estrutura Corporativa** é o nome dado à consolidação das informações mínimas sobre pessoal, cargos, funções de confiança e lotações.

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Prerequisites

1. Dependências PHP

    PHP ^8.0

    [Extensões](https://getcomposer.org/doc/03-cli.md#check-platform-reqs)

    ```bash
    composer check-platform-reqs
    ```

2. [GitHub Package Dependencies](/../../network/dependencies)

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Installation

1. Instalar via **[composer](https://getcomposer.org/)**:

    ```bash
    composer require fruivita/corporate
    ```

2. Publicar as migrations necessárias

    ```bash
    php artisan vendor:publish --provider='FruiVita\Corporate\CorporateServiceProvider' --tag='migrations'
    ```

3. Opcionalmente publicar as configurações

    ```bash
    php artisan vendor:publish --provider='FruiVita\Corporate\CorporateServiceProvider' --tag='config'
    ```

4. Opcionalmente publicar as traduções

    ```bash
    php artisan vendor:publish --provider='FruiVita\Corporate\CorporateServiceProvider' --tag='lang'
    ```

    As strings disponíveis para tradução são as que seguem. Altere-as de acordo com a necessidade.

    ```json
    {
        "End of corporate structure import": "Fim da importação da estrutura corporativa",
        "Start of corporate structure import": "Início da importação da estrutura corporativa",
        "The file entered could not be read": "O arquivo informado não pôde ser lido",
        "The file must be in [:attribute] format": "O arquivo precisa ser no formato [:attribute]",
        "Validation failed": "Validação falhou"
    }
    ```

    >Este package já possui traduções para **pt-br** e **en**.

    &nbsp;

⬆️ [Voltar](#table-of-contents)

&nbsp;

## How it works

O arquivo com a **Estrutura Corporativa** deve ser oferecido a este package em formato **XML** estruturado da seguinte forma:

```xml
<?xml version='1.0' encoding='UTF-8'?>
<base>
    <cargos>
        <!-- Cargos:
            id: integer, obrigatório e maior que 1
            nome: string, obrigatório e tamanho entre 1 e 255
            -->
        <cargo id="1" nome="Cargo 1"/>
        <cargo id="2" nome="Cargo 2"/>
    </cargos>
    <funcoes>
        <!-- Funções comissionadas:
            id: integer, obrigatório e maior que 1
            nome: string, obrigatório e tamanho entre 1 e 255
            -->
        <funcao id="1" nome="Função 1"/>
        <funcao id="2" nome="Função 2"/>
    </funcoes>
    <lotacoes>
        <!-- Lotações:
            id: integer, obrigatório e maior que 1
            nome: string, obrigatório e tamanho entre 1 e 255
            sigla: string, obrigatório e tamanho entre 1 e 50
            idPai: integer, opcional, id de uma lotação existente
            -->
        <lotacao id="1" nome="Lotação 1" sigla="Sigla 1"/>
        <lotacao id="2" nome="Lotação 2" sigla="Sigla 2" idPai=""/>
        <lotacao id="3" nome="Lotação 3" sigla="Sigla 3" idPai="1"/>
    </lotacoes>
    <pessoas>
        <!-- Pessoas:
            nome: string, obrigatório e tamanho entre 1 e 255
            sigla: string, obrigatório, usuário do LDAP Server e tamanho máximo de 20
            cargo: integer, obrigatório, id de um dos cargos informados
            lotacao: integer, obrigatório, id de uma das lotações informadas
            funcaoConfianca: integer, opcional, id de uma das funções confiança informadas
            -->
        <pessoa id="1" nome="Pessoa 1" sigla="Sigla 1" cargo="1" lotacao="2" funcaoConfianca=""/>
        <pessoa id="2" nome="Pessoa 2" sigla="Sigla 2" cargo="1" lotacao="2" funcaoConfianca="2"/>
    </pessoas>
</base>
```

&nbsp;

Para realizar a importação, são expostos os seguintes métodos:

&nbsp;

✏️ **import**

Assinatura e uso: Importa a estrutura definida no arquivo informado

```php
use FruiVita\Corporate\Facades\Corporate;

/**
 * @param string $file_path full path do arquivo XML
 * 
 * @throws \FruiVita\Corporate\Exceptions\FileNotReadableException
 * @throws \FruiVita\Corporate\Exceptions\UnsupportedFileTypeException
 *
 * @return void
 */
Corporate::import($file_path);
```

Retorno: void

&nbsp;

🚨 **Exceptions**:

- **import** lança **\FruiVita\Corporate\Exceptions\FileNotReadableException** caso não tenha permissão de leitura no arquivo ou ele não seja encontrado
- **import** lança **\FruiVita\Corporate\Exceptions\UnsupportedFileTypeException** caso o arquivo não seja um arquivo **XML**

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Testing and Continuous Integration

```bash
composer analyse
composer test
composer coverage
```

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Changelog

Por favor, veja o [CHANGELOG](CHANGELOG.md) para maiores informações sobre o que mudou em cada versão.

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Contributing

Por favor, veja [CONTRIBUTING](CONTRIBUTING.md) para maiores detalhes sobre como contribuir.

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Code of conduct

Para garantir que todos sejam bem vindos a contribuir com este projeto open-source, por favor leia e siga o [Código de Conduta](CODE_OF_CONDUCT.md).

⬆️ [Back](#conteúdo)

&nbsp;

## Security Vulnerabilities

Por favor, veja na [política de segurança](/../../security/policy) como reportar vulnerabilidades ou falhas de segurança.

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Support and Updates

A versão mais recente receberá suporte e atualizações sempre que houver necessidade. As demais, receberão atualizações por 06 meses após terem sido substituídas por uma nova versão sendo, então, descontinuadas.

| Version | PHP     | Release    | End of Life |
|---------|---------|------------|-------------|
| 1.x     | ^8.0    | 11-03-2022 | dd-mm-yyyy  |

🐛 Encontrou um bug?!?! Abra um **[issue](/../../issues/new?assignees=fcno&labels=bug%2Ctriage&template=bug_report.yml&title=%5BA+concise+title+for+the+bug%5D)**.

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Roadmap

> ✨ Alguma ideia nova?!?! Inicie **[uma discussão](/../../discussions/new?category=ideas)**.

A lista a seguir contém as necessidades de melhorias identificadas e aprovadas que serão implementadas na primeira janela de oportunidade.

- [ ] n/a

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Credits

- [Fábio Cassiano](https://github.com/fcno)

- [All Contributors](/../../contributors)

⬆️ [Voltar](#table-of-contents)

&nbsp;

## Thanks

👋 Agradeço às pessoas e organizações abaixo por terem doado seu tempo na construção de projetos open-source que foram usados neste package.

- ❤️ [Laravel](https://github.com/laravel) pelos packages:

  - [illuminate/collections](https://github.com/illuminate/collections)

  - [illuminate/database](https://github.com/illuminate/database)

  - [illuminate/support](https://github.com/illuminate/support)

- ❤️ [Orchestra Platform](https://github.com/orchestral) pelo package [orchestral/testbench](https://github.com/orchestral/testbench)

- ❤️ [FriendsOfPHP](https://github.com/FriendsOfPHP) pelo package [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

- ❤️ [Nuno Maduro](https://github.com/nunomaduro) pelo package [nunomaduro/larastan](https://github.com/nunomaduro/larastan)

- ❤️ [PEST](https://github.com/pestphp) pelos packages:

  - [pestphp/pest](https://github.com/pestphp/pest)

  - [pestphp/pest-plugin-laravel](https://github.com/pestphp/pest-plugin-laravel)

- ❤️ [Sebastian Bergmann](https://github.com/sebastianbergmann) pelo package [sebastianbergmann/phpunit](https://github.com/sebastianbergmann/phpunit)

- ❤️ [PHPStan](https://github.com/phpstan) pelos packages:

  - [phpstan/phpstan](https://github.com/phpstan/phpstan)

  - [phpstan/phpstan-deprecation-rules](https://github.com/phpstan/phpstan-deprecation-rules)

- ❤️ [ergebnis](https://github.com/ergebnis) pelo package [ergebnis/composer-normalize](https://github.com/ergebnis/composer-normalize)

- ❤️ [Shivam Mathur](https://github.com/shivammathur) pela Github Action [shivammathur/setup-php](https://github.com/shivammathur/setup-php)

- ❤️ [GP](https://github.com/paambaati) pela Github Action [paambaati/codeclimate-action](https://github.com/paambaati/codeclimate-action)

- ❤️ [Stefan Zweifel](https://github.com/stefanzweifel) pelas Github Actions:

  - [stefanzweifel/git-auto-commit-action](https://github.com/stefanzweifel/git-auto-commit-action)

  - [stefanzweifel/changelog-updater-action](https://github.com/stefanzweifel/changelog-updater-action)

💸 Algumas dessas pessoas ou organizações possuem alguns produtos/serviços que podem ser comprados. Se você puder ajudá-los comprando algum deles ou se tornando um patrocinador, mesmo que por curto período, ajudará toda a comunidade **open-source** a continuar desenvolvendo soluções para todos.

⬆️ [Voltar](#table-of-contents)

&nbsp;

## License

The MIT License (MIT). Por favor, veja o **[License File](../LICENSE.md)** para maiores informações.

⬆️ [Voltar](#table-of-contents)
