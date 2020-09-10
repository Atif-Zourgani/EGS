<?php

namespace App\Event;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class ChangingPasswordEvent implements EventSubscriberInterface
{
    private $router;
    private $container;
    private $security;

    public function __construct(UrlGeneratorInterface $router, ContainerInterface $container, Security $security)
    {
        $this->router = $router;
        $this->container = $container;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onPasswordChangingSuccess',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function onPasswordChangingSuccess(FormEvent $event)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $this->security->getUser();
        $user->setPasswordChangedAt(new \DateTime('Europe/Paris'));
        $userManager->updateUser($user);

        $url = $this->router->generate('home');
        $event->setResponse(new RedirectResponse($url));
    }
}