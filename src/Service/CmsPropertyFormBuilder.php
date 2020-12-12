<?php


namespace MelodyNG\Core\Service;


use MelodyNG\Core\Attribute\CmsProperty;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class CmsPropertyFormBuilder
{

  public function __construct(
    private CmsPropertyReader $cmsPropertyReader,
    private DataTransferObjectHandler $dataTransferObjectHandler,
    private FormFactoryInterface $formFactory
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
  public function buildForm(string $entityClass, array $data, array $options = []): FormInterface {
    $formBuilder = $this->formFactory->createBuilder(FormType::class, $data, $options);

    /**
     * @var string $propertyName
     * @var CmsProperty $cmsProperty
     */
    foreach ($this->cmsPropertyReader->getCmsProperties($entityClass) as $propertyName => $cmsProperty)
      $formBuilder->add($propertyName, $cmsProperty->formType, $cmsProperty->formOptions);

    return $formBuilder->getForm();
  }

}