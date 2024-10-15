<?php

namespace icloudems\assignment;

class csv extends module
{
    public static function index()
    {
        phpinfo();
    }
    public static function cleanup()
    {
        try {
            $db = self::init();
            $db->query("truncate temp;");
            view::api([
                "status" => "success",
                "message" => "Data truncated successfully",
                "next" => [
                    "url" => "/csv/prepare",
                    "title" => "Preparing...",
                    "message" => "Preparing for import"
                ]
            ]);
            //code...
        } catch (\Throwable $th) {
            view::api([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public static function loadtemp()
    {
        try {
            $db = self::init();
            $resp = $db->query("
                LOAD DATA LOCAL INFILE '/var/www/uploads/out-tmp.csv' INTO TABLE temp FIELDS TERMINATED BY ','  OPTIONALLY ENCLOSED BY '\'' LINES TERMINATED BY '\n' IGNORE 6 LINES
            ");
            view::api([
                "status" => "success",
                "message" => "Data imported successfully",
                "next" => [
                    "url" => "/csv/summary",
                    "title" => "Summarizing...",
                    "message" => "Summarizing uploaded data."
                ]
            ]);
            //code...
        } catch (\Throwable $th) {
            view::api([
                "status" => "failed",
                "message" => $th->getMessage()
            ]);
        }
    }
    public static function summary()
    {
        $db = self::init();
        $db->mode(1);
        $resp = $db->select("temp", "'Total Records' as name,count(*) as value", "union 
            select  'Sum of Due Amount' as name,sum(due_amount) as value from temp union 
            select  'Sum of Paid Amount' as name,sum(paid_amount) as value from temp union 
            select  'Sum of Concession Amount' as name,sum(concession_amount) as value from temp union 
            select  'Sum of Scholarship Amount' as name,sum(scholarship_amount) as value from temp union 
            select  'Sum of Reverse Concession Amount' as name,sum(reverse_concession_amount) as value from temp union 
            select  'Sum of Write off Amount' as name,sum(write_off_amount) as value from temp union 
            select  'Sum of Adjusted Amount' as name,sum(adjusted_amount) as value from temp union 
            select  'Sum of Refund Amount' as name,sum(refund_amount) as value from temp union 
            select  'Sum of Fund Trancfer Amount' as name,sum(fund_trancfer_amount) as value from temp
        ");
        view::api([
            "status" => "success",
            "data" => $resp,
            "next" => [
                "title" => "Summary of Temp Table",
                "action" => "/table"
            ]
        ]);
    }
    public static function upload()
    {
        switch (request::method()) {
            case 'GET':
                view::render("setup.twig", [
                    "page" => [
                        "title" => "Upload CSV",
                        "form" => "upload.twig"
                    ]
                ]);
                # code...
                break;
            case 'POST':
                $file = parent::upload();
                move_uploaded_file($file, PROJECTROOT . "/uploads/tmp.csv");
                view::api([
                    "status" => "success",
                    "message" => "File Uploaded",
                    "next" => [
                        "url" => "/csv/cleanup",
                        "title" => "Cleaning Up...",
                        "message" => "Cleaning up temp table"
                    ]
                ]);
                # code...
                break;

            default:
                # code...
                break;
        }
    }
    public static function prepare()
    {
        $filepath = PROJECTROOT . "/uploads/tmp.csv";
        $filepath = parent::convertToUTF8($filepath);
        view::api([
            "status" => "success",
            "message" => "Ready for import",
            "next" => [
                "url" => "/csv/loadtemp",
                "title" => "Processing...",
                "message" => "Importing to temp table"
            ]
        ]);
    }
}
