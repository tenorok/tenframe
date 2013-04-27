<ul class="mod-admin-menu">
    {{ begin items }}
        <li class="mod-admin-menu__item">

            {{ begin deactive }}
                <div class="mod-admin-menu__item-wrap">
                    <a href="{{ $href }}" class="mod-admin-menu__url">{{ $title }}</a>
                </div>
            {{ end }}

            {{ begin active }}
                <div class="mod-admin-menu__item-wrap mod-admin-menu__item-wrap_active">{{ $title }}</div>
            {{ end }}

            {{ begin sub }}
                <ul class="mod-admin-menu__submenu">
                    {{ begin subitems }}

                        {{ begin deactive }}
                            <li class="mod-admin-menu__submenu-item">
                                <a href="{{ $href }}" class="mod-admin-menu__url">{{ $title }}</a>
                            </li>
                        {{ end }}

                        {{ begin active }}
                            <li class="mod-admin-menu__submenu-item mod-admin-menu__submenu-item_active">{{ $title }}</li>
                        {{ end }}

                    {{ end }}
                </ul>
            {{ end }}
        </li>
    {{ end }}
</ul>