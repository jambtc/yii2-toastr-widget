<?php

namespace jambtc\ToastrWidget\assets;

use yii\web\AssetBundle;

class ToastrAsset extends AssetBundle
{
    // toastr 2.1.4 da npm: dichiara compatibilita' con jQuery 3 (la 2.1.3 bower chiede jQuery < 3)
    public $sourcePath = '@npm/toastr/build';
    public $css = ['toastr.min.css'];
    public $js = ['toastr.min.js'];
    // in fondo alla pagina: con POS_HEAD Yii sposterebbe anche jQuery nell'<head> (le chiamate sono in POS_READY)
    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}