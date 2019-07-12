<?php
use \App\Integration\TwoPerformantOperations;

class UpdateCoupons
{
    private $twoPerformantAPI = null;

    const COUPONS_STATUS_ACCEPTED = 'accepted';

    public function __construct() {
        require '2PerformantIntegration/TwoPerformantOperations.php';
        $this->twoPerformantAPI = new TwoPerformantOperations(new \App\Config\Config(), new \Monolog\Logger(TwoPerformantOperations::class));
    }

    public function start() {
        $this->buildPromotions();
    }

    private function buildPromotions($page = 1) {
        $promotions = $this->twoPerformantAPI->getPromotions($page, self::COUPONS_STATUS_ACCEPTED);

        if(empty($promotions)) {
            return;
        }

        foreach($promotions as $promotion) {
            if($this->promotionExists($promotion)) {
                continue;
            }

            $this->addPromotion($promotion);
        }

        return $this->buildPromotions($page + 1);
    }

    private function promotionExists($promotion) {
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_coupons` WHERE 2performant_promotion_id = '".$promotion->getId()."'");
        $promotion = mysqli_fetch_assoc($query);

        return !empty($promotion);
    }

    private function addPromotion($promotion) {
        if(empty($retailer = $this->getRetailer($promotion->getProgramId()))) {
            return false;
        }

        global $conn;

        $retailerId = $retailer['retailer_id'];
        $title = mysqli_real_escape_string($conn, $promotion->getName());
        $couponType = 'discount';
        $startDate = $promotion->getStartDate();
        $endDate = $promotion->getEndDate();
        $twoPerformantPromotionId = $promotion->getId();
        $added = date('Y-m-d H:i:s');
        $link = $promotion->getLink();

        smart_mysql_query("INSERT INTO `cashbackengine_coupons` (`retailer_id`, `title`, `coupon_type`, `link`, `start_date`, `end_date`, `added`, `2performant_promotion_id`)
        VALUES('$retailerId', '$title', '$couponType', '$link', '$startDate', '$endDate', '$added', '$twoPerformantPromotionId')");
    }

    private function getRetailer($programId) {
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_retailers` WHERE program_id = '$programId'");
        return mysqli_fetch_assoc($query);
    }
}