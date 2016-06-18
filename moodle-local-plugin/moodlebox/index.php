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
 * Main file
 *
 * @package    local
 * @subpackage moodlebox
 * @copyright  2016 Nicolas Martignoni
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/formslib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('local_moodlebox', '', null);

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$strheading = get_string('pluginname', 'local_moodlebox');
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

exec('uname -srm', $kernelversion);
exec('hostnamectl | grep \'Operating System\' | cut -d\':\' -f2', $raspbianversion);
$cpuload = sys_getloadavg();
exec('cat /var/lib/misc/dnsmasq.leases', $leases);
$dhcpclientnumber = count($leases);
exec('awk \'{print $1/1000" °C"}\' /sys/class/thermal/thermal_zone0/temp', $cputemperature);
exec('awk \'{print $1/1000" Mhz"}\' /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq', $cpufrequency);
exec('uptime -p', $uptime);

class restartshutdown_form extends moodleform {
  public function definition() {
    $mform = $this->_form;
    $buttonarray = array();
    $buttonarray[] = & $mform->createElement('submit', 'restartbutton',
                                              get_string('restart', 'local_moodlebox'));
    $buttonarray[] = & $mform->createElement('submit', 'shutdownbutton',
                                              get_string('shutdown', 'local_moodlebox'));
    $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    $mform->closeHeaderBefore('buttonar');
  }
}

echo $OUTPUT->header();

// System information section
echo $OUTPUT->heading(get_string('systeminfo', 'local_moodlebox'));
echo $OUTPUT->box_start('generalbox');

echo '<p>' . get_string('kernelversion', 'local_moodlebox') . ' ' . $kernelversion[0] . '</p>';
echo '<p>' . get_string('raspbianversion', 'local_moodlebox') . ' ' . $raspbianversion[0] . '</p>';
echo '<p>' . get_string('cpuload', 'local_moodlebox') . ' ('. $cpuload[0] . ', ' . $cpuload[1] . ', ' . $cpuload[2] . ')</p>';
echo '<p>' . get_string('cputemperature', 'local_moodlebox') . ' '. $cputemperature[0] . '</p>';
echo '<p>' . get_string('cpufrequency', 'local_moodlebox') . ' '. $cpufrequency[0] . '</p>';
echo '<p>' . get_string('uptime', 'local_moodlebox') . ' '. $uptime[0] . '</p>';
echo '<p>' . get_string('dhcpclientnumber', 'local_moodlebox') . ' ' . $dhcpclientnumber . '</p>';
if ($dhcpclientnumber > 0) {
  echo '<ul>';
  foreach($leases as $row) {
    $item = explode(' ', $row);
    echo '<li>' . $item[2] . ' : ' . $item[3] . '</li>';
  }
  echo '</ul>';
}
echo $OUTPUT->box_end();

// Restart-shutdown section
echo $OUTPUT->heading(get_string('restartstop', 'local_moodlebox'));
echo $OUTPUT->box_start('generalbox');

$restartshutdownform = new restartshutdown_form();
$restartshutdownform->display();

if ($data = $restartshutdownform->get_data()) {
// see http://stackoverflow.com/questions/5226728/how-to-shutdown-ubuntu-with-exec-php
  if (!empty($data->restart)) {
    $file = fopen('.reboot-server','w');
    fwrite($file, 'Reboot now');
    fclose($file);
    echo '<div class="alert alert-block">' . get_string('restartmessage', 'local_moodlebox') . '</div>';
  }
  if (!empty($data->shutdown)) {
    $file = fopen('.shutdown-server','w');
    fwrite($file, 'Shutdown now');
    fclose($file);
    echo '<div class="alert alert-block">' . get_string('shutdownmessage', 'local_moodlebox') . '</div>';
  }
}

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
