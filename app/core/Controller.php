<?php
namespace App\Core;

class Controller
{
    protected function view($path, $data = [], $useLayout = true)
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../views/' . $path . '.php';
        $content = ob_get_clean();

        if ($useLayout) {
            require __DIR__ . '/../views/layouts/layout.php';
        } else {
            echo $content;
        }
    }
}
