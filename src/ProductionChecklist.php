<?php

namespace Drupal\production_checklist;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class ProductionChecklist.
 */
class ProductionChecklist implements ProductionChecklistInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ProductionChecklist object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

}
