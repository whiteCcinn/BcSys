# BcSys

API-Swoole开发框架.
# 特点
1. 使用了Swoole进行服务器的开发,常驻内存，结合Swoole的特点,常态信息进行内存常驻
2. Mysql结合PDO和数据库连接池的特点，减少数据库IO操作，并且增加了Mysql连接心跳包检测机制
3. Redis结合了phpredis的扩展和Mysql一样，实现了连接池的特性
4. 控制器支持回调函数的写法，也支持回调函数+协程的方式写法
5. 实现了sh脚本启动服务
6. sql执行错误异常log追踪日志
7. 采用了动态依赖注入的写法,结合IOC/DI的思想对象统一管理
8. 支持异步Mysql连接客户端 --ps:阻塞同步业务不建议使用,导致数据无法同步
# 说明
- 禁用了define等污染环境的函数系统变量的使用，所有的变量都其特定的命名空间
- 使用了全局变量桶来存储全局变量
# 启动例子
```shell
chmod 777 Bcsys/sh/bc
Bcsys/sh/bc start
```
<<<<<<< HEAD
# nginx配置例子
=======

# nginx例子
>>>>>>> 5de9aecfc59ec00b94a96a6c109d5935d030b583

```
server {
    listen       80;
<<<<<<< HEAD
    server_name  music.crazylaw.cn;

	// 如果要用到html而非API形式的时候，需要指定root路径，否则会直接进入rewrite模式
    root /wwwroot/share/music;
    index  index.html index.htm index.php;

    // 由于路由机制，必须配置重写路径
    if (!-e $request_filename) {
       rewrite ^/(.*)  /index.php?$1 last;
    }
=======
    server_name  yourserver_name;


    index  index.html index.htm index.php;

   # 路由机制必须加上这一块
   if (!-e $request_filename) {
      rewrite ^/(.*)  /index.php?$1 last;
   }
>>>>>>> 5de9aecfc59ec00b94a96a6c109d5935d030b583


    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }

<<<<<<< HEAD
    # 此处端口号为：9555，可以自定义设置端口
=======
    # 代理端口可进行修改
    
>>>>>>> 5de9aecfc59ec00b94a96a6c109d5935d030b583
    location ~ \.php$ {
             proxy_pass http://127.0.0.1:9555$request_uri;
             proxy_http_version 1.1;
             proxy_set_header Connection "keep-alive";
             proxy_set_header X-Real-IP $remote_addr;
             fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
             include fastcgi_params;
    }
<<<<<<< HEAD
}

```
=======

```
>>>>>>> 5de9aecfc59ec00b94a96a6c109d5935d030b583
