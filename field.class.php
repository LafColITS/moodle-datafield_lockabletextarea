<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Class definition of lockabletextarea datafield.
 *
 * @package    datafield_lockabletextarea
 * @copyright  2024 onwards Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\notification;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../textarea/field.class.php');

/**
 * Class definition of lockabletextarea datafield.
 *
 * @package    datafield_lockabletextarea
 * @copyright  2024 onwards Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_field_lockabletextarea extends data_field_textarea {
    /** @var string The internal datafield type */
    public $type = 'lockabletextarea';

    /**
     * Output control for editing content.
     *
     * @param int $recordid the id of the data record.
     * @param object $formdata the submitted form.
     *
     * @return string
     */
    public function display_add_field($recordid = 0, $formdata = null) {
        global $DB;

        $context = \context_module::instance($this->cm->id);
        if ($this->field->param6 === 'on' && !has_capability('datafield/lockabletextarea:manage', $context)) {
            // Readonly mode.
            $text = '';
            $itemid = $this->field->id;
            $field = 'field_'.$itemid;
            if ($recordid &&
                    $content = $DB->get_record('data_content', ['fieldid' => $this->field->id, 'recordid' => $recordid])) {
                $text = $content->content;
            }

            $str = '<div title="' . s($this->field->description) . '" class="d-inline-flex">';
            $str .= '<div class="mod-data-input">';
            $str .= '<div><textarea id="'.$field.'" name="'.$field.'" rows="'.$this->field->param3.'" class="form-control" ' .
                'cols="'.$this->field->param2.'" readonly>'.s($text).'</textarea></div>';
            $str .= '</div></div></div>';
            return $str;
        } else {
            // Normal mode.
            return parent::display_add_field($recordid, $formdata);
        }
    }

    /**
     * Update the content.
     *
     * We do a set of permissions checks and then punt to the parent class.
     *
     * @param int $recordid the record id
     * @param string $value the content
     * @param string $name field name
     *
     * @return bool
     */
    public function update_content($recordid, $value, $name='') {
        global $DB;

        $context = \context_module::instance($this->cm->id);
        if ($this->field->param6 === 'on' && !has_capability('datafield/lockabletextarea:manage', $context)) {
            return true;
        }

        return parent::update_content($recordid, $value, $name);
    }
}
