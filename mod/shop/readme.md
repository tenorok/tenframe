# Модуль &mdash; Интернет-магазин

Версия 0.0.4

- [Настройка](#settings)

## Подмодули
- Витрина
    - [Работа с категориями](#categories)
    - [Добавление товара](#add)
    - [Изменение товара](#edit)
    - [Удаление товара](#delete)
    - [Отображение товара](#select)
    - [Теги](#tags)
    - [Корзина, заказы](#orders)
    - [Поиск](#search)
- Сотрудники
- Склад и недвижимость
- Транспорт
- Статистика

### <a id="settings"></a>Настройка

Файл `/conf/pages.php` содержит массив соответствий имён страниц магазина и страниц административной панели.
Имена значений нужно брать из `/mod/admin/conf/menu.php`, опции `name`.

    return array(
        'categories' => 'admin_categories_page'         // Страница категорий
    );