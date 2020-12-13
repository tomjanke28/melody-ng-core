<?php


namespace MelodyNG\Core\Service;


use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Column;
use InvalidArgumentException;
use LogicException;
use MelodyNG\Core\Attribute\CmsProperty;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class CmsPropertyFormBuilder
{

  public function __construct(
    private CmsPropertyReader $cmsPropertyReader,
    private DataTransferObjectHandler $dataTransferObjectHandler,
    private FormFactoryInterface $formFactory,
    private EntityManagerInterface $entityManager,
    private Reader $annotationReader
  ) {
  }

  /**
   * Builds a form based on the given entity instance.
   *
   * @param object $entity
   * @param array $options
   * @return FormInterface
   */
  public function buildFormForEntity(object $entity, array $options = []): FormInterface {
    return $this->buildForm(get_class($entity), $this->dataTransferObjectHandler->createDataTransferObject($entity), $options);
  }

  /**
   * Builds a form based on the given entity class and data array.
   *
   * @param string $entityClass
   * @param array $data
   * @param array $options
   * @return FormInterface
   */
  public function buildForm(string $entityClass, array $data = [], array $options = []): FormInterface {
    $formBuilder = $this->formFactory->createBuilder(FormType::class, $data, $options);

    /**
     * @var string $propertyName
     * @var CmsProperty $cmsProperty
     */
    foreach ($this->cmsPropertyReader->getCmsProperties($entityClass) as $propertyName => $cmsProperty)
      $formBuilder->add($propertyName, $cmsProperty->formType ?? $this->guessFormType($entityClass, $propertyName), $cmsProperty->formOptions);

    return $formBuilder->getForm();
  }

  private function guessFormType(string $entityClass, string $propertyName) {
    $classMetadata = $this->entityManager->getMetadataFactory()->getMetadataFor($entityClass);
    try {
      /** @var Column $column */
      if (($column = $this->annotationReader->getPropertyAnnotation((new ReflectionClass($classMetadata->getName()))->getProperty($propertyName), Column::class)) === null)
        throw new LogicException("Cannot guess form type with no column definition.");

      return match ($column->type) {
        "smallint", "integer", "bigint" => IntegerType::class,
        "decimal", "float" => NumberType::class,
        "string", "ascii_string" => TextType::class,
        "text" => TextareaType::class,
        "boolean" => CheckboxType::class,
        "date" => DateType::class,
        "datetime" => DateTimeType::class,
        "time" => TimeType::class,
        default => throw new InvalidArgumentException("Cannot guess form type for '{$entityClass}::{$propertyName}' with type '{$column->type}'")
      };
    } catch (ReflectionException $reflectionException) {
      throw new InvalidArgumentException("Class '{$entityClass}' does not exist or does not contain property '{$propertyName}'.", 0, $reflectionException);
    }
  }

}