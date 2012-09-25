<form method="post" action="{{ $action }}">
	<ul class="mod-admin-logon">
		<li class="mod-admin-logon__item">
			<label class="mod-admin-logon__label">Логин</label>
			<div class="mod-admin-logon__value">
				<input type="text" name="login" autofocus class="mod-admin-logon__input">
			</div>
		</li>
		<li class="mod-admin-logon__item">
			<label class="mod-admin-logon__label">Пароль</label>
			<div class="mod-admin-logon__value">
				<input type="password" name="password" class="mod-admin-logon__input">
			</div>
		</li>
		<li class="mod-admin-logon__item">
			<div class="mod-admin-logon__send">
				<input type="submit" name="send" value="войти" class="mod-admin-logon__input_send">
			</div>
		</li>
		<li class="mod-admin-logon__item">
			<div class="mod-admin-logon__error">{{ $error }}</div>
		</li>
	</ul>
</form>