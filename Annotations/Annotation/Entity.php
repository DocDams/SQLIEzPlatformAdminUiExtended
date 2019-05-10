<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @package SQLI\EzPlatformAdminUiExtendedBundle\Annotations
 *
 * @Annotation
 * @Target({"CLASS"})
 */
final class Entity implements SQLIClassAnnotation
{
    /** @var bool */
    public $create = false;
    /** @var bool */
    public $update = false;
    /** @var bool */
    public $delete = false;
    /** @var string */
    public $description = "";

    /**
     * @return bool
     */
    public function isCreate(): bool
    {
        return $this->create;
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->update;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->delete;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}