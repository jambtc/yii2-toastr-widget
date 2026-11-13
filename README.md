# Yii2 Toastr Widget 🔥  

[![Latest Stable Version](https://poser.pugx.org/jambtc/yii2-toastr-widget/v/stable)](https://packagist.org/packages/jambtc/yii2-toastr-widget)
[![Total Downloads](https://poser.pugx.org/jambtc/yii2-toastr-widget/downloads)](https://packagist.org/packages/jambtc/yii2-toastr-widget)
[![License](https://poser.pugx.org/jambtc/yii2-toastr-widget/license)](https://packagist.org/packages/jambtc/yii2-toastr-widget)

Un semplice widget Yii2 che integra [Toastr.js](https://codeseven.github.io/toastr/) per mostrare notifiche eleganti basate su messaggi flash.

---

## 📦 Installazione

Puoi installarlo facilmente tramite [Composer](https://getcomposer.org/):

```bash
composer require jambtc/yii2-toastr-widget
```

Toastr arriva come pacchetto `npm-asset/toastr` (versione 2.1.4, compatibile con jQuery 3).
Il progetto Yii2 deve poter installare i pacchetti `npm-asset/*`: con il template base
di Yii2 basta il repository [asset-packagist](https://asset-packagist.org) nel `composer.json`:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://asset-packagist.org"
    }
]
```

### Novità 1.2.0

- Richiede PHP >= 8.1.
- I messaggi sono codificati HTML (`Html::encode()`) prima di arrivare a toastr, che di default li
  inserisce come HTML: un flash con dati dell'utente o di un servizio esterno non può più iniettare markup.
  Per HTML voluto: `'encode' => false`.
- JSON con UTF-8 leggibile (`è` resta `è`).
- Messaggi vuoti o non testuali (array, oggetti, `null`, `bool`) vengono saltati.
- Opzioni toastr configurabili con `clientOptions`; senza configurazione l'aspetto è quello di prima.
- Script e asset vengono registrati solo se ci sono messaggi da mostrare.
- `ToastrAsset` è caricato in fondo alla pagina (`POS_END`): jQuery non viene più spostato nell'`<head>`.

**Aggiornando dalla 1.1:**

- se usi `toastr` anche da JavaScript tuo (es. risposte AJAX), registra l'asset nel layout:
  `\jambtc\ToastrWidget\assets\ToastrAsset::register($this);` (prima lo faceva il widget su ogni pagina);
- se chiami `toastr` da script inline nel corpo della pagina prima del caricamento (non in `POS_READY`/`POS_LOAD`),
  riporta l'asset nell'`<head>` da configurazione:

```php
'components' => [
    'assetManager' => [
        'bundles' => [
            \jambtc\ToastrWidget\assets\ToastrAsset::class => [
                'jsOptions' => ['position' => \yii\web\View::POS_HEAD],
            ],
        ],
    ],
],
```

### Novità 1.1.0

- Toastr da `npm-asset/toastr` 2.1.4 invece di `bower-asset/toastr` 2.1.3: niente più blocco su jQuery < 3.
- I flash di tipo `danger` vengono mostrati come `error` (prima chiamavano `toastr.danger`, che non esiste).

## Configurazione

Nel layout principale (es: views/layouts/main.php), inserisci il widget:

```php
\jambtc\ToastrWidget\Toast::widget();
```

### Opzioni

```php
\jambtc\ToastrWidget\Toast::widget([
    // opzioni toastr, unite ai default (closeButton, progressBar, toast-bottom-right, 15s, ...)
    'clientOptions' => [
        'positionClass' => 'toast-top-center',
        'timeOut' => 5000,
    ],
    // true (default): ogni messaggio passa da Html::encode().
    // false solo per messaggi con HTML voluto e senza dati esterni.
    'encode' => true,
]);
```

| Proprietà | Default | Descrizione |
| --- | --- | --- |
| `clientOptions` | `[]` | Opzioni [toastr](https://github.com/CodeSeven/toastr#options); i default sono in `Toast::DEFAULT_CLIENT_OPTIONS` |
| `encode` | `true` | Codifica HTML dei messaggi |
| `toastTypes` | `error`, `danger`, `success`, `info`, `warning` | Tipo di flash Yii => metodo toastr |

## Test

```bash
composer install
composer test
```
