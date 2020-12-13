<?php


namespace MelodyNG\Core\Attribute;

use Attribute;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Attribute(Attribute::TARGET_CLASS)]
class CmsEntity
{

  public ?string $label;

  public function __construct(array $options = []) {
    $resolver = new OptionsResolver();

    $resolver->setDefault("label", null);
    $resolver->setAllowedTypes("label", ["string", "null"]);

    [
      "label" => $this->label
    ] = $resolver->resolve($options);
  }

}