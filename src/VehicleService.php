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
class VehicleService extends Client
{
    public function __construct(){
        if (!array_key_exists('NUMMERPLADE_API_TOKEN', $_ENV)){
            throw new \Exception("NUMMERPLADE_API_TOKEN er ikke sat i .env");
        }
        parent::__construct($_ENV['NUMMERPLADE_API_TOKEN']);
    }
}