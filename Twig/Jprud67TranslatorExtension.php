<?php

namespace Jprud67\TranslatorBundle\Twig;

use Doctrine\Common\Persistence\ObjectManager;
use Jprud67\TranslatorBundle\Entity\Translation;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Jprud67TranslatorExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Jprud67TranslatorExtension constructor.
     * @param RequestStack $requestStack
     * @param ObjectManager $objectManager
     */
    public function __construct(RequestStack $requestStack,ObjectManager $objectManager)
    {
        $this->requestStack = $requestStack;
        $this->objectManager = $objectManager;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('jp_trans', [$this, 'jprud67_trans']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('jp_trans', [$this, 'jprud67_trans']),
        ];
    }

    public function jprud67_trans($entity,$field)
    {
        $reflection=new \ReflectionClass($entity);
        $name_array=explode("\\",$reflection->getName());
        $objectClass=$reflection->getName();
        if ($name_array[0]=="Proxies"){
            $name_array=array_slice($name_array,"2");
            $objectClass=implode('\\', $name_array);
        }
        $locale=$this->requestStack->getCurrentRequest()->getLocale();
        $translation=$this->objectManager->getRepository(Translation::class)->findOneBy([
            "objectClass"=>$objectClass,
            "locale"=>$locale,
            "field"=>$field,
            "foreignKey"=>$entity->getId()
        ]);


        $method="get".ucfirst($field);

       return $translation?$translation->getContent():$entity->$method();
    }
}
