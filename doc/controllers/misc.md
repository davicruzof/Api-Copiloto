# Misc

```php
$miscController = $client->getMiscController();
```

## Class Name

`MiscController`

## Methods

* [Create](/doc/controllers/misc.md#create)
* [Create Password](/doc/controllers/misc.md#create-password)
* [Terms](/doc/controllers/misc.md#terms)
* [Valida Token](/doc/controllers/misc.md#valida-token)


# Create

```php
function create(Createrequest $body): void
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `body` | [`Createrequest`](/doc/models/createrequest.md) | Body, Required | - |

## Response Type

`void`

## Example Usage

```php
$body_dataNascimento = '2000/10/10';
$body_nome = 'Ana Almeida';
$body_email = 'davidejesu5@gmail.com';
$body_telefone = '79998020550';
$body_sexo = 'f';
$body = new Models\Createrequest(
    $body_dataNascimento,
    $body_nome,
    $body_email,
    $body_telefone,
    $body_sexo
);

$miscController->create($body);
```


# Create Password

```php
function createPassword(CreatePasswordRequest $body): void
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `body` | [`CreatePasswordRequest`](/doc/models/create-password-request.md) | Body, Required | - |

## Response Type

`void`

## Example Usage

```php
$body_id = '2';
$body_senha = '12345';
$body = new Models\CreatePasswordRequest(
    $body_id,
    $body_senha
);

$miscController->createPassword($body);
```


# Terms

```php
function terms(): void
```

## Response Type

`void`

## Example Usage

```php
$miscController->terms();
```


# Valida Token

```php
function validaToken(ValidaTokenRequest $body): void
```

## Parameters

| Parameter | Type | Tags | Description |
|  --- | --- | --- | --- |
| `body` | [`ValidaTokenRequest`](/doc/models/valida-token-request.md) | Body, Required | - |

## Response Type

`void`

## Example Usage

```php
$body_token = '3977';
$body_idUser = '2';
$body = new Models\ValidaTokenRequest(
    $body_token,
    $body_idUser
);

$miscController->validaToken($body);
```

