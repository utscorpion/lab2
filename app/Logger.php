<?php
include_once 'config.php';

class Logger
{
    protected static $loggers=array();

    protected $name;
    protected $file;
    protected $fp;
    protected static $logPath;

    public function __construct($name, $file=null){
        $this->name=$name;
        $this->file=$file;
        self::$logPath = Configurator::PATH;
        $this->open();
    }

    public function open(){
        if(self::$logPath==null){
            return ;
        }

        $this->fp=fopen($this->file==null ? self::$logPath.'/'.$this->name.'.log' : self::$logPath.'/'.$this->file,'a+');
    }

    public static function getLogger($name='root',$file=null){
        if(!isset(self::$loggers[$name])){
            self::$loggers[$name]=new Logger($name, $file);
        }

        return self::$loggers[$name];
    }

    public function log($message){
        if(!is_string($message)){
            $this->logPrint($message);

            return ;
        }

        $log='';

        $log.='['.date('D M d H:i:s Y',time()).'] ';
        if(func_num_args()>1){
            $params=func_get_args();

            $message=call_user_func_array('sprintf',$params);
        }

        $log.=$message;
        $log.="\n";

        $this->_write($log);
    }

    public function logPrint($obj){
        ob_start();

        print_r($obj);

        $ob=ob_get_clean();
        $this->log($ob);
    }

    protected function _write($string){
        fwrite($this->fp, $string);

        echo $string;
    }

    public function __destruct(){
        fclose($this->fp);
    }
}