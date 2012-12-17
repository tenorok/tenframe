<li id="c_{{ $id }}" class="mod-shop-categories__item">
	
	<div>
		<a href="{{ $id }}" class="mod-shop-categories__cat mod-shop-categories__cat_edit {{ $selected }}">{{ $name }}</a>
	</div>

	<ul class="mod-shop-categories__list">
		[[child_{{ $id }}]]
	</ul>
</li>