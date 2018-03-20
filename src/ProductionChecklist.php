<?php

namespace Drupal\production_checklist;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
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
  public function getAvailableSections() {
    return [
      'drupal_system' => t('System wide status and reports'),
      'drupal_codebase' => t('Contributed projects review'),
      'other_codebase' => t('Vendors and custom code'),
      'spam_prevention' => t('Spam prevention'),
      'security_access' => t('Security and access'),
      'content' => t('Content model review and proofreading'),
      'frontend' => t('Frontend'),
      'database' => t('Database and configuration'),
      'performance' => t('Performance and caching'),
      'test' => t('Testing'),
      'analytics' => t('Analytics'),
      'sysadmin' => t('Sysadmin and backups'),
      'seo' => t('Basic SEO'),
      'legal' => t('Legal aspects'),
      'documentation' => t('Project documentation'),
    ];
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
  public function getAvailableUpdates($type = 'security') {
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
  public function getAvailableSecurityUpdatesAmount() {
    $result = 0;
    // @todo implement
    // $updates = $this->getAvailableUpdates('security');
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getSecurityUpdatesChecklistArray() {
    $description = '';
    $pathText = '';
    $pathUrl = NULL;
    // @todo implement
    // if ($this->getAvailableSecurityUpdatesAmount() === 0) {
    // $description .= t('On last check,
    // no security updates were available.');
    // $pathText = t('Manual check');
    // $pathUrl = Url::fromRoute('update.manual_status');
    // }
    // else {
    // $description .= t('There are at least @amount security
    // updates available,
    // check @manual_status_link to get a complete status.');
    // $pathText = t('Available updates');
    // $pathUrl = Url::fromUserInput('update.status');
    // }
    $description .= t('Check available updates.');
    $pathText = t('Available updates');
    // @todo route
    $pathUrl = Url::fromRoute('update.status');
    return [
      '#title' => t('Drupal and other projects update'),
      '#description' => $description,
      'path' => [
        '#text' => $pathText,
        '#url' => $pathUrl,
      ],
    ];
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

  /**
   * {@inheritdoc}
   */
  public function getFieldsFromType($type) {
    $fields = [];
    try {
      $fields = $this->entityTypeManager->getStorage('field_storage_config')
        ->loadByProperties(['type' => 'email']);
    }
    catch (InvalidPluginDefinitionException $exception) {
      // @todo use messenger (available >= 8.5.0)
      drupal_set_message($exception->getMessage(), 'error');
    }
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmailObfuscationDescription() {
    $output = '';
    $output .= t('Are the email addresses protected against bots harvesting? Email addresses can be present in fields, WYSIWYG, Twig.');
    $fields = $this->getFieldsFromType('email');
    return $output;
  }

}
