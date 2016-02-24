<?php
namespace Album;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Album\Model\Album;
use Album\Model\AlbumTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }
    
    /**
     * In order to always use the same instance of our AlbumTable, we will use the ServiceManager to define how to create one
     * 
     * This is most easily done in the Module class where we create a method called getServiceConfig() which is automatically called by the ModuleManager and applied to the ServiceManager
     * 
     * We’ll then be able to retrieve it in our controller when we need it.
     * 
     * To configure the ServiceManager, we can either supply the name of the class to be instantiated or a factory (closure or callback) that instantiates the object when the ServiceManager needs it. 
     * 
     * We start by implementing getServiceConfig() to provide a factory that creates an AlbumTable
     * 
     * This method returns an array of factories that are all merged together by the ModuleManager before passing them to the ServiceManager. 
     * 
     * The factory for Album\Model\AlbumTable uses the ServiceManager to create an AlbumTableGateway to pass to the AlbumTable. 
     * 
     * We also tell the ServiceManager that an AlbumTableGateway is created by getting a Zend\Db\Adapter\Adapter (also from the ServiceManager) and using it to create a TableGateway object. 
     * 
     * The TableGateway is told to use an Album object whenever it creates a new result row. 
     * 
     * The TableGateway classes use the prototype pattern for creation of result sets and entities. 
     * 
     * This means that instead of instantiating when required, the system clones a previously instantiated object.
     * 
     * @return array factories
     */
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Album\Model\AlbumTable' =>  function($sm) {
                    $tableGateway = $sm->get('AlbumTableGateway');
                    $table = new AlbumTable($tableGateway);
                    return $table;
                },
                'AlbumTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Album());
                    return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
     
    }
}
