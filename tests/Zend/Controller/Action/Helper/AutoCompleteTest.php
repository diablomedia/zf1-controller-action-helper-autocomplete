<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test class for Zend_Controller_Action_Helper_AutoComplete.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_AutoCompleteTest extends PHPUnit\Framework\TestCase
{
    protected $request;
    protected $response;
    protected $front;
    protected $viewRenderer;
    protected $layout;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp(): void
    {
        Zend_Controller_Action_Helper_AutoCompleteTest_LayoutOverride::resetMvcInstance();
        Zend_Controller_Action_HelperBroker::resetHelpers();
        Zend_Controller_Action_HelperBroker::setPluginLoader(null);

        $this->request  = new Zend_Controller_Request_Http();
        $this->response = new Zend_Controller_Response_Cli();
        $this->front    = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->front->setRequest($this->request)->setResponse($this->response);

        $this->viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $this->layout       = Zend_Layout::startMvc();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown(): void
    {
    }

    public function testScriptaculousHelperThrowsExceptionOnInvalidDataFormat()
    {
        $scriptaculous = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();

        $data      = new stdClass;
        $data->foo = 'bar';
        $data->bar = 'baz';
        try {
            $encoded = $scriptaculous->encodeJson($data);
            $this->fail('Objects should be considered invalid');
        } catch (Zend_Controller_Action_Exception $e) {
            $this->assertStringContainsString('Invalid data', $e->getMessage());
        }
    }

    public function testScriptaculousHelperCreatesHtmlMarkup()
    {
        $scriptaculous               = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $scriptaculous->suppressExit = true;
        $data                        = array('foo', 'bar', 'baz');
        $formatted                   = $scriptaculous->direct($data);
        $this->assertStringContainsString('<ul>', $formatted);
        foreach ($data as $value) {
            $this->assertStringContainsString('<li>' . $value . '</li>', $formatted);
        }
        $this->assertStringContainsString('</ul>', $formatted);
    }

    public function testScriptaculousHelperSendsResponseByDefault()
    {
        $scriptaculous               = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $scriptaculous->suppressExit = true;
        $data                        = array('foo', 'bar', 'baz');
        $encoded                     = $scriptaculous->direct($data);
        $body                        = $this->response->getBody();
        $this->assertSame($encoded, $body);
    }

    public function testScriptaculousHelperDisablesLayoutsAndViewRendererByDefault()
    {
        $scriptaculous               = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $scriptaculous->suppressExit = true;
        $data                        = array('foo', 'bar', 'baz');
        $encoded                     = $scriptaculous->direct($data);
        $this->assertFalse($this->layout->isEnabled());
        $this->assertTrue($this->viewRenderer->getNoRender());
    }

    public function testScriptaculousHelperCanEnableLayoutsAndViewRenderer()
    {
        $scriptaculous               = new Zend_Controller_Action_Helper_AutoCompleteScriptaculous();
        $scriptaculous->suppressExit = true;
        $data                        = array('foo', 'bar', 'baz');
        $encoded                     = $scriptaculous->direct($data, false, true);
        $this->assertTrue($this->layout->isEnabled());
        $this->assertFalse($this->viewRenderer->getNoRender());
    }
}

class Zend_Controller_Action_Helper_AutoCompleteTest_LayoutOverride extends Zend_Layout
{
    public static function resetMvcInstance()
    {
        self::$_mvcInstance = null;
    }
}
