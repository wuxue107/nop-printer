# 用于WEB打印

## 启动
start.bat

## 获取所有打印机
GET http://localhost:8000/api/printer/get-local-printers

## 设置默认的WEB打印机
POST http://localhost:8000/api/printer/set-default-printer
参数：JSON
{"printer_name":"POS-58""}

## 打印一张图片
POST http://localhost:8000/api/job/print-image-data-url
参数：JSON
{"image_data":"data:image/png;base64,..."}

