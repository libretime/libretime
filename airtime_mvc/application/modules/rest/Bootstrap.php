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

        $downloadRoute = new Zend_Controller_Router_Route(
            'rest/media/:id/download',
            array(
                'controller' => 'media',
                'action' => 'download',
                'module' => 'rest'
            ),
            array(
                'id' => '\d+'
            )
        );
        $router->addRoute('download', $downloadRoute);

        $clearLibraryRoute = new Zend_Controller_Router_Route(
            'rest/media/clear',
            array(
                'controller' => 'media',
                'action' => 'clear',
                'module' => 'rest'
            )
        );
        $router->addRoute('clear', $clearLibraryRoute);
    }
}
