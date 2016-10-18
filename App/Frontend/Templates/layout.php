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
				<ul id="menu">
					<?php if ( isset( $menu ) ): ?>
						<?php foreach ( $menu as $key => $value ): ?>
							<li class="second-level"><a href="<?= $value ?>"><?= $key ?></a></li>
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
		<script src="/js/add-comment.js"></script>
		<script src="/js/jquery.jrumble.1.3.min.js"></script>
		<script src="/js/refresh-news-page.js"></script>
		<script src="/js/delete-comment.js"></script>
	</body>
</html>