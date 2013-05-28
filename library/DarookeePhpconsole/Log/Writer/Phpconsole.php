<?php

namespace DarookeePhpconsole\Log\Writer;

use Zend\Log\Writer\AbstractWriter;
use Zend\Log\Exception;
use Zend\Log\Formatter\Simple as SimpleFormatter;

class Phpconsole extends AbstractWriter {

    protected $_phpconsole = NULL;
    protected $_user = NULL;
    public $formatter = NULL;

    public function __construct($settings = array()) {
        if(empty($settings)) {
            throw new Exception\InvalidArgumentException(sprintf('All parameters have to be provided.'));
        }

        $pc = $this->getPhpConsole();
        $pc->set_domain($settings['domain']);
        $pc->add_user($settings['user'], $settings['userKey'], $settings['projectKey']);
        $pc->set_backtrace_depth((isset($settings['btlevel'])?$settings['btlevel']:4));
        $this->_user = $settings['user'];

        if($this->formatter === NULL)
            $this->formatter = new SimpleFormatter();
    }

    public function getPhpConsole() {
        if($this->_phpconsole === NULL) {
            include_once('phpconsole.lib.php');
            $this->_phpconsole = new \Phpconsole();
        }

        return $this->_phpconsole;
    }

    protected function doWrite(array $event) {
        $line = $this->formatter->format($event);
        $this->getPhpConsole()->send($line, $this->_user);
    }
}
