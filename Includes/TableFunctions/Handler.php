<?php

class Handler
{

    private $ImageBasepath = "https://mwonyaa.com/";

    // track update info

    public function __construct($con, $redis_con)
    {
        $this->conn = $con;
        $this->redis = $redis_con;
        $this->version = 18; // VersionCode
    }




    public   function Versioning()
    {
        $userID = isset($_GET['userID']) ? htmlspecialchars(strip_tags($_GET["userID"])) : 'general_user';
        $current_now = date('Y-m-d H:i:s');
        $user_subscription_sql = "SELECT pt.user_id AS userId, pt.subscription_type AS subscription_type, pt.amount AS planCost, pt.plan_duration AS durationInDays, UNIX_TIMESTAMP(pt.payment_created_date) * 1000 AS date FROM pesapal_transactions pt JOIN pesapal_payment_status pps ON pt.merchant_reference = pps.merchant_reference WHERE pt.subscription_type <> 'artist_circle' AND pt.user_id = '$userID' AND pps.status_code = 1 AND pt.plan_start_datetime <= '$current_now' AND pt.plan_end_datetime >= '$current_now' ORDER BY pt.payment_created_date DESC LIMIT 1";

        $subscription_details = array();
        $user_subscription_sql_result = mysqli_query($this->conn, $user_subscription_sql);
        while ($row = mysqli_fetch_array($user_subscription_sql_result)) {
            $temp = array();
            $temp['id'] = $row['userId'];
            $temp['subscription_type'] = $row['subscription_type'];
            $temp['planCost'] = $row['planCost'];
            $temp['durationInDays'] = $row['durationInDays'];
            $temp['date'] = $row['date'];
            array_push($subscription_details, $temp);
        }

        $itemRecords = array();
        $itemRecords["version"] = "20"; // build number should match
        $itemRecords["update"] = true; // update dialog dismissable
        $itemRecords["subcription"] = $subscription_details;
        $itemRecords["message"] = "We have new updates for you";
        return $itemRecords;
    }

   
}
