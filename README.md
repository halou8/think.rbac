TP5.x - 管理系统
======

[ 介绍 ]

    这是一套使用ThinkPHP5.X开发的基础系统，包含后台用户权限控制，
	后台用户分组管理、网站系统配置功能，可用他来衍生各种产品。

[ 安装方法 ]

    1 创建MYSQL数据库，导入 cms.sql
    2 把根目录下的/core/config/database.php.bak文件名改成database.php
	3 根据你的数据库，配置database.php “hostname, database, username, password, hostport”
	4 后台入口 http://domain/admin
	5 后台帐号密码 admin admin
	6 后台菜单设置方法请参考已有的那些菜单
	7 设置public目录为web可访问目录，参考thinkphp5官方配置
```
[ 目录结构 ]
    project                		应用部署目录
    ├─core                 		系统核心
    │  ├─app               		应用目录
    │  │  ├─admin        		后台模块
    │  │  │  ├─controller   		控制器目录
    │  │  │  ├─model        		模型目录    
    │  │  │  ├─lang         		语言包目录   
    │  │  │  ├─common.php               后台函数文件     
    │  │  ├─common        		公共模块目录    
    │  │  ├─home            		前台模块    
    │  │  ├─more...            		自定义添加模块            
    │  │  ├─common.php      		项目公共函数文件
    │  ├─config                      配置目录
    │  │  ├─admin        		后台配置目录
    │  │  ├─home        		前台配置目录
    │  │  ├─database.php    		数据库配置文件
    │  │  ├─define.php      		项目路径常量配置文件
    │  ├─extend            		扩展类库目录
    │  ├─config.php          		网站配置文件
    ├─public              		WEB 部署目录（对外访问目录）
    │  ├─static          		静态资源存放目录(css,js,image)
    │  ├─index.php       		系统入口文件
    ├─runtime             		系统缓存目录
    ├─template            		项目模板目录
    │  ├─admin               		后台模板目录
    │  ├─home                		前台模板目录
    ├─cms.sql             		数据库文件
```
[ 协议 ]

    本系统除ThinkPHP框架外，遵循MIT开源许可协议发布
	具体参考LICENSE.txt内容

## (thinkphp3.x RBAC)  : [https://github.com/chenbei360/tpcms/] 后期会推出 LV.x RBAC YII.x RBAC

#### 官方QQ群：192979528 小助手微信 **xunshang100**


