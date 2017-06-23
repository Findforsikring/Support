<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 23-06-2017
 * Time: 14:40
 */

namespace Findforsikring\Support;


use Kameli\NummerpladeApi\Client;

/**
 * Class VehicleService
 * @package Findforsikring\Support
 * @mixin Client
 */
class VehicleService
{
    public function __construct(){
        if (!env('NUMMERPLADE_API_TOKEN')){
            throw new \Exception("NUMMERPLADE_API_TOKEN er ikke sat i .env");
        }
        $this->client = new Client(env('NUMMERPLADE_API_TOKEN'));
    }

    function __call($name, $arguments)
    {
        return call_user_func_array([$this->client, $name], $arguments);
    }
}