<?php


namespace MelodyNG\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CmsEntity
{

  public function __construct(
    public ?string $label = null
  ) {
  }

}