<?php


namespace MelodyNG\Core\Service;


use InvalidArgumentException;
use LogicException;
use MelodyNG\Core\Attribute\ContentProperty;
use ReflectionClass;
use ReflectionException;

class CmsPropertyReader
{

  private array $loadedCmsProperties = [];
  private array $cache = [];

  public function __construct(
    private CmsEntityFinder $cmsEntityFinder
  ) {
  }

  private function readCmsProperties(string $entityClass, ?string $group): array {
    $properties = [];

    try {
      foreach (($reflectionClass = new ReflectionClass($entityClass))->getProperties() as $reflectionProperty)
        foreach ($reflectionProperty->getAttributes(ContentProperty::class) as $reflectionAttribute) {
          /** @var ContentProperty $cmsProperty */
          $cmsProperty = $reflectionAttribute->newInstance();

          if (in_array($group, $cmsProperty->groups)) {
            if (in_array($cmsPropertyIdentifier = $group === null ? "{$entityClass}:::{$reflectionProperty->getName()}" : "{$entityClass}::{$reflectionProperty->getName()}::{$group}", $this->loadedCmsProperties))
              throw new LogicException("Only one instance of CmsProperty per group allowed on '{$entityClass}::{$reflectionProperty->getName()}'.");

            $this->loadedCmsProperties[] = $cmsPropertyIdentifier;
            $properties[$reflectionProperty->getName()] = $cmsProperty;
          }
        }
    } catch (ReflectionException $reflectionException) {
      throw new InvalidArgumentException("Class '{$entityClass}' does not exist.", 0, $reflectionException);
    }

    if (sizeof($properties) === 0)
      if ($group === null)
        throw new LogicException("Class '{$entityClass}' has no marked properties.");
      else
        throw new LogicException("Class '{$entityClass}' has no marked properties in group '{$group}'.");

    return $properties;
  }

  /**
   * Gets all CmsProperty definitions from an entity class in a certain group.
   *
   * @param object|string $entityOrEntityClass
   * @param string|null $group
   * @return array<ContentProperty> Associative array: property names as keys, CmsProperty instances as values
   */
  public function getCmsProperties(object|string $entityOrEntityClass, ?string $group = null): array {
    if (is_object($entityOrEntityClass))
      $entityOrEntityClass = get_class($entityOrEntityClass);

    return array_key_exists($cacheKey = $group === null ? $entityOrEntityClass : "{$entityOrEntityClass}::{$group}", $this->cache) ?
      $this->cache[$cacheKey] :
      $this->cache[$cacheKey] = $this->readCmsProperties($entityOrEntityClass, $group);
  }

}