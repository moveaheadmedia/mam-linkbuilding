<?php


namespace MAM\Plugin\Services\Admin;


use Exception;
use MAM\Plugin\Services\WPAPI\Endpoint;
use MAM\Plugin\Services\ServiceInterface;

class DataLoader implements ServiceInterface
{

    /**
     * @var Endpoint
     */
    private $endpoint_api;

    /**
     * @var array
     */
    public $errors;

    public function __construct()
    {
        $this->endpoint_api = new Endpoint();
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        try {
            $this->endpoint_api->add_endpoint('mam-data-loader')->with_template('mam-data-loader.php')->register_endpoints();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}