<?php

abstract class Foxrate_Sdk_Api_Entity_AbstractOrder {

    const DATE_FORMAT = 'Y-m-d H:i:s';

    private $orderContainer;

    private $customer = array();

    private $orderDetails = array();

    private $products = array();

    const ORDERS_ID='orders_id';
    const ORDER_DATE='order_date';
    const ORDER_CURRENCY ='order_currency';
    const ORDER_LANGUAGE='order_language';

    const CUSTOMERS_ID = 'customers_id';
    const CUSTOMERS_CITY = 'customers_city';
    const CUSTOMERS_POSTCODE = 'customers_postcode';
    const CUSTOMERS_STATE = 'customers_state';
    const CUSTOMERS_COUNTRY = 'customers_country';
    const CUSTOMERS_TELEPHONE = 'customers_telephone';
    const CUSTOMERS_EMAIL_ADDRESS = 'customers_email_address';
    const CUSTOMERS_GENDER = 'customers_gender';
    const CUSTOMERS_BIRTHDAY = 'customers_dob';
    const CUSTOMERS_FIRSTNAME = 'customers_firstname';
    const CUSTOMERS_LASTNAME = 'customers_lastname';

    const PRODUCTS_ID = 'products_id';
    const PRODUCTS_MODEL = 'products_model';
    const PRODUCTS_NAME = 'products_name';
    const FINAL_PRICE = 'final_price';
    const PRODUCTS_CURRENCY = 'products_currency';
    const PRODUCTS_EAN = 'products_ean';
    const CATEGORY_NAME = 'category_name';
    const PRODUCTS_IMAGE = 'products_image';
    const PRODUCTS_URL = 'products_url';

    /**
     * Get Order method
     * @param $order
     */
    public function getOrder($order)
    {
        $this->orderDetails($order);
        $this->customer($order);
        $this->products($order);

        $this->orderContainer = new ArrayObject(
            array(
                'order' => $this->getOrderDetails(),
                'customer' => $this->getCustomer(),
                'products' => $this->getProducts()
            )
        );
    }

    /**
     * @param $key
     * @param $customer
     */
    public function setCustomer($key, $customer)
    {
        $this->customer[$key] = $customer;
    }

    /**
     * @return array
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Setter for order details
     * @param $key
     * @param $order
     */
    public function setOrderDetails($key, $order)
    {
        $this->orderDetails[$key] = $order;
    }

    /**
     * @return mixed
     */
    public function getOrderDetails()
    {
        return $this->orderDetails;
    }

    /**
     * Product setter
     * @param $key
     * @param $products
     */
    public function setProducts($key, $products)
    {
        $this->products[$key] = $products;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function getOrderArray()
    {
        return $this->orderContainer->getArrayCopy();
    }

    public function addProduct($product)
    {
        $this->products[] = $product;
    }

    abstract protected function orderDetails($order);

    abstract protected function customer($order);

    abstract protected function products($order);
}
