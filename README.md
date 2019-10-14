
This set offers you the possibility to add a content translation to your symfony website very easily.
# TranslatorBundle installation
1: Add the dependancy to your composer

    composer require jprud67/translator-bundle

2: Register TranslatorBundle in the Symfony kernel

```
<?php

// config/bundle.php

return [
    //...
   Jprud67\TranslatorBundle\Jprud67TranslatorBundle::class => ['all' => true],
];
```

3: Add routes routes

```
# config/routes.yaml

jprud67_translation_routing:
    resource: "@Jprud67TranslatorBundle/Resources/config/routing.yaml"
```
4: Bundle configuration
 
 ````
 # config/packages/jprud67_translator.yaml
 
 doctrine:
     orm:
         mappings:
             Jprud67_translator:
                 is_bundle: false
                 type: annotation
                 dir: '%kernel.project_dir%/Jprud67/translator-bundle/Entity'
                 prefix: 'Jprud67\TranslatorBundle\Entity'
                 alias: Jprud67_translator
 jprud67_translator:
     locales: ['en','es']
 
 ````
 5: Update the database
  
  
     php bin/console doctrine:schema:update --force
 
 6: Install the styles
 
 
     php bin/console asset:install
     
##Using TranslatorBundle
1: Add annotations to your entity


```
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jprud67\TranslatorBundle\Annotation\Translatable;
use Jprud67\TranslatorBundle\Annotation\TranslatableField;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @Translatable()
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=190)
     * @TranslatableField()
     */
    private $title;

    // ...
}
````
2: Go to url mysite.xyz/translation/webui to edit your translations

3: Show your translation in twig
    
     {% for post in posts %}
         <li>{{ jp_trans(post,'title') }}</li>
     {% endfor %}
     

