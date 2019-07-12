<?php

namespace TPerformant\API\Model;

use TPerformant\API\HTTP\Affiliate as ApiHttpAffiliate;
use TPerformant\API\Filter\AffiliateCommissionFilter;

class AffiliateProgram extends Program {
    /**
     * @inheritdoc
     */
    public function __construct($data, ApiHttpAffiliate $user = null) {
        parent::__construct($data, $user);
    }


    protected $affrequest = null;

    /**
     * Get commissions generated by the affiliate in this program
     * @return AffiliateCommission[]|Commission[]
     */
    public function getCommissions() {
        return $this->owner->getCommissions(
            (new AffiliateCommissionFilter)->query('program_name:'.$this->getName().' OR campaign_name:'.$this->getName())
        );
    }

    /**
     * Generate a quicklink in this program for the affiliate who requested it
     * @param  string   $url    The destination URL
     *
     * @return string   The quicklink URL
     */
    public function getQuicklink($url) {
        return $this->requester->getQuicklink($url, $this);
    }

    public function getAffRequest()
    {
        return $this->affrequest;
    }
}
