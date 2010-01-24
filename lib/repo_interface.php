<?php
require_once ('pearfarm/src/Pearfarm/PackageSpec.php');
abstract class RepoInterface {
  var $temp_location;
  var $repo_path;
  var $has_spec = false;
  public function __construct($path, $options = array()) {
  }
  public function get_file($file) {
  }
  public function get_tags() {
  }
  public function clone_repo($path) {
  }
  public function location() {
  }
  public function get_tag($tag) {
  }
  public function has_spec($tag) {
  }
  public function cleanup() {
  }
  public function pearfarm_build() {
    $tags = $this->get_tags();
    $tag = array_pop($tags);
    if ($this->has_spec($tag) == false) {
      $this->cleanup();
      return false;
    }
    if ($this->get_tag($tag)) {
      $specfile = $this->temp_location . '/pearfarm.spec';
      include $specfile;
      if (!isset($spec)) {
        $this->cleanup();
        return false;
      }
      $spec->writePackageFile();
      $pear_command = implode(' ', array('cd', $this->temp_location, '&&', 'pear', '-c', sys_get_temp_dir() . '/.pearrc', 'package'));
      $pear_add_channel = implode(' ', array('pear', '-c', sys_get_temp_dir() . '/.pearrc', 'channel-discover', $spec->getChannel()));
      exec($pear_add_channel, $out);
      exec($pear_command, $out2, $result);
      $file = $this->temp_location . "/{$spec->getName() }-{$spec->getReleaseVersion() }.tgz";
      return (file_exists($file)) ? $file : false;
    } else {
      $this->cleanup();
      return false;
    }
    $this->cleanup();
    return false;
  }
  public function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach(scandir($dir) as $item) {
      if ($item == '.' || $item == '..') continue;
      if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
  }
}
