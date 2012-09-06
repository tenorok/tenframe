<ul class="mod-admin-menu">
	{{ begin items }}
		<li class="mod-admin-menu__item{{ $active }}">
			{{ begin deactive }}<a href="{{ $href }}" class="mod-admin-menu__url">{{ end }}
				{{ $text }}
			{{ begin deactive }}</a>{{ end }}
		</li>
	{{ end }}
</ul>