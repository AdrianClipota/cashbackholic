<?php
/**
 * Created by PhpStorm.
 * User: cosmi
 * Date: 13-Oct-18
 * Time: 9:25 AM
 */

namespace App\Models;

/**
 * Advertiser entity
 * Class Advertiser
 * @package App\Models
 */
class Advertiser
{
    private $id;
    private $advertiserName;
    private $storeName;
    private $defaultCommission;
    private $defaultLead;
    private $url;
    private $description;
    private $status;
    private $mainUrl;
    private $programId;
    private $imageUrl;
    private $conditions;
    private $category;
    private $currency;
    private $countries;
    private $enableLeads;
    private $enableSales;

    /**
     * @return mixed
     */
    public function getDefaultLead()
    {
        return $this->defaultLead;
    }

    /**
     * @param mixed $defaultLead
     */
    public function setDefaultLead($defaultLead)
    {
        $this->defaultLead = $defaultLead;
    }

    /**
     * @return mixed
     */
    public function getEnableLeads()
    {
        return $this->enableLeads;
    }

    /**
     * @param mixed $enableLeads
     */
    public function setEnableLeads($enableLeads)
    {
        $this->enableLeads = $enableLeads;
    }

    /**
     * @return mixed
     */
    public function getEnableSales()
    {
        return $this->enableSales;
    }

    /**
     * @param mixed $enableSales
     */
    public function setEnableSales($enableSales)
    {
        $this->enableSales = $enableSales;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @param mixed $countries
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


    /**
     * @return mixed
     */
    public function getAdvertiserName()
    {
        return $this->advertiserName;
    }

    /**
     * @param mixed $advertiserName
     */
    public function setAdvertiserName($advertiserName)
    {
        $this->advertiserName = $advertiserName;
    }

    /**
     * @return mixed
     */
    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * @param mixed $storeName
     */
    public function setStoreName($storeName)
    {
        $this->storeName = $storeName;
    }

    /**
     * @return mixed
     */
    public function getDefaultCommission()
    {
        return $this->defaultCommission;
    }

    /**
     * @return mixed
     */
    public function getCashback() {
        if(!empty($this->defaultCommission)) {
            return ($this->defaultCommission / 2).'%';
        }

        if(!empty($this->defaultLead)) {
            return $this->defaultLead / 2;
        }

        return 0;
    }

    /**
     * @param mixed $defaultCommission
     */
    public function setDefaultCommission($defaultCommission)
    {
        $this->defaultCommission = $defaultCommission;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getMainUrl()
    {
        return $this->mainUrl;
    }

    /**
     * @param mixed $mainUrl
     */
    public function setMainUrl($mainUrl)
    {
        $this->mainUrl = $mainUrl;
    }

    /**
     * @return mixed
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * @param mixed $programId
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
    }

    /**
     * @param mixed $imageUrl
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @param mixed $conditions aka tos
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }
    
}