<?php


namespace MelodyNG\Core\Service;


use InvalidArgumentException;
use LogicException;
use MelodyNG\Core\Attribute\CmsProperty;
use ReflectionClass;
use ReflectionException;

class CmsPropertyReader
{

  private array $cache = [];

  private function readCmsProperties(string $entityClass): array {
    $properties = [];

    try {
      foreach (($reflectionClass = new ReflectionClass($entityClass))->getProperties() as $reflectionProperty)
        if (($reflectionAttributeCount = sizeof($reflectionAttributes = $reflectionProperty->getAttributes(CmsProperty::class))) > 0)
          if ($reflectionAttributeCount === 1)
            $properties[$reflectionProperty->getName()] = reset($reflectionAttributes)->newInstance();
          else
            throw new LogicException("Only one instance of CmsProperty allowed on '{$entityClass}::{$reflectionProperty->getName()}'.");
    } catch (ReflectionException $reflectionException) {
      throw new InvalidArgumentException("Class '{$entityClass}' does not exist.", 0, $reflectionException);
    }

    return $properties;
  }

  /**
   * Gets all CmsProperty definitions from an entity class.
   *
   * @param object|string $entityOrEntityClass
   * @return array<CmsProperty> Associative array: property names as keys, CmsProperty instances as values
   */
  public function getCmsProperties(object|string $entityOrEntityClass): array {
    if (is_object($entityOrEntityClass))
      $entityOrEntityClass = get_class($entityOrEntityClass);

    return array_key_exists($entityOrEntityClass, $this->cache) ?
      $this->cache[$entityOrEntityClass] :
      $this->cache[$entityOrEntityClass] = $this->readCmsProperties($entityOrEntityClass);
  }

}