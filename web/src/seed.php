<?php

namespace icloudems\assignment;

class seed extends module
{
    public static function branches()
    {
        $db = self::init();
        $db->mode(1);
        $data = $db->select("temp", "distinct faculty", "where faculty!=''");
        foreach ($data as $key => $value) {
            $temp = $db->select("branches", "*", "where branch_name='{$value['faculty']}'");
            if (count($temp) > 0) continue;
            $db->insert("branches", [
                "branch_name" => $value["faculty"]
            ]);
        }
        $entries = count($db->select("branches"));
        view::api([
            "status" => "success",
            "entries" => $entries
        ]);
    }
    public static function entrymode()
    {
        $db = self::init();
        $db->mode(1);
        $fldNames = [
            "id",
            "entry_mode_name",
            "crdr",
            "entry_mode_no"
        ];
        $data = [
            [1, 'DUE', 'D', 0],
            [2, 'REVDUE', 'C', 12],
            [3, 'SCHOLARSHIP', 'C', 15],
            [4, 'REVSCHOLARSHIP', 'D', 16],
            [5, 'REVCONCESSION', 'D', 16],
            [6, 'CONCESSION', 'C', 15],
            [7, 'RCPT', 'C', 0],
            [8, 'REVRCPT', 'D', 0],
            [9, 'JV', 'C', 14],
            [10, 'REVJV', 'D', 14],
            [11, 'PMT', 'D', 1],
            [12, 'REVPMT', 'C', 1],
            [13, 'Fundtransfer', '+', 1],
        ];
        foreach ($data as $key => $value) {
            $temp = $db->select("entrymode", "*", "where entry_mode_name='{$value[1]}'");
            if (count($temp) > 0) continue;
            $db->insert("entrymode", [
                $fldNames[0] => $value[0],
                $fldNames[1] => $value[1],
                $fldNames[2] => $value[2],
                $fldNames[3] => $value[3]
            ]);
        }
        $entries = count($db->select("entrymode"));
        view::api([
            "status" => "success",
            "entries" => $entries
        ]);
    }
    public static function module()
    {
        $db = self::init();
        $db->mode(1);
        $fldNames = [
            "id",
            "module_name"
        ];
        $data = [
            [1, "academic"],
            [11, "academicmisc"],
            [2, "hostel"],
            [22, "hostelmisc"],
            [3, "transport"],
            [33, "transportmisc"]
        ];
        foreach ($data as $key => $value) {
            $temp = $db->select("module", "*", "where module_name='{$value[1]}'");
            if (count($temp) > 0) continue;
            $db->insert("module", [
                $fldNames[0] => $value[0],
                $fldNames[1] => $value[1]
            ]);
        }
        $entries = count($db->select("module"));
        view::api([
            "status" => "success",
            "entries" => $entries
        ]);
    }
    public static function feecategory()
    {
        $db = self::init();
        $db->mode(1);
        $data = $db->select("temp", "distinct fee_category", "where fee_category!=''");
        $branches = $db->select("branches", "*");
        $output = [];
        foreach ($branches as $branch) {
            foreach ($data as $feecategory) {
                $temp = $db->select("feecategory", "*", "where fee_category='{$feecategory['fee_category']}' and branch_id='{$branch['id']}'");
                if (count($temp) > 0) continue;
                $db->insert("feecategory", [
                    "branch_id" => $branch["id"],
                    "fee_category" => $feecategory["fee_category"]
                ]);
            }
        }
        $entries = count($db->select("feecategory"));
        view::api(
            [
                "status" => "success",
                "entries" => $entries
            ]
        );
    }

