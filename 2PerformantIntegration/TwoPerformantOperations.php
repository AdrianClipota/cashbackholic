<?php
/**
 * Created by PhpStorm.
 * User: cosmi
 * Date: 08-Oct-18
 * Time: 10:13 PM
 */

namespace App\Integration;
require 'vendor/autoload.php';
require 'Config.php';
require 'Utils.php';
require 'Advertiser.php';
require 'Promotion.php';
require 'ConfigurationException.php';

use App\Utils\Utils;
use Monolog\Handler\StreamHandler;
use TPerformant\API\Filter\AffiliateProgramFilter;
use TPerformant\API\Filter\AffiliateAdvertiserPromotionFilter;
use TPerformant\API\HTTP\Affiliate;
use TPerformant\API\Exception;
use App\Config;
use App\Models;
use Monolog\Logger;

class TwoPerformantOperations
{

    private $config;
    private $log;

    public function __construct(Config\Config $_config, Logger $logger)
    {
        $this->config = $_config;
        $this->log = $logger;
    }

    protected function initAffiliate()
    {
        $this->log = new Logger(TwoPerformantOperations::class);
        $this->log->pushHandler(new StreamHandler("app.log", Logger::DEBUG));
        $affiliate = null;

        try
        {
            $affiliate = new Affiliate($this->config->getEmail(), $this->config->getPassword());
        }
        catch (Exception\APIException $ex)
        {
            var_dump($ex);die();
            $this->log->err($ex->getMessage());
        }

        return $affiliate;
    }

    /**
     * @author Popescu Cosmin Ionut
     * @email cosmin.popescu93@gmail.com
     */
    public function getAdvertisers($page = 1, $relation = null)
    {
        $advertisers = [];
        try
        {
            $affiliateProgramFilter = (new AffiliateProgramFilter)->page($page)->perpage(100);
            if(!empty($relation)) {
                $affiliateProgramFilter->relation($relation);
            }

            $apiResponse = $this->initAffiliate()->getPrograms($affiliateProgramFilter);
            foreach ($apiResponse as $response)
            {
                $advertiser = new Models\Advertiser();

                $advertiser->setStatus($response->getStatus());
                $advertiser->setId($response->getId());
                $advertiser->setAdvertiserName($response->getName());
                $advertiser->setDefaultCommission($response->getDefaultSaleCommissionRate());
                $advertiser->setDefaultLead($response->getAffRequest()->commission_lead_amount);
                $advertiser->setUrl($this->convertToAffiliateLink($response->getMainUrl(), $response->getUniqueCode()));
                $advertiser->setDescription($response->getDescription());
                $advertiser->setStatus($response->getStatus());
                $advertiser->setMainUrl($response->getmainUrl());
                $advertiser->setImageUrl($response->getlogoPath());
                $advertiser->setConditions($response->getTos());
                $advertiser->setCategory($response->getCategory());
                $advertiser->setCurrency($response->getCurrency());
                $advertiser->setCountries($response->getCountries());
                $advertiser->setEnableLeads($response->getEnableLeads());
                $advertiser->setEnableSales($response->getEnableSales());

                $programId = empty($response->getAffRequest()->id) ? $response->getId() : $response->getAffRequest()->id;
                $advertiser->setProgramId($programId);

                $advertisers[] = $advertiser;
            }

            $this->log->info("", ["got" => count($advertisers)]);
            return $advertisers;
        }
        catch (\App\Exception\ConfigurationException $configurationException)
        {
            $this->log->err($configurationException->getMessage());
        }
    }

    public function getPromotions($page = 1, $affrequestStatus = null) {
        $promotions = [];
        try
        {
            $promotionFilter = (new AffiliateAdvertiserPromotionFilter)->page($page)->perpage(100);

            if(!empty($affrequestStatus)) {
                $promotionFilter->affrequestStatus($affrequestStatus);
            }

            $apiResponse = $this->initAffiliate()->getPromotions($promotionFilter);

            foreach ($apiResponse as $response)
            {
                $promotion = new Models\Promotion($response);

                if($this->promotionExists($promotion)) {
                    $promotions[] = $promotion;
                    continue;
                }

                $program = $response->getProgram();

                $twoPerformantProgram = $this->initAffiliate()->getProgram($program->getId());

                // get promotion id
                if(empty($twoPerformantProgram)) {
                    continue;
                }

                $programId = empty($twoPerformantProgram->getAffRequest()->id) ? $twoPerformantProgram->getId() : $twoPerformantProgram->getAffRequest()->id;

                if(empty($programId)) {
                    continue;
                }

                $promotion->setProgramId($programId);

                // get link
                $link = $this->initAffiliate()->getQuicklink($promotion->getLandingPage());

                if(empty($link)) {
                    continue;
                }

                $promotion->setLink($link);
                $promotions[] = $promotion;
            }

            return $promotions;
        }
        catch (\App\Exception\ConfigurationException $configurationException)
        {
            $this->log->err($configurationException->getMessage());
        }
    }

    private function promotionExists($promotion) {
        $query = smart_mysql_query("SELECT * FROM `cashbackengine_coupons` WHERE 2performant_promotion_id = '".$promotion->getId()."'");
        $promotion = mysqli_fetch_assoc($query);

        return !empty($promotion);
    }
    
    private function convertToAffiliateLink($link, $program)
    {
        return $this->initAffiliate()->getQuicklink($link, $program);
    }
}