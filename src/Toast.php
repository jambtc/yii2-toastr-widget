<?php

namespace jambtc\ToastrWidget;

use jambtc\ToastrWidget\assets\ToastrAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

class Toast extends Widget
{
    /**
     * Opzioni toastr di default (stesso aspetto delle versioni precedenti).
     */
    public const DEFAULT_CLIENT_OPTIONS = [
        'closeButton'     => true,
        'progressBar'     => true,
        'positionClass'   => 'toast-bottom-right',
        'timeOut'         => '15000',
        'extendedTimeOut' => '2000',
        'showMethod'      => 'fadeIn',
        'hideMethod'      => 'fadeOut',
    ];

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

    /**
     * @var bool codifica HTML dei messaggi (toastr li inserisce come HTML: escapeHtml è false di default).
     * Disattivarlo solo per messaggi con HTML voluto e senza dati esterni.
     */
    public bool $encode = true;

    /**
     * @var array opzioni toastr, unite a DEFAULT_CLIENT_OPTIONS (le chiavi indicate qui vincono)
     */
    public array $clientOptions = [];

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
                // solo testo non vuoto: array, oggetti, bool e null diventerebbero "[object Object]" o toast vuoti
                if (!is_string($message) && !is_int($message) && !is_float($message)) {
                    continue;
                }
                $message = (string) $message;
                if (trim($message) === '') {
                    continue;
                }

                $script[] = 'toastr.' . $this->toastTypes[$type] . '(' . $this->encodeMessage($message) . ');';
            }

            $session->removeFlash($type);
        }

        if ($script === []) {
            return '';
        }

        $view = $this->getView();
        ToastrAsset::register($view);

        $options = array_merge(self::DEFAULT_CLIENT_OPTIONS, $this->clientOptions);
        array_unshift($script, 'toastr.options = ' . Json::htmlEncode($options) . ';');
        $view->registerJs(implode("\n", $script), View::POS_READY);

        return '';
    }

    /**
     * Messaggio come stringa JS: UTF-8 leggibile; JSON_HEX_TAG impedisce "</script>" anche con $encode = false.
     */
    private function encodeMessage(string $message): string
    {
        if ($this->encode) {
            $message = Html::encode($message);
        }

        return json_encode(
            $message,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR
        );
    }
}
