<?php
include_once 'C:\Users\mpfister\Desktop\UwAmp\www\formation\vendor\mobiledetect\mobiledetectlib\Mobile_Detect.php';
$detect = new Mobile_Detect;

$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablette' : 'téléphone') : 'ordinateur');

?>

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
				<!--<p>Comment ça, il n'y a presque rien ?</p><br />-->
				<p>Vous naviguez sur <?php echo $deviceType ?> !</p>
			</header>
			
			<nav>
				<ul>
					<li><a href="/">Accueil</a></li>
					<?php
					if ( $user->isAuthenticated() ):
						if ($user->getStatus() == 'admin'):
							echo "<li><a href='/admin/'>Admin</a></li>";
						endif;
						if($user->getStatus() == 'member'):
							echo "<li><a href='/admin/'>Vos news</a></li>";
						endif;
						echo "<li><a href='/admin/news-insert.html'>Ajouter une news</a></li>";
						echo "<li><a href='/admin/deconnexion.html'>Deconnexion</a></li>";
					elseif (!$user->isAuthenticated()):
						echo "<li><a href='/admin/connexion.html'>Connexion</a></li>";
						echo "<li><a href='/inscription.html'>S'inscrire</a></li>";
					endif; ?>
					
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
	</body>
</html>
