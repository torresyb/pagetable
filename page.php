<?php

$page = $_GET["p"]; // 当前页
$pagestr = '';
$total = 0; // 总数据
$pagetotal = 0; //总页数
$pagesize = 2; // 每页显示多少条数据
$showpage = 5; // 页面显示个数 奇数
$pageoffset = ($showpage-1)/2;

$start = 1;
$end = $pagetotal;

// 链接数据库
try {
    $db = new PDO("mysql:host=localhost;dbname=test","root","");
} catch (Exception $e) {
    echo $e->getMessage();
}

// pdo预处理
$data = $db->prepare("select * from cars limit ?,?");
$data->bindValue(1,($page-1)*$pagesize,PDO::PARAM_INT);
$data->bindValue(2,$pagesize,PDO::PARAM_INT);
$data->execute();

// 获取总数据条数和总页码
$rs = $db->query("select count(*) from cars");
$total = $rs->fetchColumn();
$pagetotal = ceil($total/$pagesize);

// 瓶装数据并输出
$pagestr .= '<table border=1px cellspacing=0>';  
$pagestr .=  '<tr><th>ID</th><th>NAME</th><th>PRICE</th></tr>';

while ( $row = $data->fetch(PDO::FETCH_ASSOC)) {
    $pagestr .=  '<tr>';
    $pagestr .=    "<td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['money']}</td>";
    $pagestr .=  '</tr>';
    
}
$pagestr .='</table>';

// 分页字符串

if ($page>1) {
    $pagestr .='<a href="'.$_SERVER['PHP_SELF'].'?p='.($page-1).'">上一页</a>';
}


if($pagetotal>$showpage && $page>($pageoffset+1)){
    $pagestr .= '...';

    if($page+$pageoffset<=$pagetotal){
        $start = $page-$pageoffset;
        $end = $page+$pageoffset;
    }else{
        $start = $pagetotal-2*$pageoffset;
        $end = $pagetotal;
    }
    
}else{
    $end = $pagetotal<=$showpage ? $pagetotal : $showpage;
}

for ($i=$start; $i <=$end ; $i++) { 
    $pagestr .= "<a href='".$_SERVER['PHP_SELF']."?p=".$i."'>{$i}</a>";
}

if($pagetotal>$showpage && $page+$pageoffset<$pagetotal){
    $pagestr .= '...';
}

if($page<$pagetotal){
    $pagestr .='<a href="'.$_SERVER['PHP_SELF'].'?p='.($page+1).'">下一页</a>';
}

$pagestr .='<form action="'.$_SERVER['PHP_SELF'].'" method="get"><input type="text" name="p"></form><span>总页数'.$pagetotal.'</span>';

echo $pagestr;

// 清数据
$data=null;
