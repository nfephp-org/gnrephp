<?php

namespace Sped\Gnre\Test\Render;

use Sped\Gnre\Render\Html;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Sped\Gnre\Render\Html
 */
class HtmlTest extends TestCase
{

    public function testDeveRetornarUmInstanciaDoBarCode()
    {
        $html = new Html();
        $this->assertInstanceOf('\Sped\Gnre\Render\Barcode128', $html->getBarCode());
    }

    public function testDeveRetornarUmaInstanciaDoSmartyFactory()
    {
        $html = new Html();
        $this->assertInstanceOf('\Sped\Gnre\Render\SmartyFactory', $html->getSmartyFactory());
    }

    public function testDeveDefinirUmObjetoDeCodigoDeBarrasParaSerUtilizado()
    {
        $barCode = new \Sped\Gnre\Render\Barcode128();
        $html = new Html();

        $this->assertInstanceOf('\Sped\Gnre\Render\Html', $html->setBarCode($barCode));
        $this->assertSame($barCode, $html->getBarCode());
    }

    public function testDeveRetornarNullSeNaoForCriadoOhtmlDaGuia()
    {
        $html = new \Sped\Gnre\Render\Html();
        $this->assertEmpty($html->getHtml());
    }

    public function testNaoDeveGerarOhtmlDoLoteQuandoOloteEvazio()
    {
        $html = new Html();
        $mkcLote = $this->createMock('\Sped\Gnre\Sefaz\Lote');
        $mkcLote->expects($this->once())
                ->method('getGuias');
        $mkcLote->expects($this->never())
                ->method('getGuia');

        $html->create($mkcLote);

        $this->assertEmpty($html->getHtml());
    }

    public function testDeveGerarOhtmlDoLoteQuandoPossuirGuias()
    {
        $smarty = $this->createMock('\Smarty');
        $smarty->expects($this->at(0))
                ->method('assign')
                ->with('guiaViaInfo');
        $smarty->expects($this->at(1))
                ->method('assign')
                ->with('barcode');
        $smarty->expects($this->at(2))
                ->method('assign')
                ->with('guia');
        $smarty->expects($this->at(3))
                ->method('fetch')
                ->will($this->returnValue('<html></html>'));

        $smartyFactory = $this->createMock('\Sped\Gnre\Render\SmartyFactory');
        $smartyFactory->expects($this->once())
                ->method('create')
                ->will($this->returnValue($smarty));

        $html = new Html();
        $html->setSmartyFactory($smartyFactory);

        $lote = new \Sped\Gnre\Sefaz\Lote();
        $lote->addGuia(new \Sped\Gnre\Sefaz\Guia());

        $html->create($lote);

        $this->assertNotEmpty($html->getHtml());
    }
}
