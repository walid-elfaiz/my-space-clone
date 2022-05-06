<?php

	require_once('include.php');

	if(isset($_SESSION['id'])){
		$var = "Bonjour " . $_SESSION['pseudo'].", Utilise cette barre pour rechercher des utilisateurs et des messages sur le blog";
	}else{
		$var = "Bonjour à tous, Utilisez cette barre pour rechercher des utilisateurs et des messages sur le blog";	
	}


 if (isset($_POST['search'])){
	$searchq = $_POST['search'];
	$searchq = preg_replace("#[^0-9a-z]#i","",$searchq);
	
	$req_membre = $DB->prepare("SELECT * 
	FROM utilisateur 
	WHERE pseudo 
	LIKE '%$searchq%' 
	OR mail 
	LIKE '%$searchq%'") or die("could not search");
	$count = 1;
	$req_membre->execute();
	
	$req_search_user = $req_membre->fetchAll();

	$req_message = $DB->prepare("SELECT * 
	FROM topic 
	WHERE titre 
	LIKE '%$searchq%' 
	OR contenu
	LIKE '%$searchq%'") or die("could not search");
	$req_message->execute();
	
	$req_search_message = $req_message->fetchAll();

 }
?>
<!doctype html>
<html lang="fr">
	<head>
		<title>Rechercher</title>
		<?php	
			require_once('_head/meta.php');
			require_once('_head/link.php');
			require_once('_head/script.php');
		?>		
	</head>
	<body>
		<?php	
			require_once('_menu/menu.php');
		?>
		
		<h1><?= $var ?></h1>
        <form method="post" action="./site2/search.php">
		<div class="input-group">
            <input type="search" name="search" class="form-control rounded" placeholder="Rechercher utilisateur/messages" aria-label="Search" aria-describedby="search-addon" />
            <button type="button" class="btn btn-outline-primary">search</button>
        </div>
        </form>
        <div class="row">
				
                <?php
                    if (isset($_POST['search'])){
                        ?>
						<div class="col-12">
					<h1>Membres</h1>
				</div>
				<?php
						foreach($req_search_user as $rm){
							
							$chemin_avatar = null;
								
							if(isset($rm['avatar'])){
								$chemin_avatar = './site2/public/avatar/' . $rm['id'] . '/' . $rm['avatar'];
							}else{
								$chemin_avatar = './site2/public/avatar/defaut/defaut.svg';
							}
						
				?>

					<div class="card" style="width: 12rem;">
						<img class="card-img-top" src="<?= $chemin_avatar ?>" alt="Card image cap"
						style=" height: 14rem;">
						<div class="card-body">
							<h5 class="card-title"><?= $rm['pseudo'] ?></h5>
						</div>
						<div class="card-body">
							<a href="./site2/_profil/voir-profil.php?id=<?= $rm['id'] ?>" class="card-link">Voir profil</a>
						</div>
						<div class="card-body">
							<a href="./site2/_profil/voir-blog.php?id=<?= $rm['id'] ?>" class="card-link">Voir Blog</a>
						</div>
					</div>
				<?php
						}		
					
	 			?>
		</div>
		<div class="list__topic__body">

						<h1 class="list__topic__h1">Messages concernant votre recherche</h1>
						<?php
					
							foreach($req_search_message as $rlt){
								
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
						}
			 			?>
					</div>

		<?php	
			require_once('_footer/footer.php');
		?>
	</body>
</html>