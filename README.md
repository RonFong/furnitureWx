### furniture 目录部署

~~~
├─application                 应用目录
│  ├─common                   公共/父类
│  │  ├─controller            控制器目录
│  │  |  └─Controller.php     控制器总父类
│  │  ├─db                    query 扩展
│  │  |  └─Query.php          
│  │  ├─model                 模型目录
│  │  |  ├─Model.php          模型类总父类
│  │  |  └─User.php           User模型 父类
│  │  ├─validate              验证类
│  │  |  ├─BaseValidarte.php  验证类总父类
│  │  |  └─User.php           User 表模型
│  |  ├─tags.php              行为定义文件
│  │  └─behavior              行为钩子类
│  │
│  ├─admin                    后台
│  │  ├─controller            控制器目录
│  │  |  └─BaseController.php 控制器父类
│  |  ├─model                 数据库操作模型
│  │  |  ├─BaseModel.php      模型父类
│  │  |  └─User.php           User 表模型
│  │  ├─service               业务逻辑处理层
│  │  |  └─User.php           User业务处理
│  │  ├─view                  视图
│  │  ├─validate              验证类
│  |  ├─tags.php              行为定义文件
│  │  └─behavior              行为钩子类
│  │
│  ├─api                      API接口模块
│  │  ├─controller            控制器目录
│  │  |  ├─v1                 api 版本
|  │  │  |  └─User.php        api 控制器 （接口访问地址）
│  │  |  └─BaseController.php api 控制器父类
│  |  ├─model                 数据库操作模型
│  │  |  ├─BaseModel.php      api 模型父类
│  │  |  └─User.php           User 表模型
│  │  ├─service               业务逻辑处理层
│  │  |  └─User.php           User业务处理
│  │  ├─validate              验证类│  
│  |  ├─tags.php              行为定义文件
│  │  └─behavior              行为钩子类
│  │
│  ├─service                  服务类
│  │
│  ├─extra                    自定义配置参数 
│  |  ├─property.php          商品属性参数配置
│  │  └─wx.php                小程序参数配置
│  │
│  │
│  ├─lib              
│  |  ├─enum                              枚举数据
│  │  |  └─Response.php                   http响应数据枚举
│  │  └─exception        
│  │  |  ├─BaseException.php              异常响应处理类   
│  │  |  └─ExceptionHandler.php           异常响应处理基类 
│  │
│  │
│  ├─extend             自定义数据处理方法 
│  │  └─Tree.php        数据集转树形结构
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
~~~

