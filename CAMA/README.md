![Aperçu du projet](./1.png)

# Le projet

Camagru est un projet de l'ecole 42, il s'agit de creer un site permettant de publier des photos/videos modifiables dans la gallerie de l'utilisateurs. Toutes les publications seront alors visible sur un mur public ou tout les utilisateurs pourront voir/reagir/commenter les publications.
Un mini jeu est egalement present pour le plaisir !

# ⚙️ Configuration du projet

Avant de commencer, ajoutez un fichier `.env` à la racine du projet (au même niveau que le `Makefile`).  
Remplissez-le avec les variables suivantes :

```env
MYSQL_ROOT_PASSWORD=*****   # défini le mot de passe du compte administrateur (root)
                            # indispensable pour sécuriser l’accès à MySQL et permet
                            # à d’autres services (comme PHP ou phpMyAdmin) de se
                            # connecter avec les droits d’administration

MYSQL_DATABASE=mydatabase   # le nom de la base de donne. Peut etre modifier a
                            # condition de changer le nom dans le fichier init.sql

MYSQL_USER=appuser          # L'identifiant de l'user qui aura acces a phpMyAdmin

MYSQL_PASSWORD=apppass      # Son mot de passe

PMA_HOST=mysql              # utilisée par le conteneur phpMyAdmin pour indiquer
                            # l’adresse du serveur MySQL auquel il doit se connecter


SMTP_HOST=smtp.gmail.com       # Il s'agit de l'adresse serveur SMTP de gmail
SMTP_FROM=camamailgru@gmail.com # Il s'agit du nom de l'adresse mail configure pour SMTP
SMTP_USER=camamailgru@gmail.com # Il s'agit du nom de l'adresse mail configure pour SMTP
SMTP_PASS=*********             # le mot de passe aui vous sera communique lors
                                # de la configuration SMTP de votre adresse mail

```

📧 SMTP & PHPMailer

Ces 4 variables sont nécessaires pour la configuration et le fonctionnement de PHPMailer afin d'envoyer des mails via un protocole SMTP.

Pour faire correctement fonctionner votre projet il va donc vous falloir un serveur SMTP,
c’est-à-dire un serveur de mail qui achemine sur Internet des emails d’un expéditeur à un ou plusieurs destinataires.

➡️ Cela peut être configuré facilement via Gmail, par exemple.
La seule exigence est une adresse email correspondant au domaine, avec laquelle le serveur SMTP du fournisseur peut être utilisé pour la correspondance personnelle.

Tout ce que vous avez à faire est de configurer votre boîte mails pour la bonne adresse de serveur SMTP.

🔗 Tutoriel utile : [Utiliser le serveur SMTP de Gmail](https://www.hostinger.com/fr/tutoriels/utiliser-serveur-smtp-gmail)

![Mini jeu](./2.png)
![Gallerie de photos](./3.png)
