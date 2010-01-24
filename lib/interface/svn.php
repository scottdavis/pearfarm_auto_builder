<?php
require_once(__DIR__ . '/../repo_interface.php');
//test using http://svn.phpdb.org/propel/tags/
class PearfarmSvn extends RepoInterface {
	var $temp_location;
	var $repo_path;
	var $has_spec = false;
	
	public function __construct($path, $options = array()){
		$this->repo_path = $path;
		$this->temp_location = sys_get_temp_dir() . '/' . time() . '_pearfarm';
		mkdir($this->temp_location, 0755, true);
	}
	
	public function __destruct() {
	  $this->cleanup();
	}
	
	public function get_file($file) {
		return @svn_cat($file);
	}
	
	public function get_tags() {
		$tags = array_keys(svn_ls($this->repo_path . 'tags'));
		natsort($tags);
		return $tags;
	}
	
	public function clone_repo($path) {
		if(!svn_checkout($path, realpath($this->temp_location))) {
		  return false;
		}
		return true;
	}
	
	public function location() {
		return $this->temp_location;
	}
	
	public function get_tag($tag) {
	  return $this->clone_repo($this->repo_path . 'tags/' . $tag);
	}
	
	public function has_spec($tag) {
	  return $this->get_file($this->repo_path . 'tags/' . $tag . '/pearfarm.spec');
	}
	
	public function cleanup() {
	  if(is_dir($this->temp_location)) {
	    $this->deleteDirectory($this->temp_location);
    }
	}
}
