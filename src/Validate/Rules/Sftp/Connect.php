<?php
/**
 * Jaeger
 *
 * @copyright	Copyright (c) 2015-2016, mithra62
 * @link		http://jaeger-app.com
 * @version		1.0
 * @filesource 	./Validate/Rules/Sftp/Connect.php
 */
namespace JaegerApp\Validate\Rules\Sftp;

use JaegerApp\Validate\AbstractRule;
use JaegerApp\Remote;
use JaegerApp\Remote\Sftp;

/**
 * Jaeger - FTP Connection Validation Rule
 *
 * Validates that a given credential set is accurate and working for connecting to an FTP site
 *
 * @package Validate\Rules\Ftp
 * @author Eric Lamb <eric@mithra62.com>
 */
class Connect extends AbstractRule
{

    /**
     * The Rule shortname
     * 
     * @var string
     */
    protected $name = 'sftp_connect';

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
            if (empty($params['sftp_host']) || empty($params['sftp_port']) || empty($params['sftp_root'])) {
                return false;
            }
            
            // we require either a private key file OR a username and password
            if ((empty($params['sftp_password']) && empty($params['sftp_username'])) && empty($params['sftp_private_key'])) {
                return false;
            }
            
            $filesystem = new Remote(Sftp::getRemoteClient($params));
            $return = true;
            $filesystem->getAdapter()->listContents();
            if (! $filesystem->getAdapter()->isConnected()) {
                $return = false;
            }
            
            $filesystem->getAdapter()->disconnect();
            return $return;       
            
        } catch (\Exception $e) {
            return false;
        }
    }
}

$rule = new Connect;
\JaegerApp\Validate::addrule($rule->getName(), array($rule, 'validate'), $rule->getErrorMessage());
