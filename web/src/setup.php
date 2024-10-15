<?php

namespace icloudems\assignment;

class setup extends module
{
    public static function schema()
    {
        switch (request::method()) {
            case 'GET':
                view::render("setup.twig", [
                    "page" => [
                        "title" => "Schema Setup",
                        "form" => "schema.twig"
                    ],
                    "tables" => [
                        [
                            "name" => "temp",
                            "text" => "Temporary Table"
                        ],
                        ["name" => "branches", "text" => "Branches"],
                        ["name" => "fee_category", "text" => "Fee Category"],
                        ["name" => "fee_collection_type", "text" => "Fee Collection Types"],
                        ["name" => "fee_types", "text" => "Fee Types"],
                        ["name" => "entry_modes", "text" => "Entry modes"],
                        ["name" => "modules", "text" => "Modules"],
                        ["name" => "financial_transactions", "text" => "Financial Transactions"],
                        ["name" => "financial_transaction_details", "text" => "Financial Transaction Details"],
                        ["name" => "common_fee_collection", "text" => "Common Fee Collection"],
                        ["name" => "common_fee_collection_heads", "text" => "Common Fee Collection Head Wise"],
                    ]
                ]);
                break;
            case 'POST':
                break;

            default:
                # code...
                break;
        }
    }
    public static function db()
    {
        switch (request::method()) {
            case 'GET':
                // check if file exists
                if (file_exists(PROJECTROOT . "/db.json")) controller::redirect("/setup/schema");
                view::render("setup.twig", [
                    "page" => [
                        "title" => "Database Setup",
                        "form" => "db.twig"
                    ]
                ]);
                break;
            case 'POST':
                try {
                    $inputs = request::inputs();
                    $db = new module($inputs);
                    $test = $db->tables();
                    file_put_contents(PROJECTROOT . "/db.json", json_encode($inputs));
                    view::api([
                        "status" => "success",
                        "message" => "Database Configuration Saved Successfully. Reload this page to continue."
                    ]);
                } catch (\Throwable $th) {
                    view::api([
                        "status" => "error",
                        "message" => "Unable to connect database. Please check the credentials"
                    ]);
                }
                break;
            case 'PUT':
                # code...
                break;
            case 'DELETE':
                # code...
                break;
            case 'PATCH':
                # code...
                break;
        }
    }
}
