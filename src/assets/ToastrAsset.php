<?php

namespace jambtc\ToastrWidget\assets;

use yii\web\AssetBundle;

class ToastrAsset extends AssetBundle
{
    // toastr 2.1.4 da npm: dichiara compatibilita' con jQuery 3 (la 2.1.3 bower chiede jQuery < 3)
    public $sourcePath = '@npm/toastr/build';
    public $css = ['toastr.min.css'];
    public $js = ['toastr.min.js'];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD, // <-- questa è la chiave
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}