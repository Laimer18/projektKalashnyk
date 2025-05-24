<?php
class HomeController
{
    public function index()
    {
        // Можна передати змінні, якщо треба:
        $page_title = "Головна";

        // Підключаємо шаблон (HTML буде там)
        require_once 'views/home/index.view.php';
    }
}
