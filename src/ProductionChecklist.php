<?php

namespace Drupal\production_checklist;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Config\ConfigFactory;

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
   * Drupal\Core\Extension\ModuleHandler definition.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Constructs a new ProductionChecklist object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandler $module_handler, ConfigFactory $config) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->config = $config;
  }

  /**
   * Wrapper for the module handler to check if a module is installed.
   *
   * @param string $module
   *   Module machine name.
   *
   * @return bool
   *   Is the module installed.
   */
  public function isModuleInstalled($module) {
    return $this->moduleHandler->moduleExists($module);
  }

  /**
   * Returns a summary of spam prevention needed depending on the configuration.
   *
   * @return array
   *   Anti spam summary render array.
   */
  public function getAntiSpamStatus() {
    // @todo
    $build = [];
    return $build;
  }

  /**
   * Returns the available updates for an update type.
   *
   * @param string $type
   *   Update type.
   *
   * @return array
   *   Available updates summary render array.
   */
  public function availableUpdates($type = 'security') {
    $build = [];
    if ($this->isModuleInstalled('update')) {
      $available = update_get_available(TRUE);
      $this->moduleHandler->loadInclude('update', 'compare.inc');
      $build['#data'] = update_calculate_project_data($available);
    }
    return $build;
  }

  /**
   * Returns a summary of installation status for development modules.
   *
   * @return array
   *   Development modules summary.
   */
  public function getDevelopmentModulesStatus() {
    // Webprofiler
    // Devel
    // Coder
    // ...
    $build = [];
    return $build;
  }

}
