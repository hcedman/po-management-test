<?php
session_start();
if (isset($_GET['input'])) {
    $_SESSION['input'] = $_GET['input'];
    if ($_SESSION['input'] < 1 || $_SESSION['input'] > 2) {
        $_SESSION['input'];
    }
} else {
    if (isset($_SESSION['input'])) {
        $_SESSION['input'];
    } else {
        $_SESSION['input'] = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/index.css">
    <script>
        $(document).ready(function() {
            $('#branch_id').keyup(function() {
                let brValue = $('#branch_id').val();
                let brLenght = brValue.length;
                if (brLenght >= 1) {
                    let data = {
                        'branch_id': brValue
                    };
                    $.ajax({
                        url: 'function.php',
                        type: 'post',
                        data: data,
                        success: function(result) {
                            $('#branch_name').val(result);
                        }
                    });
                } else {
                    $('#branch_id')
                }
            });

            $('#product_id').keyup(function() {
                let productValue = $('#product_id').val();
                let productLenght = productValue.length;
                if (productLenght => 6) {
                    let data_product = {
                        'product_id': productValue
                    };
                    msg_product = new Array();
                    $.ajax({
                        url: 'function.php',
                        type: 'post',
                        data: data_product,
                        success: function(result) {
                            msg_product = result.split('|');
                            $("#product_name").val(msg_product[0]);
                            $("#per_price").val(msg_product[1]);
                            $("#qty_show").text('คงเหลือ ' + msg_product[2]);
                            $('#qty').attr("max", msg_product[2])
                            total();
                        }
                    });
                }
            });
            $('#product_id2').on('change', function() {
                let selectedValue = $(this).val();
                let data_product = {
                    'product_id': selectedValue
                };
                msg_product = new Array();
                $.ajax({
                    url: 'function.php',
                    type: 'post',
                    data: data_product,
                    success: function(result) {
                        msg_product = result.split('|');
                        $("#per_price").val(msg_product[1]);
                        $("#qty_show").text('คงเหลือ ' + msg_product[2]);
                        $('#qty').attr("max", msg_product[2]);
                        $('#product_name').val(msg_product[0]);
                        total();
                    }
                });
            });

            $('#qty').on({
                keyup: function() {
                    let qtyValue = $('#qty').val();
                    total();
                },
                click: function() {
                    let qtyValue = $('#qty').val();
                    total();
                },
                blur: function() {
                    let qtyValue = $('#qty').val();
                    total();
                },
                mouseleave: function() {
                    let qtyValue = $('#qty').val();
                    total();
                }
            })

            $('#minus').click(function() {
                let qtyValue = $('#qty').val();
                qtyValue--;
                $('#qty').val(qtyValue);
                total();
            });
            $('#plus').click(function() {
                let qtyValue = $('#qty').val();
                qtyValue++;
                $('#qty').val(qtyValue);
                total();
            });

            var choose = $('#choose').text();
            if (choose == 1) {
                $('#btn_manual').removeClass().addClass("btn btn-secondary");
                $('#btn_select').removeClass().addClass("btn btn-outline-secondary");
                $('#link_input1').css("color", "white");
                $('#link_input2').css("color", "gray");
            } else {
                $('#btn_manual').removeClass().addClass("btn btn-outline-secondary");
                $('#btn_select').removeClass().addClass("btn btn-secondary");
                $('#link_input1').css("color", "gray");
                $('#link_input2').css("color", "white");
            }
        });

        function total() {
            let perPrice = $('#per_price').val();
            let qtyValue = $('#qty').val();
            let price = parseFloat(perPrice);
            let qty = parseInt(qtyValue);
            let total = price * qty;
            let vat = (total * 7) / 100;
            let allTotal = total + vat;
            $('#total').val(total.toFixed(2));
            $('#vat').val(vat.toFixed(2));
            $('#all_total').val(allTotal.toFixed(2));
        }
    </script>
    <title>Purchase Order</title>
</head>

<body>
    <?php
    include 'connect.php';
    if (isset($_POST['submit'])) {
        $branch_id = $_POST['branch_id'];
        $order_date = $_POST['order_date'];
        $revise = $_POST['revise'];
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $type = $_POST['type'];
        $quantity = $_POST['qty'];
        $per_price = $_POST['per_price'];
        $total = $_POST['total'];
        $vat = $_POST['vat'];
        $all_total = $_POST['all_total'];
        $total_product = $conn->query("select product_qty from product where product_id = $product_id");
        $result_total = $total_product->fetch_assoc();
        $total_item = $result_total['product_qty'];
        if (empty($branch_id) || empty($order_date) || empty($product_id) || empty($product_name) || empty($type) || empty($quantity) || empty($per_price) || empty($total) || empty($vat) || empty($all_total)) {
            echo "<script>alert('กรุณาใส่ข้อมูลให้ครบถ้วน')</script>";
        } elseif ($total_item >= $quantity) {
            $sql_order = "insert into `order` (branch_id, order_date, revise, product_id, product_name, type, quantity, per_price, total, vat, all_total)values(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt_order = $conn->prepare($sql_order);
            $stmt_order->bind_param('ississidddd', $branch_id, $order_date, $revise, $product_id, $product_name, $type, $quantity, $per_price, $total, $vat, $all_total);
            $stmt_order->execute();
            if ($stmt_order == true) {
                $total_qty = $total_item - $quantity;
                $update_qty = $conn->query("update product set product_qty = $total_qty");
                echo "<script>alert('ดำเนินการจัดซื้อเรียบร้อย')</script>";
            }
        } else {
            echo "<script>alert('ตรวจความถูกต้องของข้อมูล หรือจำนวนสินค้าไม่ถูกต้อง')</script>";
        }
    }
    ?>
    <nav class="navbar bg-body-tertiary">
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
    <form action="" method="post">
        <div class="container container-fluid bg-light" id="container_body">
            <div class="d-grid gap-2 d-md-block" id="menu_input">
                <button class="btn" type="button" id="btn_manual"><a href="index.php?input=1" id="link_input1">Manual</a></button>
                <button class="btn" type="button" id="btn_select"><a href="index.php?input=2" id="link_input2">Basic</a></button>
                <span style="margin-left: 1rem; color:maroon;">**เลือกประเภทการกรอกข้อมูลได้ manual สำหรับพิมพ์เองและ barcode scanner</span>
                <span id="choose" hidden><?php echo $_SESSION['input']; ?></span>
            </div>
            <div class="row" id="row_date">
                <div class="col-3" id="col_date"><input type="date" class="form-control" name="order_date" id="order_date" value="<?php echo date("Y-m-d"); ?>"></div>

            </div>
            <div class="row">
                <div class="col-7">
                    <div class="input-group mb-3">
                        <span class="input-group-text">ประเภทรายการ</span>
                        <select class="form-select" id="type" name="type" required>
                            <option selected value="">-เลือกประเภท-</option>
                            <option value="TP">TP (TREND PALETE)</option>
                            <option value="MTO">MTO (MAKE TO ORDER)</option>
                            <option value="ST">ST (STOCK)</option>
                            <option value="SF">SF (STOCK FAKE)</option>
                        </select>
                    </div>

                </div>
                <div class="col-5">
                    <div class="input-group mb-3">
                        <span class="input-group-text">*แก้ไข</span>
                        <input type="text" class="form-control" id="revise" name="revise" value="">
                    </div>
                </div>
            </div>

            <!-- /////////////////////////////////////////////////////////////-1-////////////////////////////////////////////////////////////////// -->
            <?php if ($_SESSION['input'] == 1) { ?>
                <div class="row">
                    <div class="col-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">รหัสสาขา</span>
                            <input type="text" class="form-control" name="branch_id" id="branch_id" value="" placeholder="ใส่รหัสสาขา" required>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="input-group mb-3">
                            <span class="input-group-text">ชื่อสาขา&nbsp;&nbsp;&nbsp;</span>
                            <input type="text" class="form-control" name="branch_name" id="branch_name" value="" placeholder="ไม่มีข้อมูล" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">รหัสสินค้า</span>
                            <input type="text" class="form-control" name="product_id" id="product_id" placeholder="ใส่รหัสสินค้า" required>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="input-group mb-3">
                            <span class="input-group-text">ชื่อสินค้า&nbsp;&nbsp;&nbsp;</span>
                            <input type="text" class="form-control" name="product_name" id="product_name" placeholder="ไม่มีข้อมูล" readonly>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- /////////////////////////////////////////////////////////////-2-////////////////////////////////////////////////////////////////// -->
            <?php if ($_SESSION['input'] == 2) {
                $sql_branch = $conn->query("select * from branch");
                $sql_product = $conn->query("select * from product");
            ?>
                <div class="row">
                    <div class="col-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">สาขา</span>
                            <select class="form-select" id="type" name="branch_id" id="branch_id" required>
                                <option selected value="">-เลือกสาขา-</option>
                                <?php
                                while ($result_branch = $sql_branch->fetch_assoc()) { ?>
                                    <option value="<?php echo $result_branch['branch_id']; ?>"><?php echo $result_branch['branch_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="input-group mb-3">
                            <span class="input-group-text">สินค้า</span>
                            <select class="form-select" name="product_id" id="product_id2" required>
                                <option selected value="">-เลือกสินค้า-</option>
                                <?php
                                while ($result_product = $sql_product->fetch_assoc()) { ?>
                                    <option value="<?php echo $result_product['product_id']; ?>"><?php echo $result_product['product_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <input type="text" name="product_name" id="product_name" value="" hidden>
                    </div>
                </div>
            <?php } ?>

            <div class="row" style="display:flex; flex-direction:row-reverse;">
                <div class="col-2">
                    <div class="input-group mb-3">
                        <button class="btn btn-outline-secondary" type="button" id="minus">-</button>
                        <input type="text" class="form-control" id="qty" name="qty" value="1" min="0" style="text-align: center;">
                        <button class="btn btn-outline-secondary" type="button" id="plus">+</button>
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">ราคาต่อชิ้น</span>
                        <input type="text" class="form-control" name="per_price" id="per_price" placeholder="ไม่มีข้อมูล" readonly>
                    </div>
                </div>
            </div>
            <div class="row" style="display:flex; flex-direction:row-reverse;">
                <div class="col-2">
                    <span id="qty_show" style="margin-left:3rem;"></span>
                </div>

            </div>
        </div>

        </div>
        <div class="container container-fluid bg-light" id="container_total">
            <div class="row">
                <div class="col-10" id="total_1"><span>ราคารวม</span></div>
                <div class="col-1" id="total_2"><input type="text" class="form-control-plaintext" id="total" name="total" value="0"></div>
                <div class="col-1" id="total_3"><span>บาท</span></div>

            </div>
            <div class="row">
                <div class="col-10" id="vat_1"><span>VAT 7%</span></div>
                <div class="col-1" id="vat_2"><input type="text" class="form-control-plaintext" id="vat" name="vat" value="0"></div>
                <div class="col-1" id="vat_3"><span>บาท</span></div>
            </div>
            <div class="row">
                <div class="col-10" id="all_total_1"><span>ราคารวมทั้งสิ้น</span></div>
                <div class="col-1" id="all_total_2"><input type="text" class="form-control-plaintext" id="all_total" name="all_total" value="0"></div>
                <div class="col-1" id="all_total_3"><span>บาท</span></div>
            </div>
        </div>

        <div class="container container-fluid" id="container_btn" style="margin-bottom: 2rem;">
            <button class="btn btn-danger" type="reset">ยกเลิกรายการ</button>
            <button class="btn btn-secondary bgc" type="submit" name="submit" style="color:darkslateblue; color:white;">ดำเนินการจัดซื้อ</button>
        </div>

    </form>
</body>

</html>