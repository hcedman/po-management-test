<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/report.css">
    <script>
        function alertInto(text) {
            alert(text);
            window.location.href = 'index.php';
        }
    </script>
    <title>Purchase Order</title>
</head>

<body>
    <?php
    include 'connect.php';
    $sql_branch = $conn->query("select * from branch");
    $total = 0;
    $vat = 0;
    $all_total = 0;
    ?>
    <nav class="navbar bg-body-tertiary bgch" id="header">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="image/logo.jpg" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
                UNION BUTTLE CORPORATION COMPANY LIMITED
            </a>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn bgc" id="menu1"><a href="index.php">Add Purchase</a></button>
                <button type="button" class="btn bgc" id="menu2"><a href="report.php">Report Purchase Order</a></button>
            </div>
        </div>
    </nav>
    <div class="container container-fluid bg-light" id="container_body">
        <form action="" method="post">
            <div class="row" id="row_search">
                <div class="col-5">
                    <div class="input-group">
                        <span class="input-group-text">สาขา</span>
                        <select class="form-select" name="branch_id" id="branch_id">
                            <option selected value="">ทุกสาขา</option>
                            <?php
                            while ($result_branch = $sql_branch->fetch_assoc()) { ?>
                                <option value="<?php echo $result_branch['branch_id']; ?>"><?php echo $result_branch['branch_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-4"><input type="date" class="form-control" name="order_date" id="order_date" value=""></div>
                <div class="col-2"><button class="btn btn-secondary" type="submit" name="submit" id="submit">ค้นหา</button></div>
            </div>

        </form>
    </div>
    <?php

    if (isset($_POST['submit'])) {
        if (!empty($_POST['branch_id']) && !empty($_POST['order_date'])) {
            $search_branch = $_POST['branch_id'];
            $search_date = $_POST['order_date'];
            $sql_search = $conn->prepare("select * from `order` where branch_id = ? and order_date = ?");
            $sql_search->bind_param('is', $search_branch, $search_date);
        } elseif (!empty($_POST['branch_id'])) {
            $search_branch = $_POST['branch_id'];
            $sql_search = $conn->prepare("select * from `order` where branch_id = ?");
            $sql_search->bind_param('i', $search_branch);
        } elseif (!empty($_POST['order_date'])) {
            $search_date = $_POST['order_date'];
            $sql_search = $conn->prepare("select * from `order` where order_date = ?");
            $sql_search->bind_param('s', $search_date);
        } else {
            $sql_search = $conn->prepare("select * from `order`");
        }
        $sql_search->execute();
        $result_search = $sql_search->get_result();
    ?>
        <div class="container container-fluid bg-light" id="container_body">
            <table class="table bdr" id="table_order" style="margin: 1rem auto 1rem;">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th class="text-center">วันที่</th>
                        <th class="text-center">สาขา</th>
                        <th class="text-center">Revise</th>
                        <th class="text-center">สินค้า</th>
                        <th class="text-center">ชื่อสินค้า</th>
                        <th class="text-center">ประเภท</th>
                        <th class="text-center">จำนวน</th>
                        <th class="text-center">ราคา</th>
                        <th class="text-center">รวม</th>
                        <th class="text-center">VAT</th>
                        <th class="text-center">ทั้งสิ้น</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    while ($data_report = $result_search->fetch_assoc()) {
                        $order_id = $data_report['order_id'];
                        $sql_name = $conn->query("select branch.branch_name, product.product_name from `order` 
                                                inner join branch on `order`.branch_id = branch.branch_id
                                                inner join product on `order`.product_id = product.product_id
                                                where order_id = $order_id");
                        $rows = mysqli_num_rows($sql_name);
                        if ($rows > 0) {
                            $result_name = $sql_name->fetch_assoc();
                            $data_brach_name = $result_name['branch_name'];
                            $data_product_name = $result_name['product_name'];
                        } else {
                            $data_brach_name = "-";
                            $data_product_name = "-";
                        }
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $data_report['order_id']; ?></td>
                            <td><?php echo $data_report['order_date']; ?></td>
                            <td><?php echo $data_brach_name; ?></td>
                            <td><?php echo $data_report['revise']; ?></td>
                            <td><?php echo $data_report['product_id']; ?></td>
                            <td><?php echo $data_product_name; ?></td>
                            <td class="text-center"><?php echo $data_report['type']; ?></td>
                            <td align="right"><?php echo $data_report['quantity']; ?></td>
                            <td align="right"><?php echo number_format($data_report['per_price'], 2); ?></td>
                            <td align="right"><?php echo number_format($data_report['total'], 2); ?></td>
                            <td align="right"><?php echo number_format($data_report['vat'], 2); ?></td>
                            <td align="right" style="font-weight: 600;"><?php echo number_format($data_report['all_total'], 2); ?></td>
                        </tr>
                    <?php
                        $total += $data_report['total'];
                        $vat += $data_report['vat'];
                        $all_total += $data_report['all_total'];
                    } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <div class="container container-fluid bg-light" id="container_body_3">
        <table class="table-light bdr" id="table_total" align="right">
            <tr>
                <td style="text-align: end; font-weight:600;">รวม</td>
                <td style="text-align: end; padding-right:2rem; font-weight:600;"><?php echo number_format($total, 2); ?></td>
                <td style="font-weight:600;">บาท</td>
            </tr>
            <tr>
                <td style="text-align: end; font-weight:600;">VAT 7%</td>
                <td style="text-align: end; padding-right:2rem; font-weight:600;"><?php echo number_format($vat, 2); ?></td>
                <td style="font-weight:600;">บาท</td>
            </tr>
            <tr>
                <td style="text-align: end; font-weight:600;">รวมทั้งสิ้น</td>
                <td style="text-align: end; padding-right:2rem; font-weight:600;"><?php echo number_format($all_total, 2); ?></td>
                <td style="font-weight:600;">บาท</td>
            </tr>
        </table>
    </div>

</body>

</html>