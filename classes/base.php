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
 * Base helper class for local_etemplate plugin.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_etemplate;

defined('MOODLE_INTERNAL') || die();

/**
 * Base helper class with common utility functions.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo Change this into a Singleton and get rid of static functions
 */
class base
{
    // Set constants for buttons.
    const CONTEXT_TONE = 'TONE';
    const CONTEXT_LENGTH = 'LENGTH';

    /** @var string CDN URL for pdfmake dependency. */
    private const PDFMAKE_JS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js';

    /** @var string CDN URL for pdfmake font dependency. */
    private const PDFMAKE_FONTS_JS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js';

    /** @var string CDN URL for DataTables bundle used by this plugin. */
    private const DATATABLES_BUNDLE_JS_URL = 'https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/af-2.4.0/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/cr-1.5.6/date-1.1.2/fc-4.1.0/fh-3.2.4/kt-2.7.0/r-2.3.0/rg-1.2.0/rr-1.2.8/sc-2.0.7/sb-1.3.4/sp-2.0.2/sl-1.4.0/sr-1.1.1/datatables.min.js';

    /** @var string CDN URL for DataTables stylesheet bundle used by this plugin. */
    private const DATATABLES_BUNDLE_CSS_URL = 'https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/af-2.4.0/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/cr-1.5.6/date-1.1.2/fc-4.1.0/fh-3.2.4/kt-2.7.0/r-2.3.0/rg-1.2.0/rr-1.2.8/sc-2.0.7/sb-1.3.4/sp-2.0.2/sl-1.4.0/sr-1.1.1/datatables.min.css';

    /** @var string Component used when loading strings for JS in this plugin. */
    private const JS_STRINGS_COMPONENT = 'local_cria';

    /** @var string Source component used to fetch string keys for JS. */
    private const STRING_SOURCE_COMPONENT = 'local_yulearn';

