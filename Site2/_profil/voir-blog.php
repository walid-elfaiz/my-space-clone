<?php

	require_once('../include.php');

	$get_id = (int) $_GET['id'];
	
	if($get_id <= 0){
		header('Location: membres.php');
		exit;
	}
	
	if(isset($_SESSION['id']) && $get_id == $_SESSION['id']){
		header('Location: profil.php');
		exit;
	}
	$req = $DB->prepare("SELECT *
		FROM topic 
		WHERE id_utilisateur = ?");
	
	$req->execute([$get_id]);
	
	$req_forum = $req->fetch();
	
	
    $req = $DB->prepare("SELECT T.*
		FROM topic T
		INNER JOIN utilisateur U ON U.id = T.id_utilisateur
		WHERE T.id_utilisateur = ?
		ORDER BY T.date_creation DESC");
	

	$req->execute([$get_id]);
	
	$req_liste_topics = $req->fetchAll();
	
    $req = $DB->prepare("SELECT *
		FROM utilisateur 
		WHERE id = ?");
	
	$req->execute([$get_id]);
	
	$req_user = $req->fetch();
	
?>
<!doctype html>
<html lang="fr">
	<head>
		<?php	
			require_once('../_head/meta.php');
			require_once('../_head/link.php');
			require_once('../_head/script.php');
		?>
		<title>Blog de <?= $req_user['pseudo'] ?></title>
	</head>
	<body>
		<?php	
			require_once('../_menu/menu.php');
		?>
		<div class="container">
			<div class="row">
				<div class="col-12">
                <div class="list__topic__body">
						<h1 class="list__topic__h1">Derniers Messages de <?=$req_user['pseudo']?></h1>
						<?php
							foreach($req_liste_topics as $rlt){
								
								$req = $DB->prepare("SELECT COUNT(id) AS NbCommentaire
									FROM topic_commentaire
									WHERE id_topic = ?");
								
								$req->execute([$rlt['id']]);
								
								$req_nb_commentaire = $req->fetch();
								
								$nb_commentaire = $req_nb_commentaire['NbCommentaire'];
								
						?>
						<div class="col-sm-6" style="width: 48rem;">
							<div class="card">
							<div class="card-body">
								<h5 class="card-title"><?= $rlt['titre'] ?></h5>
								<p class="card-text"><?= $rlt['contenu'] ?></p>
								<a href="./site2/_forum/topic.php?id=<?= $rlt['id'] ?>" class="btn btn-primary">Lire le message</a>
							</div>
							</div>
						</div>
						<?php		
							}
			 			?>
					</div>
				</div>
			</div>
		</div>
		<?php	
			require_once('../_footer/footer.php');
		?>
	</body>
</html>