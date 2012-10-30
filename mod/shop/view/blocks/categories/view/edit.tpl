<li class="mod-shop-categories__labelitem mod-shop-categories__labelitem_position_right">
	<label class="mod-shop-categories__label mod-shop-categories__label_long">
		<input type="checkbox" name="hide" {{ $hided }} class="mod-shop-categories__checkboxinput"> скрыть категорию
	</label>
</li>
<li class="mod-shop-categories__labelitem mod-shop-categories__labelitem_independent">
	
	<a href="" class="mod-shop-categories__link_pseudo mod-shop-categories__link_change-parent">
		<span class="mod-shop-categories__link-text">Изменить родительскую категорию</span>
		<span class="mod-shop-categories__harr">&#9650;</span>
	</a>

	<ul class="mod-shop-categories__list mod-shop-categories__list_edit">
		{{ $categories }}
	</ul>
</li>