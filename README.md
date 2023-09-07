## 简介

**TP Element Admin** 是一个免费开源的中后台模版。基于`vue`+`Element`+`TP6`开发，是一个开箱即用的后台管理系统，它可以帮助你快速搭建企业级中后台产品原型，也可用于学习参考。

# 技术栈

TP6.0
element-ui
vue-element-admin 框架

# 管理系统

1、cd 命令进入 server 目录

## 后台 API 和数据库安装和搭建

```
cd server
```

```
composer install
```

2、cd 进入 server/ 目录下,
创建数据库表并把 cms.sql 数据文件导入到数据库中

3、复制 server/.example.env 为 server/.env， 修改 server/.env 的相关配置
相关配置问题可以查看 TP6.0 官方文档

# 测试运行

```
php think run
```

4、也可以自己部署服务器，入口文件指向/server/public/中，
如配置域名为 `http://www.xxxx.com/`

配置 URL 访问，主要是隐藏/index.php 入口文件
原来的访问 URL：
http://serverName/index.php/模块/控制器/操作/[参数名/参数值...]
设置后，我们可以采用下面的方式访问：
http://serverName/模块/控制器/操作/[参数名/参数值...]
参考
https://www.kancloud.cn/manual/thinkphp5_1/353955

## 后台 PHP 文档

[完全开发手册](https://www.kancloud.cn/manual/thinkphp6_0/content) #注：后台配置详情可以看 server/README.md 文件

## 后台前端环境安装和配置

```
cd web
```

## 安装依赖

```
npm install
```

建议不要直接使用 cnpm 安装依赖，会有各种诡异的 bug。可以通过如下操作解决 npm 下载速度慢的问题

## 可以使用淘宝镜像

```
npm install --registry=https://registry.npm.taobao.org
```

## 启动服务

测试服务器管理后台请求地址在 vue.config.js 中修改 target: `http://www.xxxx.com/`,

```
npm run dev
```

## 后台前端打包

在.env.production 中修改 VUE_APP_BASE_API(打包后，管理后台请求地址)
如：VUE_APP_BASE_API = 'https://www.xxxx.com/'
打包后文件会保存到 web/dist 目录中

```
npm run build
```

在 vue.config.js 中修改 publicPath: `/`, 是前端网站保存的目录路径
把它设置为`/admin`

```
npm run build:node
```

打包后文件会保存到 web/dist 目录中,并自动保存到 server/public/admin 中

后台前端直接访问 `http://www.xxxx.com/admin` 即可
注：前端配置详情可以看 web/README.zh-CN.md 文件
