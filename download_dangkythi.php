<?php
require_once dirname(__FILE__) . '/excel_reader/PHPExcel/Classes/PHPExcel.php';
require_once('../config.php');
$lop = $_POST['malop'];
if($lop == NULL)
{
    throw new coding_exception('Mã lớp không được để trống');
}
$mathichu = $_POST['mathichu'];//dd
function download_module_offline_cohort($lop,$mathichu)
{
    echo "đang cập nhật";
}
function detect_templateid_cohort($lop,$mathichu)
{
    global $DB;
    $sql = "
    select co.templateid as loailop
from mdl232x0_course co
INNER JOIN mdl232x0_cohort c ON c.courseid = co.id
INNER JOIN mdl232x0_cohort_examcode cma ON cma.cohortid = c.id
where c.idnumber = N'$lop'
    ";
    if(!empty($mathichu))
    {
        $sql .= "and cma.examcode = N'$mathichu'";
    }
    $cohort = $DB->get_record_sql($sql,array());
    $result = $cohort->loailop;
    return $result;
}
function download_module_online_cohort($lop,$mathichu)
{
    $loailop = detect_templateid_cohort($lop,$mathichu);
    if($loailop == 1)
    {
        echo "Lớp PSS /$loailop";
    }
    else
    {
        echo "Lớp kỹ năng/lớp thường /$loailop";
    }
}
function null_examcode($mathichu) // Kiểm tra mã thi có tồn tại không
{
    global $DB;
    $sql = "select id from mdl232x0_cohort_examcode
    where examcode = N'$mathichu'";
    $cohort_examcode = $DB->get_record_sql($sql,array());
    if($cohort_examcode == NULL)
    {
        throw new coding_exception('Bạn đã nhập sai mã thi chữ');
    }
}
function detect_type_cohort($lop,$mathichu)
{   
    global $DB;
    $sql = "
    select c.online as online_status
    from mdl232x0_cohort c
    INNER JOIN mdl232x0_cohort_examcode cma ON cma.cohortid = c.id
    where c.idnumber = N'$lop'
    ";
    if(!empty($mathichu))
    {
        $sql .= "and cma.examcode = N'$mathichu'";
    }
   
    $cohort = $DB->get_record_sql($sql,array());
    if($cohort->online_status == NULL)
    {
        throw new coding_exception('Mã lớp không giống mã thi chữ');
    }
    else
    {
        $result = $cohort->online_status;
        return $result;
    }
}
if(!empty($mathichu))
{
    null_examcode($mathichu); // kiểm tra mã thi chữ true false
}
$status = detect_type_cohort($lop,$mathichu);
if($status == 1)
{
    echo "<br>Lớp Online<br>";
    download_module_online_cohort($lop,$mathichu);
}
else
{
    echo "<br>Lớp Offline<br>";
    download_module_offline_cohort($lop,$mathichu);
}