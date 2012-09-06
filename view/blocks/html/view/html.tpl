<!DOCTYPE html>
<html>
	<head>
		<title>
			{{ $title }}
		</title>
		<meta http-equiv="Content-Language" content="ru">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!-- <link type="ico" rel="shortcut icon" href="/assets/images/favicon.ico"> -->
		
		{{ $files }}
		
		<!--[if IE]>
			<link type="text/css" rel="stylesheet" href="/assets/css/style.ie.css">
		<![endif]-->
		<!--[if lt IE 8]>
			<link type="text/css" rel="stylesheet" href="/assets/css/style.ie567.css">
		<![endif]-->
		<!--[if lt IE 9]>
			<script src="/assets/js/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		{{ $body }}

		{{ begin ctx1 }}
			i
			
			{{ begin ctx2}}
				j
				{{ begin ctx3 }}
					x<br>
					{{ $key }}
				{{ end }}
			
			{{ end }}
			
			{{ begin ctx4 }}
				y
			{{ end }}
			<br>
		{{ end }}
		
		{{ begin ctx5 }}
			z
			{{ $key }}
		{{ end }}
	</body>
</html>