<?php
use \App\Integration\TwoPerformantOperations;

class UpdateRetailers {
    private $twoPerformantAPI = null;
    private $acceptedAdvertisers = [];
    private $retailers = [];
    private $categories = [];

    const ADVERTISER_STATUS_ACCEPTED = 'accepted';

    const RETAILER_STATUS_INACTIVE = "inactive";
    const RETAILER_STATUS_ACTIVE = "active";
    const ACCEPTED_CURRENCY = "RON";

    public function __construct() {
        require '2PerformantIntegration/TwoPerformantOperations.php';
        $this->twoPerformantAPI = new TwoPerformantOperations(new \App\Config\Config(), new \Monolog\Logger(TwoPerformantOperations::class));
    }

    public function start() {
        $this->buildAcceptedAdvertisers();
        $this->updateRetailersCategories();
        $this->buildRetailers();
        $this->populateRetailersTable();
    }

    private function buildAcceptedAdvertisers($page = 1) {
        $advertisersChunk = $this->twoPerformantAPI->getAdvertisers($page, self::ADVERTISER_STATUS_ACCEPTED);

        if(!empty($advertisersChunk)) {
            foreach($advertisersChunk as $advertiser) {
                if(empty($advertiser->getCashback()) || $advertiser->getCurrency() != self::ACCEPTED_CURRENCY) {
                    continue;
                }

                $this->acceptedAdvertisers[$advertiser->getProgramId()] = $advertiser;
            }

            $this->buildRetailersCategories($advertisersChunk);
            $this->buildAcceptedAdvertisers($page + 1);
        }
    }

    private function buildRetailers() {
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_retailers`");
        while($retailer = mysqli_fetch_assoc($query)) {
            $this->retailers[$retailer['program_id']] = $retailer;
        }
    }

    private function buildRetailersCategories($advertisers) {
        foreach($advertisers as $advertiser) {
            $advertiserCategory = $advertiser->getCategory();

            $this->categories[$advertiserCategory->id] = $advertiserCategory->name;
        }
    }

    private function updateRetailersCategories() {
        foreach($this->categories as $category) {
            $query = smart_mysql_query("SELECT * FROM `cashbackengine_categories` WHERE name = '$category'");

            if(empty(mysqli_fetch_row($query))) {
                smart_mysql_query("INSERT INTO `cashbackengine_categories`(`name`) VALUES('$category') ");
            }
        }
    }

    private function populateRetailersTable() {
        foreach($this->acceptedAdvertisers as $acceptedAdvertiser) {
            if($this->isProgramIdInRetailers($acceptedAdvertiser->getProgramId())) {
                $this->updateRetailerDetails($acceptedAdvertiser->getProgramId());
                continue;
            }

            $this->insertAdvertiser($acceptedAdvertiser);
            $this->updateRetailerDetails($acceptedAdvertiser->getProgramId());
        }

        $this->inactivateAdvertisers();
    }

    private function insertAdvertiser($advertiser) {
        global $conn;
        $title = $advertiser->getAdvertiserName();
        $programID = $advertiser->getProgramId();
        $url = $advertiser->getUrl();
        $image = $advertiser->getImageUrl();
        $cashback = $advertiser->getCashback();
        $conditions = mysqli_real_escape_string($conn, $advertiser->getConditions());
        $description = mysqli_real_escape_string($conn, $advertiser->getDescription());
        $website = $advertiser->getUrl();
        $retailerUrl = $advertiser->getUrl();
        $seoTitle = $advertiser->getAdvertiserName();
        $added = date('Y-m-d H:i:s');

        $query = "INSERT INTO `cashbackengine_retailers` (`title`, `program_id`, `url`, `image`, `cashback`, `conditions`, `description`, `website`, `retailer_url`, `seo_title`, `added`) 
            VALUES ('$title', '$programID', '$url', '$image', '$cashback', '$conditions', '$description', '$website', '$retailerUrl', '$seoTitle', '$added')";
        smart_mysql_query($query);
    }

    private function isProgramIdInRetailers($programId) {
        $isProgramInRetailers = !empty($this->retailers[$programId]);

        // verify inactive status
        if($isProgramInRetailers) {
            $retailer = $this->retailers[$programId];

            if($retailer['status'] != self::RETAILER_STATUS_ACTIVE) {
                smart_mysql_query("UPDATE `cashbackengine_retailers` SET `status` = '".self::RETAILER_STATUS_ACTIVE."' WHERE retailer_id = ".$retailer['retailer_id']);
            }
        }

        return $isProgramInRetailers;
    }

    private function inactivateAdvertisers() {
        foreach($this->retailers as $retailer) {
            if(!array_key_exists($retailer['program_id'], $this->acceptedAdvertisers)) {
                smart_mysql_query("UPDATE `cashbackengine_retailers` SET `status` = '".self::RETAILER_STATUS_INACTIVE."' WHERE retailer_id = ".$retailer['retailer_id']);
            }
        }
    }

    private function updateRetailerDetails($programId) {
        $this->updateRetailerCategory($programId);
        $this->updateRetailerCountry($programId);
    }

    private function updateRetailerCategory($programId) {
        // get retailer
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_retailers` WHERE program_id = '$programId'");
        $retailer = mysqli_fetch_assoc($query);

        if(empty($retailer)) {
            return;
        }

        $categoryName = $this->acceptedAdvertisers[$programId]->getCategory()->name;

        if(empty($categoryName)) {
            return;
        }

        // get category
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_categories` WHERE name = '$categoryName'");
        $category = mysqli_fetch_assoc($query);

        if(empty($category)) {
            return;
        }

        // get retailer category
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_retailer_to_category` WHERE retailer_id = '".$retailer['retailer_id']."'");
        $retailerCategory = mysqli_fetch_assoc($query);

        // insert
        if(empty($retailerCategory)) {
            return smart_mysql_query("INSERT INTO `cashbackengine_retailer_to_category` VALUES('".$retailer['retailer_id']."', '".$category['category_id']."')");
        }

        // update
        return smart_mysql_query("UPDATE `cashbackengine_retailer_to_category` SET category_id = '".$category['category_id']."' WHERE retailer_id = '".$retailer['retailer_id']."'");
    }

    private function updateRetailerCountry($programId) {
        if(empty($advertiser = $this->acceptedAdvertisers[$programId])) {
            return;
        }

        // get retailer
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_retailers` WHERE program_id = '$programId'");
        $retailer = mysqli_fetch_assoc($query);

        if(empty($retailer)) {
            return;
        }

        // clear countries
        smart_mysql_query("DELETE FROM `cashbackengine_retailer_to_country` WHERE retailer_id = '".$retailer['retailer_id']."'");

        if(empty($advertiser->getCountries())) {
            return false;
        }

        foreach($advertiser->getCountries() as $country) {
            $query = smart_mysql_query("SELECT * FROM `cashbackengine_countries` WHERE code = '".$country->code."'");
            $countryDB = mysqli_fetch_assoc($query);

            smart_mysql_query("INSERT INTO `cashbackengine_retailer_to_country` VALUES('".$retailer['retailer_id']."', '".$country['country_id']."')");
        }
    }
}