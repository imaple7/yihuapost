<?php
define("pageName", "首页头条管理");
$requiredLevel = 10;
require_once ("../admin_files/webUI.php");
require_once("../admin_files/classes/HouseSystem.php");
require_once("../admin_files/classes/User.php");
require_once ("../admin_files/user_function.php");

$db = new Database();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php getTitle(pageName) ?>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php importLink("
    <!-- DataTables -->
    <link rel=\"stylesheet\" href=\"../plugins/datatables/dataTables.bootstrap.css\">
    <!-- DataTables Responsive CSS -->
    <link href=\"../plugins/datatables/extensions/Responsive/css/dataTables.responsive.css\" rel=\"stylesheet\">
    <!-- blueimp Gallery styles -->
    <link rel=\"stylesheet\" href=\"//blueimp.github.io/Gallery/css/blueimp-gallery.min.css\">
    <!-- CSS to style the Jquery-Confirm -->
    <link rel=\"stylesheet\" href=\"../plugins/jquery-confirm/dist/jquery-confirm.min.css\">

<style>
.ui-state-highlight
{
height: 42px;
}
</style>
    ") ?>

    <link href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/css/base/jquery.ui.all.css" rel="stylesheet">
    <link href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/css/lightness/jquery-ui-1.10.2.custom.min.css"
          rel="stylesheet">

</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">


    <?php echoheader() ?>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <?php echoNavBarHtml(5) ?>

    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo pageName; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> 主页</a></li>
                <li class="active"><?php echo pageName; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <style>
                #sortable {
                    list-style-type: none;
                    margin: 0;
                    padding: 0;
                    width: 60%;
                }

                #sortable li {
                    margin: 0 3px 3px 3px;
                    padding: 0.4em;
                    padding-left: 1.5em;
                    font-size: 1.4em;
                }

                #sortable li span {
                    position: absolute;
                    margin-left: -1.3em;
                }
            </style>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">编辑列表</h3>
                </div>

                <!-- /.box-header -->
                <div class="box-body">
                    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">添加</button>
                    <button type="button" class="btn btn-danger btn-lg"  onclick="deleteAllData()">重置所有</button>

                    <hr>
                    <div class="list-group" id="sortable">
                        <?php

                        $db = new Database();
                        $select = "SELECT * FROM web_index ORDER BY ord ASC"; // select every element in the table in order by order column

                        $mResult = $db->query($select);

                        if ($mResult && mysqli_num_rows($mResult) > 0) {
                            while ($array = mysqli_fetch_assoc($mResult)) {
                                $id = $array["ID"];
                                $containerID = $array["container_id"];

                                $sql = "SELECT ID, title FROM web_house WHERE ID = $containerID";
                                $result = $db->query($sql);

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $mHouse = new HouseSystem($row['ID']);

                                        $mTitle = $mHouse->getSystemTitle();
                                        echo " <div class=\"list-group-item\" id='item-$id'>$mTitle <span class=\"pull-right text-muted\"><button class=\"btn btn-danger btn-xs\" onclick=\"deleteIndexPost('$id','$mTitle');\" >移除</button></span></div>";
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-sm-offset-10 col-sm-2">
                            </div>
                            <div class="col-sm-offset-11 col-sm-1">
                                <button id="save-btn" onclick="saveData()" class="btn btn-success">保存</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-footer-->
                </div>
                <!-- /.box -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">系统列表</h4>
                </div>
                <div class="modal-body">
                    <?php
                    // define variables and set to empty values
                    $houseID = $houseStatus = $sellType = $title = $price = $houseYear = $houseMark = "...";
                    $houseModel = $houseTrims = $houseKilometre = $houseTransmission = "...";
                    $houseColor = $houseDrivetrain = $houseCondition = $houseFuel = "...";


                    $houseDoor = $houseSeat = $houseBodyType = $description = $mainPic = "";

                    $nameErr = $iconErr = $listErr = $priceErr = $urlErr = $imgErr = "";
                    $name = $price = $icon = $listID = $url = $img = "";

                    $check = true;

                    ?>
                    <table id="house-list" width="100%" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>平方米ID</th>
                            <th>副标题</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT * FROM web_house";
                        $result = $db->query($sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($rows = mysqli_fetch_assoc($result)) {
                                $postID = $rows['ID'];
                                $house = new HouseSystem($postID);

                                $houseID = $house->getHouseId();
                                $systemTitle = $house->getSystemTitle();

                                echo "
                                <tr>
                                    <td>" . $houseID . "</td>
                                    <td>" . $systemTitle . "</td>

                                    ";

                                echo "
                                <td>
                                    <button onclick=\"addIndexPost('$postID','$systemTitle')\" class=\"btn btn-primary \">
                                    <i class=\"glyphicon glyphicon-adjust\"></i>
                                    <span>添加到头条</span>
                                    </button>
                                </td>
                                </tr>";
                            }
                        }

                        function checkIfEmpty($inputString)
                        {
                            if (empty($inputString)) {
                                return "";
                            } else {
                                return $inputString;
                            }
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>

        </div>
    </div>

    <?php getFooter() ?>

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>f
    <ol class="indicator"></ol>
</div>


<!-- jQuery 3.1.1 -->
<script src="../plugins/jQuery/jquery-3.1.1.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="../plugins/datatables/extensions/Responsive/js/dataTables.responsive.js"></script>
<!-- JavaScript to the Jquery-Confirm -->
<script src="../plugins/jquery-confirm/dist/jquery-confirm.min.js"></script>


<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="../plugins/jquery.ui.touch-punch/jquery.ui.touch-punch.min.js"></script>

<script>
    $(document).ready(function () {
        $('.sidebar-menu').tree()
    })
</script>

<!-- page script -->
<script>
    $(document).ready(function () {
        $('#house-list').DataTable({
            responsive: true
        });
    });
</script>

<script>
    $(document).ready(function () {
        var ul_sortable = $('#sortable'); //setup one variable for sortable holder that will be used in few places


        /*
         * jQuery UI Sortable setup
         */
        ul_sortable.sortable({
            revert: 100,
            placeholder: "ui-state-highlight"

        });
        ul_sortable.disableSelection();


    });


    /*
* Saving and displaying serialized data
*/

    function saveData() {
        var ul_sortable = $('#sortable'); //setup one variable for sortable holder that will be used in few places

        var sortable_data = ul_sortable.sortable('serialize'); // serialize data from ul#sortable

        $.confirm({
            autoClose: 'confirm|3000',
            buttons: {
                confirm: {
                    text: '确定',
                    action: function () {
                    }
                }
            },
            content: function () {
                var self = this;
                return $.ajax({
                    url: 'ajax/updateindex.php',
                    data: sortable_data,
                    dataType: 'json',
                    method: 'POST'
                }).done(function (response) {
                    var result = response.result;
                    if (result) {
                        self.setTitle('保存成功');
                        self.setContent('新排序已成功保存');
                        self.setType('green');
                    }
                    else {
                        self.setTitle('保存失败');
                        self.setContent('抱歉，保存失败。');
                        self.setContentAppend('<br>' + '抱歉！内部数据错误，请稍后重试。');
                        self.setType('red');
                    }
                }).fail(function () {
                    self.setTitle('保存失败');
                    self.setContent('服务器错误，保存失败。');
                    self.setType('red');
                });
            }
        });
    }

    function deleteAllData() {
        $.confirm({
            title: '确认重置吗？',
            type: 'red',
            typeAnimated: true,
            buttons: {
                danger: {
                    text: '移除',
                    action: function () {
                        $.confirm({
                            houseClose: 'confirm|3000',
                            buttons: {
                                confirm: {
                                    text: '确定',
                                    action: function () {
                                        location.reload();
                                    }
                                }
                            },
                            content: function () {
                                var self = this;
                                return $.ajax({
                                    url: 'ajax/hotindex.php',
                                    dataType: 'json',
                                    method: 'POST',
                                    data: {
                                        type: 'delete-list-all'
                                    }
                                }).done(function (response) {
                                    var result = response.result;
                                    if (result) {
                                        self.setTitle('重置成功');
                                        self.setContent('重置成功');
                                        self.setType('green');
                                    }
                                    else {
                                        self.setTitle('移除失败');
                                        self.setContent('重置失败');
                                        self.setContentAppend('<br>' + '抱歉！内部数据错误，请稍后重试。');
                                        self.setType('red');
                                    }

                                }).fail(function () {
                                    self.setTitle('服务器连接失败');
                                    self.setContent('抱歉！服务器连接失败，请检查网络后重试。');
                                    self.setType('red');
                                });
                            }
                        });
                    }
                },
                cancel: {
                    text: '取消',
                    btnClass: 'btn-red',
                    action: function () {
                    }
                }
            }
        });
    }

    function addIndexPost(postIndex, postTitle) {
        $.confirm({
            autoClose: 'confirm|3000',
            buttons: {
                confirm: {
                    text: '确定',
                    action: function () {
                        location.reload();
                    }
                }
            },
            content: function () {
                var self = this;
                return $.ajax({
                    url: 'ajax/hotindex.php',
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        id: postIndex,
                        type: 'add-list-index'
                    }
                }).done(function (response) {
                    var result = response.result;
                    if (result) {
                        self.setTitle('添加成功');
                        self.setContent('新排序已成功保存');
                        self.setType('green');
                    }
                    else {
                        self.setTitle('添加失败');
                        self.setContent('抱歉，添加失败。');
                        self.setContentAppend('<br>' + response.message);
                        self.setType('red');
                    }
                }).fail(function () {
                    self.setTitle('添加失败');
                    self.setContent('服务器错误，添加失败。');
                    self.setType('red');
                });
            }
        });
    }

    function deleteIndexPost(postIndex, postTitle) {
        $.confirm({
            title: '确认从头条中移除吗？',
            content: postTitle,
            type: 'red',
            typeAnimated: true,
            buttons: {
                danger: {
                    text: '移除',
                    action: function () {
                        $.confirm({
                            houseClose: 'confirm|3000',
                            buttons: {
                                confirm: {
                                    text: '确定',
                                    action: function () {
                                        location.reload();
                                    }
                                }
                            },
                            content: function () {
                                var self = this;
                                return $.ajax({
                                    url: 'ajax/hotindex.php',
                                    dataType: 'json',
                                    method: 'POST',
                                    data: {
                                        id: postIndex,
                                        type: 'delete-list-index'
                                    }
                                }).done(function (response) {
                                    var result = response.result;
                                    if (result) {
                                        self.setTitle('移除成功');
                                        self.setContent(postTitle + ' 移除成功');
                                        self.setType('green');
                                    }
                                    else {
                                        self.setTitle('移除失败');
                                        self.setContent(postTitle+ ' 移除失败');
                                        self.setContentAppend('<br>' + '抱歉！内部数据错误，请稍后重试。');
                                        self.setType('red');
                                    }

                                }).fail(function () {
                                    self.setTitle('服务器连接失败');
                                    self.setContent('抱歉！服务器连接失败，请检查网络后重试。');
                                    self.setType('red');
                                });
                            }
                        });
                    }
                },
                cancel: {
                    text: '取消',
                    btnClass: 'btn-red',
                    action: function () {
                    }
                }
            }
        });
    }


</script>
</body>
</html>
