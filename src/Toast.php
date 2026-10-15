<?php

namespace jambtc\ToastrWidget;

use Yii;
use yii\base\Widget;

class Toast extends Widget
{
    /**
     * @var array tipo di flash Yii => metodo Toastr (Toastr non ha "danger": diventa "error")
     */
    public $toastTypes = [
        'error'   => 'error',
        'danger'  => 'error',
        'success' => 'success',
        'info'    => 'info',
        'warning' => 'warning',
    ];

    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();

        $script = [];

        foreach ($flashes as $type => $messages) {
            if (!isset($this->toastTypes[$type])) {
                continue;
            }

            foreach ((array) $messages as $message) {
                $jsMessage = json_encode($message);
                $jsType = $this->toastTypes[$type];
                $script[] = "toastr.{$jsType}({$jsMessage});";
            }

            $session->removeFlash($type);
        }

        // Imposta le opzioni di Toastr
        $toastrOptions = <<<JS
            toastr.options = {
                'closeButton': true,
                'progressBar': true,
                'positionClass': 'toast-bottom-right',
                'timeOut': '15000',
                'extendedTimeOut': '2000',
                'showMethod': 'fadeIn',
                'hideMethod': 'fadeOut'
            };
        JS;

        // Registra il codice JavaScript con le opzioni
        $this->getView()->registerJs($toastrOptions, \yii\web\View::POS_READY);

        // poi l'alert
        if (!empty($script)) {
            $this->getView()->registerJs(implode("\n", $script), \yii\web\View::POS_READY);
        }

        // Registra l'asset Toastr (pacchetto npm-asset/toastr)
        \jambtc\ToastrWidget\assets\ToastrAsset::register($this->getView());
    }
}
