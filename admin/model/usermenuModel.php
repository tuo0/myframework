<?php
namespace admin\model;

use core\lib\model;

class usermenuModel extends model{
    public $table = 'admin_usermenu';
    
    /**
     * 检查用户是否有权限访问菜单
     * @param int $userid 用户ID
     * @param String $control 控制器名
     * @param String $action 方法名
     * @return Mixed 如果有权限返回菜单详情，无权限重新登录返回0，无权限 返回首页返回 －1
     */
    public function checkMenuAccess($userid,$control,$action){
        
        //如果已经登录，再次打开 登录页，则跳转到主页
        if( $control =='default' && in_array($action,array('login')) && $userid != -1 ){
            return -1;
        }
        
    	/*
    	SELECT * FROM admin_usermenu as um
    	INNER JOIN admin_user as au  ON find_in_set('*',um.allowusergroup) OR find_in_set('1',um.allowusergroup)
    	WHERE um.control='default' AND um.action='index';
    	*/
    	$control = $this->quote($control);
    	$action  = $this->quote($action);
    	$filed = 'um.title,um.descript,um.control,um.action,um.is_enable,um.rec_log';
    	
    	$result = null;
    	//用户未登陆
    	if( -1 == $userid ){
    		//检查菜单是否可任何人查看
    		$sql = "SELECT {$filed} FROM {$this->table} as um WHERE find_in_set('*',um.allowusergroup)  AND um.control={$control} AND um.action={$action} Limit 1" ;
    		
    	    if( ( $statement = $this->query( $sql ) ) != false ){
    		    $result = $statement -> fetch(\PDO::FETCH_ASSOC);
    		}
    	}else{
    	    //检查 用户 是否有权限查看菜单
    		$userid  = $this->quote( $userid );

    		$sql="SELECT {$filed} FROM {$this->table} as um INNER JOIN admin_user as au ON find_in_set('*',um.allowusergroup) OR find_in_set(au.groupid,um.allowusergroup) WHERE um.control={$control} AND um.action={$action} AND au.userid={$userid} Limit 1";
    		
    		if( ( $statement = $this->query( $sql ) ) != false ){
    		    $result = $statement -> fetch(\PDO::FETCH_ASSOC);
    		}
    	}
    	
    	if( empty($result) ){
    	    if( $userid != -1 ){
    	       //菜单不存
                return -1;
    	    }else{
    	       //重新登录
    	       return 0;
    	    }
    	}else{
    		if( $result['is_enable']!=1 && $userid != -1 ){
    		    //没有权限，跳转首页
    			return -1;
    		}
    	}
    	return $result;
    }
    
    /**
     * 检查 menu 状态
     * @param String $control  控制器名称
     * @param String $action   方法名称
     * @return int 1：存在 ， -1：不存在，-2：菜单关闭
     */
    public function checkMenuExist($control,$action){
        $control = $this->quote( $control );
        $action  = $this->quote( $action );
        
        $sql = "SELECT is_enable FROM {$this->table} WHERE `control` = {$control} AND `action` = {$action} Limit 1";
        
        $result = false;
        
        if( ( $statement = $this->query( $sql ) ) != false ){
            $result = $statement -> fetch();
        }
        
        if($result != false){
            if( $result['is_enable'] == 1 ){
                //菜单正常 返回1
                return 1;
            }else{
                //菜单被禁用，返回-2
                return -2;
            }
        }else{
            //菜单不存在
            return -1;
        }
    }
    
    /**
     * 获取所有菜单列表
     */
    public function getMenuList(){
        
        $sql = "SELECT * FROM {$this->table} WHERE parentid = 0";
        
        $result = array();
        
        if( ( $statement = $this->query( $sql ) ) != false ){
            $result = $statement -> fetchAll();
        }
        
        return $result;
    }
    
    /**
     * 获取菜单详情
     * @param int $menuid 菜单ID
     * @return Array 菜单详情
     */
    public function getOne( $menuid ){
        $menuid = $this->quote($menuid);
        
        $sql = "SELECT `menuid`,`parentid`,`parentstr`,`title`,`descript`,`control`,`action`,`is_enable`,`allowusergroup`,`rec_log`,`sort`,`is_show` FROM {$this->table} WHERE menuid = {$menuid} LIMIT 1";
        
        $result = array();
        
        if( ( $statement = $this->query( $sql ) ) != false ){
            $result = $statement -> fetch( \PDO::FETCH_ASSOC );
        }
        
        return $result;
    }
    
    /**
     * 更新菜单详情
     * @param int $menuid   菜单ID
     * @param array $data   需要更新的字段、
     * @return int          受影响的行数
     */
    public function updateMenu( $menuid , $data ){
        
        return $this->update( $this->table , $data , [ 'menuid' => $menuid ] );
        
    }
}