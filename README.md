<h1 align="center"> helpers </h1>

<p align="center"> 一些开发的常用函数，方便直接在开发中使用 </p>

---

### 持续更新

> * 此仓库处于持续构建优化阶段，后续在空闲之余将会继续更新和优化内容

---

## 安装

```shell
$ composer require ihome/helpers -vvv
```

## 使用

```php
    <?php 
        // 获取10位随机字符串
        $string = getRandString(10);
        var_dump($string);
        
        // 浮点数判断大小
        $result = bcCompNumber(1.1, '<', 2.2);
        var_dump($result);// true
    ?>
```

## 具体文档请参考
[ihome/helpers 开源文档](https://xzrnoz527j.k.topthink.com/@6gp5d7y2b3/wendangshuoming.html)

## 贡献

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/ihome/helpers/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/ihome/helpers/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT