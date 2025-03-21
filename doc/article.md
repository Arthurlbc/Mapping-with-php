# Mapping Doctrine avec PHP

Développeur chez KnpLabs, j’aimerais aujourd’hui vous parler de **mapping**. Cet article fait suite à un projet en PHP/Symfony où nous avons choisi **Doctrine** comme ORM.

Nous avons tous déjà rencontré la problématique d’entités surchargées d’annotations, que nous avons résolue en déplaçant ce mapping dans du XML, parfois mal formaté et jamais testé.

J’aimerais vous présenter une autre alternative :

**Méconnu mais pourtant si pratique**, plein d’avantages, mais ayant certains prérequis, le mapping Doctrine avec PHP est un outil qui m’a personnellement beaucoup plu. C’est dans le cadre d’un projet **from scratch**, avec un découpage en oignon permettant une séparation claire des responsabilités en trois couches distinctes, que nous avons choisi d’utiliser le mapping Doctrine avec PHP.

L’architecture choisie a pour but de séparer la partie métier pure des dépendances et interactions que peut avoir l’application. Ainsi, notre projet n’est pas fortement dépendant des modifications (non-maintenance ou suppression) des différentes librairies utilisées.

Pour plus d’informations, je vous invite à lire cet article : [Architecture hexagonal](https://TODO).

Il est déconseillé d’utiliser directement un modèle comme entité Doctrine, car cela ne respecte pas les standards de l’architecture hexagonale en mélangeant logique métier et configuration ORM, en plus d’alourdir les fichiers, ce qui les rend illisibles.

## Exemple : Mapping directement dans le modèle

En plus de la présence de l’ORM dans le domaine, c’est déjà chargé pour une classe pourtant simple !

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

Même classe représentant notre modèle du domaine :  
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
            ->build();

        $builder->createField("name", Types::STRING)
            ->length(255)
            ->nullable(false)
            ->build();
    }
}
```
→ **plus de lisibilité et séparation des responsabilités !**

## Avantages de notre approche :

- Sépare le modèle du domaine (classe **PHP** "pure") de son son mapping dans l’infrastructure: Pas de logique de dépendances dans notre logique métier.
- Cela permet d’avoir une implémentation plus concise et logique.
- Nous évitons aussi la nécessité d’un troisième fichier dédié au mapping, ce qui simplifie la structure du projet.
- **Testable !**
- Couvert par les différents outils (**php-cs-fixer**, **PHPStan**...).

## Inconvénients :

- La prise en main.
- Cela peut paraître verbeux à première vue.

Comme vu sur les écrans, nous pouvons créer une entité qui étend notre modèle. Ensuite, **Doctrine** nous fournit tous les outils nécessaires pour créer notre mapping. Je conseille tout de même d’abstraire cette logique via des services personnalisé qui seront la seul partie du code a modifier en cas de changement d'ORM. Je vous invite à consulter la classe **ClassMetaData** de Doctrine et la classe **ClassMetaDataBuilder**. L’une permet de charger votre entité et l’autre de construire votre mapping.

## Création de Types Personnalisés avec Doctrine

Il est possible de créer des types personnalisés en complément des types fournis par Doctrine. Pour cela, il faut :

- Créer une classe qui étend **Type** de Doctrine.
- Redéfinir les méthodes **convertToDatabaseValue()** et **convertToPHPValue()**.

Pour maintenir un faible couplage entre les dépendances et notre code, nous pouvons aussi définir une classe abstraite qui étend **Type** et qui déclare des méthodes **normalize()** et **denormalize()**. Ces méthodes seront redéfinies dans chaque sous-classe en fonction des besoins spécifiques.

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

## Gestion des Relations entre Entités

Les relations **Many-to-Many** et **Many-to-One** sont bien sûr possibles avec le mapping en PHP, mais elles peuvent rapidement complexifier un projet à grande échelle. De mon point de vue, il est préférable d’éviter ces liens directs entre entités dans le cas de projet amener a évoluer et évoluer. Pour des petits projets la problèmatique n'est pas la même et souvent la rapidité de création sera a privilegié.

Pour des projets de moyenne à grande envergure, mettre en place ces relations au début du projet peut vite aboutir à des requêtes très complexes, fetchant des données parfois non utiles.

À la place, j’ai choisi de stocker sous forme de **JSON** en base de données et d'array côté **PHP** les identifiants des modèles liés à mon entité. Ensuite, à l’aide de méthodes **add()**, **remove()**, **has()** et **get()**, je peux interagir avec ces identifiants et les récupérer au besoin. Mes **UseCase** étant wrapés dans une transaction Doctrine via le middleware **doctrine_transaction**, je ne manipule ainsi que des ids et fetch uniquement les objets requis par mon cas d’utilisation.

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

L’adoption de l’architecture hexagonale et du découpage en oignon, couplée à Doctrine, nous a permis d’organiser notre code de manière claire et évolutive. En séparant la logique métier des dépendances externes, nous gagnons en maintenabilité et en testabilité. De plus, le mapping PHP permet d’être couvert par des outils d'analyse statique comme **PHPStan**.

Pour un exemple complet, consultez ce repository GitHub : [Mapping Doctrine avec PHP](https://github.com/Arthurlbc/Mapping-with-php).

