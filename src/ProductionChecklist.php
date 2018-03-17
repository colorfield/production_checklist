<?php

namespace Drupal\production_checklist;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Link;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;

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
   * Drupal\Core\Render\Renderer definition.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Constructs a new ProductionChecklist object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandler $module_handler, Renderer $renderer, ConfigFactory $config) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->renderer = $renderer;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public function isModuleInstalled($module) {
    return $this->moduleHandler->moduleExists($module);
  }

  /**
   * {@inheritdoc}
   */
  public function isSiteMultilingual() {
    // @todo dependency injection
    /** @var \Drupal\Core\Language\LanguageManagerInterface $languageManager */
    $languageManager = \Drupal::service('language_manager');
    return $languageManager->isMultilingual();
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectLink($project) {
    $uri = 'https://drupal.org/project/' . $project;
    $projectName = str_replace('_', ' ', $project);
    $projectName = ucwords($projectName);
    $url = Url::fromUri($uri);
    $link = Link::fromTextAndUrl($projectName, $url);
    $link = $link->toRenderable();
    return $this->renderer->renderRoot($link);
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectStatusLink($project, $should_install = TRUE) {
    // @todo improve UI, with should install hint.
    $status = t('Is *not* installed');
    // @todo check if the project is a module, a theme or a distro.
    if ($this->isModuleInstalled($project)) {
      $status = t('Is installed');
    }
    $build = [
      '#theme' => 'project_status_link',
      '#link' => $this->getProjectLink($project),
      '#status' => $status,
    ];
    return $this->renderer->renderRoot($build);
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectsListStatusLink(array $projects, $should_install = TRUE) {
    $items = [];
    foreach ($projects as $project) {
      $items[] = $this->getProjectStatusLink($project, $should_install);
    }
    $build['status-link-list'] = [
      '#theme' => 'item_list',
      '#items' => $items,
      '#type' => 'ul',
    ];
    return $this->renderer->renderRoot($build);
  }

  /**
   * {@inheritdoc}
   */
  public function getAntiSpamStatusLink() {
    $projects = ['honeypot', 'captcha', 'recaptcha'];
    return $this->getProjectsListStatusLink($projects);
  }

  /**
   * {@inheritdoc}
   */
  public function getDevelopmentModulesStatusLink() {
    $projects = ['devel', 'coder'];
    return $this->getProjectsListStatusLink($projects, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function availableUpdates($type = 'security') {
    $build = [];
    if ($this->isModuleInstalled('update')) {
      $available = update_get_available(TRUE);
      $this->moduleHandler->loadInclude('update', 'compare.inc');
      $build['#data'] = update_calculate_project_data($available);
    }
    return $this->renderer->renderRoot($build);
  }

  /**
   * {@inheritdoc}
   */
  public function getModulesPageLink() {
    $route = 'system.modules_list';
    $link = Link::createFromRoute(t('Modules'), $route);
    $link = $link->toRenderable();
    return $this->renderer->renderRoot($link);
  }

  /**
   * {@inheritdoc}
   */
  public function getModulesPageTextUrl() {
    return [
      '#text' => t('Modules'),
      '#url' => Url::fromRoute('system.modules_list'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getModulesUninstallPageTextUrl() {
    return [
      '#text' => t('Uninstall modules'),
      '#url' => Url::fromRoute('system.modules_uninstall'),
    ];
  }

}
