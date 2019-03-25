<?php
namespace Riata\ExampleBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\Container;

class ShowConnectionEventSubscriber implements EventSubscriberInterface
{
    protected $container;

    public function __construct (Container $container)
    {
        $this->container = $container;
    }
    public static function getSubscribedEvents() 
    {
        return array(
            KernelEvents::REQUEST => array(
                array('showConnectionResponse', 100)
            )
        );
    }

    public function showConnectionResponse(GetResponseEvent $event) 
    {
        $this->changeDatabaseNameTo('symfony');
    }

    public function changeDatabaseNameTo($databaseName) 
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $connection = $em->getConnection();
        $refConn = new \ReflectionObject($connection);
        $refParams = $refConn->getProperty('_params');
        $refParams->setAccessible('public'); //we have to change it for a moment

        $params = $refParams->getValue($connection);
        $params['dbname'] = $databaseName;

        $refParams->setAccessible('private');
        $refParams->setValue($connection, $params);
    }
}
