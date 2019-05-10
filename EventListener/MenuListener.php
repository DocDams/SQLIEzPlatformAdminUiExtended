<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\EventListener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuListener implements EventSubscriberInterface
{
    const SQLI_ADMIN_MENU_ROOT = "sqli_admin__menu_root";
    const SQLI_ADMIN_MENU_ENTITIES = "sqli_admin__menu_entities";
    const SQLI_ADMIN_MENU_PARAMETERS = "sqli_admin__menu_parameters";

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents()
    {
        return array(ConfigureMenuEvent::MAIN_MENU => 'onMainMenuBuild');
    }

    public function onMainMenuBuild(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::SQLI_ADMIN_MENU_ROOT,
            [
                'label' => 'SQLI Admin',
            ]
        );

        if( $this->authorizationChecker->isGranted( 'ez:sqli_admin:list_entities' ) )
        {
            $menu[self::SQLI_ADMIN_MENU_ROOT]->addChild(
                self::SQLI_ADMIN_MENU_ENTITIES,
                [
                    'label' => 'Entités',
                    'route' => 'sqli_ez_platform_admin_ui_extended_entities_homepage',
                ]
            );
        }
    }
}