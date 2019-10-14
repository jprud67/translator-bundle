<?php
/*
 * This file is part of the Jprud67/TranslatorBundle
 *
 * (c) Prudence Dieudonné ASSOGBA <jprud67@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jprud67\TranslatorBundle\Controller;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Column;
use Jprud67\TranslatorBundle\Annotation\Reader\TranslatableAnnotationReader;
use Jprud67\TranslatorBundle\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TranslationController
 */
class TranslationController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var TranslatableAnnotationReader
     */
    private $translatableAnnotationReader;
    /**
     * @var array
     */
    private $locales;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * TranslationController constructor.
     * @param ObjectManager $objectManager
     * @param TranslatableAnnotationReader $translatableAnnotationReader
     * @param TranslatorInterface $translator
     * @param AnnotationReader $reader
     * @param array $locales
     */
    public function __construct(
        ObjectManager $objectManager,
        TranslatableAnnotationReader $translatableAnnotationReader,
        TranslatorInterface $translator,
        AnnotationReader $reader,
        $locales=array()
    ){

        $this->objectManager = $objectManager;
        $this->translatableAnnotationReader = $translatableAnnotationReader;
        $this->locales = $locales;
        $this->translator = $translator;
        $this->reader = $reader;
    }

    /**
     * @Route("/webui", name="jprud67_translation_webui",methods={"GET"})
     * @return Response
     * @throws \ReflectionException
     */
    public function jprud67_translation_webui()
    {
        //dd($this->getMetaDataArray());
       return $this->render("@Jprud67Translator/translation/translation_webui.html.twig",[
        "metaDataArray"=>$this->getMetaDataArray(),
        "locales"=>$this->locales
       ]);

    }



    /**
     * @Route("/trans_save",name="jprud67_translation_save",methods={"POST","GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function jprud67_translation_save(Request $request){
        $field=$request->request->get("field");
        $locale=$request->request->get("locale");
        $class=$request->request->get("class");
        $content=$request->request->get("content");
        $foreignKey=$request->request->get("foreignKey");
        if ($field&&$locale&&$class&&$foreignKey){
            $find_trans=$this->objectManager->getRepository(Translation::class)->findOneBy([
                "field"=>$field,
                "objectClass"=>$class,
                "locale"=>$locale,
                "foreignKey"=>$foreignKey
                ]);
            if ($find_trans){
                $find_trans->setContent($content);
                $this->objectManager->flush();
                return $this->json($this->translator->trans("update_trans",[],"jprud67translator"),200);
            }else{
                $new_trans=new Translation();
                $new_trans->setField($field);
                $new_trans->setLocale($locale);
                $new_trans->setObjectClass($class);
                $new_trans->setForeignKey($foreignKey);
                $new_trans->setContent($content);
                $this->objectManager->persist($new_trans);
                $this->objectManager->flush();
                return $this->json($this->translator->trans("add_trans",[],"jprud67translator"),200);
            }
        }
        return $this->json(false,200);
    }

    /**
     * @Route("/trans_load",name="jprud67_translation_load",methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function jprud67_translation_load(){

        $translations=$this->objectManager->getRepository(Translation::class)->findAll();
        return $this->json(["trans"=>$translations]);
    }

    /**
     * @throws \ReflectionException
     */
    private function getMetaDataArray(){
        $metaData=$this->objectManager->getMetadataFactory()->getAllMetadata();
        $metaDataArray=[];
        $count=0;
        foreach ($metaData as $classMetaData) {

            $class=$classMetaData->getName();


            if ($this->translatableAnnotationReader->isTranslatable($class)){

                $fields=$this->translatableAnnotationReader->getTranslatableField($class);
                $name_array=explode("\\",$class);
                $className=end($name_array);
                $metaDataArray[$count]=array(
                    "class"=>$class,
                    "className"=>$className,
                );
                if ($fields){
                    $entities=$this->objectManager->getRepository($class)->findAll();
                    foreach ($fields as $field=>$value) {

                        foreach ($classMetaData->getReflectionClass()->getProperties() as $property) {
                            if ($property->getName()==$field){
                                $annotation_colum=$this->reader->getPropertyAnnotation($property,Column::class);
                               $type= $annotation_colum->type;
                            }
                        }
                        foreach ($entities as $entity) {
                            $method="get".ucfirst($field);
                            $metaDataArray[$count]["fields"][$field][]=array(
                                "content"=>$entity->$method(),
                                "foreignKey"=>$entity->getId(),
                                "type"=>$type?$type:null
                            );
                        }
                    }

                    $count++;
                }

            }
        }
        return $metaDataArray;
    }

}