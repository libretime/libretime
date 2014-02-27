<?

class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $restRoute = new Zend_Rest_Route($front, array(), array(
            'rest'=> array('media')));
        assert($router->addRoute('rest', $restRoute));
    }
}