<div class="mod-admin-header">
    <a href="/" class="mod-admin-header-tosite">&larr; <span class="mod-admin-header-tosite__text">Перейти на сайт</span></a>
    <form method="post" action="{{ $action }}" class="mod-admin-header-formout">
        <div class="mod-admin-header-login">{{ $login }}</div>
        <input type="submit" name="logout" value="выйти" class="mod-admin-header-logout">
    </form>
</div>