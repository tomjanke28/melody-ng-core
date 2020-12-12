<?php


namespace MelodyNG\Core\Attribute;

use Attribute;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CmsProperty
{

  public ?string $formType;
  public array $formOptions;

  public function __construct(array $options) {
    $resolver = new OptionsResolver();

    $resolver->setDefault("form_type", null);
    $resolver->setAllowedTypes("form_type", ["string", "null"]);

    $resolver->setDefault("form_options", []);
    $resolver->setAllowedTypes("form_options", ["array"]);

    [
      "form_type" => $this->formType,
      "form_options" => $this->formOptions
    ] = $resolver->resolve($options);
  }

}