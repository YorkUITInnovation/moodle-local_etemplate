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