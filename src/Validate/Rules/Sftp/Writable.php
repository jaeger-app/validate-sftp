<?php
/**
 * Jaeger
 *
 * @copyright	Copyright (c) 2015-2016, mithra62
 * @link		http://jaeger-app.com
 * @version		1.0
 * @filesource 	./Validate/Rules/Sftp/Writable.php
 */
namespace JaegerApp\Validate\Rules\Sftp;

use JaegerApp\Validate\AbstractRule;
use JaegerApp\Remote;
use JaegerApp\Remote\Sftp;
use JaegerApp\Remote\Local;

/**
 * Jaeger - FTP Writable Validation Rule
 *
 * Validates that a given path is writable by the supplied credentiasl directory
 *
 * @package Validate\Rules\Ftp
 * @author Eric Lamb <eric@mithra62.com>
 */
class Writable extends AbstractRule
{

    /**
     * The Rule shortname
     * 
     * @var string
     */
    protected $name = 'sftp_writable';

    /**
     * The error template
     * 
     * @var string
     */
    protected $error_message = 'Can\'t connect to {field}';

    /**
     * (non-PHPdoc)
     * 
     * @see \mithra62\Validate\RuleInterface::validate()
     * @ignore
     *
     */
    public function validate($field, $input, array $params = array())
    {
        try {
            if ($input == '' || empty($params['0'])) {
                return false;
            }
            
            $params = $params['0'];
            if (empty($params['sftp_host']) || empty($params['sftp_password']) || empty($params['sftp_username']) || empty($params['sftp_port']) || empty($params['sftp_root'])) {
                return false;
            }
            
            $local = new Remote(new Local(dirname($this->getTestFilePath())));
            $filesystem = new Remote(Sftp::getRemoteClient($params));
            
            if ($local->has($this->test_file)) {
                $contents = $local->read($this->test_file);
                
                $filesystem->getAdapter()->setRoot($params['sftp_root']);
                
                if ($filesystem->has($this->test_file)) {
                    $filesystem->delete($this->test_file);
                } else {
                    if ($filesystem->write($this->test_file, $contents)) {
                        $filesystem->delete($this->test_file);
                    }
                }
            }
            
            $filesystem->getAdapter()->disconnect();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

$rule = new Writable;
\JaegerApp\Validate::addrule($rule->getName(), array($rule, 'validate'), $rule->getErrorMessage());