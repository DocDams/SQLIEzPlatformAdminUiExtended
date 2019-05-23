<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @package SQLI\EzPlatformAdminUiExtendedBundle\Annotations
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class EntityProperty implements SQLIPropertyAnnotation
{
    /** @var bool */
    public $visible = true;
    /** @var bool */
    public $readonly = false;
    /** @var string */
    public $description = "";

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}