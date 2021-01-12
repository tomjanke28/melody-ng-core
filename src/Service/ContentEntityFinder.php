<?php


namespace MelodyNG\Core\Service;


use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use LogicException;
use MelodyNG\Core\Attribute\ContentEntity;
use MelodyNG\Core\Exception\NoCmsEntityException;
use ReflectionClass;
use ReflectionException;

class ContentEntityFinder
{

  private ?array $cache = null;

  public function __construct(
    private EntityManagerInterface $entityManager
  ) {
  }

  private function findCmsEntities(): array {
    $entities = [];

    foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $classMetadata)
      try {
        if (($reflectionAttributeCount = sizeof($reflectionAttributes = ($reflectionClass = new ReflectionClass($classMetadata->getName()))->getAttributes(ContentEntity::class))) > 0)
          if ($reflectionAttributeCount === 1)
            $entities[$reflectionClass->getName()] = reset($reflectionAttributes)->newInstance();
          else
            throw new LogicException("Only one instance of CmsEntity allowed on '{$reflectionClass->getName()}'.");
      } catch (ReflectionException $reflectionException) {
        throw new InvalidArgumentException("Class '{$classMetadata->getName()}' does not exist.", 0, $reflectionException);
      }

    return $entities;
  }

  /**
   * Gets all CmsEntity definitions from all registered doctrine entities.
   *
   * @return array<ContentEntity> Associative array: class names as keys, CmsEntity instances as values
   */
  public function getCmsEntities(): array {
    return $this->cache === null ?
      $this->cache = $this->findCmsEntities() :
      $this->cache;
  }

  /**
   * @param string $entityClass
   * @return ContentEntity
   */
  public function getCmsEntity(string $entityClass): ContentEntity {
    if (array_key_exists($entityClass, $entities = $this->getCmsEntities()))
      return $entities[$entityClass];

    throw new NoCmsEntityException("Class '{$entityClass}' is no registered CmsEntity.");
  }

}