<div align="center">
	<h1>TP Vue Admin</h1>
</div>

[![license](https://img.shields.io/badge/license-MIT-green.svg)](./LICENSE) ![](https://img.shields.io/github/stars/KoeyZeng/tp-vue-admin)

## ✨ 简介

使用`vue`,`vue-element-admin`,`TP6`等主流技术开发的开箱即用的后台管理系统，基于`vue-element-admin`搭间前端部分，用原生态的`TP6`技术搭建后台 API 和数据库，快速搭建企业级中后台产品原型。
<br>server 目录，存放后台代码，基于 TP6 目录结构
<br>web 目录，存放前端代码，基于 vue-element-admin 目录结构

## 🚀 项目演示

[演示地址](http://119.91.225.224:8088/admin)
![image](https://github.com/KoeyZeng/tp-vue-admin/server/public/static/images/demo.jpg)

## 📦 安装

### 后台 API 和数据库安装和搭建

- 获取项目代码

```bash

git clone https://github.com/KoeyZeng/tp-vue-admin.git
```

- cd 命令进入 server 目录

```

cd server

```

- 使用 composer 安装依赖

```
composer install
```

- 创建数据库并导入数据
- 进入 server/ 目录下找到 cms.sql,
  <br>创建数据库表并把 cms.sql 数据文件导入到数据库中
  <br>
- 复制 server/.example.env 为 server/.env， 修改 server/.env 的相关配置<br>
- 相关配置问题可以查看 TP6.0 官方文档
  <br> -测试运行

```

php think run

```

- 也可以自己部署服务器，入口文件指向/server/public/中，
  如配置域名为 `http://www.xxxx.com/`

配置 URL 访问，主要是隐藏/index.php 入口文件
原来的访问 URL：
http://serverName/index.php/模块/控制器/操作/[参数名/参数值...]
设置后，我们可以采用下面的方式访问：
http://serverName/模块/控制器/操作/[参数名/参数值...]
参考
https://www.kancloud.cn/manual/thinkphp6_0/1037488

### 后台 PHP 文档

[完全开发手册](https://www.kancloud.cn/manual/thinkphp6_0/content) #注：后台配置详情可以看 server/README.md 文件

## 后台前端环境安装和配置

- 进入 web 目录

```

cd web

```

- 安装依赖

```

npm install

```

- 建议不要直接使用 cnpm 安装依赖，会有各种诡异的 bug。可以通过如下操作解决 npm 下载速度慢的问题

- 可以使用淘宝镜像

```

npm install --registry=https://registry.npm.taobao.org

```

- 启动服务

测试服务器管理后台请求地址在 vue.config.js 中修改 target: `http://www.xxxx.com/`,

```

npm run dev

```

- 后台前端打包

在.env.production 中修改 VUE_APP_BASE_API(打包后，管理后台请求地址)
如：VUE_APP_BASE_API = 'https://www.xxxx.com/'
打包后文件会保存到 web/dist 目录中

```

npm run build

```

- 在 vue.config.js 中修改 publicPath: `/`, 是前端网站保存的目录路径
  把它设置为`/admin`

```

npm run build:node

```

- 打包后文件会保存到 web/dist 目录中,并自动保存到 server/public/admin 中

后台前端直接访问 `http://www.xxxx.com/admin` 即可
注：前端配置详情可以看 web/README.zh-CN.md 文件
