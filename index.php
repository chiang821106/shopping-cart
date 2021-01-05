<?php 
require_once("connMysql.php");
//預設每頁筆數
$pageRow_records = 6;
//預設頁數
$num_pages = 1;
//若已經翻頁，將頁數更新
if(isset($_GET['page'])){
    $num_pages = $_GET['page'];
}
//本頁開始紀錄筆數 = (頁數-1) * 每頁紀錄筆數
$startRow_records = ($num_pages -1) * $pageRow_records;
//若有分類關鍵字時未加限制顯示筆數的SQL敘述句
if(isset($_GET['cid'])&&($_GET['cid']!="")){
    $query_RecProduct = "SELECT * FROM product WHERE categoryid=? ORDER BY productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $stmt->bind_param('i',$_GET['cid']);
//若有搜尋關鍵字時未加限制顯示筆數的SQL敘述句
}elseif(isset($_GET['keyword'])&&($_GET['keyword']!="")){
    $query_RecProduct ="SELECT * FROM product WHERE productname LIKE ? OR description LIKE ? ORDER BY productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $keyword = "%".$_GET['keyword']."%";
    $stmt->bind_param('ss',$keyword,$keyword);
//若有價格區間關鍵字時未加限制顯示筆數的SQL敘述句 
}elseif(isset($_GET['price1']) && isset($_GET['price2']) && ($_GET['price1'] <= $_GET['price2']) ){
    $query_RecProduct ="SELECT * FROM product WHERE product WHERE productprice BETWEEN ? AND ?  ORDER BY productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $stmt->bind_param('ii',$_GET['price1'],$_GET['price1']);
//預設狀況下未加限制顯示筆數的SQL敘述句
}else{
    $query_RecProduct = "SELECT * FROM product ORDER BY productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
}
$stmt->execute();
//以未加上限制顯示筆數的SQL敘述句查詢資料到$all_RecProduct中
$all_RecProduct = $stmt->get_result();
//計算總筆數
$total_records = $all_RecProduct->num_rows;
//計算總頁數=(總筆數/每頁筆數)後無條件進位
$total_pages = ceil($total_records/$pageRow_records);
//------------------------------------------------------------------------------------------------------------------------------------
//繫結產品目錄資料
$query_RecCategory = "SELECT category.categoryid,category.categoryname,category.categorysort,
                      count(product.productid)as productNum FROM category 
                      LEFT JOIN product ON category.categoryid = product.categoryid 
                      GROUP BY category.categoryid,category.categoryname,category.categorysort 
                      ORDER BY category.categorysort ASC";
$RecCategory = $db_link->query($query_RecCategory);
//計算資料總筆數
$query_RecTotal = "SELECT count(productid) as totalNum FROM product";
$RecTotal = $db_link->query($query_RecTotal);
$row_RecTotal = $RecTotal->fetch_assoc();
//------------------------------------------------------------------------------------------------------------------------------------
//返回URL參數
function keepURL(){
    $keepURL = "";
    if(isset($_GET['keyword'])) $keepURL.="&keyword=".urlencode($_GET['keyword']);
    if(isset($_GET['price1'])) $keepURL.="&price1=".$_GET['price1'];
    if(isset($_GET['price2'])) $keepURL.="&price2=".$_GET['price2'];
    if(isset($_GET['cid'])) $keepURL.="&cid=".$_GET['cid'];
    return $keepURL;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購物車練習</title>
</head>

<body>
    <!-- 搜尋功能 -->
    <div class="categorybox">
        <p class="heading"><img src="images/16-cube-orange.png" width="16" height="16" align="absmiddle" alt="">產品搜尋
            <span class="smalltext">Search</span></p>
        <form name="form1" method="get" action="index.php">
            <p>
                <input name="keyword" id="keyword" value="請輸入關鍵字" size="12" onClick="this.value='';" type="text">
                <input id="button" value="查詢" type="submit">
            </p>
        </form>

        <p class="heading"><img src="images/16-cube-orange.png" width="16" height="16" align="absmiddle" alt="">價格區間
            <span class="smalltext">Price</span></p>
        <form name="form2" method="get" id="form2" action="index.php">
            <p>
                <input name="price1" id="price1" value="0" size="3" type="text">
                <input name="price2" id="price2" value="0" size="3" type="text">
                <input id="button2" value="查詢" type="submit">
            </p>
        </form>
    </div>
    <!-- 商品分類 -->
    <div class="categorybox">
        <p class="heading"><img src="images/16-cube-orange.png" width="16" height="16" align="absmiddle" alt="">產品目錄
            <span class="smalltext">Category</span></p>
        <ul>
            <li><a href="index.php">所有產品 <span class="categorycount">
                        (<?php echo $row_RecTotal['totalNum']; ?>)</span></a></li>
            <?php while($row_RecCategory = $RecCategory->fetch_assoc()){ ?>
            <li><a href="index.php?cid=<?php $row_RecCategory['categoryid']; ?>"><?php echo $row_RecCategory['categoryname']; ?>
                    <span class="categorycount">(<?php echo $row_RecCategory['productNum']; ?>)</span></a></li>
            <?php } ?>
        </ul>

    </div>
</body>

</html>