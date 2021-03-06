<?php
    @header("content-type:text/html; charset=UTF-8");
    define('DS',DIRECTORY_SEPARATOR);           //分隔符
   
    define('NICK', realpath('..'));
    define('CORE',NICK.DS.'core');              //Core 核心目录
    define('APP',NICK.DS.'admin');              //APP 核心目录
    define('MODULE','admin');                   //Model 类目录名称
    
    define('DEBUG', true);                      //是否 调试模式
    define('RECORD_LOG',true);                  //是否记录日志
    
    require_once NICK.DS.'vendor'.DS.'autoload.php';
    
    if(DEBUG){
        //加载第三方异常显示插件
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
        
        error_reporting(E_ALL);
        ini_set('display_errors','On');
    }else{
        ini_set('display_errors','Off');
    }
    
    require_once  CORE.DS.'common'.DS.'function.php';
    
    require_once CORE.DS.'a.php';
    
    spl_autoload_register('\core\A::load');
    
    //自定义错误处理
    set_error_handler(function( $errno , $errstr , $errfile , $errline ){
        //
    });
    
    //初始化视图层
    $GLOBALS['oViews'] = \core\A::singleton('\core\lib\views');
    
    $GLOBALS['oViews']->assign('templatepath',DS.'admin'.DS.'views'.DS.'notebook');     //配置 资源文件 位置
    
    //SESSION初始化
    $GLOBALS['oSession'] = \core\A::singleton('\core\lib\session');
    
    //Cache缓存初始化
    $GLOBALS['oCcahce'] = \core\A::singleton('\core\lib\Cache');
    
    $config = array(
        'dispatcher' => 'admin\authdispatcher',           //调度器配置
    );
    
    \core\A::run($config);
?>