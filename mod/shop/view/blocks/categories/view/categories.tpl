<div class="mod-shop-categories">
    <a href="/{{ $page }}/modshop/categories/add/" class="mod-shop-categories__add">Добавить категорию</a>
    <h2 class="mod-shop-categories__h2">Существующие категории</h2>
    <ul class="mod-shop-categories__mainlist mod-shop-categories__list">

        {{ $categories }}

    </ul>
    <div class="mod-shop-categories__dropdown">
        <div class="mod-shop-categories__dropdown-item">
            <a href="" class="mod-shop-categories__dropdown-menu mod-shop-categories__dropdown-menu_add-product">Добавить товар</a>
        </div>
        <div class="mod-shop-categories__dropdown-item">
            <a href="" class="mod-shop-categories__dropdown-menu mod-shop-categories__dropdown-menu_add-category">Добавить подкатегорию</a>
        </div>
        <div class="mod-shop-categories__dropdown-item">
            <a href="" class="mod-shop-categories__dropdown-menu mod-shop-categories__dropdown-menu_edit">Изменить категорию</a>
        </div>
    </div>
</div>