<?php
#$sn = $_GET["sn"];
$sn = isset($_GET["sn"]) ? $_GET["sn"] : $argv[1];
$redis = new Redis();
$redis->connect('localhost',6379);
$flag = $redis->get($sn);
if($flag) {
    sleep(3);
} else {
    $redis->incr($sn);
}
try{
    $dbo = new PDO("mysql:host=127.0.0.1;dbname=playground","root","");
    $res = $dbo->query("select * from payment where sn=".$sn. " and status=0");
    $paymentInfo = $res->fetch(PDO::FETCH_ASSOC);
    echo microtime(true);
    if($paymentInfo) {
        $dbo->beginTransaction();
        $sql = "update payment set status=1 where sn=".$sn;
        $res = $dbo->query($sql);
        if($res) {
            $sql = "update user_account set amount=amount+".$paymentInfo["amount"]." where uid=".$paymentInfo["uid"];
            $res = $dbo->query($sql);
            if($res) {
                $dbo->commit();
                echo "success\n";
            } else {
                $db->rollback();
                echo "fail1\n";
            }
        } else {
            echo "fail2\n";
            $dbo->rollback();
        }
    } else {
        echo "none record or has dealed\n";
    }
} catch (\Exception $e){
    echo $e->getMessage."\n";
}
$redis->del($sn);

