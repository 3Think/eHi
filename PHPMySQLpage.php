<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=utf-8">
<title>PHP+MYSQL分页原理与实现</title>
</head>
<style>
body {
	font-size: 12px;
	font-family: verdana;
	width: 100%;
}

div.page {
	text-align: center;
}

div.content {
	height: 300px;
}

div.page a {
	border: #aaaadd 1px solid;
	text-decoration: none;
	padding: 2px 5px 2px 5px;
	margin: 2px;
}

div.page span.current {
	border: #000099 1px solid;
	background-color: #000099;
	text-decoration: none;
	padding: 4px 6px 4px 6px;
	margin: 2px;
	color: #fff;
	font-weight: bold;
}

div.page span.disable {
	border: #eee 1px soild;
	padding: 2px 5px 2px 5px;
	margin: 2px;
	color: #ddd;
}

div.page form {
	display: inline;
}
</style>
<body>
	<h1 align="center">PHP+MYSQL分页原理与实现</h1>
	<hr />
<?php
/* 1.传入页码； */
$page = $_GET['p'];
// $funy=intval($_GET['p']);
// echo $funy;
// $page=(isset($_GET['p'])&&intval($_GET['p']))?intval($_GET['p']):1;

/* 2.根据页码取出数据：php->mysql */
// echo $m1 = memory_get_usage();echo "<br/>";
$host = 'localhost';
$username = 'root';
$password = '1';
$db = 'test';
$pageSize = 10;
$show_page = 5;

// 连接数据库
// $conn=mysql_connect($host,$username,$password);
$conn = mysqli_connect($host, $username, $password, $db);
if (! $conn) {
    echo "数据库连接失败";
    exit();
}
// 选择所要操作的数据库
// mysql_select_db($db);
// 设置数据库编码格式
mysqli_query($conn, "SET NAMES UTF8");

// 获取数据总数
$total_sql = "SELECT count(*) count FROM page";
$resultp = mysqli_query($conn, $total_sql);
//$total = mysqli_num_rows($resultp);
$row = mysqli_fetch_assoc($resultp);
$total = $row['count'];
	
// echo "总条数：".$total;
// 计算页数
$total_pages = ceil($total / $pageSize);
// 检查页码的合法性
if (isset($page) && intval($page) && ($page <= $total_pages)) {
    $page = intval($page);
} else {
    echo "警示：您所输入的页码不存在，";
    echo "当前为您显示第一页！！";
    echo "<hr/>";
    $page = 1;
}
// 编写SQL获取分页数据SELECT * FROM 表名 LIMIT 起始位置，显示条数
$sql = "SELECT * FROM page LIMIT " . (($page - 1) * $pageSize) . " ,{$pageSize}";
// 把SQL语句传送到数据库
$result = mysqli_query($conn, $sql);
// 处理我们的数据
echo "<div class='content'>";
echo '<table border=1 cellspacing=0 width=40% align=center>';
echo '<tr><td>ID号</td><td>姓名</td></tr>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo '</tr>';
    // print_r($row);
}
echo '</table>';
echo "</div>";

// 释放结果关闭连接
mysqli_free_result($result);
mysqli_close($conn);

/* 3.显示数据+分页条 */
$page_banner = "<div class='page'>";
if ($page > 1) {
    $page_banner .= "<a href='" . $_SERVER['PHP_SELF'] . "?p=1'>首页</a>";
    $page_banner .= "<a href='" . $_SERVER['PHP_SELF'] . "?p=" . ($page - 1) . "'><上一页</a>";
} else {
    $page_banner .= "<span class='disable'>首页</a></span>";
    $page_banner .= "<span class='disable'><上一页</a></span>";
}
// echo $_SERVER['PHP_SELF'];
// 页数计算偏移量
$pageoffset = ($show_page - 1) / 2;
// 初始化数据
$start = 1;
$end = $total_pages;
if ($total_pages > $show_page) {
    // 省略前部
    if ($page > $pageoffset + 1) {
        $page_banner .= "...";
    }
    if ($page > $pageoffset) {
        $start = $page - $pageoffset;
        $end = $total_pages > $page + $pageoffset ? $page + $pageoffset : $total_pages;
    } else {
        $start = 1;
        $end = $show_page;
    }
    if ($page + $pageoffset > $total_pages) {
        $start = $start - ($page + $pageoffset - $end);
        $end = $total_pages;
    }
}
for ($i = $start; $i <= $end; $i ++) {
    if ($page == $i) {
        $page_banner .= "<span class='current'>{$i}</span>";
    } else {
        $page_banner .= "<a href='" . $_SERVER['PHP_SELF'] . "?p=" . $i . "'>{$i}</a>";
    }
}
// 省略尾部
if ($total_pages > $show_page && $total_pages > $page + $pageoffset) {
    $page_banner .= "...";
}
if ($page < $total_pages) {
    $page_banner .= "<a href='" . $_SERVER['PHP_SELF'] . "?p=" . ($page + 1) . "'>下一页></a>";
    $page_banner .= "<a href='" . $_SERVER['PHP_SELF'] . "?p=" . $total_pages . "'>尾页</a>";
} else {
    $page_banner .= "<span class='disable'>下一页></a></span>";
    $page_banner .= "<span class='disable'>尾页</a></span>";
}

$page_banner .= "共{$total_pages}页,";
// 实现页码跳转
$page_banner .= "<form action=mypage3.php method='get'>";
$page_banner .= "到第<input type='int' size='2' name='p'>页,";
$page_banner .= "<input type='submit' value='确定'>";
$page_banner .= "</form></div>";
echo $page_banner;
// 显示当前时间
date_default_timezone_set('Asia/ShangHai');
$today = date('20y年m月d日h时i分s秒,星期w', time()); // 获取当天日期
                                            // $week = date("w")//获取当天星期几									
echo "<b>当前时间" . $today . ",天气QING</b>";
//获取天气
// echo '<iframe name="weather_inc" src="http://i.tianqi.com/index.php?c=code&id=112" width="110" height="150" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>';	
echo "<hr/>";
?>
<div id="copyright" style="text-align:left">
<strong>联系地址：</strong>
	<address>@HENNAN ZHENGZHOU</address>
	</div>
</body>
</html>
