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

Assicurati che il tuo progetto Yii2 supporti l’installazione di pacchetti bower-asset/*. Se non funziona, aggiungi nel composer.json principale:

```json
"config": {
    "fxp-asset": {
        "enabled": true
    }
}
```

## Configurazione

Nel layout principale (es: views/layouts/main.php), inserisci il widget:

```php
\jambtc\ToastrWidget\Toast::widget();
```
