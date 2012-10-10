<div class="mod-shop-categories">
	<form action="/{{ $page }}/modshop/categories/insert/" method="post" class="mod-shop-categories__addform">
		<input type="hidden" name="catparent" value="{{ $parent }}">
		<ul class="mod-shop-categories__labellist">
			<li class="mod-shop-categories__labelitem">
				<label for="mod-shop-categories-catname" class="mod-shop-categories__label">Название</label>
				<input type="text" name="catname" placeholder="Телевизоры" id="mod-shop-categories-catname" autofocus class="mod-shop-categories__textinput">
			</li>
			<li class="mod-shop-categories__labelitem">
				<label for="mod-shop-categories-catalias" class="mod-shop-categories__label">Алиас</label>
				<input type="text" name="catalias" placeholder="tv" id="mod-shop-categories-catalias" class="mod-shop-categories__textinput">
			</li>
		</ul>
		<h2 class="mod-shop-categories__h2">Поля</h2>
		<ul class="mod-shop-categories__fieldlist">
			<li class="mod-shop-categories__fielditem">
				<ul class="mod-shop-categories__labellist">
					<li class="mod-shop-categories__labelitem mod-shop-categories__existlist">
						<label class="mod-shop-categories__label mod-shop-categories__label_long">
							<select name="existfield[]" class="mod-shop-categories__selectinput mod-shop-categories__selectinput_right-column">
								<option value="new" class="mod-shop-categories__optioninput">Новое поле</option>

								{{ begin existfields }}
									<option value="{{ $id }}" class="mod-shop-categories__optioninput">{{ $category }} &mdash; {{ $field }}</option>
								{{ end }}

							</select>
						</label>
					</li>
					<li class="mod-shop-categories__labelitem">
						<label class="mod-shop-categories__label">Имя поля</label>
						<input type="text" name="name[]" placeholder="Цена" class="mod-shop-categories__textinput">
					</li>
					<li class="mod-shop-categories__labelitem">
						<label class="mod-shop-categories__label">Тип поля</label>
						<select name="type[]" class="mod-shop-categories__selectinput">
							
							{{ begin types }}
								<option value="{{ $value }}" class="mod-shop-categories__optioninput">{{ $text }}</option>
							{{ end }}

							<!-- <option value="int" class="mod-shop-categories__optioninput">Целое число</option>
							<option value="str" class="mod-shop-categories__optioninput">Строка</option>
							<option value="flt" class="mod-shop-categories__optioninput" selected>Дробное число</option> -->
						</select>
					</li>
					<li class="mod-shop-categories__labelitem">
						<label class="mod-shop-categories__label">Количество</label>
						<input type="number" name="count[]" value="1" placeholder="1" class="mod-shop-categories__numberinput">
					</li>
					<li class="mod-shop-categories__labelitem">
						<label class="mod-shop-categories__label mod-shop-categories__label_long">
							<input type="checkbox" class="mod-shop-categories__checkboxinput"> выпадающий список
						</label>
					</li>
					<li class="mod-shop-categories__labelitem mod-shop-categories__hiddenitem">
						<label class="mod-shop-categories__label mod-shop-categories__label_left-column">Значения</label>
						<input type="text" name="options_0[]" placeholder="Значение 1" class="mod-shop-categories__textinput mod-shop-categories__textinput_right-column">
					</li>
				</ul>
			</li>
		</ul>
		<input type="submit" value="Создать категорию" class="mod-shop-categories__submit">
	</form>
</div>