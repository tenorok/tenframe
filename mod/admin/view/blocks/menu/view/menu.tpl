<ul class="mod-admin-menu">
	{{ begin items }}
		<li class="mod-admin-menu__item{{ $active }}">
			
			{{ begin deactive }}<a href="{{ $href }}" class="mod-admin-menu__url">{{ end }}
				{{ $text }}
			{{ begin deactive }}</a>{{ end }}

			<ul>
				{{ begin subitems }}
					<li>
						<a href="{{ $href }}" class="mod-admin-menu__url">{{ $text }}</a>
					</li>
				{{ end }}
			</ul>
		</li>
	{{ end }}
</ul>