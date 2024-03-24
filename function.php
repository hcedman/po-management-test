<?php
include 'connect.php';
if (isset($_POST['branch_id'])) {
    $branch_id = $_POST['branch_id'];
    $stmt_branch = $conn->prepare("select branch_name from branch where branch_id = ?");
    $stmt_branch->bind_param('i', $branch_id);
    $stmt_branch->execute();
    $result_branch = $stmt_branch->get_result();
    $data_branch = $result_branch->fetch_assoc();
    if ($count_row = mysqli_num_rows($result_branch) > 0) {

        echo $data_branch['branch_name'];
    }else{
        echo 'ไม่พบสาขา';
    }
}
if(isset($_POST['product_id'])){
    $product_id = $_POST['product_id'];
    $stmt_product = $conn->prepare("select * from product where product_id = ?");
    $stmt_product->bind_param('i', $product_id);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();
    while($data_product = $result_product->fetch_assoc()){
        echo $data_product['product_name'].'|'.$data_product['product_price'].'|'.$data_product['product_qty'];
    }
    
    
}


?>