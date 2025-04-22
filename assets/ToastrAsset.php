<?php

namespace jambtc\ToastrWidget\assets;

use yii\web\AssetBundle;

class ToastrAsset extends AssetBundle
{
    public $sourcePath = '@bower/toastr'; // Assumendo che venga caricato da bower
    public $css = ['toastr.min.css'];
    public $js = ['toastr.min.js'];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD, // <-- questa è la chiave
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset', // o Bootstrap5, in base a cosa usi
    ];
}