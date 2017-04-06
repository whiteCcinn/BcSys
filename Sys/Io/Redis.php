<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/22
 * Time: 10:25
 */

namespace Bc\Sys\Io;


/**
 * Class Redis
 * 1.目前配置文件支持持久化连接和短连接
 * 2.直接Host/Port连接，不支持sock文件连接
 *
 * @package Bc\Sys\Io

 * @author         caiwh<471113744@qq.com>
 * @since          1.0.0
 */
class Redis
{
  /**
   * 官方启动API：
   * [redis.sock/ip:Port]
   * 1.connect('Host','Port','TimeOut','Retry') 短连接 true or false
   * 2.pconnect('Host','Port',TimeOut)  持久化连接 true or false
   * 3.Auth('password') 校验密码 true or false
   * 4.select('db') 改变当前连接数据库 true or false
   * 5.close() 断开连接 -持久化连接的时候无效
   * 6.setOption(key,value) 设置配置
   *   Redis::OPT_SERIALIZER  Redis::SERIALIZER_NONE       不使用序列化数据
   *   Redis::OPT_SERIALIZER  Redis::SERIALIZER_PHP        用PHP的序列化函数序列化数据
   *   Redis::OPT_SERIALIZER  Redis::SERIALIZER_IGBINARY   使用igbinary序列化数据 - 需要在编译安装phpredis的进行额外的配置添加开启
   *   Redis::OPT_PREFIX      myAppName:                   key前缀
   * 7.getOption(key)  获取配置
   * 8.ping() 检查连接状态 true or exception
   * 9.echo(msg) 发送一条消息到redis，redis返回一条一样的消息
   *
   * 官方String操作API
   * 1.get(key) 获取值 string or false
   * 2.set(key,value,TimeOut = 0) 设置值,如果设置了TimeOut,则会去调用setex()设置有效时间  true or ??
   * 3.setEx(key,ttl-second,value)/pSetEx(key,ttl-millisecond,value) 设置ttl时间，setEx单位为秒，pSetEx单位为毫秒 true or ??
   * 4.setNx(key,value) 如果key不存在的时候才设置 true or false
   * 5.del/delete(key1,[key2],[key3]) 删除指定的key，如果第一个key是数组，则会循环删除数据里面的所有key，返回被删除个数  int
   * 6.exists(key)  指定key是否存在 true or false
   * 7.incr(key)/incrBy(key,+int)  incr如果不存在key的时候，默认为0，调用一次之后会变成1.incrBy按阶梯增长，返回key的值  int
   * 8.incrByFloat(key,float)  同上，只不过增长的数据为float
   * 9.decr(key)/decrBy(key,-int)  incr如果不存在key的时候，默认为0，调用一次之后会变成-1.incrBy按阶梯减少，返回key的值  int
   * 10.mGet/getMultiple(array(key,[key2]...)) 批量获取key的值,如果不存在的话,则在对面的position中填充false
   * 11.getSet(key,value) 返回当前key的值，并且随后立刻设置value
   * 12.randomKey()  从所有存在的key中随机返回一个key
   * 13.move(key,db2) 移动当前db的一个key到另外一个db2中。  true or false
   * 14.rename(key1,key2)/renameKey(key1,key2)  对key1进行重命名为key2  true or false
   * 15.renameNx(key1,key2) 如果key2，没有重复的情况下，才重命名  true or false
   * 16.expire(key,ttl-second)/setTimeout(key,ttl-second)/pexpire(key,ttl-millisecond) 设置ttl时间，注意pexpire单位是秒
   * 17.expireAt(key,ttl-second-timestamp)/pexpireAt(key,ttl-millisecond-timestamp) 设置ttl到期时间，时间戳
   * 18.keys/getKeys(match-pattern) 返回匹配到的key，'*'作为通配符
   * 19.scan(&$iterator) 返回一个迭代器，包含了所有的key，取代过多的key导致阻塞等待的时间，并且时间复杂度为O(1)
   * 20.object(encoding/refcount/idletime,key) 分别可以返回编码类型（redis内置编码类型），引用次数，空转时间，分别返回int/string or false
   * 21.type(key) 返回key的类型 \Redis::TYPE
   * 22.append(key,string) 追加字符串到原有的key后面，返回追加之后的长度 int
   * 23.getRange(key,start,end) key结果值截取 返回截取之后的字符串 string
   * 24.setRange(key,offset,value) 从offset位改变原来key结果值，返回替换之后的长度 int
   * 25.strLen(key) 返回key结果值字符串的长度
   * 26.getBit(key,offset) 返回偏移位
   * 27.setBit(key,offset,value) 设置
   * 28.ttl/pttl(key) 返回剩下生命时间（ttl-second,pttl-millisecond），-1的时候代表没有设置，-2代表key不存在
   * 29.mSet/mSetNx(array(key=>value,[key=>value])) 把批量set的操作放在一个原子性操作里面  true or false
   * 30.persist(key) 删除key的剩下生命时间，使之变成持久存在 true or false(if key does not exists ttl)
   *
   * 官方Hash操作API
   * 1.
   */
  private $setting
      = [
//          'SockFile'=>'',
//          'Host'=>'',
//          'Port'=>'',
//          'Auth'=>'',
//          ''=>'',
          'TimeOut'   => 2.5, // second
          'Retry'     => 3,
          'Serialize' => \Redis::SERIALIZER_PHP
      ];

  public $redis = null;

  private static $method = [];

  public function __construct($config = [])
  {
    $this->setting = array_merge($this->setting, $config);
    $this->connect();
  }

  private function connect()
  {
    try
    {
      $this->redis = new \Redis();

      if (isset($this->setting['pconnect']) && $this->setting['pconnect'] === 1)
      {
        $this->redis->pconnect($this->setting['Host'], $this->setting['Port'], $this->setting['TimeOut']);
      } else
      {
        $this->redis->connect($this->setting['Host'], $this->setting['Port'], $this->setting['TimeOut'], $this->setting['Retry']);
      }

      if (isset($this->setting['Auth']))
      {
        $AuthRe = $this->redis->Auth($this->setting['Auth']);
        if (!$AuthRe)
        {
          return false;
        }
      }

      if (isset($this->setting['Serialize']))
      {
        $this->redis->setOption(\Redis::OPT_SERIALIZER, $this->setting['Serialize']);
      }


      $status = $this->isConnect();
      if (!$status)
      {
        return false;
      }
    } catch (\Exception $e)
    {
      throw new \Exception($e->getMessage());
    }
  }

  private function isConnect()
  {
    try
    {
      $status = $this->redis->ping();

      if ($status === '+PONG')
      {
        unset($status);

        return true;
      }

      return false;

    } catch (\Exception $e)
    {
      throw new \Exception($e->getMessage());
    }
  }

  public function __call($name, $arguments)
  {
    if (empty(self::$method))
    {
      self::$method = get_class_methods('\Redis');
    }

    if (!in_array($name, self::$method))
    {
      throw new \Exception('Does not exists you calling function');
    }

    $result = call_user_func_array([$this->redis, $name], $arguments);

    return $result;
  }


}