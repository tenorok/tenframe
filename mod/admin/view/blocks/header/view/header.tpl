<div class="mod-admin-header">
    <a href="/" class="mod-admin-header__tosite">&larr; <span class="mod-admin-header__tosite-text">Перейти на сайт</span></a>
    <form method="post" action="{{ $action }}" class="mod-admin-header__formout">
        <div class="mod-admin-header__login">{{ $login }}</div>
        <input type="submit" name="logout" value="выйти" class="mod-admin-header__logout">
    </form>
</div>