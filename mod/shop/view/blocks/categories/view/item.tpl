<li id="c_{{ $id }}" class="mod-shop-categories__item">
    
    <div class="mod-shop-categories__name">
        <span class="mod-shop-categories__draggable"></span>
        
        {{ begin visible }}
            <a href="/{{ $page }}/modshop/categories/{{ $id }}/" class="mod-shop-categories__cat">{{ $name }}</a>
        {{ end }}
        
        {{ begin hidden }}
            <a href="/{{ $page }}/modshop/categories/{{ $id }}/" class="mod-shop-categories__cat mod-shop-categories__cat_hided" title="Скрытая категория">{{ $name }}</a>
        {{ end }}
        
    </div>

    <ul class="mod-shop-categories__list">
        [[child_{{ $id }}]]
    </ul>
</li>