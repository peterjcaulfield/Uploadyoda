<?php

return array(
    /**
     * The upload path relative to the apps public directory
     */
    'uploads_directory' => 'packages/quasimodal/uploadyoda/uploads',
    'allowed_mime_types' => array( 'jpg', 'png', 'jpeg', 'gif', 'pdf', 'avi', 'mkv', 'wmv', 'ogg', 'mp4' ),
    'max_file_size' => (1000 * 1000 * 50),
    'layout' => 'uploadyoda::layouts.master',
    'auth' => 'uploadyodaAuth'
);
