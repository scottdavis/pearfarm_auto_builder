<?php
require_once (__DIR__ . '/../lib/interface/svn.php');
require_once ('PHPUnit/Framework.php');
class PearfarmSvnTest extends PHPUnit_Framework_TestCase {
  const REPO = 'http://svn.phpdb.org/propel/';
  const TESTREPO = 'http://www.golemaggowns.com/svn/bobs_other_package/';
  public function loadPropel() {
    $this->svn = new PearfarmSvn(self::REPO);
    $this->tags = $this->svn->get_tags();
  }
  public function tareDown() {
    $this->svn->cleanup();
  }
  public function testGetTags() {
    $this->loadPropel();
    $this->assertTrue(is_array($this->tags));
  }
  public function testGetFile() {
    $this->loadPropel();
    $tag = array_pop($this->tags);
    $text = $this->svn->get_file(self::REPO . 'tags/' . $tag . '/INSTALL');
    $this->assertTrue(is_string($text));
  }
  public function testHasSpecFailsWithFalse() {
    $this->loadPropel();
    $tag = array_pop($this->tags);
    $this->assertFalse($this->svn->has_spec($tag));
  }
  public function testCheckoutsTagThenCleansUp() {
    $this->loadPropel();
    $tag = array_pop($this->tags);
    $this->svn->get_tag($tag);
    exec('cd ' . $this->svn->location() . '&& ls -al ', $out);
    $this->assertTrue(count($out) > 0);
    $this->svn->cleanup();
    $this->assertFalse(is_dir($this->svn->location()));
  }
  public function testPearFarmBuildsFromSvn() {
    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, 'pearfarm');
    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, 'mmm_pears');
    svn_auth_set_parameter(SVN_AUTH_PARAM_DONT_STORE_PASSWORDS, true);
    svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);
    $this->svn = new PearfarmSvn(self::TESTREPO);
    $this->assertTrue(strpos($this->svn->pearfarm_build(), '.tgz') !== false);
    $this->svn->cleanup();
  }
  public function testhasSpecFileReturnsSpec() {
    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, 'pearfarm');
    svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, 'mmm_pears');
    svn_auth_set_parameter(SVN_AUTH_PARAM_DONT_STORE_PASSWORDS, true);
    svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);
    $this->svn = new PearfarmSvn(self::TESTREPO);
    $tags = $this->svn->get_tags();
    $tag = array_pop($tags);
    $spec = $this->svn->has_spec($tag);
    $this->assertTrue(strpos($spec, '$spec') !== false);
  }
}
