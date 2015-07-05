<?php
/**
 * File: log.class.php
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-6-23
 * Time: 下午8:08
 */
define('LOG_PATH', dirname(dirname(__FILE__)) . '/logs/');


/**
 * Logging Class
 * @subpackage    Libraries
 * @category    Logging
 * @link
 */
class Logs
{

    private $log_path = LOG_PATH;
    private $_threshold = 5;
    private $_date_fmt = 'Y-m-d H:i:s';
    private $_enabled = TRUE;
    private $_levels = array('ERROR' => '1', 'DEBUG' => '2', 'INFO' => '3', 'ACCESS' => '4', 'ALL' => '5');
    public $error;

    /**
     * Constructor
     *
     * @access    public
     */
    function __construct()
    {
        if (!is_dir($this->log_path)) {
            if (!mkdir($this->log_path))
                $this->_enabled = FALSE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Write Log File
     *
     * @access    public
     * @param    string    the error level
     * @param    string    the error message
     * @param    bool    whether the error is a native PHP error
     * @return    bool
     */
    function write_log($msg, $level = 'ERROR')
    {
        if ($this->_enabled === FALSE) {
            $levels = '';
            foreach ($this->_levels as $level)
                $levels .= $level . ',';
            $this->error = 'no permission to this path：' . $this->log_path;
            $this->error .= 'or no such level log, only support one of these: ' . rtrim($levels, ',');
            return FALSE;
        }

        $level = strtoupper($level);

        if (!isset($this->_levels[$level]) || ($this->_levels[$level] > $this->_threshold)) {
            $this->error = 'level threshold bigger than max：' . $this->_threshold;
            return FALSE;
        }

        $filepath = $this->log_path . $level . '-' . date('Y-m-d') . '.log';
        $message = '';

        if (!$fp = fopen($filepath, 'ab')) {
            $this->error = 'can not open file：' . $filepath;
            return FALSE;
        }

        $message .= $level . ' - ' . date($this->_date_fmt) . ' --> ' . $msg . "\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        chmod($filepath, FILE_WRITE_MODE);

        return TRUE;
    }

}