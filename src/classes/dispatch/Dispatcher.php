<?php

namespace iutnc\deefy\dispatch;
use \iutnc\deefy\action as AC;
use iutnc\deefy\auth\Auth;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\action\AddUserAction;
use iutnc\deefy\action\SigninAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\Logout;
use iutnc\deefy\action\Profil;
use iutnc\deefy\action\Display;
use iutnc\deefy\action\addseriepref;
use iutnc\deefy\action\suppseriepref;
use iutnc\deefy\action\DejaVue;
use iutnc\deefy\action\EnCours;
use iutnc\deefy\action\Token;

use iutnc\deefy\test\PageEpisode;
use iutnc\deefy\test\AfficherSerie;

class Dispatcher{
  protected ?string $action=null;
  protected ?string $token=null;

  public function __construct(){
    $this->action=isset($_GET['action'])?$_GET['action']:null;
    $this->token=isset($_GET['token'])?$_GET['token']:null;
  }

  public function run(){
    $html=null;
    switch ($this->action) {

      /*
      Rajouter les fonctionnalites ici (action)
      */

        case 'register':
          $execution=new AddUserAction();
          $html=$execution->execute();
          break;

        case 'connexion':
          $execution=new SigninAction();
          $html=$execution->execute();
          break;

        case 'profil':
          $execution=new Profil();
          $html=$execution->execute();
          break;

        case 'logout':
          $execution=new Logout();
          $html=$execution->execute();
          break;

        case 'addseriepref':
          $execution=new addseriepref();
          $html=$execution->execute();
          header('Location: index.php');
          break;

        case 'suppseriepref':
          $execution=new suppseriepref();
          $html=$execution->execute();
          header('Location: index.php');
          break;

        case 'dejavue':
          $execution=new DejaVue();
          $html=$execution->execute();
          break;

        case 'encours':
          $execution=new EnCours();
          $html=$execution->execute();
          break;

        default:
            $html="";
            if(!isset($_GET['function'])){
          $html.='<h2>Bienvenue</h2>';
          if(!is_null($this->action)){
              //$html.="<p>HOME</p>";
            $html.=AfficherSerie::afficherSerie($this->action);
          }
          else if(isset($_SESSION['utilisateur'])){
            $execution=new Display();
            $html.=$execution->execute();
          }
            }
          break;
      }

      if(!is_null($this->token)){
        $execution=new Token();
        $html.=$execution->execute();
        $this->token=null;
      }

      echo $this->renderPage($html);
    }

  public function renderPage(string $html):string{
    if(!isset($_SESSION['utilisateur'])){
      $options=<<<END
      <li><a href="?action=register">S'incrire</a></li>
      <li><a href="?action=connexion">Se connecter</a></li>
      END;
    }

    /*
    Rajouter les liens des actions ci-dessous
    */

    else{
      $email=$_SESSION['email'];
      $options=<<<end
      <li><a href="?action=profil">Profil</a></li>
      <li><a href="?action=dejavue">Series déjà vues</a></li>
      <li><a href="?action=encours">Series en cours de visionnage</a></li>
      <li><a href="?action=logout">Se déconnecter</a></li>
      </nav>Vous êtes connecté : <strong>$email</strong><br>
      end;

        if (isset($_GET['function']))
            if(isset($_GET['id'])) {
                $options .= AfficherSerie::afficherSerie($_GET['id']);
            }
            else if (isset($_GET['episode'])) {
                $options .= PageEpisode::afficherEpisode($_GET['episode']);
            }

    }

    return <<<END
    <!DOCTYPE html>
    <html lang="fr">
      <head>
        <title>NetVOD</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1 /">
        <link rel="stylesheet" href="src/classes/dispatch/page.css" />
      </head>
      <body>
        <h1>NetVOD</h1>
        <nav><ul>
          <li><a href="index.php">Accueil</a></li>
          $options
      </nav><br>
      $html
    </body>
  </html>
  END;
  }
}
