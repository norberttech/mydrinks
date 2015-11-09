<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\EventListener;

use MyDrinks\Domain\Exception\Recipe\StepException;
use MyDrinks\UserInterface\Symfony\AppBundle\Controller\AdminRecipeController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class RecipeExceptionListener 
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }
    
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof StepException) {
            return ;
        }
        
        $reflection = new \ReflectionClass(get_class($exception));

        $event->getRequest()->getSession()->getBag('flashes')->add(
            'error',
            $this->translator->trans('error.recipe.' . lcfirst($reflection->getShortName()))
        );
        
        $targetUrl = $event->getRequest()->getSession()->get(
            AdminRecipeController::EXCEPTION_TARGET_URL_KEY,
            $event->getRequest()->getUri()
        );
        
        $event->setResponse(new RedirectResponse($targetUrl));
    }
}