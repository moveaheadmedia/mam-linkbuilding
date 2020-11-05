<?php


namespace MAM\Plugin;


use MAM\Plugin\Services\Admin\Import;
use MAM\Plugin\Services\Admin\Orders;
use MAM\Plugin\Services\Base\Enqueue;
use MAM\Plugin\Services\Admin\Clients;
use MAM\Plugin\Services\Admin\Sectors;
use MAM\Plugin\Services\Admin\Editors;
use MAM\Plugin\Services\Admin\Agencies;
use MAM\Plugin\Services\Admin\Resources;
use MAM\Plugin\Services\Admin\Export\Export;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_services()
    {
        return [
            Export::class,
            Import::class,
            Orders::class,
            Enqueue::class,
            Clients::class,
            Sectors::class,
            Editors::class,
            Agencies::class,
            Resources::class
        ];
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     * @return void
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param  string $class    class from the services array
     * @return object instance new instance of the class
     */
    private static function instantiate($class)
    {
        return new $class();
    }
}
