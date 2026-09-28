<?php

$files = [
    'user/app/Http/Controllers/HomeController.php',
    'user/app/Http/Controllers/CourseController.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Add iscach and is_listed where missing. 
        // Note: we'll do search and replace carefully.
    }
}
