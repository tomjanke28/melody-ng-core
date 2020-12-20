<?php


namespace MelodyNG\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CmsProperty
{

  public function __construct(
    public ?string $formType = null,
    public array $formOptions = [],
    public array $groups = [null]
  ) {
  }

}