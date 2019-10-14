<?php
/*
 * This file is part of the Jprud67/TranslatorBundle
 *
 * (c) Prudence Dieudonné ASSOGBA <jprud67@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Jprud67\TranslatorBundle\Util;

use Doctrine\Common\Persistence\ObjectManager;
use Jprud67\TranslatorBundle\Entity\Translation;

class Trans {
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Trans constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function findTranslations($entity) {
        $translations=$this->objectManager->getRepository(Translation::class)->findBy(['objectClass'=>get_class($entity)]);
        return $translations;
    }
}
