<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

/**
 * This class generates form components for processing Event
 *
 */
class CRM_Volunteer_Form_Volunteer extends CRM_Event_Form_ManageEvent {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  function preProcess() {
    parent::preProcess();
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $vid = NULL;

    $active = CRM_Volunteer_BAO_Project::isActive($this->_id, CRM_Event_DAO_Event::$_tableName);
    $this->assign('active', $active);

    $params = array(
      'entity_id' => $this->_id,
      'entity_table' => CRM_Event_DAO_Event::$_tableName,
    );
    $projects = CRM_Volunteer_BAO_Project::retrieve($params);

    if (count($projects) === 1) {
      $p = current($projects);
      $vid = $p->id;
    }

    $this->assign('vid', $vid);

    $buttons = array(
      array(
        'type' => 'upload',
        'name' => $active ? ts('Disable') : ts('Enable Volunteer Management'),
        'isDefault' => TRUE,
      ),
    );
    $this->addButtons($buttons);
  }

  /**
   * Function to process the form. Enables/disables Volunteer Project. If the
   * Project does not already exist, it is created, along with a "flexible" Need.
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    $params = array(
      'entity_id' => $this->_id,
      'entity_table' => CRM_Event_DAO_Event::$_tableName,
    );

    // see if this project already exists
    $projects = CRM_Volunteer_BAO_Project::retrieve($params);

    if (count($projects) === 1) {
      $p = current($projects);
      if ($p->is_active) {
        $p->disable();
      } else {
        $p->enable();
      }
    // if the project doesn't already exist and the user enabled vol management
    } else {
      $project = CRM_Volunteer_BAO_Project::create($params);

      $need = array(
        'project_id' => $project->id,
        'is_flexible' => '1',
        'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
      );
      CRM_Volunteer_BAO_Need::create($need);
    }

    parent::endPostProcess();
  }

  /**
   * Return a descriptive name for the page, used in wizard header
   *
   * @return string
   * @access public
   */
  public function getTitle() {
    return ts('Volunteers');
  }
}

