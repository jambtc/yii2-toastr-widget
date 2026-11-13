<?php

namespace jambtc\ToastrWidget\tests;

use yii\web\Session;

/**
 * Sessione in memoria per i test: i flash di Yii lavorano su $_SESSION senza avviare la sessione PHP.
 */
class ArraySession extends Session
{
    public function open()
    {
        $_SESSION ??= [];
    }

    public function close()
    {
    }

    public function getIsActive()
    {
        return true;
    }
}
