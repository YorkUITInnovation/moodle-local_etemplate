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
 * Email templates JavaScript module.
 *
 * @module     local_etemplate/email_templates
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import notification from 'core/notification';
import ajax from 'core/ajax';
import {get_string as getString} from 'core/str';

export const init = () => {
    deleteEmailTemplate();
};

/**
 * Delete Unit
 */
function deleteEmailTemplate() {
    // Pop-up notification when .btn-local-organization-delete-campus is clicked
    document.querySelectorAll('.btn-local-etemplate-delete-email').forEach(button => {
        button.addEventListener('click', function () {
            // Get the data id attribute value
            var id = this.getAttribute('data-id');
            var row = this.closest('tr');
            var delete_string = getString('delete', 'local_etemplate');
            var delete_template = getString('delete_email_template', 'local_etemplate');
            var cancel = getString('cancel', 'local_etemplate');
            var could_not_delete_unit = getString('could_not_delete_email_template', 'local_etemplate');
            // Notification
            notification.confirm(delete_string, delete_template, delete_string, cancel, function () {
                // Delete the record
                var deleteCampus = ajax.call([{
                    methodname: 'etemplate_email_delete',
                    args: {
                        id: id
                    }
                }]);
                deleteCampus[0].done(function () {
                    row.remove();
                }).fail(function () {
                   notification.alert(could_not_delete_unit);
                });
            });

        });
    });

}