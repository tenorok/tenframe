<li id="c_{{ $id }}" class="mod-shop-categories__item">
	
	<div class="mod-shop-categories__name">
		<span class="mod-shop-categories__draggable"></span>
		<a href="/{{ $page }}/modshop/categories/{{ $id }}/" class="mod-shop-categories__cat">{{ $name }}</a>
	</div>

	<ul class="mod-shop-categories__list">
		[[child_{{ $id }}]]
	</ul>
</li>