<?php

namespace icloudems\assignment;

class table extends module
{
    private static function sql($sql)
    {
        try {
            $db = self::init();
            $db->query($sql);
            view::api([
                "status" => "success"
            ]);
        } catch (\Throwable $th) {
            view::api([
                "status" => "failed",
                "message" => $th->getMessage()
            ]);
        }
    }
    public static function temp()
    {
        $resp = self::sql("CREATE TABLE IF NOT EXISTS `temp` (
                `sr_` text DEFAULT NULL COMMENT 'Sr.',
                `date` text DEFAULT NULL COMMENT 'Date',
                `academic_year` text DEFAULT NULL COMMENT 'Academic Year',
                `session` text DEFAULT NULL COMMENT 'Session',
                `alloted_category` text DEFAULT NULL COMMENT 'Alloted Category',
                `voucher_type` text DEFAULT NULL COMMENT 'Voucher Type',
                `voucher_no_` text DEFAULT NULL COMMENT 'Voucher No.',
                `roll_no_` text DEFAULT NULL COMMENT 'Roll No.',
                `admno_uniqueid` text DEFAULT NULL COMMENT 'Admno/UniqueId',
                `status` text DEFAULT NULL COMMENT 'Status',
                `fee_category` text DEFAULT NULL COMMENT 'Fee Category',
                `faculty` text DEFAULT NULL COMMENT 'Faculty',
                `program` text DEFAULT NULL COMMENT 'Program',
                `department` text DEFAULT NULL COMMENT 'Department',
                `batch` text DEFAULT NULL COMMENT 'Batch',
                `receipt_no_` text DEFAULT NULL COMMENT 'Receipt No.',
                `fee_head` text DEFAULT NULL COMMENT 'Fee Head',
                `due_amount` float(10,2) DEFAULT NULL COMMENT 'Due Amount',
                `paid_amount` float(10,2) DEFAULT NULL COMMENT 'Paid Amount',
                `concession_amount` float(10,2) DEFAULT NULL COMMENT 'Concession Amount',
                `scholarship_amount` float(10,2) DEFAULT NULL COMMENT 'Scholarship Amount',
                `reverse_concession_amount` float(10,2) DEFAULT NULL COMMENT 'Reverse Concession Amount',
                `write_off_amount` float(10,2) DEFAULT NULL COMMENT 'Write Off Amount',
                `adjusted_amount` float(10,2) DEFAULT NULL COMMENT 'Adjusted Amount',
                `refund_amount` float(10,2) DEFAULT NULL COMMENT 'Refund Amount',
                `fund_trancfer_amount` float(10,2) DEFAULT NULL COMMENT 'Fund TranCfer Amount',
                `remarks` text DEFAULT NULL COMMENT 'Remarks'
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    public static function branches()
    {
        self::sql("CREATE TABLE IF NOT EXISTS `branches` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `branch_name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
    }
    public static function fintxn()
    {
        view::render("autorun.twig", [
            "page" => [
                "title" => "Seeding Data",
                "message" => "",
                "next" => [
                    "url" => "/seed/fintxndetail?start=1&end=100",
                    "title" => "Loading Financial Transaction Details..."
                ]
            ]
        ]);
    }
    public static function fee_category()
    {
        self::sql(" CREATE TABLE IF NOT EXISTS `feecategory` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `branch_id` int(11) NOT NULL,
                `fee_category` varchar(255) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `branch_id` (`branch_id`),
                CONSTRAINT `feecategory_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
    }
    public static function fee_collection_type()
    {
        self::sql("CREATE TABLE IF NOT EXISTS `feecollectiontypes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `branch_id` int(11) NOT NULL,
                `collection_head` varchar(255) NOT NULL,
                `collection_desc` text NOT NULL,
                PRIMARY KEY (`id`),
                KEY `branch_id` (`branch_id`),
                CONSTRAINT `feecollectiontypes_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
    public static function fee_types()
    {
        self::sql(" CREATE TABLE IF NOT EXISTS `feetypes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `fee_category` int(11) NOT NULL,
                `fee_name` varchar(255) NOT NULL,
                `collection_id` int(11) NOT NULL,
                `branch_id` int(11) NOT NULL,
                `seq_id` int(11) NOT NULL COMMENT 'Unique for each fee name',
                `fee_type_ledger` text NOT NULL,
                `fee_headtype` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `fee_category` (`fee_category`),
                KEY `collection_id` (`collection_id`),
                KEY `branch_id` (`branch_id`),
                CONSTRAINT `feetypes_ibfk_1` FOREIGN KEY (`fee_category`) REFERENCES `feecategory` (`id`) ON DELETE CASCADE,
                CONSTRAINT `feetypes_ibfk_2` FOREIGN KEY (`collection_id`) REFERENCES `feecollectiontypes` (`id`) ON DELETE CASCADE,
                CONSTRAINT `feetypes_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
    }
    public static function entry_modes()
    {
        self::sql(" CREATE TABLE IF NOT EXISTS `entrymode` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `entry_mode_name` varchar(255) NOT NULL,
                  `crdr` varchar(1) NOT NULL,
                  `entry_mode_no` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
    }
    public static function modules()
    {
        self::sql("CREATE TABLE IF NOT EXISTS `module` (
                `id` int(11) NOT NULL,
                `module_name` varchar(255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
    public static function financial_transactions()
    {
        self::sql("CREATE TABLE IF NOT EXISTS `financial_trans` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `module_id` int(11) NOT NULL,
                `trans_id` varchar(10) NOT NULL,
                `adm_no` varchar(20) NOT NULL,
                `amount` float(10,2) NOT NULL,
                `crdr` varchar(1) NOT NULL,
                `tran_date` date NOT NULL,
                `acad_year` varchar(10) NOT NULL,
                `entry_mode` int(11) NOT NULL,
                `voucherno` varchar(255) NOT NULL,
                `branch_id` int(11) NOT NULL,
                `type_of_concession` text NOT NULL,
                PRIMARY KEY (`id`),
                KEY `entry_mode` (`entry_mode`),
                KEY `branch_id` (`branch_id`),
                CONSTRAINT `financial_trans_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
    public static function financial_transaction_details()
    {
        self::sql("CREATE TABLE IF NOT EXISTS `financial_trans_detail` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `fincial_trans_id` int(11) NOT NULL,
                `module_id` int(11) NOT NULL,
                `amount` float(10,2) NOT NULL,
                `head_id` int(11) NOT NULL,
                `crdr` varchar(1) NOT NULL,
                `branch_id` int(11) NOT NULL,
                `head_name` text NOT NULL,
                PRIMARY KEY (`id`),
                KEY `branch_id` (`branch_id`),
                KEY `head_id` (`head_id`),
                CONSTRAINT `financial_trans_detail_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
                CONSTRAINT `financial_trans_detail_ibfk_2` FOREIGN KEY (`head_id`) REFERENCES `feetypes` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
    public static function common_fee_collection()
    {
        self::sql("CREATE TABLE IF NOT EXISTS `common_fee_collection` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `module_id` int(11) NOT NULL,
            `trans_id` int(11) NOT NULL,
            `admno` varchar(100) NOT NULL,
            `rollno` varchar(100) NOT NULL,
            `amount` float(10,2) NOT NULL,
            `branch_id` int(11) NOT NULL,
            `academic_year` varchar(10) NOT NULL,
            `financial_year` varchar(10) NOT NULL,
            `displayReceiptNo` varchar(50) NOT NULL,
            `entrymode` int(11) NOT NULL,
            `paidDate` date NOT NULL,
            `inactive` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `branch_id` (`branch_id`),
            CONSTRAINT `common_fee_collection_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");
    }
    public static function common_fee_collection_heads()
    {
        self::sql("CREATE TABLE IF NOT EXISTS `common_fee_collection_headwise` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `moduleid` int(11) NOT NULL,
                `receipt_id` int(11) NOT NULL,
                `head_id` int(11) NOT NULL,
                `head_name` text NOT NULL,
                `branch_id` int(11) NOT NULL,
                `amount` float(10,2) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `head_id` (`head_id`),
                KEY `branch_id` (`branch_id`),
                CONSTRAINT `common_fee_collection_headwise_ibfk_2` FOREIGN KEY (`head_id`) REFERENCES `feetypes` (`id`) ON DELETE CASCADE,
                CONSTRAINT `common_fee_collection_headwise_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
    }
    public static function index()
    {
        $db = self::init();
        $tables = array_map(function ($tbl) {
            $db = module::init();
            $db->mode(1);
            return [
                "name" => $tbl,
                "rows" => $db->select($tbl, "count(*) as count")[0]['count']
            ];
        }, array_filter(
            $db->tables(),
            function ($arr) {
                return !array_search(
                    $arr,
                    ['', "temp", "common_fee_collection", "common_fee_collection_headwise", "financial_trans_detail", "financial_trans"]
                );
            }
        ));
        // view::api($tables);
        view::render("tables.twig", [
            "page" => [
                "title" => "Tables"
            ],
            "tables" => $tables
        ]);
    }
}
