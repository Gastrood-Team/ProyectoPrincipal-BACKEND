<?php

use Cloudinary\Configuration\Configuration;

return function () {
    Configuration::instance([
        'cloud' => [
            // 'cloud_name' => "dldwuc3ka",
            // 'api_key' => "712229859512223",
            // 'api_secret' => "apL5SFBUek98VBFNFJxnk_-EwG8"
            'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
            'api_key' => $_ENV['CLOUDINARY_API_KEY'],
            'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
        ]
    ]);
};
?>