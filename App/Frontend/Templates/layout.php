<!DOCTYPE html>
<html>
	<head>
		<title>
			<?= isset( $title ) ? $title : 'Mon super site' ?>
		</title>
		
		<meta charset="utf-8" />
		
		<link rel="stylesheet" href="/css/Envision.css" type="text/css" />
	</head>
	
	<body>
		<div id="wrap">
			<header>
				<h1><a href="/">Mon super site</a></h1>
				<?php if ( $user->getLogin() ): ?>
				<p><?= $user->getLogin() ?>
					<?php else: ?>
				<p>Non connecté
					<?php endif; ?>
					<?php if ( isset( $device_type ) ): ?>
						sur <?= $device_type ?>
					<?php endif; ?>
				</p><br />
			</header>
			
			<nav>
				<ul>
					<?php if ( isset( $menu ) ): ?>
						<?php foreach ( $menu as $key => $value ): ?>
							<li><a href="<?= $value ?>"><?= $key ?></a></li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</nav>
			
			<div id="content-wrap">
				<section id="main">
					<?php if ( $user->hasFlash() ) {
						echo '<p style="text-align: center;">', $user->getFlash(), '</p>';
					} ?>
					
					<?= $content ?>
				</section>
			</div>
			
			<footer></footer>
			
		</div>
		<script src="/js/jquery-3.1.1.min.js"></script>
		<script src="/js/news-page.js"></script>
	</body>
</html>