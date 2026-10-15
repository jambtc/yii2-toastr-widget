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

### Novità 1.1.0

- Toastr da `npm-asset/toastr` 2.1.4 invece di `bower-asset/toastr` 2.1.3: niente più blocco su jQuery < 3.
- I flash di tipo `danger` vengono mostrati come `error` (prima chiamavano `toastr.danger`, che non esiste).

## Configurazione

Nel layout principale (es: views/layouts/main.php), inserisci il widget:

```php
\jambtc\ToastrWidget\Toast::widget();
```
