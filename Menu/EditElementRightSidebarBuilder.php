<?php

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Section Edit contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */

namespace SQLI\EzPlatformAdminUiExtendedBundle\Menu;

use EzSystems\EzPlatformAdminUi\Menu\AbstractBuilder;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;

class EditElementRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__SAVE   = 'edit_element__sidebar_right__save';
    const ITEM__CANCEL = 'edit_element__sidebar_right__cancel';

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return "sqli_ezplatform_admin_ui.menu.edit_element.sidebar_right";
    }

    /**
     * @param array $options
     * @return ItemInterface
     * @throws InvalidArgumentException
     */
    public function createStructure( array $options ): ItemInterface
    {
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem( 'root' );

        $menu->setChildren( [
                                self::ITEM__SAVE   => $this->createMenuItem(
                                    self::ITEM__SAVE,
                                    [
                                        'attributes' => [
                                            'class'      => 'btn--trigger',
                                            'data-click' => sprintf( '#%s', $options['save_button_name'] ),
                                        ],
                                        'extras'     => [ 'icon' => 'save' ],
                                    ]
                                ),
                                self::ITEM__CANCEL => $this->createMenuItem(
                                    self::ITEM__CANCEL,
                                    [
                                        'uri' => $options['cancel_url'],
                                        'extras'     => [ 'icon' => 'circle-close' ],
                                    ]
                                ),
                            ] );

        return $menu;
    }

    /**
     * @return Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            ( new Message( self::ITEM__SAVE, 'sqli_admin' ) )->setDesc( 'Save' ),
            ( new Message( self::ITEM__CANCEL, 'sqli_admin' ) )->setDesc( 'Discard changes' ),
        ];
    }
}