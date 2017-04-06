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
# 启动例子
```shell
chmod 777 Bcsys/sh/bc
Bcsys/sh/bc start
```
