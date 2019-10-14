<?php
/*
 *
 * (c) Prudence D. ASSOGBA <jprud67@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Jprud67\TranslatorBundle\Annotation\Reader;



use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\ObjectManager;
use Jprud67\TranslatorBundle\Annotation\Translatable;
use Jprud67\TranslatorBundle\Annotation\TranslatableField;


class TranslatableAnnotationReader
{
    /**
     * @var AnnotationReader
     */
    private $reader;


    /**
     * TranslatableAnnotationReader constructor.
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param $entity string
     * @return bool
     * @throws \ReflectionException
     */
    public function isTranslatable($entity){
        $reflection=new \ReflectionClass($entity);
        return $this->reader->getClassAnnotation($reflection,Translatable::class) != null;
    }


    /**
     * @param $entity string
     * @return array
     * @throws \ReflectionException
     */
    public function getTranslatableField($entity){
        if(!$this->isTranslatable($entity)){
            return [];
        }
        $reflection=new \ReflectionClass($entity);
        $properties=[];
        foreach ($reflection->getProperties() as $property) {
            $annotation=$this->reader->getPropertyAnnotation($property,TranslatableField::class);
            if($annotation !== null){
                $properties[$property->getName()]=$annotation;
            }
        }
        return $properties;
    }
}