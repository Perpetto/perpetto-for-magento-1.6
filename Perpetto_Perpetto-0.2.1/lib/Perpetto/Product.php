<?php

/**
 * Class Perpetto_Product
 */
class Perpetto_Product
{
    protected $id;
    protected $name;
    protected $image;
    protected $url;
    protected $listPrice;
    protected $currency;
    protected $availability;
    protected $categories = array();
    protected $category;
    protected $price;
    protected $brand;
    protected $tags = array();
    protected $summary;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Perpetto_Product
     */
    public function setId($id)
    {

        $this->id = (int)$id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Perpetto_Product
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Perpetto_Product
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Perpetto_Product
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * @param number $listPrice
     * @return Perpetto_Product
     */
    public function setListPrice($listPrice)
    {
        $this->listPrice = $listPrice;
        return $this;
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
     * @return Perpetto_Product
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param bool $availability
     * @return Perpetto_Product
     */
    public function setAvailability($availability)
    {
        $this->availability = (int)(bool)$availability;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return Perpetto_Product
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
        return $this;
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
     * @return Perpetto_Product
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return Perpetto_Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     * @return Perpetto_Product
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return Perpetto_Product
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     * @return Perpetto_Product
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = get_object_vars($this);

        return $data;
    }
}
