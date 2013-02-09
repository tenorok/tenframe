<ul class="mod-admin-menu">
    {{ begin items }}
        <li class="mod-admin-menu__item">
            
            <div class="mod-admin-menu__item-wrap{{ $active }}">
                {{ begin deactive }}<a href="{{ $href }}" class="mod-admin-menu__url">{{ end }}
                    {{ $title }}
                {{ begin deactive }}</a>{{ end }}
            </div>

            {{ begin sub }}
                <ul class="mod-admin-submenu">
                    {{ begin subitems }}
                        <li class="mod-admin-submenu__item{{ $active }}">
                            
                            {{ begin deactive }}<a href="{{ $href }}" class="mod-admin-menu__url">{{ end }}
                                {{ $title }}
                            {{ begin deactive }}</a>{{ end }}

                        </li>
                    {{ end }}
                </ul>
            {{ end }}
        </li>
    {{ end }}
</ul>