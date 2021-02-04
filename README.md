# 用于小票打印，目前适用于Window（Windows 7、Windows 10）
    是一个小票打印的Api服务，使用php的mike42/escpos-php库。
    内容部包含一个独立解压版的php7.3.4,所有不需另行安装php环境

# 使用方法

## 安装服务
- 下载源码的zip包
- 解压源码包
- 运行install.bat

## 打印机配置页（或使用下面的接口调用进行配置）
- 安装后，会自动启动服务，并打开打印配置页面
- 打印配置页URL : http://localhost:8077/printer-setting
![alt](./printer-setting.png)
![alt](./printer-test.jpg)

- 配置页说明
```
在操作之前，请先在安装好小票打印机及驱动。

1.添加打印机：选择安装的小票打印机，点击“+”号
如果列表内为空，则新添加的打印机会成为默认打印机。
注意:“虚拟打印机”是无法添加的

2.点击打印测试页，会跳转到预览页，点击右侧，打印按钮。
```

## 启动服务
    运行:printer-start.bat

## 停止服务
    运行:printer-stop.bat

## 从接口进行配置打印机
- 获取所有打印机
```
GET http://localhost:8077/api/printer/get-local-printers
```


- 配置添加的小票打印机
```$xslt
POST http://localhost:8077/api/printer/set-printer
参数：JSON
{"printer_name":"POS-58","is_default":true}
```

## 打印小票

- 因为各种小票打印机支持的功能差异太大，所有就只实现图片打印，市场上80%的小票打印机都支持图片。
- 测试页就是使用html转canvas图片进行打印的

```
POST http://localhost:8077/api/job/print-image-data-url
参数：JSON
printer_name: 可以不传或为空，则使用默认打印机
{"printer_name":"POS-58","image_data":"data:image/png;base64,..."}
```


## 启动项目
运行：start.bat




