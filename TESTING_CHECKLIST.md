# Moodle 5.1 Upgrade Testing Checklist

## Pre-Deployment
- [x] All code files updated with GPL headers
- [x] Version requirements updated to Moodle 5.1
- [x] External API migrated to core_external namespace
- [x] Bootstrap 5 compatibility verified
- [x] Autoloading implemented properly
- [x] Bug fixes applied (typo in lib.php)

## Deployment Steps

### 1. Backup (CRITICAL)
```bash
# Backup database
docker exec -it <container_name> php admin/cli/backup.php

# Backup plugin files (if needed)
docker exec -it <container_name> tar -czf /tmp/etemplate_backup.tar.gz /path/to/moodledata
```

### 2. Deploy to Container
Files are already in place at:
`/Users/christian/Docker/early_alert_docker/html/local/etemplate/`

### 3. Run Moodle Upgrade
```bash
docker exec -it <container_name> php admin/cli/upgrade.php --non-interactive
```

### 4. Rebuild JavaScript
```bash
docker exec -it <container_name> php admin/cli/grunt.php amd
```

### 5. Purge Caches
```bash
docker exec -it <container_name> php admin/cli/purge_caches.php
```

## Post-Deployment Testing

### Basic Functionality
- [ ] Navigate to Site administration → Plugins → Plugins overview
- [ ] Verify plugin version shows: **2026013000**
- [ ] Verify required Moodle version shows: **5.1**
- [ ] No errors in Moodle logs

### Email Templates List Page
- [ ] Navigate to: Site administration → Local plugins → Email Templates
- [ ] Page loads without errors
- [ ] Template list displays correctly
- [ ] Filter form works (active/inactive toggle)
- [ ] Search/filter functionality works
- [ ] Action buttons display correctly

### Create Template
- [ ] Click "Add email template" button
- [ ] Form loads correctly
- [ ] All fields are present and functional:
  - [ ] Template type radio buttons
  - [ ] Name field
  - [ ] Subject field
  - [ ] Message editor
  - [ ] Language dropdown
  - [ ] Unit/Campus selection
  - [ ] Message type selection
  - [ ] Active checkbox
- [ ] Form validation works
- [ ] Save template successfully
- [ ] Redirects to list page
- [ ] New template appears in list

### Edit Template
- [ ] Click edit button on a template
- [ ] Form loads with existing data
- [ ] All fields are editable
- [ ] Save changes successfully
- [ ] Changes reflected in list

### View Template (Read-only)
- [ ] Click view button on a template
- [ ] Form displays in read-only mode
- [ ] All fields are disabled
- [ ] Return to list works

### Clone Template
- [ ] Click clone button on a template
- [ ] Confirmation dialog appears
- [ ] Confirm clone action
- [ ] New template created as inactive
- [ ] Name prefixed with "Copy of"
- [ ] Success message displayed

### Delete Template (AJAX)
- [ ] Click delete button on a template
- [ ] Confirmation dialog appears (JavaScript)
- [ ] Confirm deletion
- [ ] Template removed from list without page reload
- [ ] Success notification appears
- [ ] Check browser console for errors (F12)

### Undelete Template
- [ ] Switch to "View inactive" templates
- [ ] Find a deleted template
- [ ] Click undelete button
- [ ] Template restored successfully
- [ ] Template appears in active list

### Capabilities Testing
Test with different user roles:

#### Manager Role
- [ ] Can view templates
- [ ] Can create templates
- [ ] Can edit templates
- [ ] Can delete templates
- [ ] Can undelete templates
- [ ] Can view system reserved templates

#### Create Test User Without Permissions
- [ ] User redirected from email templates page
- [ ] Cannot access create/edit pages directly

### Web Service Testing
- [ ] Test AJAX delete function works
- [ ] Check web service is registered:
  ```bash
  docker exec -it <container_name> php admin/cli/cfg.php --name=externalservicesenabled
  ```
- [ ] No errors in browser console during AJAX operations

### Visual/UI Testing
- [ ] All buttons styled correctly (Bootstrap 5)
- [ ] Table responsive on smaller screens
- [ ] Icons display correctly (Font Awesome)
- [ ] Colors and spacing look correct
- [ ] No CSS conflicts
- [ ] Forms render properly
- [ ] Notifications appear correctly

### Performance Testing
- [ ] Page load time acceptable
- [ ] Large template lists (50+) load properly
- [ ] No JavaScript errors in console
- [ ] No PHP errors in Moodle error log

### Database Integrity
- [ ] Check tables exist:
  ```sql
  SHOW TABLES LIKE 'mdl_local_et_%';
  ```
- [ ] Verify data intact:
  ```sql
  SELECT COUNT(*) FROM mdl_local_et_email;
  ```
- [ ] Check indexes are present
- [ ] Foreign keys functioning

### Error Handling
- [ ] Try to delete non-existent template (should show error)
- [ ] Try to edit without permissions (should redirect)
- [ ] Submit form with empty required fields (validation works)
- [ ] Test with invalid template ID
- [ ] Check error messages are user-friendly

### Logs Review
Check Moodle logs for any issues:
```bash
# View PHP error log
docker exec -it <container_name> tail -f /path/to/php_error.log

# View Moodle debug log (if debug mode enabled)
# Navigate to: Reports → Logs
```

### Browser Compatibility (if needed)
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari
- [ ] Mobile responsive view

## Issues Found

Document any issues here:

| Issue | Severity | Description | Status |
|-------|----------|-------------|--------|
|       |          |             |        |

## Sign-off

- [ ] All critical tests passed
- [ ] No blocking issues found
- [ ] Plugin ready for production use

**Tested by:** ___________________  
**Date:** ___________________  
**Moodle Version:** 5.1  
**Plugin Version:** 2026013000  
**Environment:** Docker Container

## Rollback Procedure (If Needed)

If critical issues are found:

1. Restore database backup
2. Replace plugin files with previous version
3. Run upgrade.php
4. Purge caches
5. Document issues for review

## Notes

Add any additional observations or notes here:

---

**Happy Testing! 🚀**
