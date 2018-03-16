<?php

namespace Drupal\production_checklist;

/**
 * Interface ProductionChecklistInterface.
 */
interface ProductionChecklistInterface {

  /**
   * Wrapper for the module handler to check if a module is installed.
   *
   * @param string $module
   *   Module machine name.
   *
   * @return bool
   *   Is the module installed.
   */
  public function isModuleInstalled($module);

  /**
   * Returns the link to a project on Drupal.org.
   *
   * @param string $project
   *   The project machine name.
   *
   * @return string
   *   Link to the project.
   */
  public function getProjectLink($project);

  /**
   * Returns the status of a project and its Drupal.org page.
   *
   * @param string $project
   *   The project machine name.
   * @param bool $should_install
   *   Indicates if the installation or uninstallation is recommended.
   *
   * @return string
   *   Markup for project status link.
   */
  public function getProjectStatusLink($project, $should_install = TRUE);

  /**
   * Returns a summary of projects status and links.
   *
   * @param array $projects
   *   Projects machine name list.
   * @param bool $should_install
   *   Indicates if the installation or uninstallation is recommended.
   *
   * @return string
   *   Projects status link list.
   */
  public function getProjectsListStatusLink(array $projects, $should_install = TRUE);

  /**
   * Returns a summary of the anti spam modules status and links.
   *
   * @return string
   *   Anti spam status link list.
   */
  public function getAntiSpamStatusLink();

  /**
   * Returns a summary of development modules status and links.
   *
   * @return string
   *   Development modules status link list.
   */
  public function getDevelopmentModulesStatusLink();

  /**
   * Returns the available updates for an update type.
   *
   * @param string $type
   *   Update type.
   *
   * @return string
   *   Available updates summary.
   */
  public function availableUpdates($type = 'security');

  /**
   * Returns the module page link.
   *
   * @return string
   *   Markup for the modules page link.
   */
  public function getModulesPageLink();

  /**
   * Returns the module page link as a checklist array.
   *
   * @return array
   *   Text and url array keys.
   */
  public function getModulesPageTextUrl();

  /**
   * Returns the module uninstall page link as a checklist array.
   *
   * @return array
   *   Text and url array keys.
   */
  public function getModulesUninstallPageTextUrl();

}
