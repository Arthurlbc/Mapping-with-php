# Mapping Doctrine avec PHP : Une Alternative aux Annotations et XML

## Introduction

Lorsque l’on travaille avec Doctrine sous PHP/Symfony, on est souvent confronté à la problématique du **mapping des entités**. Les solutions courantes reposent sur les **annotations** ou les fichiers **XML/YAML**, mais elles présentent plusieurs inconvénients :
- Les annotations alourdissent les classes et mêlent logique métier et configuration ORM.
- Le mapping XML/YAML est souvent difficile à maintenir et à tester.

Dans cet article, je vais vous présenter une alternative méconnue mais très pratique : **le mapping Doctrine avec PHP**. Cette approche, bien que nécessitant un léger apprentissage, apporte de nombreux avantages en termes de **lisibilité, flexibilité et testabilité**.

## Pourquoi éviter le mapping par annotations ?

Prenons un exemple classique d'entité avec un mapping par annotations :

```php
/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User {
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private int $id;

    /** @ORM\Column(type="string", length=255) */
    private string $name;
}
```

Bien que cette approche soit simple, elle mélange **logique métier et configuration ORM**, ce qui peut poser des problèmes de lisibilité et de maintenance.

## Le mapping Doctrine avec PHP

Avec cette approche, nous séparons totalement le mapping de l’entité :

```php
class User {
    protected int $id;
    
    public function __construct(protected string $name) {}
    
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
}
```

Et voici le mapping correspondant dans une classe dédiée :

```php
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataBuilder;
use App\Infrastructure\Doctrine\ORM\Entity;

class UserMapping {
    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable("users");

        $builder->createField("id", Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField("name", Types::STRING)
            ->length(255)
            ->nullable(false)
            ->build();
    }
}
```

### ✅ **Avantages de cette approche**

- **Séparation des responsabilités** : le modèle métier reste indépendant de l’ORM.
- **Meilleure lisibilité** et fichiers plus légers.
- **Testabilité accrue** : couvert par **PHPStan**, **php-cs-fixer**, et autres outils d’analyse.
- **Flexibilité** : plus facile à modifier en cas de changement d’ORM.

### ❌ **Inconvénients**

- Nécessite un peu plus de code et d’organisation.
- Moins intuitif au départ pour ceux habitués aux annotations.

## Création de Types Personnalisés avec Doctrine

Doctrine permet aussi de créer des types personnalisés, par exemple pour gérer des **Value Objects**.

### Exemple : un type `Email`

```php
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use MyApp\Domain\ValueObject\Email;

class EmailType extends Type {
    public const NAME = 'email';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return "VARCHAR(255)";
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return new Email($value);
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value instanceof Email ? (string)$value : null;
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

## Gestion des Relations entre Entités

Doctrine facilite la gestion des relations entre entités, mais leur utilisation abusive peut alourdir les requêtes et rendre le projet moins scalable.

Plutôt que de lier directement les entités avec `ManyToOne` ou `ManyToMany`, nous pouvons stocker les **identifiants** des entités liées sous forme de JSON et les récupérer uniquement lorsque c'est nécessaire.

### Exemple d’implémentation

```php
class Order {
    private array $productIds = [];
    
    public function addProduct(int $productId) {
        $this->productIds[] = $productId;
    }
    
    public function getProducts(): array {
        return $this->productIds;
    }
}
```

**Pourquoi ?**
- **Moins de requêtes SQL complexes**.
- **Moins de couplage** entre les entités.
- **Plus grande flexibilité** dans l’évolution du modèle de données.

## Conclusion

L’utilisation du **mapping Doctrine avec PHP** permet de mieux structurer son code en séparant la logique métier de l’ORM. Associé à une architecture hexagonale et une gestion réfléchie des relations entre entités, cela garantit **une meilleure maintenabilité et une évolutivité accrue**.

Cependant, cette approche demande un léger effort d’apprentissage et une bonne organisation du projet. Adoptez-la en fonction des besoins spécifiques de votre application ! 🚀

