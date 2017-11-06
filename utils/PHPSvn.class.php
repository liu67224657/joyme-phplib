<?php

namespace Joyme\utils;

class PHPSvn {

    /**
     * svn command options
     * 
     * @var array
     */
    private $opts = array(
        'username' => '',
        'password' => '',
        'no-auth-cache' => ' ',
        'config-dir' => '', ///usr/home/finance/.subversion
    );

    /**
     * error log file
     * 
     * will not save data in this file
     * just a tmp file store tmp error message
     * @var string
     */
    private $errorfile = '/tmp/phpsvn.err';

    /**
     * error message
     * 
     * if no error, error = ''
     * @var string
     */
    private $error = '';

    /**
     * the value to return
     * 
     * @var array
     */
    private $retvalue = array();

    /**
     * svn path
     * 
     * @var string
     */
    private $svnpath = '';

    /**
     * local file path where file stored
     * 
     * @var string
     */
    private $targetpath = '.';

    /**
     * actions tags
     * 
     * @var array
     */
    private $shorttags = array('a', 'u', 'd');

    /**
     * set opts of svn command
     * 
     * allow opts:
     * username : your svn username
     * password : your svn password
     * config-dir : your execute user config dir
     * @param array $options
     */
    public function setoptions($options = array()) {
        $this->opts = array_merge($this->opts, $options);
    }

    /**
     * set svn path & localpath
     * 
     * @param string $svnpath svn path
     * @param string $targetpath local path
     * @return void
     */
    public function setpath($svnpath, $targetpath = '.') {
        $this->svnpath = $svnpath;
        $this->targetpath = realpath($targetpath);
    }

    /**
     * update from server
     * 
     * @return mixed array on success or false on error
     */
    public function update() {
        return $this->docmd('up');
    }

    /**
     * commit file to server
     * 
     * @return mixed array on success or false on error
     */
    public function commit() {
        return $this->docmd('ci', '-m "phpphtch"');
    }

    /**
     * add file to svn
     * 
     * @param string $file filename
     * @return mixed array on success or false on error
     */
    public function add($file) {
        return $this->docmd('add', $file);
    }

    /**
     * chectout file from svn to local
     * 
     * @return mixed array on success or false on error
     */
    public function checkout() {
        return $this->docmd('co', $this->svnpath);
        //checked out revision 240772
    }

    /**
     * execute command for svn
     * 
     * support commands:add/checkout(co)/cleanup/commit(ci)/copy(cp)/delete(del,remove,rm)/diff(di)/update (up)
     * todo commands:export
     * help (?, h)
     * import
     * info
     * list (ls)
     * lock
     * log
     * merge
     * mkdir
     * move (mv, rename, ren)
     * propdel (pdel, pd)
     * propedit (pedit, pe)
     * propget (pget, pg)
     * proplist (plist, pl)
     * propset (pset, ps)
     * resolved
     * revert
     * status (stat, st)
     * switch (sw)
     * @param string $cmd
     * @param string $param
     */
    public function docmd($cmd, $param = '') {
        chdir($this->targetpath);
        $cmd = "{$cmd} {$param} ";
        $result = $this->shell($cmd);
        return $this->result($result);
    }

    /**
     * error message last time
     * 
     * @return string error message
     */
    public function error() {
        return $this->error;
    }

    /**
     * format the result handle
     * 
     * @param string $result result string
     * @return string
     */
    private function result($result) {
        if ($result === false) {
            return false;
        }
        foreach (explode("\n", $result) as $line) {
            $line = trim($line);
            $this->retline($line);
        }
        return $this->retvalue;
    }

    private function retline($line) {
        $line = strtolower($line);
        if (empty($line)) {
            return;
        }
        $retvalue = array();
        if (in_array($line[0], $this->shorttags)) {
            $retvalue['a'] = $line[0];
            $retvalue['v'] = trim(substr($line, 2));
        } else {
            preg_match('/([0-9]+)/', $line, $match);
            $num = intval(empty($match[0]) ? 0 : $match[0]);
            if ($num > 0) {
                $retvalue['a'] = 'v';
                $retvalue['v'] = $num;
            }
        }
        $this->retvalue[] = $retvalue;
    }

    /**
     * get svn file version from result line
     * 
     * @param string $line result line
     * @return mixed version number or false if on error
     */
    private function getversionbyline($line) {
        $line = trim(strtolower($line));
        if (preg_match('/([0-9]+)/', $line, $match)) {
            return $match[0];
        }
        return false;
    }

    /**
     * exec shell command
     * 
     * @access private
     * @param string $cmd command to be executed
     * @return string result string should been displayed on stdout, 
     * @return return false if on error
     */
    private function shell($cmd) {
        $opts = '';
        foreach ($this->opts as $key => $item) {
            if (!empty($item)) {
                $opts .= "--{$key} {$item} ";
            }
        }
        $result = @shell_exec("svn {$opts}" . $cmd . ' 2> ' . $this->errorfile);
        if ($this->isresulterror()) {
            return false;
        }
        return $result;
    }

    /**
     * check if on error
     * 
     * @param string $result shell result string
     * @return boolen
     */
    private function isresulterror() {
        $this->error = file_get_contents($this->errorfile);
        if (empty($this->error)) {
            return false;
        }
        return true;
    }

}

?>