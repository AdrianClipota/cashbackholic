<?php

namespace App\Models;

class Promotion
{
    private $startDate;
    private $endDate;
    private $id;
    private $landingPage;
    private $name;
    private $programId;
    private $link;

    public function __construct($details) {
        $this->startDate = $this->convertDate($details->getPromotionStart());
        $this->endDate = $this->convertDate($details->getPromotionEnd());
        $this->id = $details->getId();
        $this->name = $details->getName();
        $this->landingPage = $details->getLandingPageLink();
    }

    private function convertDate($date) {
        return date('Y-m-d H:i:s', strtotime($date));
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @param mixed $programId
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;
    }

    /**
     * @return mixed
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLandingPage()
    {
        return $this->landingPage;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}