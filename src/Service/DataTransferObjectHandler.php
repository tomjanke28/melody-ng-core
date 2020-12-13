<?php


namespace MelodyNG\Core\Service;


use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DataTransferObjectHandler
{

  private PropertyAccessor $propertyAccessor;

  public function __construct(
    private CmsPropertyReader $cmsPropertyReader
  ) {
    $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
  }

  /**
   * Creates a data transfer object and clones the properties.
   *
   * @param object $fromEntity
   * @return array Associative array: property names as keys, cloned values as values
   */
  public function createDataTransferObject(object $fromEntity): array {
    $dataTransferObject = [];

    foreach ($this->cmsPropertyReader->getCmsProperties($fromEntity) as $propertyName => $cmsProperty)
      $this->propertyAccessor->setValue($dataTransferObject, "[{$propertyName}]", is_object($value = $this->propertyAccessor->getValue($fromEntity, $propertyName)) ? clone $value : $value);

    return $dataTransferObject;
  }

  /**
   * Writes a data transfer object to an entity, if the property has changed.
   *
   * @param array $dataTransferObject
   * @param object $toEntity
   * @return object The entity
   */
  public function writeDataTransferObject(array $dataTransferObject, object $toEntity): object {
    foreach ($this->cmsPropertyReader->getCmsProperties($toEntity) as $propertyName => $cmsProperty)
      if (($newValue = $this->propertyAccessor->getValue($dataTransferObject, "[{$propertyName}]")) != $this->propertyAccessor->getValue($toEntity, $propertyName))
        $this->propertyAccessor->setValue($toEntity, $propertyName, $newValue);

    return $toEntity;
  }

}