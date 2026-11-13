<?php

namespace jambtc\ToastrWidget\tests;

use jambtc\ToastrWidget\assets\ToastrAsset;
use jambtc\ToastrWidget\Toast;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\helpers\FileHelper;
use yii\web\Application;
use yii\web\JqueryAsset;
use yii\web\View;

class ToastTest extends TestCase
{
    private string $assetDir;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->assetDir = sys_get_temp_dir() . '/toastr-widget-test-' . uniqid();
        FileHelper::createDirectory($this->assetDir);

        new Application([
            'id' => 'toastr-widget-test',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'test',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
                'session' => ArraySession::class,
                'assetManager' => [
                    'basePath' => $this->assetDir,
                    'baseUrl' => '/assets',
                ],
            ],
        ]);
    }

    protected function tearDown(): void
    {
        FileHelper::removeDirectory($this->assetDir);
        Yii::$app = null;
        $_SESSION = [];
    }

    private function render(array $config = []): View
    {
        $view = new View();
        Toast::widget(['view' => $view] + $config);

        return $view;
    }

    private function js(View $view): string
    {
        return implode("\n", $view->js[View::POS_READY] ?? []);
    }

    public function testMessagesAreHtmlEncodedByDefault(): void
    {
        Yii::$app->session->setFlash('error', '<img src=x onerror=alert(1)> & "ciao"');

        $js = $this->js($this->render());

        $this->assertStringContainsString('toastr.error("&lt;img src=x onerror=alert(1)&gt; &amp; &quot;ciao&quot;");', $js);
        $this->assertStringNotContainsString('<img', $js);
    }

    public function testEncodeCanBeDisabledForExplicitHtml(): void
    {
        Yii::$app->session->setFlash('info', '<b>Salvato</b></script><script>alert(1)</script>');

        $js = $this->js($this->render(['encode' => false]));

        $this->assertStringContainsString('toastr.info("\u003Cb\u003ESalvato\u003C/b\u003E', $js);
        $this->assertStringNotContainsString('</script>', $js, 'JSON_HEX_TAG neutralizza la chiusura del tag script');
        $this->assertStringNotContainsString('<b>', $js);
    }

    public function testUtf8StaysReadable(): void
    {
        Yii::$app->session->setFlash('success', 'Già salvato: città, perché €');

        $js = $this->js($this->render());

        $this->assertStringContainsString('toastr.success("Già salvato: città, perché €");', $js);
    }

    public function testInvalidUtf8DoesNotThrow(): void
    {
        Yii::$app->session->setFlash('warning', "ok \xC3\x28");

        $js = $this->js($this->render(['encode' => false]));

        $this->assertStringContainsString('toastr.warning("ok ', $js);
    }

    public function testEmptyAndNonTextMessagesAreSkipped(): void
    {
        Yii::$app->session->setFlash('error', ['', '   ', null, true, false, ['nested'], new \stdClass()]);
        Yii::$app->session->setFlash('info', '');

        $view = $this->render();

        $this->assertSame([], $view->js);
        $this->assertArrayNotHasKey(ToastrAsset::class, $view->assetBundles);
        $this->assertFalse(Yii::$app->session->hasFlash('error'), 'flash consumato anche se scartato');
    }

    public function testMultipleMessagesAndNumbers(): void
    {
        Yii::$app->session->setFlash('success', ['Primo', 42, 'Secondo']);

        $js = $this->js($this->render());

        $this->assertStringContainsString("toastr.success(\"Primo\");\ntoastr.success(\"42\");\ntoastr.success(\"Secondo\");", $js);
    }

    public function testDangerBecomesErrorAndUnknownTypesAreKept(): void
    {
        Yii::$app->session->setFlash('danger', 'Errore');
        Yii::$app->session->setFlash('custom', 'Altro');

        $js = $this->js($this->render());

        $this->assertStringContainsString('toastr.error("Errore");', $js);
        $this->assertStringNotContainsString('toastr.danger', $js);
        $this->assertStringNotContainsString('Altro', $js);
        $this->assertFalse(Yii::$app->session->hasFlash('danger'));
        $this->assertTrue(Yii::$app->session->hasFlash('custom'));
    }

    public function testDefaultOptionsAreUnchanged(): void
    {
        Yii::$app->session->setFlash('info', 'Ciao');

        $js = $this->js($this->render());

        $this->assertStringStartsWith(
            'toastr.options = {"closeButton":true,"progressBar":true,"positionClass":"toast-bottom-right",'
            . '"timeOut":"15000","extendedTimeOut":"2000","showMethod":"fadeIn","hideMethod":"fadeOut"};',
            $js
        );
    }

    public function testClientOptionsAreMergedWithDefaults(): void
    {
        Yii::$app->session->setFlash('info', 'Ciao');

        $js = $this->js($this->render(['clientOptions' => ['positionClass' => 'toast-top-center', 'timeOut' => 5000]]));

        $this->assertStringContainsString('"positionClass":"toast-top-center"', $js);
        $this->assertStringContainsString('"timeOut":5000', $js);
        $this->assertStringContainsString('"closeButton":true', $js);
        $this->assertStringNotContainsString('toast-bottom-right', $js);
    }

    public function testClientOptionsAreSafeInsideScript(): void
    {
        Yii::$app->session->setFlash('info', 'Ciao');

        $js = $this->js($this->render(['clientOptions' => ['closeHtml' => '<button></script></button>']]));

        $this->assertStringNotContainsString('</script>', $js);
    }

    public function testNothingIsRegisteredWithoutMessages(): void
    {
        $view = $this->render();

        $this->assertSame([], $view->js);
        $this->assertSame([], $view->assetBundles);
    }

    public function testAssetIsRegisteredAtEndOfBody(): void
    {
        Yii::$app->session->setFlash('success', 'Ok');

        $view = $this->render();

        $this->assertArrayHasKey(ToastrAsset::class, $view->assetBundles);
        $this->assertSame(View::POS_END, $view->assetBundles[ToastrAsset::class]->jsOptions['position']);
        $this->assertSame(
            View::POS_END,
            $view->assetBundles[JqueryAsset::class]->jsOptions['position'] ?? View::POS_END,
            'jQuery non forzato in <head>'
        );
    }
}