        /**
         * Creates the Moodle page header.
         *
     * @param string $url Current page url
     * @param string $pagetitle Page title
     * @param string $pageheading Page heading (Note hard coded to site fullname)
         * @param mixed $context The page context (SYSTEM, COURSE, MODULE etc)
     * @param string $pagelayout The page context (SYSTEM, COURSE, MODULE etc)
     * @return HTML Contains page information and loads all Javascript and CSS
     * @global \moodle_page $PAGE
     */
    public static function page($url, $pagetitle, $pageheading, $context = null, $pagelayout = 'base')
    {
                global $PAGE;

        $context = \context_system::instance();

        $PAGE->set_url($url);
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pageheading);
        $PAGE->set_pagelayout($pagelayout);
        $PAGE->set_context($context);
        // We need datatables to work. So we load it from cdn.
        // We also load one JS file that initialises all datatables.
        // This same file is used throughout, including in the blocks.
        self::loadJQueryJS();
    }

    /**
     * Load jQuery/DataTables dependencies and language strings.
     */
    public static function loadJQueryJS()
    {
        global $PAGE;

        $stringman = get_string_manager();
        $strings = $stringman->load_component_strings(self::STRING_SOURCE_COMPONENT, current_language());

        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->jquery_plugin('ui-css');
        self::load_datatables_dependencies();
        $PAGE->requires->strings_for_js(array_keys($strings), self::JS_STRINGS_COMPONENT);
    }

    /**
     * Load external JS/CSS dependencies used by plugin tables.
     */
    private static function load_datatables_dependencies(): void
    {
        global $PAGE;

        $PAGE->requires->js(new \moodle_url(self::PDFMAKE_JS_URL), true);
        $PAGE->requires->js(new \moodle_url(self::PDFMAKE_FONTS_JS_URL), true);
        $PAGE->requires->js(new \moodle_url(self::DATATABLES_BUNDLE_JS_URL), true);
        $PAGE->requires->css(new \moodle_url(self::DATATABLES_BUNDLE_CSS_URL));
    }

    /**
     * Sets filemanager options.
     *
     * @param \stdClass $context
     * @param int $maxfiles
     * @return array
     * @global \stdClass $CFG
     */
    public static function getFileManagerOptions($context, $maxfiles = 1)
    {
        global $CFG;

        return ['subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => $maxfiles];
    }

    /**
     * Sets editor options.
     *
     * @param \stdClass $context
     * @return array
     * @global \stdClass $CFG
     */
    public static function getEditorOptions($context)
    {
        global $CFG;

        return ['subdirs' => 1, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => -1,
            'changeformat' => 1, 'context' => $context, 'noclean' => 1, 'trusttext' => 0];
    }

    /**
     * Locale-formatted strftime using \IntlDateFormatter (PHP 8.1 compatible)
     * This provides a cross-platform alternative to strftime() for when it will be removed from PHP.
     * Note that output can be slightly different between libc sprintf and this function as it is using ICU.
     *
     * Usage:
     * use function \PHP81_BC\strftime;
     * echo strftime('%A %e %B %Y %X', new \DateTime('2021-09-28 00:00:00'), 'fr_FR');
     *
     * Original use:
     * \setlocale('fr_FR.UTF-8', LC_TIME);
     * echo \strftime('%A %e %B %Y %X', strtotime('2021-09-28 00:00:00'));
     *
     * @param string $format Date format
     * @param integer|string|DateTime $timestamp Timestamp
     * @return string
     * @author BohwaZ <https://bohwaz.net/>
     */
    public static function strftime(string $format, $timestamp = null, ?string $locale = null): string
    {
        if (null === $timestamp) {
            $timestamp = new \DateTime;
        } elseif (is_numeric($timestamp)) {
            $timestamp = date_create('@' . $timestamp);

            if ($timestamp) {
                $timestamp->setTimezone(new \DateTimezone(date_default_timezone_get()));
            }
        } elseif (is_string($timestamp)) {
            $timestamp = date_create($timestamp);
        }

        if (!($timestamp instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException('$timestamp argument is neither a valid UNIX timestamp, a valid date-time string or a DateTime object.');
        }

        $locale = substr((string)$locale, 0, 5);

        $intl_formats = [
            '%a' => 'EEE',    // An abbreviated textual representation of the day	Sun through Sat
            '%A' => 'EEEE',    // A full textual representation of the day	Sunday through Saturday
            '%b' => 'MMM',    // Abbreviated month name, based on the locale	Jan through Dec
            '%B' => 'MMMM',    // Full month name, based on the locale	January through December
            '%h' => 'MMM',    // Abbreviated month name, based on the locale (an alias of %b)	Jan through Dec
        ];

        $intl_formatter = function (\DateTimeInterface $timestamp, string $format) use ($intl_formats, $locale) {
            $tz = $timestamp->getTimezone();
            $date_type = \IntlDateFormatter::FULL;
            $time_type = \IntlDateFormatter::FULL;
            $pattern = '';

            // %c = Preferred date and time stamp based on locale
            // Example: Tue Feb 5 00:45:10 2009 for February 5, 2009 at 12:45:10 AM
            if ($format == '%c') {
                $date_type = \IntlDateFormatter::LONG;
                $time_type = \IntlDateFormatter::SHORT;
            }
            // %x = Preferred date representation based on locale, without the time
            // Example: 02/05/09 for February 5, 2009
            elseif ($format == '%x') {
                $date_type = \IntlDateFormatter::SHORT;
                $time_type = \IntlDateFormatter::NONE;
            } // Localized time format
            elseif ($format == '%X') {
                $date_type = \IntlDateFormatter::NONE;
                $time_type = \IntlDateFormatter::MEDIUM;
            } else {
                $pattern = $intl_formats[$format];
            }

            return (new \IntlDateFormatter($locale, $date_type, $time_type, $tz, null, $pattern))->format($timestamp);
        };

        // Same order as https://www.php.net/manual/en/function.strftime.php
        $translation_table = [
            // Day
            '%a' => $intl_formatter,
            '%A' => $intl_formatter,
            '%d' => 'd',
            '%e' => function ($timestamp) {
                return sprintf('% 2u', $timestamp->format('j'));
            },
            '%j' => function ($timestamp) {
                // Day number in year, 001 to 366
                return sprintf('%03d', $timestamp->format('z') + 1);
            },
            '%u' => 'N',
            '%w' => 'w',

            // Week
            '%U' => function ($timestamp) {
                // Number of weeks between date and first Sunday of year
                $day = new \DateTime(sprintf('%d-01 Sunday', $timestamp->format('Y')));
                return sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
            },
            '%V' => 'W',
            '%W' => function ($timestamp) {
                // Number of weeks between date and first Monday of year
                $day = new \DateTime(sprintf('%d-01 Monday', $timestamp->format('Y')));
                return sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
            },

            // Month
            '%b' => $intl_formatter,
            '%B' => $intl_formatter,
            '%h' => $intl_formatter,
            '%m' => 'm',

            // Year
            '%C' => function ($timestamp) {
                // Century (-1): 19 for 20th century
                return floor($timestamp->format('Y') / 100);
            },
            '%g' => function ($timestamp) {
                return substr($timestamp->format('o'), -2);
            },
            '%G' => 'o',
            '%y' => 'y',
            '%Y' => 'Y',

            // Time
            '%H' => 'H',
            '%k' => function ($timestamp) {
                return sprintf('% 2u', $timestamp->format('G'));
            },
            '%I' => 'h',
            '%l' => function ($timestamp) {
                return sprintf('% 2u', $timestamp->format('g'));
            },
            '%M' => 'i',
            '%p' => 'A', // AM PM (this is reversed on purpose!)
            '%P' => 'a', // am pm
            '%r' => 'h:i:s A', // %I:%M:%S %p
            '%R' => 'H:i', // %H:%M
            '%S' => 's',
            '%T' => 'H:i:s', // %H:%M:%S
            '%X' => $intl_formatter, // Preferred time representation based on locale, without the date

            // Timezone
            '%z' => 'O',
            '%Z' => 'T',

            // Time and Date Stamps
            '%c' => $intl_formatter,
            '%D' => 'm/d/Y',
            '%F' => 'Y-m-d',
            '%s' => 'U',
            '%x' => $intl_formatter,
        ];

        $out = preg_replace_callback('/(?<!%)(%[a-zA-Z])/', function ($match) use ($translation_table, $timestamp) {
            if ($match[1] == '%n') {
                return "\n";
            } elseif ($match[1] == '%t') {
                return "\t";
            }

            if (!isset($translation_table[$match[1]])) {
                throw new \InvalidArgumentException(sprintf('Format "%s" is unknown in time format', $match[1]));
            }

            $replace = $translation_table[$match[1]];

            if (is_string($replace)) {
                return $timestamp->format($replace);
            } else {
                return $replace($timestamp, $match[1]);
            }
        }, $format);

        $out = str_replace('%%', '%', $out);
        return $out;
    }
    public static function get_editor_options($context) {
        global $CFG;
        return array('subdirs'=>1, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1, 'changeformat'=>1, 'context'=>$context, 'noclean'=>1, 'trusttext'=>0);
    }

    /**
     * Return all roles the user has
     * @return array
     * @throws \dml_exception
     */
    public static function get_advisor_roles() {
        global $DB, $USER;
        // Get all assigned roles for the user
        $advisor_roles = $DB->get_records('local_organization_advisor', ['user_id' => $USER->id]);

        $permissions = [];
        // Group all identical user_context into separate arrays. For example, all DEPARTMENT user_contexts should be in one array
        // and all UNIT user_contexts should be in another array
        foreach ($advisor_roles as $role) {
            if (!isset($permissions[$role->user_context])) {
                $permissions[$role->user_context] = [];
            }
            $permissions[$role->user_context][] = [
                'instance_id' => $role->instance_id,
                'context' => $role->user_context
            ];
        }

        return $permissions;

    }


    public static function getTemplatePermissions($unitid, $context, $userid = null) {
        global $USER;
        if (!$userid){
            $userid = $USER->id;
        }
        $permissions = array();
        if (is_numeric($unitid)) {
            $unitclass = new \local_organization\unit($unitid);
            $unitInfo = $unitclass->get_name();
            // unit permission checks
            $permissions = array(
                'canCreate' => \local_organization\base::has_capability('local/etemplate:create', $context, $userid, true, $unitid, 'UNIT'),
                'canDelete' => \local_organization\base::has_capability('local/etemplate:delete', $context, $userid, true, $unitid, 'UNIT'),
                'canEdit' => \local_organization\base::has_capability('local/etemplate:edit', $context, $userid, true, $unitid, 'UNIT'),
                'canUndelete' => \local_organization\base::has_capability('local/etemplate:undelete', $context, $userid, true, $unitid, 'UNIT'),
                'canView' => \local_organization\base::has_capability('local/etemplate:view', $context, $userid, true, $unitid, 'UNIT'),
                'canViewSystemReserved' => \local_organization\base::has_capability('local/etemplate:view_system_reserved', $context, $userid, true, $unitid, 'UNIT'),
                'unitInfo' => $unitInfo,
            );
        } else {
            $explodedUnit = explode("_", $unitid);
            if (count($explodedUnit) == 2) {
                $unit = new \local_organization\unit($explodedUnit[0]);
                $department = new \local_organization\department($explodedUnit[1]);
                $unitInfo = "";
                if ($unit->get_name() != "") {
                    $unitInfo .= $unit->get_name();
                } else {
                    $unitInfo .= "{missing unit/faculty}";
                }
                $unitInfo .= " / ";
                if ($department->get_name() != "") {
                    $unitInfo .= $department->get_name();
                } else {
                    $unitInfo .= "{missing department}";
                }
            }
            // department permission checks
            $permissions = array(
                'canCreate' => \local_organization\base::has_capability('local/etemplate:create', $context, $userid, true, $department->get_id(), 'DEPARTMENT') ||
                    \local_organization\base::has_capability('local/etemplate:create', $context, $userid, true, $unit->get_id(), 'UNIT'),
                'canDelete' => \local_organization\base::has_capability('local/etemplate:delete', $context, $userid, true, $department->get_id(), 'DEPARTMENT') ||
                    \local_organization\base::has_capability('local/etemplate:delete', $context, $userid, true, $unit->get_id(), 'UNIT'),
                'canEdit' => \local_organization\base::has_capability('local/etemplate:edit', $context, $userid, true, $department->get_id(), 'DEPARTMENT') ||
                    \local_organization\base::has_capability('local/etemplate:edit', $context, $userid, true, $unit->get_id(), 'UNIT'),
                'canUndelete' => \local_organization\base::has_capability('local/etemplate:undelete', $context, $userid, true, $department->get_id(), 'DEPARTMENT') ||
                    \local_organization\base::has_capability('local/etemplate:undelete', $context, $userid, true, $unit->get_id(), 'UNIT'),
                'canView' => \local_organization\base::has_capability('local/etemplate:view', $context, $userid, true, $department->get_id(), 'DEPARTMENT') ||
                    \local_organization\base::has_capability('local/etemplate:view', $context, $userid, true, $unit->get_id(), 'UNIT'),
                'canViewSystemReserved' => \local_organization\base::has_capability('local/etemplate:view_system_reserved', $context, $userid, true, $department->get_id(), 'DEPARTMENT') ||
                    \local_organization\base::has_capability('local/etemplate:view_system_reserved', $context, $userid, true, $unit->get_id(), 'UNIT'),
                'unitInfo' => $unitInfo,
            );
        }
        if (is_siteadmin($userid)){
            $permissions['canCreate'] = true;
            $permissions['canDelete'] = true;
            $permissions['canEdit'] = true;
            $permissions['canUndelete'] = true;
            $permissions['canView'] = true;
            $permissions['canViewSystemReserved'] = true;
        }
        return $permissions;
    }

    public static function get_unit_options() {
        global $DB;

        // Campuses
        $campuses_array = $DB->get_records_menu('local_organization_campus', [], 'name', 'id, name');
        $campuses = ['' => get_string('select', 'local_etemplate')];
        foreach ($campuses_array as $id => $name) {
            $campuses[$id . '_CAMPUS'] = $name;
        }

        // Faculties
        $faculties_sql = "SELECT ou.id, ou.name, oc.name As campus
                          FROM {local_organization_unit} ou
                          JOIN {local_organization_campus} oc ON oc.id = ou.campus_id
                          ORDER BY campus, ou.name";
        $faculties_results = $DB->get_records_sql($faculties_sql);
        $faculties = [];
        foreach ($faculties_results as $faculty) {
            $faculties[$faculty->id . '_UNIT'] = $faculty->campus . ' / ' . $faculty->name;
        }

        // CK OCT25: DEPRECATED Departments and Major
        $major_sql = "SELECT od.id, od.name AS department, ou.name AS unit, oc.name AS campus
                      FROM {local_organization_dept} od
                      JOIN {local_organization_unit} ou ON ou.id = od.unit_id
                      JOIN {local_organization_campus} oc ON oc.id = ou.campus_id
                      ORDER BY campus, unit, department";
        $majors = $DB->get_records_sql($major_sql);
        $major_select = [];
        foreach ($majors as $major) {
            $major_select[$major->id . '_DEPT'] = $major->campus . ' / ' . $major->unit . ' / ' . $major->department;
        }

        return [
            get_string('campus', 'local_etemplate') => $campuses,
            get_string('faculty', 'local_etemplate') => $faculties,
            // DEPRECATED
            //get_string('major', 'local_etemplate') => $major_select
        ];
    }

    /**
     * Return unit options filtered to only those the current user is authorized to manage.
     *
     * Site admins see all units via get_unit_options(). Non-admins see only the campus/unit
     * entries covered by their advisor assignments (campus admins get all units in their campus).
     *
     * @param int|null $userid  Defaults to $USER->id.
     * @return array  Selectgroups array suitable for addElement('selectgroups', ...).
     */
    public static function get_unit_options_for_user(?int $userid = null): array {
        global $USER, $DB;

        $userid = $userid ?? (int) $USER->id;

        if (is_siteadmin($userid)) {
            return self::get_unit_options();
        }

        $advisor_rows = $DB->get_records('local_organization_advisor', ['user_id' => $userid]);
        if (empty($advisor_rows)) {
            return [
                get_string('campus', 'local_etemplate') => ['' => get_string('select', 'local_etemplate')],
            ];
        }

        $campusids = [];
        $unitids   = [];

        foreach ($advisor_rows as $row) {
            switch ($row->user_context) {
                case 'CAMPUS':
                    $campusids[] = (int) $row->instance_id;
                    break;
                case 'UNIT':
                    $unitids[] = (int) $row->instance_id;
                    $cid = $DB->get_field('local_organization_unit', 'campus_id', ['id' => $row->instance_id]);
                    if ($cid) {
                        $campusids[] = (int) $cid;
                    }
                    break;
                case 'DEPARTMENT':
                case 'DEPT':
                    $uid = $DB->get_field('local_organization_dept', 'unit_id', ['id' => $row->instance_id]);
                    if ($uid) {
                        $unitids[] = (int) $uid;
                        $cid = $DB->get_field('local_organization_unit', 'campus_id', ['id' => $uid]);
                        if ($cid) {
                            $campusids[] = (int) $cid;
                        }
                    }
                    break;
            }
        }

        $campusids = array_values(array_unique($campusids));
        $unitids   = array_values(array_unique($unitids));

        $campuses = ['' => get_string('select', 'local_etemplate')];
        if (!empty($campusids)) {
            [$insql, $inparams] = $DB->get_in_or_equal($campusids, SQL_PARAMS_NAMED, 'cp');
            foreach ($DB->get_records_select('local_organization_campus', "id $insql", $inparams, 'name', 'id, name') as $c) {
                $campuses[$c->id . '_CAMPUS'] = $c->name;
            }
        }

        $faculties = [];
        // Campus admins inherit access to all units within their campus.
        if (!empty($campusids)) {
            [$insql, $inparams] = $DB->get_in_or_equal($campusids, SQL_PARAMS_NAMED, 'cup');
            $rows = $DB->get_records_sql(
                "SELECT ou.id, ou.name, oc.name AS campus
                   FROM {local_organization_unit} ou
                   JOIN {local_organization_campus} oc ON oc.id = ou.campus_id
                  WHERE oc.id $insql ORDER BY campus, ou.name",
                $inparams
            );
            foreach ($rows as $f) {
                $faculties[$f->id . '_UNIT'] = $f->campus . ' / ' . $f->name;
            }
        } elseif (!empty($unitids)) {
            [$insql, $inparams] = $DB->get_in_or_equal($unitids, SQL_PARAMS_NAMED, 'up');
            $rows = $DB->get_records_sql(
                "SELECT ou.id, ou.name, oc.name AS campus
                   FROM {local_organization_unit} ou
                   JOIN {local_organization_campus} oc ON oc.id = ou.campus_id
                  WHERE ou.id $insql ORDER BY campus, ou.name",
                $inparams
            );
            foreach ($rows as $f) {
                $faculties[$f->id . '_UNIT'] = $f->campus . ' / ' . $f->name;
            }
        }

        return [
            get_string('campus', 'local_etemplate') => $campuses,
            get_string('faculty', 'local_etemplate') => $faculties,
        ];
    }

    /**
     * Check whether the specified user's advisor roles grant access to the unit identified
     * by a form unit value in "id_TYPE" format (e.g. "5_UNIT", "3_CAMPUS", "7_DEPT").
     *
     * Hierarchy rules:
     * - Campus admins have access to all units and departments within their campus.
     * - Unit admins have access to all departments within their unit.
     * - Site admins always have access.
     *
     * @param string     $unit_value    Unit value in "id_TYPE" format from the template form.
     * @param array|null $advisor_roles Advisor roles from self::get_advisor_roles(); fetched if null.
     * @param int|null   $userid        Defaults to $USER->id.
     * @return bool
     */
    /**
     * Convenience wrapper: check whether the current user has access to a template by its DB id.
     *
     * Loads the template record, derives the unit value, then delegates to
     * user_can_access_unit_value(). Returns false when the template does not exist.
     *
     * @param int      $template_id  Primary key of local_et_email.
     * @param int|null $userid       Defaults to $USER->id.
     * @return bool
     */
    public static function user_can_access_template_id(int $template_id, ?int $userid = null): bool {
        global $USER, $DB;

        $userid = $userid ?? (int) $USER->id;

        if (is_siteadmin($userid)) {
            return true;
        }

        $record = $DB->get_record('local_et_email', ['id' => $template_id], '*', IGNORE_MISSING);
        if (!$record) {
            return false;
        }

        // Build unit value from stored context + unit ID.
        if (!empty($record->context) && !empty($record->unit)) {
            $unit_value = $record->unit . '_' . $record->context;
        } else {
            // Fallback: derive from campus/faculty shortnames (course-based templates).
            $unit_value = self::get_unit_value_from_template_data($record) ?? '';
        }

        return self::user_can_access_unit_value($unit_value, null, $userid);
    }

    public static function user_can_access_unit_value(string $unit_value, ?array $advisor_roles = null, ?int $userid = null): bool {
        global $USER, $DB;

        $userid = $userid ?? (int) $USER->id;

        if (is_siteadmin($userid)) {
            return true;
        }

        if (empty($unit_value)) {
            return false;
        }

        if ($advisor_roles === null) {
            $advisor_roles = self::get_advisor_roles();
        }

        if (empty($advisor_roles)) {
            return false;
        }

        $parts     = explode('_', $unit_value);
        $unit_type = strtoupper(end($parts));
        $unit_id   = (int) $parts[0];

        $campusids = array_column($advisor_roles['CAMPUS'] ?? [], 'instance_id');
        $unitids   = array_column($advisor_roles['UNIT'] ?? [], 'instance_id');
        $deptids   = array_merge(
            array_column($advisor_roles['DEPARTMENT'] ?? [], 'instance_id'),
            array_column($advisor_roles['DEPT'] ?? [], 'instance_id')
        );

        switch ($unit_type) {
            case 'CAMPUS':
                return in_array($unit_id, $campusids, false);

            case 'UNIT':
                if (in_array($unit_id, $unitids, false)) {
                    return true;
                }
                // Campus admins inherit access to all units within their campus.
                if (!empty($campusids)) {
                    [$insql, $inparams] = $DB->get_in_or_equal($campusids, SQL_PARAMS_NAMED, 'ucid');
                    if ($DB->record_exists_select('local_organization_unit', "id = :uid AND campus_id $insql",
                            array_merge(['uid' => $unit_id], $inparams))) {
                        return true;
                    }
                }
                return false;

            case 'DEPT':
                if (in_array($unit_id, $deptids, false)) {
                    return true;
                }
                // Unit admins inherit access to departments within their unit.
                if (!empty($unitids)) {
                    [$insql, $inparams] = $DB->get_in_or_equal($unitids, SQL_PARAMS_NAMED, 'duid');
                    if ($DB->record_exists_select('local_organization_dept', "id = :did AND unit_id $insql",
                            array_merge(['did' => $unit_id], $inparams))) {
                        return true;
                    }
                }
                // Campus admins inherit access to departments via campus → unit chain.
                if (!empty($campusids)) {
                    $dept_unit_id = (int) $DB->get_field('local_organization_dept', 'unit_id', ['id' => $unit_id]);
                    if ($dept_unit_id) {
                        [$insql, $inparams] = $DB->get_in_or_equal($campusids, SQL_PARAMS_NAMED, 'dcid');
                        if ($DB->record_exists_select('local_organization_unit', "id = :duid AND campus_id $insql",
                                array_merge(['duid' => $dept_unit_id], $inparams))) {
                            return true;
                        }
                    }
                }
                return false;

            default:
                return false;
        }
    }

    public static function get_unit_value_from_template_data($formdata) {
        global $DB;

        // CK OCT25: DEPRECATED Departments and Major
        if (!empty($formdata->department)) {
            $sql = "SELECT d.id FROM {local_organization_dept} d
                    JOIN {local_organization_unit} u ON u.id = d.unit_id
                    JOIN {local_organization_campus} c ON c.id = u.campus_id
                    WHERE d.name = :department
                      AND u.shortname = :faculty
                      AND c.shortname = :campus";
            $unit_id = $DB->get_field_sql($sql, ['department' => $formdata->department, 'faculty' => $formdata->faculty, 'campus' => $formdata->campus]);
            if ($unit_id) {
                return $unit_id . '_DEPT';
            }
        } else if (!empty($formdata->faculty)) {
            $sql = "SELECT u.id FROM {local_organization_unit} u
                    JOIN {local_organization_campus} c ON c.id = u.campus_id
                    WHERE u.shortname = :faculty
                      AND c.shortname = :campus";
            $unit_id = $DB->get_field_sql($sql, ['faculty' => $formdata->faculty, 'campus' => $formdata->campus]);
            if ($unit_id) {
                return $unit_id . '_UNIT';
            }
        } else if (!empty($formdata->campus)) {
            $unit_id = $DB->get_field('local_organization_campus', 'id', ['shortname' => $formdata->campus]);
            if ($unit_id) {
                return $unit_id . '_CAMPUS';
            }
        }
        return null;
    }
}
