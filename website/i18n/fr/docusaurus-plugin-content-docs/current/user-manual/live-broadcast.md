---
title: Diffusion en direct
---

## MIXXX

[Mixxx](https://www.mixxx.org) is a cross-platform Open Source application for DJs.

Installé sur un ordinateur de bureau ou un ordinateur portable, Mixxx complète votre serveur LibreTime pour fournir un système complet pour la diffusion en direct et programmée. Bien que Mixxx ait de nombreuses fonctionnalités conçues pour les DJ qui ont besoin d'une correspondance de rythme et d'un étirement du temps indépendant de la hauteur, le programme peut être utilisé pour tout type de diffusion déclenchée manuellement, y compris des plateaux radio ou des émissions en direct

Mixxx supporte une grande variété de surfaces de contrôle matérielles populaires, qui peuvent être connectées à votre ordinateur à l'aide d'un câble USB. Une surface de contrôle peut remplacer ou augmenter un mixeur analogique dans votre studio, selon vos besoins de mixage et de diffusion en direct.

Mixxx 1.9.0 ou plus inclut un client de streaming en direct qui, comme LibreTime, est compatible avec les serveurs média Icecast et SHOUTcast. Cette fonction peut également être utilisée pour diffuser des flux de Mixxx directement dans LibreTime, en utilisant **Afficher source** ou **Master Source**.

TPour configurer Mixxx pour le streaming dans LibreTime, cliquez sur **Options**, **Préférences**, puis** Direct** dans le menu principal de Mixxx. Pour le **Type de serveur,** sélectionnez **Icecast 2** par défaut. Pour l'**Hôte**, le **Point de montage**, le **Port**, le **Login** et le **Mot de passe** utilisez les Paramètres de Flux d'Entrée configurés dans la page **Flux** de LibreTime, dans le menu **Système** de LibreTime.

## B.U.T.T. (Broadcast Using This Tool)

<iframe
   width="560"
   height="315"
   src="https://www.youtube-nocookie.com/embed/4GLsU9hPTtM"
   frameborder="0"
   allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
   allowfullscreen
></iframe>

### Configurer

1. Téléchargez et installez BUTT pour votre système d'exploitation.[BUTT](https://danielnoethen.de/). Note : assurez-vous d'avoir installé la version 0.1.17 de BUTT ou une version plus récente.
   _Note: be sure you have butt version 0.1.17 or newer installed_
2. Ouvrez BUTT
3. Cliquer **settings**
4. Sous **Main** > **Server**, cliquez sur **ADD**
   - Tapez LibreTime (ou votre station) sous Nom
   - Cliquez sur le bouton radio à côté de **IceCast** sous Type
   - Type l'URL (adresse de la page Web) de votre station sous **Addresse**:
   - Tapez **8002** sous **Port**:
   - Tapez votre mot de passe de compte DJ sous **Password**
   - Tapez **/show** sous IceCast mountpoint:
     -Tapez votre login dj sous **IceCast user:**
5. Clicquer **ADD**
6. Still in settings click, **Audio** and select your audio input device under **Audio Device**

### Heure du programme

1.Quand c'est presque l'heure de votre programme, allez sur votre page LibreTime et regardez l'heure en haut à droite quand votre programme commence, allez sur Butt. 2. Cliquez sur le bouton blanc Play (troisième bouton au milieu). 3. Si le message "connecting..." s'affiche, suivi du temps de diffusion avec un compteur, félicitations, vous êtes connecté ! 4. Allez sur la page LibreTime et en haut à droite sous Source Streams l'onglet à côté de Show Source est à gauche et Orange - si c'est le cas et que Current affiche Live Show vous êtes connecté. 5. S'il est gris, cliquez sur l'interrupteur **Show Source** à droite de celui-ci et il activera votre émission et vous serez diffusé. Remarque : le fait que la connexion automatique soit activée ou non est un paramètre spécifique à la station, qui peut donc fonctionner dans les deux cas.

### Enregistrement de votre programme

Vous pouvez enregistrer votre émission sous butt en cliquant sur le cercle rouge du bouton d'enregistrement à gauche. Par défaut, il enregistrera un mp3 basé sur la date et l'heure dans votre répertoire personnel/utilisateur.

Tout devrait maintenant fonctionner et vous pouvez diffuser votre émission pendant toute la durée du créneau horaire. Si vous décidez d'arrêter la diffusion avant la fin, cliquez sur le bouton carré blanc **Stop** pour vous déconnecter. Ensuite, allez sur la page LibreTime et si la source de diffusion ne s'est pas déconnectée automatiquement, vous pouvez la cliquer à droite et elle devrait devenir grise.
