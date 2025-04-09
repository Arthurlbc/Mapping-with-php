# Mapping Doctrine avec PHP
![PHP_Doctrine](../apps/back/public/images/logos_php_dotrine.png)

Développeur chez **KnpLabs**, j’aimerais aujourd’hui vous parler de **mapping**. Cet article fait suite à un projet en PHP/Symfony où nous avons choisi **Doctrine** comme ORM (Object Relational Mapper).

Nous avons tous déjà été confrontés à la problématique d’entités surchargées d’annotations, que nous avons résolue en déplaçant ce mapping dans du XML, parfois mal formaté et jamais testé.

Cet article aborde une alternative :

**Méconnu mais pourtant si pratique**, plein d’avantages, mais avec certains prérequis, le mapping Doctrine avec PHP est une technique qui m’a personnellement beaucoup plu. C’est dans le cadre d’un projet **from scratch**, avec un découpage du code en oignon permettant une séparation claire des responsabilités en trois couches distinctes, que nous avons choisi d’utiliser le mapping Doctrine avec PHP.

Pour plus d’informations, je vous invite à lire cet article : [Architecture hexagonale avec Symfony](https://knplabs.com/fr/blog/architecture-hexagonale-comment-ladopter/).

Mais quelle que soit l'architecture que vous choisissez pour votre projet, le mapping avec PHP est possible et permettra d'alléger vos entités, dans le cas où vous utilisiez les annotations Doctrine, et surtout de séparer votre modèle de données de leur implémentation avec l'ORM sans pour autant utiliser des fichiers XML.

## Exemple : Mapping directement dans le modèle

En plus de la présence de l’ORM dans une classe représentant un modèle métier, c’est déjà chargé pour une classe pourtant simple !

```php
/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User {
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private int $id;

    public function __construct(
        /** @ORM\Column(type="string", length=255) */
        private string $name
    ){}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

Même classe sans annotations (c'est déjà plus léger) :  
```php
class User {
    protected int $id;
    
    public function __construct(protected string $name) {}
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
}
```

Et son mapping dans l’infrastructure :  

```php
class UserMapping extends User
{
    public static function loadMetadata(ClassMetadata $metadata) {

        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable("users");

        $builder->createField("id", Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->nullable(false)
            ->build();

        $builder->createField("name", Types::STRING)
            ->length(255)
            ->nullable(false)
            ->build();
    }
}
```
→ **plus de lisibilité et séparation des responsabilités !**

## Avantages de cette approche :

- Sépare le modèle du domaine (classe **PHP** "pure") de son mapping : Pas de dépendances dans la logique métier.
- Cela permet une implémentation plus concise et logique.
- Pas de troisième fichier de mapping, ce qui simplifie la structure du projet.
- Simplicité de définition de types personnalisés (comme pour les value objects) -> et **Testable !** avec phpspec par exemple.
- Couvert par les différents outils (**php-cs-fixer**, **PHPStan**...).

## Inconvénients :

- Changement d'habitude -> Nécessite une prise en main
- La verbosité à première vue, mais qui garantit une totale maîtrise du mapping.

Comme vu sur les exemples, nous pouvons créer une entité qui étend notre modèle. Ensuite, **Doctrine** nous fournit tous les outils nécessaires pour créer notre mapping. Je vous invite à consulter la classe **[ClassMetadata](https://github.com/andreia/doctrine/blob/master/lib/Doctrine/ORM/Mapping/ClassMetadata.php)** de Doctrine et la classe **[ClassMetadataBuilder](https://github.com/webmozart/doctrine-orm/blob/master/lib/Doctrine/ORM/Mapping/Builder/ClassMetadataBuilder.php)**. L’une permet de charger votre entité et l’autre de construire votre mapping.

## Création de Types Personnalisés avec Doctrine

Il est possible de créer des types personnalisés en complément des types fournis par Doctrine. Pour cela, il faut :

- Créer une classe qui étend **Type** de Doctrine.
- Redéfinir les méthodes **convertToDatabaseValue()** et **convertToPHPValue()**.

Chaque type personnalisé doit également définir une constante pour le nom de la colonne et implémenter la méthode **getName()** qui retourne cette valeur.

## Exemple
```php
class EmailType extends Type {
    public const NAME = 'email';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return "VARCHAR(255)";
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return new Email($value);
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value instanceof Email ? (string) $value : null;
    }
    
    public function getName() {
        return self::NAME;
    }
}
```

Cela permet d’intégrer proprement un `Email` en tant qu’objet de valeur au sein des entités.
```php
        $builder->createField("email", EmailType::NAME)
            ->nullable(false)
            ->build();
```

Il est tout de même préférable d’abstraire cette logique via des services personnalisés qui seront la seule partie du code à modifier en cas de changement d'ORM.
Et pour maintenir un faible couplage entre les dépendances et notre code, nous pouvons définir une classe abstraite qui étend **Type** et qui déclare des méthodes **normalize()** et **denormalize()**. Ces méthodes seront redéfinies dans chaque sous-classe en fonction des besoins spécifiques, comme dans l'exemple ci-dessous :

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @template ValueObject of mixed
 * @template Normalization of mixed
 */
abstract class ValueObjectType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * @param ?ValueObject $value
     *
     * @return ?Normalization
     */
    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        if (null === $value) {
            return null;
        }

        return $this->normalize($value);
    }

    /**
     * @param ?Normalization $value
     *
     * @return ?ValueObject
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        if (null === $value) {
            return null;
        }

        return $this->denormalize($value);
    }

    /**
     * @param ValueObject $valueObject
     *
     * @return Normalization
     */
    abstract protected function normalize(mixed $valueObject): mixed;

    /**
     * @param Normalization $normalized
     *
     * @return ValueObject
     */
    abstract protected function denormalize(mixed $normalized): mixed;
}
```

## Gestion des Relations entre Entités

Les relations **One-to-Many** et **Many-to-One** sont bien sûr possibles avec le mapping en PHP, mais elles peuvent rapidement rendre complexe un projet à grande échelle. Il est possible d’éviter ces liens directs entre entités dans le cas de projets amenés à évoluer. Pour des petits projets, la problématique n'est pas la même, et la rapidité de création sera souvent privilégiée

Pour des projets de moyenne à grande envergure, mettre en place ces relations au début du projet peut vite aboutir à des requêtes très complexes, qui récupèrent des données parfois inutiles.

À la place, j’ai choisi de stocker sous forme de **JSON** en base de données et d'array côté **PHP** les identifiants des modèles liés à mon entité. Ensuite, à l’aide des méthodes **add()**, **remove()**, **has()** et **get()**, je peux interagir avec ces identifiants et les récupérer au besoin. Mes **UseCase** étant encapsulés dans une transaction Doctrine via le middleware **doctrine_transaction**, je ne manipule ainsi que des IDs et fetch uniquement les objets requis par mon cas d’utilisation.

```php
class Order {
    private array $productIds = [];
    
    public function addProduct(int $productId) {
        $this->productIds[] = $productId;
    }
    
    public function getProducts(): array {
        return $this->productIds;
    }

       public function hasProduct(string $productId): bool
    {
        return in_array($productId, $this->productIds);
    }

    public function removeProduct(string $productIdToRemove): void
    {
        if ($this->hasProduct($productIdToRemove)) {
            $this->productIds = array_filter($this->productIds, fn ($productId) => $productId !== $productIdToRemove);
        }
    }
}
```

Cette approche permet :

- Une plus grande flexibilité dans la gestion des relations.
- Une simplification de la gestion des dépendances entre entités.
- Une meilleure évolutivité en cas de changements dans le modèle de données.

## Conclusion

Merci de votre attention, c’est la fin de cet article sur le mapping doctrine avec PHP. Comme nous avons pu le voir cette méthode apporte avantages et inconvénients, et comme tout en développement ne doit être utilisé qu'après réflexion sur le besoin de l'application.
Dans le cadre d'une application avec un métier complexe, elle trouve tout son intérêt, pour sa maintenabilité, sa séparation du code métier et sa fluidité une fois les mécanismes en place.

Pour un exemple complet, consultez ce dépôt GitHub : [Mapping Doctrine avec PHP](https://github.com/Arthurlbc/Mapping-with-php).

