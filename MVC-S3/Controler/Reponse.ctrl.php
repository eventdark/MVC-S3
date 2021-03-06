<?php
  session_start();
  require_once('../Model/contactDAO.class.php');
  require_once('../Model/contact.class.php');
  require_once('../Model/membresDAO.class.php');
  require_once('../Model/membres.class.php');
  include("../Framework/view.class.php");

  $view = new View("../View/reponse.view.php");
  $config = parse_ini_file('../Config/config.ini');



  if (isset($_SESSION['unMembre'])) {
    // On cree une variable message dans la vue vide car meme si il n'y a pas de message(s) on auras pas d'erreur.
    $view->message = '';

    $m = unserialize($_SESSION['unMembre']);
    // On cree un objet membre
    $membres = new MembresDAO();
    // on peut le cree en objet memebre evec l'eamail pour etre sur que toute les information soit a jour
    // si le membre a confirmer son mail entre temps il faut actualiser c'est donne car la session date de l'inscription
    $unMembre = $membres->getMembres($m->getemail());
    // si l'utilisateur est un admin il n'y en a qu'un seul
    // il a acces a toute les demande de contact :
    $contact = new ContactDAO();
    if ($unMembre->getid() == $config['id_admin'] and $unMembre->getemail() == $config['mail_admin']) { //id: 5e7e5af311095  // mail: admin@leviny.fr  //var_dump($config);
      $lescontacts = $contact->getAllContact();
      $view->objetReponses = $lescontacts;
      $view->admin = $unMembre->getid();
      if (isset($_POST['formContact'])) {
        $message = htmlspecialchars($_POST['user_message']);
        $view->message = $message;
        $id = htmlspecialchars($_POST['id']);
        $c = $contact->getContactid($id);
        $contact->update_reponse($c, $message);
      }
    } else {
      $lescontacts = $contact->getContact($unMembre->getid());
      $view->objetReponses = $lescontacts;
    }
  }

  $view->show();
?>
