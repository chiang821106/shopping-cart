<?php 
require_once("connMysql.php");
//購物車開始
require_once("mycart.php");
session_start();
$cart = & $_SESSION['cart'];//將購物車的值設定為session
if(!is_object($cart)) $cart = new myCart();
//新增購物車內容
if(isset($_POST["cartaction"]) && ($_POST["cartaction"]=="add")){
    $cart->add_item($_POST['id'],$_POST['qty'],$_POST['price'],$_POST['name']);
    header("Location:cart.php");
}
//購物車結束
//繫結產品資料
$query_RecProduct = "SELECT * FROM product WHERE productid=?";
$stmt = $db_link->prepare($query_RecProduct);
$stmt->bind_param('i',$_GET['id']);
$stmt->execute();
$RecProduct = $stmt->get_result();
$row_RecProduct = $RecProduct->fetch_assoc();
//繫結產品目錄資料
$query_RecCategory = "SELECT category.categoryid,category.categoryname,category.categorysort,
                      count(product.productid)as productNum FROM category
                      LEFT JOIN product ON category.categoryid = product.categoryid
                      GROUP BY category.categoryid,category.categoryname,category.categorysort
                      ORDER BY category.categorysort ASC";
$RecCategory = $db_link->query($query_RecCategory);
//計算資料總筆數
$query_RecTotal = "SELECT count(productid)as totalNum FROM product";
$RecTotal = $db_link->query($query_RecTotal);
$row_RecTotal = $RecTotal->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購物車練習-購物車</title>
</head>

<body>
    <div class="albumDiv">
        <div class="picDiv">
            <?php if($row_RecProduct['productimages']==""){ ?>
            <img src="images/nopic.png" alt="暫無圖片" width="120" height="120" border="0">
            <?php }else{ ?>
            <img src="proimg/<?php echo $row_RecProduct['productimages']; ?>"
                alt="<?php echo $row_RecProduct['productname']; ?>" width="135" height="135" border="0">
            <?php } ?>
        </div>
        <div class="albuminfo">
            <span class="smalltext">特價</span>
            <span class="redword"><?php echo $row_RecProduct['productprice']; ?></span>
            <span class="smalltext"> 元</span>
        </div>
    </div>
    <div class="titleDiv">
        <?php echo $row_RecProduct['productname']; ?>
    </div>
    <div class="dataDiv">
        <p><?php echo nl2br($row_RecProduct['description']); ?></p>
        <hr width="100%" size="1">
        <form action="" method="post" name="form3">
            <input type="hidden" name="id" id="id" value="<?php echo $row_RecProduct['productid'] ?>">
            <input type="hidden" name="name" id="name" value="<?php echo $row_RecProduct['productname'] ?>">
            <input type="hidden" name="price" id="price" value="<?php echo $row_RecProduct['productprice'] ?>">
            <input type="hidden" name="qty" id="qty" value="1">
            <input type="hidden" name="cartaction" id="cartaction" value="add">
            <input type="submit" name="button3" id="button3" value="加入購物車">
            <input type="button" name="button4" id="button4" value="回上一頁" onClick="window.history.back();">
        </form>
    </div>

</body>

</html>