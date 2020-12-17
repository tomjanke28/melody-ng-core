<?php


namespace MelodyNG\Core\Attribute;

use Attribute;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CmsProperty
{

  public ?string $formType;
  public array $formOptions;
  public ?string $group;

  public function __construct(array $options = []) {
    $resolver = new OptionsResolver();

    $resolver->setDefault("form_type", null);
    $resolver->setAllowedTypes("form_type", ["string", "null"]);

    $resolver->setDefault("form_options", []);
    $resolver->setAllowedTypes("form_options", ["array"]);

    $resolver->setDefault("group", null);
    $resolver->setAllowedTypes("group", ["string", "null"]);

    [
      "form_type" => $this->formType,
      "form_options" => $this->formOptions,
      "group" => $this->group
    ] = $resolver->resolve($options);
  }

}