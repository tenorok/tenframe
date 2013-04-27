<li id="c_{{ $id }}" class="mod-shop-categories__item">
    
    <div>
        
        {{ begin item }}
            <a href="{{ $id }}" class="mod-shop-categories__cat mod-shop-categories__cat_edit">{{ $name }}</a>
        {{ end }}

        {{ begin parent }}
            <a href="{{ $id }}" class="mod-shop-categories__cat mod-shop-categories__cat_edit mod-shop-categories__cat_selected_yes">{{ $name }}</a>
        {{ end }}

    </div>

    <ul class="mod-shop-categories__list">
        [[child_{{ $id }}]]
    </ul>
</li>