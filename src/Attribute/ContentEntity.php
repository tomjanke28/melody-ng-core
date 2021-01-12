<?php


namespace MelodyNG\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ContentEntity
{

  public function __construct(
    public ?string $label = null
  ) {
  }

}