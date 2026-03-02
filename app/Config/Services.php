<?php

namespace Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    // Add GoogleDrive service to the services configuration
    public static function googleDrive($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('googleDrive');
        }

        return new \App\Services\GoogleDriveService();
    }
}