    public static function feecollectiontypes()
    {
        $db = self::init();
        $db->mode(1);
        $branches = $db->select("branches", "*");
        $id = 1;
        foreach ($db->select("module") as $module) {
            // print_r($module);die();
            foreach ($branches as $branch) {
                $temp = $db->select(
                    "feecollectiontypes",
                    "*",
                    "where branch_id='{$branch['id']}' and collection_head='{$module['module_name']}'"
                );
                if (count($temp) > 0) continue;
                $db->insert("feecollectiontypes", [
                    "collection_head" => $module['module_name'],
                    "collection_desc" => $module['module_name'],
                    "branch_id" => $branch["id"]
                ]);
            }
        }
        $entries = count($db->select("feecollectiontypes"));
        view::api([
            "status" => "success",
            "entries" => $entries
        ]);
    }
    public static function feetypes()
    {
        try {
            $db = self::init();
            $db->mode(1);
            $data = array_reduce($db->select("temp", "distinct fee_head", "where fee_head!=''"), function ($acc, $cur) {
                $acc[] = [
                    "fee_type" => $cur["fee_head"],
                    "id" => (count($acc) + 1)
                ];
                return $acc;
            }, []);
            $output = [];
            $branches = $db->select("branches", "*");
            foreach ($branches as $branch) {
                $feecategories = $db->select("feecategory", "*", "where branch_id='{$branch['id']}'");
                $collectiontypes = $db->select("feecollectiontypes", "*", "where branch_id='{$branch['id']}'");
                foreach ($feecategories as $feecategory) {
                    foreach ($collectiontypes as $collectiontype) {
                        foreach ($data as $key => $value) {
                            $temp = $db->select("feetypes", "*", "where fee_category = '{$feecategory['id']}' and fee_name = '{$value['fee_type']}' and collection_id = '{$collectiontype['id']}' and branch_id = '{$branch['id']}' and seq_id = '{$value['id']}' and fee_type_ledger = '{$value['fee_type']}' and fee_headtype = 1");
                            if (count($temp) > 0) continue;

                            $db->insert("feetypes", [
                                "fee_category" => $feecategory["id"],
                                "fee_name" => $value['fee_type'],
                                "collection_id" => $collectiontype["id"],
                                "branch_id" => $branch["id"],
                                "seq_id" => $value['id'],
                                "fee_type_ledger" => $value['fee_type'],
                                "fee_headtype" => 1
                            ]);
                        }
                        # code...
                    }
                    # code...
                }
            }
            $entries = count($db->select("feetypes"));
            view::api([
                "status" => "success",
                "entries" => $entries
            ]);
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            view::api([
                "status" => "failed",
                "message" => $th->getMessage()
            ]);
        }
    }
    public static function financial_trans()
    {

        $db = self::init();
        $db->mode(1);
        $amt = [
            "DUE" => "due_amount",
            "REVDUE" => "write_off_amount",
            "SCHOLARSHIP" => "scholarship_amount",
            "REVSCHOLARSHIP" => "reverse_concession_amount",
            "REVCONCESSION" => "reverse_concession_amount",
            "CONCESSION" => "concession_amount",
            "RCPT" => "paid_amount",
            "REVRCPT" => "paid_amount",
            "JV" => "adjusted_amount",
            "REVJV" => "adjusted_amount",
            "PMT" => "refund_amount",
            "REVPMT" => "refund_amount",
            "Fundtransfer" => "fund_trancfer_amount"
        ];
        $branches = array_reduce($db->select("branches"), function ($acc, $cur) {
            $acc[$cur["branch_name"]] = $cur["id"];
            return $acc;
        }, []);
        $db->mode(2);
        $entrymodes = $db->select("entrymode", "entry_mode_name as id,crdr,entry_mode_no");
        // view::api($entrymodes);
        $db->mode(1);
        $inputs = request::inputs();
        // print_r($inputs);die;
        for ($i = $inputs['start'] ?? 1; $i <= $inputs['end'] ?? 100; $i++) {

            $temp = $db->select("temp", "roll_no_,receipt_no_,admno_uniqueid,fee_head,date,academic_year,fee_category,voucher_type,voucher_no_,faculty,sum(due_amount),sum(paid_amount),sum(concession_amount),sum(scholarship_amount),sum(reverse_concession_amount),sum(write_off_amount),sum(adjusted_amount),sum(refund_amount),sum(fund_trancfer_amount)", " where sr_ = {$i} limit 1")[0];
            $temp['date'] = implode("-", array_reverse(explode("-", $temp['date'])));
            $entryMode = $temp["voucher_type"];
            $fintran = [
                "module_id" => 1,
                "trans_id" => self::voucher($temp['voucher_no_']),
                "adm_no" => $temp['admno_uniqueid'],
                "amount" => $temp[$amt[$entryMode]],
                "crdr" => $entrymodes[$entryMode]['crdr'],
                "tran_date" => $temp['date'],
                "acad_year" => $temp['academic_year'],
                "entry_mode" => $entrymodes[$entryMode]['entry_mode_no'],
                "voucherno" => $temp['voucher_no_'],
                "branch_id" => $branches[$temp['faculty']],
                "type_of_concession" => ['CONCESSION' => 1, 'SCHOLARSHIP' => 2][$entryMode] ?? null
            ];
            $resp = $db->insert("financial_trans", $fintran);
            $fee_category = $db->select("feecategory", "*", "where branch_id={$branches[$temp['faculty']]} and fee_category = '{$temp['fee_category']}'")[0] ?? [
                'id' => 0,
                'branch_id' => 0,
                'fee_category' => ''
            ];
            $fee_head = $db->select("feetypes", "*", "where fee_name ='{$temp['fee_head']}' and fee_category={$fee_category['id']}")[0] ?? [
                'name' => '',
                'id' => 0
            ];
            $fintran_detail = [
                "fincial_trans_id" => $resp['id'],
                "module_id" => 1,
                "amount" => $fintran['amount'],
                "head_id" => $fee_head['id'],
                "crdr" => $fintran['crdr'],
                "branch_id" => $branches[$temp['faculty']],
                "head_name" => $fee_head['fee_name'] ?? $temp['fee_head'],
            ];
            $db->insert("financial_trans_detail", $fintran_detail);
            $common_fee_collection = [
                "module_id" => 1,
                "trans_id" => 0,
                "admno" => $temp['admno_uniqueid'],
                "rollno" => $temp['roll_no_'],
                "amount" => $temp[$amt[$entryMode]],
                "branch_id" => $branches[$temp['faculty']],
                "academic_year" => $temp['academic_year'],
                "financial_year" => $temp['academic_year'],
                'displayReceiptNo' => $temp['receipt_no_'],
                'entrymode' => $entrymodes[$entryMode]['entry_mode_no'],
                'paidDate' => $temp['date'],
                'inactive' => strstr("REV", $entryMode) ? 1 : 0
            ];
            $cfc = $db->insert("common_fee_collection", $common_fee_collection);
            $common_fee_collection_headwise = [
                "moduleid" => 1,
                "receipt_id" => $cfc['id'],
                "head_id" => $fee_head['id'] ?? 0,
                "head_name" => $fee_head['name'] ?? $temp['fee_head'],
                "branch_id" => $branches[$temp['faculty']],
                "amount" => $temp[$amt[$entryMode]],
            ];
            $db->insert("common_fee_collection_headwise", $common_fee_collection_headwise);
        }
        view::render("transtat.twig", [
            "status" => "Success",
            "page" => [
                "title" => "Loading Transactions",
                "start" => $inputs['end'] ?? 1,
                "limit" => 100
            ],
            "entries" => [
                "common_fee_collection_headwise" => $db->select("common_fee_collection_headwise", "count(*) as count")[0]['count'],
                "common_fee_collection" => $db->select("common_fee_collection", "count(*) as count")[0]['count'],
                "financial_trans" => $db->select("financial_trans", "count(*) as count")[0]['count'],
                "financial_trans_detail" => $db->select("financial_trans_detail", "count(*) as count")[0]['count'],
            ]
        ]);
    }
    public static function voucher($voucher)
    {
        $db = self::init();
        $db->mode(1);
        $db->query("CREATE TABLE IF NOT EXISTS `voucher_tmp`(
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `voucher_no` text NOT NULL,
            PRIMARY KEY (`id`)
        )");
        $resp = $db->select("voucher_tmp", "id", "where voucher_no='{$voucher}'");
        if (count($resp) > 0) {
            return $resp[0];
        }
        return $db->insert("voucher_tmp", [
            "voucher_no" => $voucher
        ]);
    }
    public static function rcpt($rcpt)
    {
        $db = self::init();
        $db->mode(1);
        $db->query("CREATE TABLE IF NOT EXISTS `rcpt_tmp`(
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `receipt_no` text NOT NULL,
            PRIMARY KEY (`id`)
        )");
        $resp = $db->select("rcpt_tmp", "id", "where receipt_no='{$rcpt}'");
        if (count($resp) > 0) {
            return $resp[0];
        }
        return $db->insert("rcpt_tmp", [
            "receipt_no" => $rcpt
        ]);
    }
    public static function fintxndetail()
    {
        $inputs = request::inputs();
        $db = self::init();
        $db->mode(1);
        $db->query("CREATE TABLE IF NOT EXISTS temp_fintxndetail(id int not null)");
        $db->query("CREATE TABLE IF NOT EXISTS temp_vouchers as select voucher_no_,count(*) as records from temp group by voucher_no_");
        $temp = $db->select("temp_vouchers", "*", "limit 1")[0] ?? [];
        if (isset($temp['id']) == false) {
            $db->query("ALTER TABLE temp_vouchers ADD COLUMN id INT NOT NULL AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT");
        }
        $db->query("CREATE TABLE IF NOT EXISTS temp_receipt as select receipt_no_,count(*) as records from temp group by receipt_no_");
        $temp = $db->select("temp_receipt", "*", "limit 1")[0] ?? [];
        if (isset($temp['id']) == false) {
            $db->query("ALTER TABLE temp_receipt ADD COLUMN id INT NOT NULL AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT");
        }
        
        $amt = [
            "DUE" => "due_amount",
            "REVDUE" => "write_off_amount",
            "SCHOLARSHIP" => "scholarship_amount",
            "REVSCHOLARSHIP" => "reverse_concession_amount",
            "REVCONCESSION" => "reverse_concession_amount",
            "CONCESSION" => "concession_amount",
            "RCPT" => "paid_amount",
            "REVRCPT" => "paid_amount",
            "JV" => "adjusted_amount",
            "REVJV" => "adjusted_amount",
            "PMT" => "refund_amount",
            "REVPMT" => "refund_amount",
            "Fundtransfer" => "fund_trancfer_amount"
        ];
        $branches = array_reduce($db->select("branches"), function ($acc, $cur) {
            $acc[$cur["branch_name"]] = $cur["id"];
            return $acc;
        }, []);
        $db->mode(2);
        $entrymodes = $db->select("entrymode", "entry_mode_name as id,crdr,entry_mode_no");
        $db->mode(1);
        $counter=$db->select("temp_fintxndetail")[0]??['id'=>0];
        $clause="where sr_ > {$counter['id']} ";
        foreach ($db->select("temp", "sr_,roll_no_,receipt_no_,admno_uniqueid,fee_head,date,academic_year,fee_category,voucher_type,voucher_no_,faculty,due_amount,paid_amount,concession_amount,scholarship_amount,reverse_concession_amount,write_off_amount,adjusted_amount,refund_amount,fund_trancfer_amount", " {$clause} limit 2000")
         as $temp) {
            $fee_category = $db->select("feecategory", "*", "where branch_id={$branches[$temp['faculty']]} and fee_category = '{$temp['fee_category']}'")[0] ?? [
                'id' => 0,
                'branch_id' => 0,
                'fee_category' => ''
            ];
            $fee_head = $db->select("feetypes", "*", "where fee_name ='{$temp['fee_head']}' and fee_category={$fee_category['id']}")[0] ?? [
                'name' => '',
                'id' => 0
            ];
            $entryMode = $temp["voucher_type"];
            $voucherid = self::voucher($temp['voucher_no_'])['id'];
            $db->insert("financial_trans_detail", [
                "fincial_trans_id" => $voucherid,
                "module_id" => 1,
                "amount" => $temp[$amt[$entryMode]],
                "head_id" => $fee_head['id'],
                "crdr" => $entrymodes[$entryMode]['crdr'],
                "branch_id" => $branches[$temp['faculty']],
                "head_name" => $fee_head['fee_name'] ?? $temp['fee_head'],
            ]);
            $rcpt_id = self::rcpt($temp['receipt_no_'])['id'];
            $db->insert("common_fee_collection_headwise", [
                "moduleid" => 1,
                "receipt_id" => $rcpt_id,
                "head_id" => $fee_head['id'] ?? 0,
                "head_name" => $fee_head['name'] ?? $temp['fee_head'],
                "branch_id" => $branches[$temp['faculty']],
                "amount" => $temp[$amt[$entryMode]],
            ]);
            $db->update("temp_fintxndetail", [ "id" => $temp["sr_"]]);
        }

        $fee_collection = $db->select("common_fee_collection_headwise", "count(*) as count")[0]['count'] ?? 0;
        $fintrandet = $db->select("financial_trans_detail", "count(*) as count")[0]['count'] ?? 0;
        view::api([
            "status" => "success",
            "next" => [
                "url" => "/seed/fintxndetail",
                "title" => "Loading Financial Transaction Details...",
                "message" => "Common Fee Collection{$fee_collection} | Financial Transaction Details {$fintrandet}"
            ]
        ]);
    }
    public static function comfeecollection() {}
    public static function comfeecollectionheadwise() {}
}
