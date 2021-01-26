## 弹出打印机属性
rundll32 printui.dll ,PrintUIEntry /p /n "POS-58"

## 共享本地打印机
cscript prncnfg.vbs -t -p "POS-58" -h "POS-58" +shared

## 打印机管理script
C:\Windows\System32\Printing_Admin_Scripts\zh-CN\
## 打印相关命令参考
https://docs.microsoft.com/en-us/windows-server/administration/windows-commands/print-command-reference

## 添加网络打印机
@echo off
net use \\192.168.1.230\IPC$ "密码" /user:"用户名"
rundll32 printui.dll,PrintUIEntry /in /u /z /q /n "\\192.168.1.230\HPLaserJ1319"
rundll32 printui.dll,PrintUIEntry /y /n "\\192.168.1.230\HPLaserJ1319"

## 设为默认打印机
rundll32 printui.dll,PrintUIEntry /y /n "POS-58"

## 管理界面
printmanagement.msc

## 打印机管理命令

```text
用法: rundll32 printui.dll,PrintUIEntry [options] [@commandfile]
   /a[file] 二进制文件名
   /b[name] 基本打印机名
   /c[name] 如果操作在远程机器上，unc 机器名
   /dl 删除本地打印机
   /dn 删除网络打印机连接
   /dd 删除打印机驱动程序
   /e 显示打印首选项
   /f[file] inf 文件或输出文件
   /F[file] 使用 /f 指定的 INF 文件可能依赖的 INF 文件的位置
   /ga 添加每个机器打印机连接(用户登录时此连接将传播到用户)
   /ge 枚举每个机器打印机连接
   /gd 删除每个机器打印机连接(用户登录时此连接将被删除)
   /h[arch] 驱动程序体系结构，为下列之一: x86 或 x64 或 Itanium 
   /ia 用 INF 文件安装打印机驱动程序
   /id 用添加打印机驱动程序向导安装打印机驱动程序
   /if 用 INF 文件安装打印机
   /ii 用带 INF 文件的添加打印机向导安装打印机
   /il 用添加打印机向导安装打印机
   /im 使用添加打印机向导安装打印机(跳过网络列出的打印机)
   /in 添加网络打印机连接
   /ip 使用网络打印机安装向导安装打印机
   /j[provider] 打印提供程序名称
   /k 安装打印机时将测试页打印到指定打印机，不能与命令组合使用
   /l[path] 打印机驱动程序源路径
   /m[model] 打印机驱动程序型号名称
   /n[name] 打印机名称
   /o 显示打印机队列视图
   /p 显示打印机属性
   /q 安静模式，不显示错误消息
   /r[port] 端口名称
   /s 显示服务器属性
   /Ss 将打印机设置存储到文件
   /Sr 从文件还原打印机设置
   存储或还原必须放在命令末尾的打印机设置选项标志:
	2	PRINTER_INFO_2
	7	PRINTER_INFO_7
	c	颜色配置文件
	d	PrinterData
	s	安全描述符
	g	全局 DevMode
	m	最低设置
	u	用户 DevMode
	r	解决名称冲突
	f	强制使用名称
	p	解析端口
	i	驱动程序名称冲突
   /u 使用现有打印机驱动程序(如果已安装)
   /t[#] 起始的从零开始的索引页
   /v[version] 驱动程序版本，为下列之一:“类型 2 - 内核模式”或“类型 3 - 用户模式”
   /w 如果在 inf 中找不到指定的驱动程序，则提示用户找一个驱动程序
   /y 将打印机设置为默认打印机
   /Xg 获取打印机设置
   /Xs 设置打印机设置
   /z 不自动共享此打印机
   /Y 不自动生成打印机名称
   /K 更改 /h 的含义，使其接受 2、3、4 (分别代表 x86、x64 或 Itanium)，并更改 /v 的含义，使其接受 3 (代表“类型 3 - 用户模式”)
   /Z 共享此打印机，只能与 /if 选项一起使用
   /? 提供此消息的帮助
   @[file] 命令行参数文件
   /Mw[message] 提交命令之前显示警告消息
   /Mq[message] 提交命令之前显示确认消息
   /W[flags] 指定向导(用于 APW 和 APDW)的标志和开关
	r	使向导可以从上一页重新启动
   /G[flags] 指定全局标志和开关
	w	禁止显示安装驱动程序警告 UI (超安静模式)
   /R 强制使用所选驱动程序替换现有驱动程序

例如:
   运行服务器属性: 
rundll32 printui.dll,PrintUIEntry /s /t1 /c\\machine
   运行打印机属性: 
rundll32 printui.dll,PrintUIEntry /p /n\\machine\printer
   本地运行添加打印机向导: 
rundll32 printui.dll,PrintUIEntry /il 
   在 \\machine: 
rundll32 printui.dll,PrintUIEntry /im /c\\machine 上运行添加打印机向导
   运行队列查看: 
rundll32 printui.dll,PrintUIEntry /o /n\\machine\printer
   运行 INF 安装: 
rundll32 printui.dll,PrintUIEntry /if /b "Test Printer" /f c:\infpath\infFile.inf /r "lpt1:" /m "Brother DCP-128C"
   运行 INF 安装(具有 INF 依赖关系)。在此示例中，prnbr002.inf 依赖于 ntprint.inf
rundll32 printui.dll, PrintUIEntry /ia /m "Brother DCP-128C" /K /h x64 /v 3 /f "c:\infpath\prnbr002.inf" /F "c:\infpath\ntprint.inf"
   使用 INF 运行添加打印机向导: 
rundll32 printui.dll,PrintUIEntry /ii /f c:\infpath\infFile.inf
   使用内置打印机驱动程序添加打印机: 
rundll32 printui.dll,PrintUIEntry /if /b "Test Printer" /r "lpt1:" /m "Brother DCP-128C"
   添加每个机器打印机连接(用户登录时此连接将传播到用户): 
rundll32 printui.dll,PrintUIEntry /ga /c\\machine /n\\machine\printer /j"LanMan Print Services"
   删除每个机器打印机连接(用户登录时此连接将被删除): 
rundll32 printui.dll,PrintUIEntry /gd /c\\machine /n\\machine\printer
   枚举每个机器打印机连接: 
rundll32 printui.dll,PrintUIEntry /ge /c\\machine
   使用 INF 添加打印机驱动程序: 
rundll32 printui.dll,PrintUIEntry /ia /c\\machine /m "Brother DCP-128C" /h "x86" /v "Type 3 - User Mode" /f c:\infpath\infFile.inf
   使用 INF 添加打印机驱动程序: 
rundll32 printui.dll,PrintUIEntry /ia /K /c\\machine /m "Brother DCP-128C" /h "x86" /v 3
   添加内置打印机驱动程序: 
rundll32 printui.dll,PrintUIEntry /ia /c\\machine /m "Brother DCP-128C" /h "Intel" /v "Type 3 - Kernel Mode"
   删除打印机驱动程序: 
rundll32 printui.dll,PrintUIEntry /dd /c\\machine /m "Brother DCP-128C" /h "x86" /v "Type 3 - User Mode"
   删除打印机驱动程序: 
rundll32 printui.dll,PrintUIEntry /dd /K /c\\machine /m "Brother DCP-128C" /h "x86" /v 3
   将打印机设置为默认打印机: 
rundll32 printui.dll,PrintUIEntry /y /n "printer"
   设置打印机注解: 
rundll32 printui.dll,PrintUIEntry /Xs /n  "printer" comment "My Cool Printer"
   取得打印机设置: 
rundll32 printui.dll,PrintUIEntry /Xg /n "printer"
   取得文件中的打印机设置保存结果: 
rundll32 printui.dll,PrintUIEntry /f "results.txt" /Xg /n "printer"
   设置打印机设置命令使用:
rundll32 printui.dll,PrintUIEntry /Xs /n "printer" ?
   将所有打印机设置存入一个文件: 
rundll32 printui.dll,PrintUIEntry /Xs /n "printer" ?
   从一个文件还原所有打印机设置: 
rundll32 printui.dll,PrintUIEntry /Sr /n "printer" /a "file.dat"
   将二级打印机信息存入一个文件: 
rundll32 printui.dll,PrintUIEntry /Ss /n "printer" /a "file.dat" 2
   从一个文件还原打印机安全描述符: 
rundll32 printui.dll,PrintUIEntry /Sr /n "printer" /a "file.dat" s
   从一个文件还原打印机全局 devmode 和打印机数据: 
rundll32 printui.dll,PrintUIEntry /Sr /n "printer" /a "file.dat" g d
   从文件最少设置还原并分析端口名: 
rundll32 printui.dll,PrintUIEntry /Sr /n "printer" /a "file.dat" m p
   启用打印机的客户端呈现: 
rundll32 printui.dll,PrintUIEntry /Xs /n"printer" ClientSideRender enabled
   禁用打印机的客户端呈现: 
rundll32 printui.dll,PrintUIEntry /Xs /n"printer" ClientSideRender disabled
```
