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

    protected function json($success, $error = null, $extra = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge([
            "success" => $success,
            "error" => $error
        ], $extra));
        exit;
    }
}
