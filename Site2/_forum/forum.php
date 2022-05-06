<?php

	require_once('../include.php');
	
	
	$req = $DB->prepare("SELECT * 
		FROM forum
		ORDER BY ordre");
	
	$req->execute();
	
	$req_forum = $req->fetchAll();

?>

<!doctype html>
<html lang="fr">
	<head>
		<?php	
			require_once('../_head/meta.php');
			require_once('../_head/link.php');
			require_once('../_head/script.php');
		?>
		<title>Forum</title>
	</head>
	<body>
		<?php	
			require_once('../_menu/menu.php');
		?>
		<div class="container">
			<div class="row">
				<div class="col-2"></div>
				<div class="col-8">
					<div class="forum__body">
						<h1 class="forum__h1">Forum</h1>
						<div class="forum__body__btn">
							<a href="./Site2/_forum/creer-topic.php" class="forum__btn__create">
								<i class="bi bi-plus btn__create"></i> Créer un topic
							</a>
						</div>
						<?php
							foreach($req_forum as $rf){
								
								$req = $DB->prepare("SELECT COUNT(id) AS NbCommentaire
									FROM topic
									WHERE id_forum = ?");
								
								$req->execute([$rf['id']]);
								
								$req_nb_topic = $req->fetch();
								
								$nb_topic = $req_nb_topic['NbCommentaire'];
								
								if($nb_topic > 1){
									$lib_nb_topic = "Il y a " . $nb_topic . ' topics';
								}else{
									$lib_nb_topic = "Il y a " . $nb_topic . ' topic';
								}
						?>
						<a href="./site2/_forum/liste-topics.php?id=<?= $rf['id'] ?>" class="list__link__forum">
						<button type="button" class="btn btn-primary">
						<?= $rf['titre'] ?> <span class="badge badge-success"><?= $lib_nb_topic ?></span>
						</button>
						</a>
						<?php		
							}
				 		?>
					</div>
					<div class="list__topic__body">
						<h1 class="list__topic__h1">Derniers Messages du Blog</h1>
						<?php
							require_once('../include.php');

						$get_id = $_SESSION['id'];	
						$req = $DB->prepare("SELECT *
							FROM topic 							
							ORDER BY date_creation DESC	");
						
						$req->execute();
						
						$req_liste_topics = $req->fetchAll();

						?>
						<?php
							foreach($req_liste_topics as $rlt){
								
								$req = $DB->prepare("SELECT COUNT(id) AS NbCommentaire
									FROM topic_commentaire
									WHERE id_topic = ?");
								
								$req->execute([$rlt['id']]);
								
								$req_nb_commentaire = $req->fetch();
								
								$nb_commentaire = $req_nb_commentaire['NbCommentaire'];

								$req2 = $DB->prepare("SELECT pseudo
									FROM utilisateur
									WHERE id = ?");
								
								$req2->execute([$rlt['id_utilisateur']]);
								
								$req2_user = $req2->fetch();
								
								$user = $req2_user['pseudo'];
								
						?>
						<div class="col-sm-6" style="width: 48rem;">
							<div class="card">
							<div class="card-body">
								<h5 class="card-title"><?= $rlt['titre'] ?></h5>
								<p class="card-text"><?= $rlt['contenu'] ?></p>
								<div> <?= $user ?> </div>
								<div><i class="bi bi-chat"></i> <?= $nb_commentaire ?></div>
								<div>Le <?= date_format(date_create($rlt['date_creation']), 'd/m/Y à H:i') ?></div>
								<a href="./site2/_forum/topic.php?id=<?= $rlt['id'] ?>" class="btn btn-primary">Lire le message</a>
							</div>
							</div>
						</div>
						<?php		
							}
			 			?>
					</div>
					<nav aria-label="Page navigation example">
					<ul class="pagination">
						<li class="page-item"><a class="page-link" href="#">Previous</a></li>
						<li class="page-item"><a class="page-link" href="#">1</a></li>
						<li class="page-item"><a class="page-link" href="#">2</a></li>
						<li class="page-item"><a class="page-link" href="#">3</a></li>
						<li class="page-item"><a class="page-link" href="#">Next</a></li>
					</ul>
					</nav>
				</div>
			</div>
		</div>
		<?php	
			require_once('../_footer/footer.php');
		?>
	</body>
</html>