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
 * Plugin library.
 *
 * @package    tool_moodlebox
 * @copyright  2017 onwards Nicolas Martignoni <nicolas@martignoni.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Parse config files with "setting=value" syntax, ignoring commented lines
 * beginnning with a hash (#).
 *
 * @param file $file to parse
 * @param bool $mode (optional)
 * @param int $scanner_mode (optional)
 * @return associative array of parameters, value
 */
function tool_moodlebox_parse_config_file($file, $mode = false, $scannermode = INI_SCANNER_NORMAL) {
    return parse_ini_string(preg_replace('/^#.*\\n/m', '', @file_get_contents($file)), $mode, $scanner_mode);
}
